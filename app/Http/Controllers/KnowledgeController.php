<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeController extends Controller
{
    public function index()
    {
        // Mengambil semua data pengetahuan, diurutkan dari yang terbaru dengan paginasi
        $knowledges = \App\Models\Knowledge::with(['category', 'user', 'tags'])->latest()->paginate(10);
        
        return view('knowledge.index', compact('knowledges'));
    }
    // 1. Menampilkan Form Upload
    public function create()
    {
        // Mengambil semua data kategori untuk ditampilkan di dropdown (select)
        $categories = Category::all();
        
        return view('knowledge.create', compact('categories'));
    }

    // 2. Memproses Data yang Dikirim
    public function store(Request $request)
    {
        // A. Validasi input dari user
        $request->validate([
            'judul' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tipe' => 'required|in:Teks,Video,Gambar,Audio',
            'deskripsi' => 'nullable|string',
            'file_upload' => 'nullable|file|max:51200', // Maks 50MB
            'tags' => 'nullable|string', // Format teks biasa: "spbe, panduan, ai"
            'penulis' => 'nullable|string|max:255',
            'kolaborator' => 'nullable|string|max:255',
            'url_teks' => 'nullable|url|max:255',
        ]);

        // B. Proses Upload File (jika ada)
        $filePath = null;
        if ($request->hasFile('file_upload')) {
            // Menyimpan file ke folder 'storage/app/public/uploads'
            $filePath = $request->file('file_upload')->store('uploads', 'public');
        }

        // C. Simpan ke tabel Knowledge
        $knowledge = Knowledge::create([
            'user_id' => Auth::id(), // ID user yang sedang login
            'category_id' => $request->category_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tipe' => $request->tipe,
            'file_path' => $filePath,
            'status' => 'Diajukan', // Sesuai analisismu, harus divalidasi Analisis Pengetahuan
            'penulis' => $request->penulis,
            'kolaborator' => $request->kolaborator,
            'url_teks' => $request->url_teks,
        ]);

        // D. Proses Label/Tags (Relasi Many-to-Many)
        if ($request->tags) {
            // Memecah teks "spbe, panduan" menjadi array ["spbe", "panduan"]
            $tagNames = array_map('trim', explode(',', $request->tags));
            $tagIds = [];

            foreach ($tagNames as $tagName) {
                // firstOrCreate: Cari tag di database, jika tidak ada, buat baru otomatis!
                $tag = Tag::firstOrCreate(['nama_label' => strtolower($tagName)]);
                $tagIds[] = $tag->id;
            }

            // Memasukkan relasi ke tabel knowledge_tag
            $knowledge->tags()->sync($tagIds);
        }

        // E. Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('knowledge.index')->with('success', 'Pengetahuan berhasil diajukan dan menunggu validasi.');
    }

    public function show(Knowledge $knowledge)
    {
        $knowledge->increment('views_count');
        $knowledge->load(['category', 'user', 'tags']);
        return view('knowledge.show', compact('knowledge'));
    }

    public function edit(Knowledge $knowledge)
    {
        $categories = Category::all();
        return view('knowledge.edit', compact('knowledge', 'categories'));
    }

    public function update(Request $request, Knowledge $knowledge)
    {
        // Handle "Batal Ajukan" — revert status from "Diajukan" to "Draft"
        if ($request->has('batal_ajukan')) {
            $knowledge->update(['status' => 'Draft']);
            return redirect()->route('knowledge.index')->with('success', 'Pengajuan berhasil dibatalkan. Status kembali ke Draft.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tipe' => 'required|in:Teks,Video,Gambar,Audio',
            'deskripsi' => 'nullable|string',
            'tags' => 'nullable|string',
            'file_upload' => 'nullable|file|max:51200',
            'penulis' => 'nullable|string|max:255',
            'kolaborator' => 'nullable|string|max:255',
            'url_teks' => 'nullable|url|max:255',
        ]);

        // Determine new status: if currently Draft, re-submit as "Diajukan"
        $newStatus = ($knowledge->status === 'Draft') ? 'Diajukan' : $knowledge->status;

        $updateData = [
            'category_id' => $request->category_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tipe' => $request->tipe,
            'status' => $newStatus,
            'penulis' => $request->penulis,
            'kolaborator' => $request->kolaborator,
            'url_teks' => $request->url_teks,
        ];

        if ($request->hasFile('file_upload')) {
            if ($knowledge->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($knowledge->file_path);
            }
            $updateData['file_path'] = $request->file('file_upload')->store('uploads', 'public');
        }

        $knowledge->update($updateData);

        if ($request->tags) {
            $tagNames = array_map('trim', explode(',', $request->tags));
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                $tag = Tag::firstOrCreate(['nama_label' => strtolower($tagName)]);
                $tagIds[] = $tag->id;
            }
            $knowledge->tags()->sync($tagIds);
        }

        return redirect()->route('knowledge.index')->with('success', 'Pengetahuan berhasil diperbarui.');
    }

    public function destroy(Knowledge $knowledge)
    {
        $knowledge->delete();
        return redirect()->route('knowledge.index')->with('success', 'Pengetahuan berhasil dihapus.');
    }
}