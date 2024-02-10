<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Récupérer la liste des posts
Route::get('/posts', [PostController::class, 'index']);

//  Ajouter un post
Route::post('/posts/create', [PostController::class, 'store']);

// Modifier un post
Route::put('/posts/{post}/edit', [PostController::class, 'update']);

// Supprimer un post
Route::delete('/posts/{post}/delete', [PostController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
