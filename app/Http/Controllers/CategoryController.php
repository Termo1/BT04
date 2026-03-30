<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        return response()->json(['categories' => $categories], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:64', 'unique:categories,name'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Kategória bola úspešne vytvorená.',
            'category' => $category,
        ], Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Kategória nenájdená.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['category' => $category], Response::HTTP_OK);
    }

    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Kategória nenájdená.',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:64', 'unique:categories,name,' . $id],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Kategória bola úspešne aktualizovaná.',
            'category' => $category,
        ], Response::HTTP_OK);
    }

    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Kategória nenájdená.',
            ], Response::HTTP_NOT_FOUND);
        }

        $category->delete();

        return response()->json([
            'message' => 'Kategória bola úspešne odstránená.',
        ], Response::HTTP_OK);
    }
}
