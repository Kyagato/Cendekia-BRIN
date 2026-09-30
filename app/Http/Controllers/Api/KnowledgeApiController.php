<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KnowledgeResource;
use App\Models\Category;
use App\Models\Knowledge;
use App\Models\Tag;
use App\Services\KnowledgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class KnowledgeApiController extends Controller
{
    protected KnowledgeService $knowledgeService;

    public function __construct(KnowledgeService $knowledgeService)
    {
        $this->knowledgeService = $knowledgeService;
    }

    #[OA\Get(
        path: '/knowledge',
        operationId: 'getKnowledgeList',
        summary: 'Daftar repositori pengetahuan (Publik / Filterable)',
        description: 'Mendukung filter berdasarkan kata kunci (q), tipe media, kategori, label, instansi, sort, dan pagination.',
        tags: ['Knowledge'],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', required: false, description: 'Pencarian teks judul, deskripsi, penulis', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'tipe', in: 'query', required: false, description: 'Filter tipe: Teks, Video, Gambar, Audio', schema: new OA\Schema(type: 'string', enum: ['Teks', 'Video', 'Gambar', 'Audio'])),
            new OA\Parameter(name: 'kategori', in: 'query', required: false, description: 'ID kategori', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort', in: 'query', required: false, description: 'Sorting: terbaru, terpopuler, az, za', schema: new OA\Schema(type: 'string', default: 'terbaru')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Jumlah data per halaman (default: 12)', schema: new OA\Schema(type: 'integer', default: 12)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil mengambil daftar pengetahuan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'meta', type: 'object')
                    ]
                )
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 12), 1), 50);

        $query = Knowledge::with(['category', 'user', 'tags'])
            ->where('status', 'Disetujui');

        // Gunakan filter bawaan model Knowledge (termasuk sorting via parameter 'sort')
        $query->filter($request->all());

        $knowledge = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => KnowledgeResource::collection($knowledge->items()),
            'meta' => [
                'current_page' => $knowledge->currentPage(),
                'last_page' => $knowledge->lastPage(),
                'per_page' => $knowledge->perPage(),
                'total' => $knowledge->total(),
            ]
        ]);
    }

    #[OA\Get(
        path: '/knowledge/{id}',
        operationId: 'getKnowledgeDetail',
        summary: 'Detail dokumen pengetahuan & catat view',
        tags: ['Knowledge'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Data pengetahuan ditemukan'),
            new OA\Response(response: 404, description: 'Pengetahuan tidak ditemukan')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $knowledge = Knowledge::with(['category', 'user', 'tags'])->find($id);

        if (!$knowledge) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengetahuan tidak ditemukan',
            ], 404);
        }

        // Increment views count secara atomik
        $knowledge->increment('views_count');

        return response()->json([
            'status' => 'success',
            'data' => new KnowledgeResource($knowledge),
        ]);
    }

    #[OA\Post(
        path: '/knowledge',
        operationId: 'storeKnowledge',
        summary: 'Kirim / Unggah pengetahuan baru (Sanctum)',
        security: [['sanctum' => []]],
        tags: ['Knowledge'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['judul', 'category_id', 'tipe'],
                    properties: [
                        new OA\Property(property: 'judul', type: 'string', example: 'Panduan Keamanan Siber'),
                        new OA\Property(property: 'category_id', type: 'integer', example: 1),
                        new OA\Property(property: 'tipe', type: 'string', enum: ['Teks', 'Video', 'Gambar', 'Audio']),
                        new OA\Property(property: 'deskripsi', type: 'string', example: 'Ringkasan panduan...'),
                        new OA\Property(property: 'detail', type: 'string', example: '<p>Detail lengkap materi</p>'),
                        new OA\Property(property: 'penulis', type: 'string', example: 'Diskominfo'),
                        new OA\Property(property: 'kolaborator', type: 'string', example: 'BSSN'),
                        new OA\Property(property: 'tags', type: 'string', example: 'keamanan,sop,data'),
                        new OA\Property(property: 'file', type: 'string', format: 'binary'),
                        new OA\Property(property: 'gambar_sampul', type: 'string', format: 'binary'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Pengetahuan berhasil diajukan'),
            new OA\Response(response: 422, description: 'Validasi form gagal')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tipe' => 'required|in:Teks,Video,Gambar,Audio',
            'deskripsi' => 'nullable|string',
            'detail' => 'nullable|string',
            'penulis' => 'nullable|string|max:255',
            'kolaborator' => 'nullable|string|max:255',
            'status_akses' => 'nullable|in:public,private',
            'tags' => 'nullable|string',
            'file' => 'nullable|file|max:51200', // max 50MB
            'gambar_sampul' => 'nullable|image|max:5120', // max 5MB
        ]);

        $filePath = null;
        $fileName = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $filePath = $uploadedFile->store('knowledge_files', 'public');
            $fileName = $uploadedFile->getClientOriginalName();
            $fileSize = $uploadedFile->getSize();
        }

        $coverPath = null;
        if ($request->hasFile('gambar_sampul')) {
            $coverPath = $request->file('gambar_sampul')->store('knowledge_covers', 'public');
        }

        // Tentukan status awal: auto-approve jika role Admin / Analis
        $status = ($user->isAdmin() || $user->isAnalyst()) ? 'Disetujui' : 'Diajukan';

        $knowledge = Knowledge::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'judul' => $validated['judul'],
            'tipe' => $validated['tipe'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'detail' => $validated['detail'] ?? null,
            'penulis' => $validated['penulis'] ?? $user->name,
            'kolaborator' => $validated['kolaborator'] ?? null,
            'status_akses' => $validated['status_akses'] ?? 'public',
            'status' => $status,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'gambar_sampul' => $coverPath,
            'tanggal_terbit' => now(),
        ]);

        // Simpan tags bila ada
        if (!empty($validated['tags'])) {
            $tagNames = array_map('trim', explode(',', $validated['tags']));
            $tagIds = [];
            foreach ($tagNames as $name) {
                if ($name !== '') {
                    $tag = Tag::firstOrCreate(['nama_label' => $name]);
                    $tagIds[] = $tag->id;
                }
            }
            $knowledge->tags()->sync($tagIds);
        }

        $knowledge->load(['category', 'user', 'tags']);

        return response()->json([
            'status' => 'success',
            'message' => 'Dokumen pengetahuan berhasil diajukan (' . $status . ')',
            'data' => new KnowledgeResource($knowledge),
        ], 201);
    }

    #[OA\Delete(
        path: '/knowledge/{id}',
        operationId: 'deleteKnowledge',
        summary: 'Hapus dokumen pengetahuan (Pemilik atau Admin)',
        security: [['sanctum' => []]],
        tags: ['Knowledge'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Pengetahuan berhasil dihapus'),
            new OA\Response(response: 403, description: 'Tidak memiliki izin'),
            new OA\Response(response: 404, description: 'Pengetahuan tidak ditemukan')
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $knowledge = Knowledge::find($id);
        if (!$knowledge) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengetahuan tidak ditemukan',
            ], 404);
        }

        $user = $request->user();
        if ($knowledge->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki hak untuk menghapus dokumen ini.',
            ], 403);
        }

        $this->knowledgeService->deleteKnowledge($knowledge);

        return response()->json([
            'status' => 'success',
            'message' => 'Dokumen pengetahuan berhasil dihapus',
        ]);
    }
}
