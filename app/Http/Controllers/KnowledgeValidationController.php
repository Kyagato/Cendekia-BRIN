<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\Tag;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KnowledgeValidationController extends Controller
{
    /**
     * Menampilkan daftar artikel pengetahuan yang perlu divalidasi.
     */
    public function index(Request $request): View
    {
        $query = Knowledge::with(['user', 'category'])
            ->where('status', '!=', 'Draft')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $knowledges = $query->paginate(10)->withQueryString();

        return view('knowledge.validasi_index', compact('knowledges'));
    }

    /**
     * Menampilkan form peninjauan & pengeditan validasi artikel pengetahuan.
     */
    public function show(Knowledge $knowledge): View
    {
        $categories = Category::all();

        return view('knowledge.validasi', compact('knowledge', 'categories'));
    }

    /**
     * Memperbarui data artikel pengetahuan saat proses validasi berlangsung.
     */
    public function update(Request $request, Knowledge $knowledge): RedirectResponse
    {
        $validated = $request->validate([
            'judul'          => 'required|string|max:255',
            'tipe'           => 'required|in:Teks,Gambar,Video,Audio',
            'url_teks'       => 'nullable|string',
            'penulis'        => 'nullable|string|max:255',
            'kolaborator'    => 'nullable|string|max:255',
            'deskripsi'      => 'nullable|string',
            'detail'         => 'nullable|string',
            'category_id'    => 'nullable|exists:categories,id',
            'tanggal_terbit' => 'nullable|date',
            'file'           => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada dan file baru diupload
            if ($knowledge->file_path && Storage::disk('public')->exists($knowledge->file_path)) {
                Storage::disk('public')->delete($knowledge->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('knowledge_files', 'public');
        }

        $knowledge->update($validated);

        if ($request->has('tags')) {
            $tagsInput = explode(',', $request->tags ?? '');
            $tagIds = [];
            foreach ($tagsInput as $tagName) {
                $trimmed = trim($tagName);
                if ($trimmed !== '') {
                    $tag = Tag::firstOrCreate(['nama_label' => $trimmed]);
                    $tagIds[] = $tag->id;
                }
            }
            $knowledge->tags()->sync($tagIds);
        }

        AuditLog::record(
            'KNOWLEDGE_VALIDATE_UPDATE',
            "Memperbarui data artikel pada proses validasi: '{$knowledge->judul}' (ID: {$knowledge->id})",
            ['knowledge_id' => $knowledge->id, 'judul' => $knowledge->judul]
        );

        return back()->with('success', 'Data pengetahuan berhasil diperbarui.');
    }

    /**
     * Menyetujui (Approve) konten pengetahuan dan mempublikasikannya.
     */
    public function approve(Knowledge $knowledge): RedirectResponse
    {
        $knowledge->update(['status' => 'Disetujui']);

        AuditLog::record(
            'KNOWLEDGE_APPROVE',
            "Menyetujui konten pengetahuan: '{$knowledge->judul}' (ID: {$knowledge->id})",
            ['knowledge_id' => $knowledge->id, 'judul' => $knowledge->judul]
        );

        return back()->with('success', 'Konten berhasil disetujui.');
    }

    /**
     * Menolak (Reject) konten pengetahuan.
     */
    public function reject(Request $request, Knowledge $knowledge): RedirectResponse
    {
        $alasanTolak = $request->input('alasan_tolak');

        $knowledge->update(['status' => 'Ditolak']);

        AuditLog::record(
            'KNOWLEDGE_REJECT',
            "Menolak konten pengetahuan: '{$knowledge->judul}' (ID: {$knowledge->id})" . ($alasanTolak ? " dengan alasan: {$alasanTolak}" : ""),
            [
                'knowledge_id' => $knowledge->id,
                'judul'        => $knowledge->judul,
                'alasan_tolak' => $alasanTolak,
            ]
        );

        return redirect()->route('validasi.index')->with('success', 'Konten berhasil ditolak.');
    }
}
