<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Knowledge::with(['category', 'user', 'tags'])
            ->whereIn('status', ['Diajukan', 'Disetujui', 'Ditolak']);

        // Filter: Hanya tampilkan riwayat unggahan milik user yang sedang login (kecuali Admin)
        if (!in_array($user->role, ['Super Admin', 'Admin Pusat', 'Admin IPPD']) && $user->email !== 'superadmin@brin.go.id') {
            $query->where('user_id', $user->id);
        }

        $query->latest();

        if ($request->has('tipe') && $request->tipe != '') {
            $query->where('tipe', $request->tipe);
        }

        if ($request->has('status') && $request->status != '') {
            $statusMap = [
                'diajukan' => 'Diajukan',
                'diterima' => 'Disetujui',
                'ditolak'  => 'Ditolak'
            ];
            if (isset($statusMap[strtolower($request->status)])) {
                $query->where('status', $statusMap[strtolower($request->status)]);
            }
        }

        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $knowledges = $query->paginate(10, ['*'], 'page_knowledges')->appends($request->all());

        // Get drafts for logged-in user (or all drafts for admins)
        $draftsQuery = Knowledge::with(['category', 'user', 'tags'])
            ->where('status', 'Draft');

        if (!in_array($user->role, ['Super Admin', 'Admin Pusat', 'Admin IPPD']) && $user->email !== 'superadmin@brin.go.id') {
            $draftsQuery->where('user_id', $user->id);
        }

        $drafts = $draftsQuery->latest()
            ->paginate(5, ['*'], 'page_drafts')
            ->appends($request->all());

        return view('knowledge.index', compact('knowledges', 'drafts'));
    }

    /**
     * Cek apakah user yang sedang login memiliki hak otomatis disetujui
     * (Role: Super Admin, Admin Pusat, Admin IPPD, Analisis Pengetahuan / Analis Pengetahuan)
     */
    private function isAutoApproveUser(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        $autoApproveRoles = [
            'Super Admin',
            'Admin Pusat',
            'Admin IPPD',
            'Analisis Pengetahuan',
            'Analis Pengetahuan',
        ];

        return in_array($user->role, $autoApproveRoles) || $user->email === 'superadmin@brin.go.id';
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
        $rules = [
            'judul' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tipe' => 'required|in:Teks,Video,Gambar,Audio',
            'deskripsi' => 'nullable|string',
            'detail' => 'nullable|string',
            'tanggal_terbit' => 'nullable|date',
            'status_akses' => 'nullable|string|in:public,private',
            'tags' => 'nullable|string',
            'penulis' => 'nullable|string|max:255',
            'kolaborator' => 'nullable|string|max:255',
        ];

        // Validasi berbeda berdasarkan tipe
        if ($request->tipe === 'Gambar') {
            $rules['file_upload'] = 'required|image|max:5120'; // Gambar wajib upload, maks 5MB
        } elseif ($request->tipe === 'Audio') {
            $rules['audio_file'] = [
                'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    if ($value && $value->isValid()) {
                        $ext = strtolower($value->getClientOriginalExtension());
                        $allowed = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'wma', 'mp4', 'webm', 'mpga'];
                        if (!in_array($ext, $allowed)) {
                            $fail('File audio harus berformat MP3, WAV, OGG, M4A, AAC, atau FLAC.');
                        }
                    }
                }
            ];
            $rules['file_upload'] = 'nullable|image|max:5120'; // Thumbnail opsional
        } else {
            $rules['file_upload'] = 'nullable|file|max:51200'; // Maks 50MB untuk tipe lain
            $rules['url_teks'] = 'nullable|url|max:255';
        }

        $request->validate($rules, [
            'audio_file.required' => 'File audio wajib diupload untuk format Audio.',
            'file_upload.required' => 'File gambar wajib diupload untuk format Gambar.',
            'file_upload.image' => 'Thumbnail harus berupa gambar (PNG, JPG, GIF, WEBP).',
            'audio_file.max' => 'Ukuran file audio tidak boleh lebih dari 10MB.',
            'file_upload.max' => 'Ukuran thumbnail gambar tidak boleh lebih dari 5MB.',
        ]);

        // B. Proses Upload File (jika ada)
        $filePath = null;
        $urlTeks = $request->url_teks;

        if ($request->tipe === 'Audio') {
            $audioPath = null;
            if ($request->hasFile('audio_file')) {
                $audioPath = $request->file('audio_file')->store('uploads', 'public');
            }

            if ($request->hasFile('file_upload')) {
                // Ada thumbnail gambar yang diupload
                $filePath = $request->file('file_upload')->store('uploads', 'public');
                $urlTeks = $audioPath;
            } else {
                // Tidak ada thumbnail, simpan path audio di file_path
                $filePath = $audioPath;
            }
        } else {
            if ($request->hasFile('file_upload')) {
                $filePath = $request->file('file_upload')->store('uploads', 'public');
            }
        }

        $status = $request->input('status', 'Diajukan');
        if ($status !== 'Draft') {
            if ($this->isAutoApproveUser()) {
                $status = 'Disetujui';
            } else {
                $status = 'Diajukan';
            }
        }

        // C. Simpan ke tabel Knowledge
        $knowledge = Knowledge::create([
            'user_id' => Auth::id(), // ID user yang sedang login
            'category_id' => $request->category_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'detail' => $request->detail,
            'tanggal_terbit' => $request->tanggal_terbit,
            'status_akses' => $request->status_akses ?? 'public',
            'tipe' => $request->tipe,
            'file_path' => $filePath,
            'status' => $status,
            'penulis' => $request->penulis,
            'kolaborator' => $request->kolaborator,
            'url_teks' => $urlTeks,
            'unggulan' => $request->has('unggulan'),
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

        // E. Kembali ke halaman sebelumnya dengan pesan sukses yang sesuai
        if ($status === 'Disetujui') {
            $message = 'Pengetahuan berhasil ditambahkan dan langsung berstatus disetujui.';
        } elseif ($status === 'Draft') {
            $message = 'Pengetahuan berhasil disimpan sebagai draft.';
        } else {
            $message = 'Pengetahuan berhasil diajukan dan menunggu validasi.';
        }

        return redirect()->route('knowledge.index')->with('success', $message);
    }

    public function show(Knowledge $knowledge)
    {
        $knowledge->increment('views_count');
        $knowledge->load(['category', 'user', 'tags', 'threads.user']);
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

        $rules = [
            'judul' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tipe' => 'required|in:Teks,Video,Gambar,Audio',
            'deskripsi' => 'nullable|string',
            'detail' => 'nullable|string',
            'tanggal_terbit' => 'nullable|date',
            'status_akses' => 'nullable|string|in:public,private',
            'tags' => 'nullable|string',
            'penulis' => 'nullable|string|max:255',
            'kolaborator' => 'nullable|string|max:255',
        ];

        if ($request->tipe === 'Gambar') {
            // Saat edit, gambar tidak wajib upload ulang jika sudah ada file_path
            $rules['file_upload'] = $knowledge->file_path ? 'nullable|image|max:5120' : 'required|image|max:5120';
        } elseif ($request->tipe === 'Audio') {
            $hasAudio = $knowledge->url_teks || ($knowledge->file_path && in_array(strtolower(pathinfo($knowledge->file_path, PATHINFO_EXTENSION)), ['mp3','wav','ogg','m4a','aac','flac','wma','mp4','webm','mpga']));
            $rules['audio_file'] = [
                $hasAudio ? 'nullable' : 'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    if ($value && $value->isValid()) {
                        $ext = strtolower($value->getClientOriginalExtension());
                        $allowed = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'wma', 'mp4', 'webm', 'mpga'];
                        if (!in_array($ext, $allowed)) {
                            $fail('File audio harus berformat MP3, WAV, OGG, M4A, AAC, atau FLAC.');
                        }
                    }
                }
            ];
            $rules['file_upload'] = 'nullable|image|max:5120';
        } else {
            $rules['file_upload'] = 'nullable|file|max:51200';
            $rules['url_teks'] = 'nullable|url|max:255';
        }

        $request->validate($rules, [
            'audio_file.required' => 'File audio wajib diupload untuk format Audio.',
            'file_upload.required' => 'File gambar wajib diupload untuk format Gambar.',
            'file_upload.image' => 'Thumbnail harus berupa gambar (PNG, JPG, GIF, WEBP).',
            'audio_file.max' => 'Ukuran file audio tidak boleh lebih dari 10MB.',
            'file_upload.max' => 'Ukuran thumbnail gambar tidak boleh lebih dari 5MB.',
        ]);

        // Determine new status: jika user berole auto-approval (Super Admin, Admin Pusat, Admin IPPD, Analis Pengetahuan), langsung set ke Disetujui
        if ($this->isAutoApproveUser()) {
            $newStatus = 'Disetujui';
        } else {
            $newStatus = ($knowledge->status === 'Draft') ? 'Diajukan' : $knowledge->status;
        }

        $updateData = [
            'category_id' => $request->category_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'detail' => $request->detail,
            'tanggal_terbit' => $request->tanggal_terbit,
            'status_akses' => $request->status_akses ?? 'public',
            'tipe' => $request->tipe,
            'status' => $newStatus,
            'penulis' => $request->penulis,
            'kolaborator' => $request->kolaborator,
            'url_teks' => $request->url_teks,
            'unggulan' => $request->has('unggulan'),
        ];

        if ($request->tipe === 'Audio') {
            $audioPath = $knowledge->url_teks;
            if (!$audioPath && $knowledge->file_path && in_array(strtolower(pathinfo($knowledge->file_path, PATHINFO_EXTENSION)), ['mp3','wav','ogg','m4a','aac','flac','wma','mp4','webm','mpga'])) {
                $audioPath = $knowledge->file_path;
            }

            if ($request->hasFile('audio_file')) {
                if ($knowledge->url_teks && \Illuminate\Support\Facades\Storage::disk('public')->exists($knowledge->url_teks)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($knowledge->url_teks);
                }
                $audioPath = $request->file('audio_file')->store('uploads', 'public');
            }

            if ($request->hasFile('file_upload')) {
                if ($knowledge->file_path && $knowledge->file_path !== $audioPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($knowledge->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($knowledge->file_path);
                }
                $updateData['file_path'] = $request->file('file_upload')->store('uploads', 'public');
                $updateData['url_teks'] = $audioPath;
            } else {
                if ($knowledge->file_path && $knowledge->file_path !== $audioPath) {
                    $updateData['url_teks'] = $audioPath;
                } else {
                    $updateData['file_path'] = $audioPath;
                }
            }
        } else {
            if ($request->hasFile('file_upload')) {
                if ($knowledge->file_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($knowledge->file_path);
                }
                $updateData['file_path'] = $request->file('file_upload')->store('uploads', 'public');
            }
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
        $tags = $knowledge->tags;
        $knowledge->tags()->detach();
        $knowledge->delete();

        // Hapus tagline yang sudah tidak terikat pada artikel apapun (orphan tags)
        foreach ($tags as $tag) {
            if ($tag->knowledge()->count() === 0) {
                $tag->delete();
            }
        }

        return redirect()->route('knowledge.index')->with('success', 'Pengetahuan berhasil dihapus.');
    }
}