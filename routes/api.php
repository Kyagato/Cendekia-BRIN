<?php

use App\Http\Controllers\Api\UserApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoint API untuk Cendekia-BRIN.
| Dokumentasi Swagger tersedia di: /api/documentation
|
*/

Route::get('/health-check', function () {
    return response()->json(['status' => 'healthy']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ============================================================
// API Manajemen User & Role
// ============================================================
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    // Daftar role yang tersedia
    Route::get('/roles', [UserApiController::class, 'availableRoles']);

    // CRUD User
    Route::get('/users', [UserApiController::class, 'index']);
    Route::get('/users/{user}', [UserApiController::class, 'show']);

    // Update Role User (akan terintegrasi dengan Keycloak)
    Route::put('/users/{user}/role', [UserApiController::class, 'updateRole']);
});
