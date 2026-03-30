<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Notes
    Route::get('notes/stats/status', [NoteController::class, 'statsByStatus']);
    Route::patch('notes/actions/archive-old-drafts', [NoteController::class, 'archiveOldDrafts']);
    Route::patch('notes/{note}/toggle-pin', [NoteController::class, 'togglePin']);
    Route::get('notes-actions/search', [NoteController::class, 'search']);
    Route::get('users/{user}/notes', [NoteController::class, 'userNotesWithCategories']);

    Route::apiResource('notes', NoteController::class);

    // Tasks (nested under notes)
    Route::apiResource('notes.tasks', TaskController::class)->scoped();

    // Categories - read (any authenticated user)
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
});

// Categories - write (admin only)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
});
