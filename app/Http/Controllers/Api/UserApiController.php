<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\KeycloakAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserApiController extends Controller
{
    protected KeycloakAdminService $keycloakService;

    public function __construct(KeycloakAdminService $keycloakService)
    {
        $this->keycloakService = $keycloakService;
    }
    /**
     * Menampilkan daftar semua user.
     */
    #[OA\Get(
        path: '/admin/users',
        operationId: 'getUsers',
        summary: 'Daftar semua user',
        description: 'Mengembalikan daftar user beserta role-nya. Bisa difilter berdasarkan role dan pencarian nama/email.',
        security: [['sanctum' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'role',
                in: 'query',
                required: false,
                description: 'Filter berdasarkan role (contoh: Admin, Moderator, Anggota)',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'q',
                in: 'query',
                required: false,
                description: 'Pencarian berdasarkan nama atau email',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                required: false,
                description: 'Jumlah data per halaman (min: 1, max: 100, default: 15)',
                schema: new OA\Schema(type: 'integer', default: 15, minimum: 1, maximum: 100)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil mengambil daftar user',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'data',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                                            new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                                            new OA\Property(property: 'role', type: 'string', example: 'Anggota'),
                                            new OA\Property(property: 'instansi', type: 'string', example: 'BRIN'),
                                            new OA\Property(property: 'foto_profil', type: 'string', nullable: true),
                                            new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', nullable: true),
                                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                        ]
                                    )
                                ),
                                new OA\Property(property: 'links', type: 'object'),
                                new OA\Property(property: 'meta', type: 'object'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden (Akses ditolak: Hanya untuk Admin)'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }

        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Batasi per_page antara 1 sampai 100 untuk mencegah kelebihan memori (DoS)
        $perPage = min(max((int) $request->get('per_page', 15), 1), 100);

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => UserResource::collection($users)->response()->getData(true),
        ]);
    }

    /**
     * Menampilkan detail satu user.
     */
    #[OA\Get(
        path: '/admin/users/{id}',
        operationId: 'getUser',
        summary: 'Detail satu user',
        description: 'Mengembalikan data lengkap dari user berdasarkan ID.',
        security: [['sanctum' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID user',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil mengambil detail user',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                                new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                                new OA\Property(property: 'role', type: 'string', example: 'Anggota'),
                                new OA\Property(property: 'instansi', type: 'string', example: 'BRIN'),
                                new OA\Property(property: 'jenis_kelamin', type: 'string', example: 'L'),
                                new OA\Property(property: 'keycloak_id', type: 'string', nullable: true),
                                new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', nullable: true),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'User tidak ditemukan'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden (Akses ditolak: Hanya untuk Admin)'),
        ]
    )]
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'instansi' => $user->instansi,
                'jenis_kelamin' => $user->jenis_kelamin,
                'keycloak_id' => $user->keycloak_id,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    /**
     * Menampilkan daftar role yang tersedia.
     */
    #[OA\Get(
        path: '/admin/roles',
        operationId: 'getAvailableRoles',
        summary: 'Daftar role yang tersedia',
        description: 'Mengembalikan semua role yang ada di sistem (sesuai Keycloak Realm Roles).',
        security: [['sanctum' => []]],
        tags: ['Roles'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil mengambil daftar role',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'roles',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: ['Super Admin', 'Admin Pusat', 'Admin', 'Anggota', 'Analisis Pengetahuan', 'Moderator']
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden (Akses ditolak: Hanya untuk Admin)'),
        ]
    )]
    public function availableRoles(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'roles' => User::ALL_ROLES,
            ],
        ]);
    }

    /**
     * Mengubah role user.
     */
    #[OA\Put(
        path: '/admin/users/{id}/role',
        operationId: 'updateUserRole',
        summary: 'Ubah role user',
        description: 'Mengubah role user di database lokal. Nantinya akan terintegrasi dengan Keycloak Admin API untuk sinkronisasi realm role.',
        security: [['sanctum' => []]],
        tags: ['Roles'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID user yang ingin diubah role-nya',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Data role baru',
            content: new OA\JsonContent(
                required: ['role'],
                properties: [
                    new OA\Property(
                        property: 'role',
                        type: 'string',
                        description: 'Role baru untuk user',
                        enum: ['Super Admin', 'Admin Pusat', 'Admin', 'Anggota', 'Analisis Pengetahuan', 'Moderator'],
                        example: 'Moderator'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Role berhasil diubah',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: "Role berhasil diubah dari 'Anggota' menjadi 'Moderator'."),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                                new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                                new OA\Property(property: 'old_role', type: 'string', example: 'Anggota'),
                                new OA\Property(property: 'new_role', type: 'string', example: 'Moderator'),
                                new OA\Property(property: 'keycloak_synced', type: 'boolean', example: false),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'User tidak ditemukan'),
            new OA\Response(response: 422, description: 'Validasi gagal (role tidak valid)'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden (Akses ditolak: Hanya untuk Admin/Super Admin)'),
        ]
    )]
    public function updateRole(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        // Pencegahan eskalasi role hierarkis:
        // Jika bukan Super Admin, tidak diizinkan mengubah user Super Admin atau mempromosikan user ke Super Admin
        if ($currentUser && !$currentUser->isSuperAdmin()) {
            if ($user->isSuperAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki hak akses untuk mengubah role Super Admin.',
                ], 403);
            }

            if ($request->input('role') === User::ROLE_SUPER_ADMIN) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya Super Admin yang berhak memberikan role Super Admin.',
                ], 403);
            }
        }

        // Toleransi jika user memasukkan "Analis Pengetahuan" (akan dinormalisasi ke "Analisis Pengetahuan")
        if ($request->input('role') === 'Analis Pengetahuan') {
            $request->merge(['role' => User::ROLE_ANALIS]);
        }

        $validated = $request->validate([
            'role' => 'required|string|in:' . implode(',', User::ALL_ROLES),
        ]);

        $oldRole = $user->role;
        $newRole = $validated['role'];

        $keycloakSynced = false;
        $keycloakMessage = null;

        // Sinkronisasi ke Keycloak jika user terhubung dengan Keycloak SSO
        if (!empty($user->keycloak_id)) {
            $kcResult = $this->keycloakService->syncUserRole($user->keycloak_id, $newRole);
            $keycloakSynced = $kcResult['keycloak_synced'];
            $keycloakMessage = $kcResult['message'];
        }

        $user->update(['role' => $newRole]);

        $message = "Role berhasil diubah dari '{$oldRole}' menjadi '{$newRole}'.";
        if ($keycloakMessage) {
            $message .= " ({$keycloakMessage})";
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'old_role' => $oldRole,
                'new_role' => $newRole,
                'keycloak_synced' => $keycloakSynced,
            ],
        ]);
    }
}
