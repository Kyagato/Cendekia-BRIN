<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthApiController extends Controller
{
    /**
     * Login dan dapatkan Bearer Token Sanctum.
     */
    #[OA\Post(
        path: '/login',
        operationId: 'apiLogin',
        summary: 'Login API & Dapatkan Sanctum Token',
        description: 'Autentikasi dengan email dan password untuk mendapatkan token Bearer Sanctum. Token ini dapat ditempelkan di tombol "Authorize" pada Swagger UI.',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Kredensial login',
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'superadmin@brin.go.id'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'admin123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Login berhasil.'),
                        new OA\Property(property: 'token', type: 'string', example: '1|xxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Super Administrator'),
                                new OA\Property(property: 'email', type: 'string', example: 'superadmin@brin.go.id'),
                                new OA\Property(property: 'role', type: 'string', example: 'Super Admin'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal'
            ),
            new OA\Response(
                response: 401,
                description: 'Email atau password salah'
            ),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Logout dan hapus token yang sedang aktif.
     */
    #[OA\Post(
        path: '/logout',
        operationId: 'apiLogout',
        summary: 'Logout API & Revoke Token',
        description: 'Menghapus token yang sedang digunakan saat ini.',
        security: [['sanctum' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Logout berhasil, token telah dicabut.'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil, token telah dicabut.',
        ]);
    }
}
