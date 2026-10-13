<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Services\KnowledgeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Tampilkan halaman daftar direktori kategori.
     */
    public function index(Request $request): View
    {
        $query = Category::withCount(['knowledge' => function ($q) {
            $q->whereNull('deleted_at');
        }]);

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where('nama_kategori', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('nama_kategori')->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Tambah kategori baru ke direktori.
     */
    public function store(Request $request, KnowledgeService $knowledgeService): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori',
        ], [
            'nama_kategori.required' => 'Nama kategori direktori wajib diisi.',
            'nama_kategori.unique'   => 'Nama kategori direktori sudah terdaftar.',
            'nama_kategori.max'      => 'Nama kategori maksimal 255 karakter.',
        ]);

        $category = Category::create([
            'nama_kategori' => trim($validated['nama_kategori']),
        ]);

        AuditLog::record(
            'CATEGORY_CREATED',
            "Pengguna '" . Auth::user()->name . "' (" . Auth::user()->email . ") menambahkan direktori kategori baru: '{$category->nama_kategori}'",
            [
                'category_id'   => $category->id,
                'nama_kategori' => $category->nama_kategori,
                'creator_role'  => Auth::user()->role,
            ],
            Auth::user()
        );

        $knowledgeService->invalidateStatsCache();

        return redirect()->route('admin.categories.index')
            ->with('success', "Direktori kategori '{$category->nama_kategori}' berhasil ditambahkan.");
    }

    /**
     * Perbarui nama kategori direktori.
     */
    public function update(Request $request, Category $category, KnowledgeService $knowledgeService): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'nama_kategori')->ignore($category->id),
            ],
        ], [
            'nama_kategori.required' => 'Nama kategori direktori wajib diisi.',
            'nama_kategori.unique'   => 'Nama kategori direktori sudah terdaftar.',
            'nama_kategori.max'      => 'Nama kategori maksimal 255 karakter.',
        ]);

        $oldName = $category->nama_kategori;
        $newName = trim($validated['nama_kategori']);

        if ($oldName === $newName) {
            return redirect()->route('admin.categories.index')
                ->with('info', "Tidak ada perubahan pada nama direktori kategori.");
        }

        $category->update([
            'nama_kategori' => $newName,
        ]);

        AuditLog::record(
            'CATEGORY_UPDATED',
            "Pengguna '" . Auth::user()->name . "' memperbarui nama direktori kategori dari '{$oldName}' menjadi '{$newName}'",
            [
                'category_id' => $category->id,
                'old_name'    => $oldName,
                'new_name'    => $newName,
                'updater_role'=> Auth::user()->role,
            ],
            Auth::user()
        );

        $knowledgeService->invalidateStatsCache();

        return redirect()->route('admin.categories.index')
            ->with('success', "Direktori kategori '{$oldName}' berhasil diperbarui menjadi '{$newName}'.");
    }

    /**
     * Hapus kategori dari direktori (hanya jika tidak memuat dokumen pengetahuan).
     */
    public function destroy(Category $category, KnowledgeService $knowledgeService): RedirectResponse
    {
        $knowledgeCount = $category->knowledge()->count();

        if ($knowledgeCount > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Kategori '{$category->nama_kategori}' tidak dapat dihapus karena masih memuat {$knowledgeCount} dokumen pengetahuan. Silakan pindahkan atau hapus dokumen terkait terlebih dahulu.");
        }

        $deletedName = $category->nama_kategori;
        $deletedId = $category->id;
        $category->delete();

        AuditLog::record(
            'CATEGORY_DELETED',
            "Pengguna '" . Auth::user()->name . "' menghapus direktori kategori: '{$deletedName}'",
            [
                'category_id'   => $deletedId,
                'nama_kategori' => $deletedName,
                'deleter_role'  => Auth::user()->role,
            ],
            Auth::user()
        );

        $knowledgeService->invalidateStatsCache();

        return redirect()->route('admin.categories.index')
            ->with('success', "Direktori kategori '{$deletedName}' berhasil dihapus.");
    }
}
