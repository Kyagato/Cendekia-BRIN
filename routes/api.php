<?php

use App\Http\Controllers\Api\AuthApiController;
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
// API Manajemen User & Role
// ============================================================
Route::prefix('admin')->middleware(['auth:sanctum', 'role:Super Admin,Admin Pusat,Admin'])->group(function () {
    // Daftar role yang tersedia
    Route::get('/roles', [UserApiController::class, 'availableRoles']);

    // CRUD User
    Route::get('/users', [UserApiController::class, 'index']);
    Route::get('/users/{user}', [UserApiController::class, 'show']);

    // Update Role User (akan terintegrasi dengan Keycloak)
    Route::put('/users/{user}/role', [UserApiController::class, 'updateRole']);
});
