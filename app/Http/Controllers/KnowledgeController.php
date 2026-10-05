<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKnowledgeRequest;
use App\Http\Requests\UpdateKnowledgeRequest;
use App\Models\Category;
use App\Models\Knowledge;
use App\Services\KnowledgeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KnowledgeController extends Controller
{
    protected KnowledgeService $knowledgeService;

    public function __construct(KnowledgeService $knowledgeService)
    {
        $this->knowledgeService = $knowledgeService;
    }

    /**
     * Menampilkan daftar riwayat dan draf pengetahuan.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Knowledge::with(['category', 'user', 'tags'])
            ->whereIn('status', ['Diajukan', 'Disetujui', 'Ditolak']);

        // Filter: Hanya tampilkan riwayat unggahan milik user yang sedang login (kecuali Admin)
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $query->latest();

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('status')) {
            $statusMap = [
                'diajukan' => 'Diajukan',
                'diterima' => 'Disetujui',
                'ditolak'  => 'Ditolak'
            ];
            if (isset($statusMap[strtolower($request->status)])) {
                $query->where('status', $statusMap[strtolower($request->status)]);
            }
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $knowledges = $query->paginate(10, ['*'], 'page_knowledges')->appends($request->all());

        // Ambil draft khusus user yang sedang login
        $drafts = Knowledge::with(['category', 'user', 'tags'])
            ->where('status', 'Draft')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(5, ['*'], 'page_drafts')
            ->appends($request->all());

        return view('knowledge.index', compact('knowledges', 'drafts'));
    }

    /**
     * Menampilkan form upload pengetahuan baru.
     */
    public function create(): View
    {
        $categories = Category::all();
        return view('knowledge.create', compact('categories'));
    }

    /**
     * Menyimpan pengetahuan baru menggunakan FormRequest dan KnowledgeService.
     */
    public function store(StoreKnowledgeRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();

        $knowledge = $this->knowledgeService->createKnowledge(
            data: $validatedData,
            fileUpload: $request->file('file_upload'),
            audioFile: $request->file('audio_file'),
            user: $user
        );

        if ($knowledge->status === 'Disetujui') {
            $message = 'Pengetahuan berhasil ditambahkan dan langsung berstatus disetujui.';
        } elseif ($knowledge->status === 'Draft') {
            $message = 'Pengetahuan berhasil disimpan sebagai draft.';
        } else {
            $message = 'Pengetahuan berhasil diajukan dan menunggu validasi.';
        }

        return redirect()->route('knowledge.index')->with('success', $message);
    }

    /**
     * Menampilkan detail pengetahuan untuk pratinjau dashboard admin.
     */
    public function show(int $id): View
    {
        $user = Auth::user();

        // Cari pengetahuan termasuk yang ada di tong sampah jika milik user sendiri atau user adalah admin
        $knowledgeQuery = Knowledge::withTrashed()->with(['category', 'user', 'tags', 'threads.user']);

        if (!$user->isAdmin()) {
            $knowledge = $knowledgeQuery->where(function ($q) use ($user) {
                $q->whereNull('deleted_at')
                  ->orWhere('user_id', $user->id);
            })->findOrFail($id);
        } else {
            $knowledge = $knowledgeQuery->findOrFail($id);
        }

        if (!$knowledge->trashed()) {
            $knowledge->increment('views_count');
        }

        return view('knowledge.show', compact('knowledge'));
    }

    /**
     * Menampilkan form edit pengetahuan.
     */
    public function edit(Knowledge $knowledge): View
    {
        Gate::authorize('edit-knowledge', $knowledge);

        $categories = Category::all();
        return view('knowledge.edit', compact('knowledge', 'categories'));
    }

    /**
     * Memperbarui pengetahuan menggunakan UpdateKnowledgeRequest dan KnowledgeService.
     */
    public function update(UpdateKnowledgeRequest $request, Knowledge $knowledge): RedirectResponse
    {
        Gate::authorize('edit-knowledge', $knowledge);

        // Handle aksi "Batal Ajukan" — kembalikan status dari Diajukan ke Draft
        if ($request->has('batal_ajukan')) {
            $knowledge->update(['status' => 'Draft']);
            return redirect()->route('knowledge.index')->with('success', 'Pengajuan berhasil dibatalkan. Status kembali ke Draft.');
        }

        $this->knowledgeService->updateKnowledge(
            knowledge: $knowledge,
            data: $request->validated(),
            fileUpload: $request->file('file_upload'),
            audioFile: $request->file('audio_file'),
            user: $request->user()
        );

        return redirect()->route('knowledge.index')->with('success', 'Pengetahuan berhasil diperbarui.');
    }

    /**
     * Menghapus artikel pengetahuan ke tong sampah (Soft Delete).
     */
    public function destroy(Knowledge $knowledge): RedirectResponse
    {
        Gate::authorize('delete-knowledge', $knowledge);

        $this->knowledgeService->deleteKnowledge($knowledge);

        return redirect()->route('knowledge.index')->with('success', 'Pengetahuan berhasil dipindahkan ke tong sampah.');
    }

    /**
     * Menampilkan daftar pengetahuan yang telah dihapus (Tong Sampah) milik user.
     */
    public function trash(Request $request): View
    {
        $user = Auth::user();

        // Hanya tampilkan sampah milik user yang sedang login
        $query = Knowledge::onlyTrashed()
            ->with(['category', 'tags', 'user'])
            ->where('user_id', $user->id)
            ->latest('deleted_at');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $trashedKnowledges = $query->paginate(10)->withQueryString();

        return view('knowledge.trash', compact('trashedKnowledges'));
    }

    /**
     * Memulihkan artikel pengetahuan dari tong sampah (Restore).
     */
    public function restore(int $id): RedirectResponse
    {
        $user = Auth::user();

        $knowledge = Knowledge::onlyTrashed()
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $this->knowledgeService->restoreKnowledge($knowledge);

        return redirect()->route('knowledge.trash')->with('success', 'Pengetahuan berhasil dipulihkan.');
    }

    /**
     * Menghapus artikel pengetahuan secara permanen dari tong sampah.
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $user = Auth::user();

        $knowledge = Knowledge::onlyTrashed()
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $this->knowledgeService->forceDeleteKnowledge($knowledge);

        return redirect()->route('knowledge.trash')->with('success', 'Pengetahuan berhasil dihapus permanen.');
    }
}