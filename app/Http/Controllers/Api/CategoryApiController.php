<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CategoryApiController extends Controller
{
    #[OA\Get(
        path: '/categories',
        operationId: 'getCategories',
        summary: 'Daftar semua kategori pengetahuan',
        description: 'Mengambil seluruh daftar kategori beserta jumlah dokumen terasosiasi yang berstatus Disetujui.',
        tags: ['Categories'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil mengambil daftar kategori',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $categories = Category::withCount(['knowledge' => function ($q) {
            $q->where('status', 'Disetujui');
        }])->get();

        return response()->json([
            'status' => 'success',
            'data' => CategoryResource::collection($categories),
        ]);
    }

    #[OA\Get(
        path: '/categories/{id}',
        operationId: 'getCategoryDetail',
        summary: 'Detail satu kategori',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Detail kategori ditemukan'),
            new OA\Response(response: 404, description: 'Kategori tidak ditemukan')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $category = Category::withCount(['knowledge' => function ($q) {
            $q->where('status', 'Disetujui');
        }])->find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => new CategoryResource($category),
        ]);
    }

    #[OA\Post(
        path: '/admin/categories',
        operationId: 'createCategory',
        summary: 'Tambah kategori baru (Admin)',
        security: [['sanctum' => []]],
        tags: ['Categories'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nama_kategori'],
                properties: [
                    new OA\Property(property: 'nama_kategori', type: 'string', example: 'Informatika & Siber'),
                    new OA\Property(property: 'deskripsi', type: 'string', example: 'Dokumen seputar infrastruktur IT & keamanan informasi')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Kategori berhasil ditambahkan'),
            new OA\Response(response: 422, description: 'Validasi gagal')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori',
            'deskripsi'     => 'nullable|string',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil ditambahkan',
            'data' => new CategoryResource($category),
        ], 201);
    }

    #[OA\Put(
        path: '/admin/categories/{id}',
        operationId: 'updateCategory',
        summary: 'Perbarui kategori (Admin)',
        security: [['sanctum' => []]],
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nama_kategori'],
                properties: [
                    new OA\Property(property: 'nama_kategori', type: 'string', example: 'Informatika'),
                    new OA\Property(property: 'deskripsi', type: 'string', example: 'Deskripsi baru')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Kategori berhasil diperbarui'),
            new OA\Response(response: 404, description: 'Kategori tidak ditemukan')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori,' . $id,
            'deskripsi'     => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil diperbarui',
            'data' => new CategoryResource($category),
        ]);
    }

    #[OA\Delete(
        path: '/admin/categories/{id}',
        operationId: 'deleteCategory',
        summary: 'Hapus kategori (Admin)',
        security: [['sanctum' => []]],
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Kategori berhasil dihapus'),
            new OA\Response(response: 400, description: 'Kategori masih memiliki pengetahuan terkait'),
            new OA\Response(response: 404, description: 'Kategori tidak ditemukan')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $category = Category::withCount('knowledge')->find($id);
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        if ($category->knowledge_count > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak dapat dihapus karena masih memuat ' . $category->knowledge_count . ' dokumen pengetahuan.',
            ], 400);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus',
        ]);
    }
}
