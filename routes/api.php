<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('notes/stats/status', [NoteController::class, 'statsByStatus']);
Route::patch('notes/actions/archive-old-drafts', [NoteController::class, 'archiveOldDrafts']);
Route::patch('notes/{note}/toggle-pin', [NoteController::class, 'togglePin']);
Route::get('notes-actions/search', [NoteController::class, 'search']);
Route::get('users/{user}/notes', [NoteController::class, 'userNotesWithCategories']);

Route::apiResource('notes', NoteController::class);
Route::apiResource('categories', CategoryController::class);