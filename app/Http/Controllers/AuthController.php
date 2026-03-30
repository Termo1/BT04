<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:128'],
            'last_name'  => ['required', 'string', 'min:2', 'max:128'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => $validated['password'],
            'role'       => 'user',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Registrácia prebehla úspešne.',
            'user' => $user,
            'token' => $token,
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Neplatné prihlasovacie údaje.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Prihlásenie bolo úspešné.',
            'user' => $user,
            'token' => $token,
        ], Response::HTTP_OK);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => $user,
            'active_sessions' => $user->tokens()->count(),
        ], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Odhlásenie bolo úspešné.',
        ], Response::HTTP_OK);
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Všetky relácie boli ukončené.',
        ], Response::HTTP_OK);
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Aktuálne heslo nie je správne.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->update([
            'password' => $validated['password'],
        ]);

        return response()->json([
            'message' => 'Heslo bolo úspešne zmenené.',
        ], Response::HTTP_OK);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['sometimes', 'required', 'string', 'min:2', 'max:128'],
            'last_name'  => ['sometimes', 'required', 'string', 'min:2', 'max:128'],
        ]);

        $request->user()->update($validated);

        return response()->json([
            'message' => 'Profil bol úspešne aktualizovaný.',
            'user' => $request->user(),
        ], Response::HTTP_OK);
    }
}
