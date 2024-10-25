<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify', [AuthController::class, 'verify']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Tags
    Route::apiResource('tags', TagController::class);

    // Posts
    Route::get('posts/trashed', [PostController::class, 'trashed']);
    Route::post('posts/{id}/restore', [PostController::class, 'restore']);
    Route::apiResource('posts', PostController::class);

    // Stats
    Route::get('stats', [StatsController::class, 'index']);
});
