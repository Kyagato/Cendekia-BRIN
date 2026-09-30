<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ForumApiController;
use App\Http\Controllers\Api\KnowledgeApiController;
use App\Http\Controllers\Api\UserApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoint API untuk MojoPedia.
| Dokumentasi Swagger tersedia di: /api/documentation
|
*/

Route::get('/health-check', function () {
    return response()->json(['status' => 'healthy']);
});

// Autentikasi API (Sanctum Token)
Route::post('/login', [AuthApiController::class, 'login'])->middleware('throttle:api-login');
Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ============================================================
// API Kategori (Publik & Admin)
// ============================================================
Route::get('/categories', [CategoryApiController::class, 'index']);
Route::get('/categories/{id}', [CategoryApiController::class, 'show'])->whereNumber('id');

// ============================================================
// API Knowledge (Publik & Protected)
// ============================================================
Route::get('/knowledge', [KnowledgeApiController::class, 'index']);
Route::get('/knowledge/{id}', [KnowledgeApiController::class, 'show'])->whereNumber('id');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/knowledge', [KnowledgeApiController::class, 'store']);
    Route::delete('/knowledge/{id}', [KnowledgeApiController::class, 'destroy'])->whereNumber('id');
});

// ============================================================
// API Forum Diskusi (Publik & Protected)
// ============================================================
Route::get('/forum/threads', [ForumApiController::class, 'index']);
Route::get('/forum/threads/{id}', [ForumApiController::class, 'show'])->whereNumber('id');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/forum/threads', [ForumApiController::class, 'store']);
    Route::post('/forum/threads/{id}/replies', [ForumApiController::class, 'reply'])->whereNumber('id');
});

// ============================================================
// API Manajemen User & Role & Admin Content
// ============================================================
Route::prefix('admin')->middleware(['auth:sanctum', 'role:Super Admin,Admin Pusat,Admin'])->group(function () {
    // Kategori CMS
    Route::post('/categories', [CategoryApiController::class, 'store']);
    Route::put('/categories/{id}', [CategoryApiController::class, 'update'])->whereNumber('id');
    Route::delete('/categories/{id}', [CategoryApiController::class, 'destroy'])->whereNumber('id');

    // Daftar role yang tersedia
    Route::get('/roles', [UserApiController::class, 'availableRoles']);

    // CRUD User
    Route::get('/users', [UserApiController::class, 'index']);
    Route::get('/users/{user}', [UserApiController::class, 'show']);

    // Update Role User (akan terintegrasi dengan Keycloak)
    Route::put('/users/{user}/role', [UserApiController::class, 'updateRole']);
});

