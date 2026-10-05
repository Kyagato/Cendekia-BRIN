<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ForumReplyResource;
use App\Http\Resources\ForumThreadResource;
use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ForumApiController extends Controller
{
    #[OA\Get(
        path: '/forum/threads',
        operationId: 'getForumThreads',
        summary: 'Daftar thread diskusi forum (Publik)',
        description: 'Menampilkan seluruh diskusi yang sudah disetujui, mendukung filter kategori, sorting terpopuler/terbaru, dan pagination.',
        tags: ['Forum'],
        parameters: [
            new OA\Parameter(name: 'kategori', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['terbaru', 'terpopuler'])),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil mengambil daftar thread forum',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 10), 1), 50);

        $query = ForumThread::approved()
            ->with(['user', 'category', 'knowledge'])
            ->withCount('replies');

        if ($request->filled('kategori')) {
            $query->where('category_id', $request->kategori);
        }

        if ($request->input('sort') === 'terpopuler') {
            $query->orderByDesc('replies_count');
        } else {
            $query->latest();
        }

        $threads = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => ForumThreadResource::collection($threads->items()),
            'meta' => [
                'current_page' => $threads->currentPage(),
                'last_page' => $threads->lastPage(),
                'per_page' => $threads->perPage(),
                'total' => $threads->total(),
            ]
        ]);
    }

    #[OA\Get(
        path: '/forum/threads/{id}',
        operationId: 'getForumThreadDetail',
        summary: 'Detail thread diskusi beserta balasan / komentar',
        tags: ['Forum'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Detail thread dan balasan'),
            new OA\Response(response: 404, description: 'Thread tidak ditemukan')
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user('sanctum');

        $query = ForumThread::with([
            'user', 
            'category', 
            'knowledge', 
            'replies' => function ($q) {
                $q->whereNull('parent_id')->with(['user', 'replies.user'])->latest();
            }
        ])->withCount('replies');

        if ($user && ($user->isAdmin() || $user->isModerator())) {
            $thread = $query->find($id);
        } elseif ($user) {
            $thread = $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('status', 'approved');
            })->find($id);
        } else {
            $thread = $query->where('status', 'approved')->find($id);
        }

        if (!$thread) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thread forum tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => new ForumThreadResource($thread),
        ]);
    }

    #[OA\Post(
        path: '/forum/threads',
        operationId: 'createForumThread',
        summary: 'Buat topik diskusi forum baru (Sanctum)',
        security: [['sanctum' => []]],
        tags: ['Forum'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['judul', 'konten', 'category_id'],
                properties: [
                    new OA\Property(property: 'judul', type: 'string', example: 'Diskusi Implementasi SPBE 2026'),
                    new OA\Property(property: 'konten', type: 'string', example: 'Bagaimana evaluasi arsitektur domain layanan di OPD Anda?'),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'knowledge_id', type: 'integer', nullable: true, example: 5),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Thread forum berhasil dibuat'),
            new OA\Response(response: 422, description: 'Validasi form gagal')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'knowledge_id' => 'nullable|exists:knowledge,id',
        ]);

        $isAutoApprove = ForumThread::canAutoApprove($user);

        $thread = ForumThread::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'knowledge_id' => $validated['knowledge_id'] ?? null,
            'judul' => strip_tags($validated['judul']),
            'konten' => strip_tags($validated['konten']),
            'status' => $isAutoApprove ? 'approved' : 'pending',
            'approved_at' => $isAutoApprove ? now() : null,
            'approved_by' => $isAutoApprove ? $user->id : null,
        ]);

        $thread->load(['user', 'category', 'knowledge']);

        return response()->json([
            'status' => 'success',
            'message' => $isAutoApprove ? 'Thread berhasil diterbitkan' : 'Thread telah diajukan dan menunggu persetujuan moderator',
            'data' => new ForumThreadResource($thread),
        ], 201);
    }

    #[OA\Post(
        path: '/forum/threads/{id}/replies',
        operationId: 'replyForumThread',
        summary: 'Kirim balasan / komentar pada thread (Sanctum)',
        security: [['sanctum' => []]],
        tags: ['Forum'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['konten'],
                properties: [
                    new OA\Property(property: 'konten', type: 'string', example: 'Sangat setuju, kami sedang menyusun SOP integrasi.'),
                    new OA\Property(property: 'parent_id', type: 'integer', nullable: true, example: null)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Balasan berhasil dikirim'),
            new OA\Response(response: 400, description: 'Thread dikunci'),
            new OA\Response(response: 404, description: 'Thread tidak ditemukan')
        ]
    )]
    public function reply(Request $request, int $id): JsonResponse
    {
        $thread = ForumThread::find($id);
        if (!$thread) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thread forum tidak ditemukan',
            ], 404);
        }

        if ($thread->status !== 'approved') {
            return response()->json([
                'status' => 'error',
                'message' => 'Thread ini belum disetujui, sehingga belum dapat menerima balasan.',
            ], 400);
        }

        if ($thread->is_locked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thread ini telah dikunci dan tidak menerima balasan baru.',
            ], 400);
        }

        $validated = $request->validate([
            'konten' => 'required|string',
            'parent_id' => 'nullable|exists:forum_replies,id',
        ]);

        $reply = ForumReply::create([
            'thread_id' => $thread->id,
            'user_id' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'konten' => strip_tags($validated['konten']),
        ]);

        $reply->load('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Balasan berhasil dikirim',
            'data' => new ForumReplyResource($reply),
        ], 201);
    }
}
