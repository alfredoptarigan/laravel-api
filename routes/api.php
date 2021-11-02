<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Spatie\Permission\Traits\HasRoles;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post("/register", [AuthController::class, 'register']);
Route::post("/login", [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get("/auth/me", [AuthController::class, 'me']);
    Route::post("/auth/logout", [AuthController::class, 'logout']);
    Route::get('/post', [PostController::class, 'index']);
    Route::post('/post', [PostController::class, 'post']);
    Route::delete('/post', [PostController::class, 'delete']);
    Route::put('/post', [PostController::class, 'edit']);
});

Route::middleware(['auth:sanctum', 'role:super admin'])->group(function () {
    Route::get('/admin/histories', [AdminController::class, 'histories']);
    Route::put('/admin/edit-post', [AdminController::class, 'editPost']);
    Route::delete('/admin/delete-post', [AdminController::class, 'deletePost']);
});
