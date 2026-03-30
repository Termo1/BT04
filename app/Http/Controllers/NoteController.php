<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     // $notes = DB::table('notes')
    //     //     ->whereNull('deleted_at')
    //     //     ->orderBy('updated_at', 'desc')
    //     //     ->get();

    //     $notes = DB::query()->orderByDesc('updated_at')->get();
    //     return response()->json(['notes' => $notes], Response::HTTP_OK);
    // }

    public function index()
    {
        $notes = Note::query()
            ->select(['id', 'user_id', 'title', 'body', 'status', 'is_pinned', 'created_at'])
            ->with([
                'user:id,first_name,last_name',
                'categories:id,name,color',
            ])
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'notes' => $notes,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],

            'title' => ['required', 'string', 'min:3', 'max:255'],
            'body'  => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'is_pinned' => ['sometimes', 'boolean'],

            'categories' => ['sometimes', 'array', 'max:3'],
            'categories.*' => ['integer', 'distinct', 'exists:categories,id'],
        ]);

        $note = Note::create([
            'user_id'   => $validated['user_id'],
            'title'     => $validated['title'],
            'body'      => $validated['body'] ?? null,
            'status'    => $validated['status'] ?? 'draft',
            'is_pinned' => $validated['is_pinned'] ?? false,
        ]);

        if (!empty($validated['categories'])) {
            $note->categories()->sync($validated['categories']);
        }

        return response()->json([
            'message' => 'Poznámka bola úspešne vytvorená.',
            'note' => $note->load([
                'user:id,first_name,last_name',
                'categories:id,name,color',
            ]),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $note = DB::table('notes')
        //     ->whereNull('deleted_at')
        //     ->where('id', $id)
        //     ->first();

        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'note' => $note
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'message' => 'Poznámka nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        // DB::table('notes')->where('id', $id)->update([
        //     'title' => $request->title,
        //     'body' => $request->body,
        //     'updated_at' => now(),
        // ]);

        $note->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return response()->json([
            'message' => 'Poznámka bola úspešne aktualizovaná.'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id) // toto je soft delete
    {
        // $note = DB::table('notes')
        //     ->whereNull('deleted_at')
        //     ->where('id', $id)
        //     ->first();

        $note = Note::find($id);

        if (!$note) {
            return response()->json(['message' => 'Poznámka nenájdená.'], Response::HTTP_NOT_FOUND);
        }

        // DB::table('notes')->where('id', $id)->update([
        //     'deleted_at' => now(),
        //     'updated_at' => now(),
        // ]);
        $note->delete();

//        DB::table('notes')->where('id', $id)->delete();

        return response()->json(['message' => 'Poznámka bola úspešne odstránená.'], Response::HTTP_OK);
    }
    public function statsByStatus()
    {
        $stats = DB::table('notes')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'stats' => $stats
        ]);
    }
    public function archiveOldDrafts()
    {
        $affected = DB::table('notes')
            ->where('status', 'draft')
            ->where('updated_at', '<', now()->subDays(30))
            ->update([
                'status' => 'archived',
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Staré koncepty boli archivované.',
            'affected_rows' => $affected
        ]);
    }

    public function userNotesWithCategories(string $userId)
    {
        $notes = DB::table('notes')
            ->join('note_category', 'notes.id', '=', 'note_category.note_id')
            ->join('categories', 'note_category.category_id', '=', 'categories.id')
            ->where('notes.user_id', $userId)
            ->orderBy('notes.updated_at', 'desc')
            ->select('notes.id', 'notes.title', 'categories.name as category')
            ->get();

        return response()->json([
            'notes' => $notes
        ]);
    }

    public function togglePin(string $id)
    {
        $note = DB::table('notes')
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->first();

        if (!$note) {
            return response()->json(['message' => 'Poznámka nenájdená.'], Response::HTTP_NOT_FOUND);
        }

        DB::table('notes')->where('id', $id)->update([
            'is_pinned' => !$note->is_pinned,
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => $note->is_pinned ? 'Poznámka bola odopnutá.' : 'Poznámka bola pripnutá.',
            'is_pinned' => !$note->is_pinned,
        ], Response::HTTP_OK);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $notes = DB::table('notes')
            ->whereNull('deleted_at')
            ->where('status', 'published')
            ->where(function ($x) use ($q) {
                $x->where('title', 'like', "%{$q}%")
                    ->orWhere('body', 'like', "%{$q}%");
            })
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'query' => $q,
            'notes' => $notes,
        ], Response::HTTP_OK);
    }
}