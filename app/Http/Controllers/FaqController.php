<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the FAQs for management.
     */
    public function index()
    {
        $faqsGrouped = Faq::orderBy('kategori_faq')->orderBy('urutan')->get()->groupBy('kategori_faq');
        $sections = Faq::select('kategori_faq')->distinct()->pluck('kategori_faq');

        return view('admin.faq.index', compact('faqsGrouped', 'sections'));
    }

    /**
     * Store a new section (Nama Bagian saja).
     */
    public function storeSection(Request $request)
    {
        $validated = $request->validate([
            'kategori_faq' => 'required|string|max:255',
        ], [
            'kategori_faq.required' => 'Nama Bagian wajib diisi.',
        ]);

        $sectionName = trim($validated['kategori_faq']);

        $exists = Faq::where('kategori_faq', $sectionName)->exists();
        if (!$exists) {
            Faq::create([
                'kategori_faq' => $sectionName,
                'pertanyaan' => null,
                'jawaban' => null,
                'urutan' => 0,
            ]);
            return redirect()->route('admin.faq.index')->with('success', 'Bagian baru "' . $sectionName . '" berhasil dibuat.');
        }

        return redirect()->route('admin.faq.index')->with('info', 'Bagian "' . $sectionName . '" sudah ada.');
    }

    /**
     * Store a newly created FAQ (Judul Baru & Isian) into a section.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_faq' => 'required|string|max:255',
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
            'urutan' => 'nullable|integer',
        ], [
            'kategori_faq.required' => 'Nama bagian / kategori FAQ wajib diisi.',
            'pertanyaan.required' => 'Judul / pertanyaan FAQ wajib diisi.',
            'jawaban.required' => 'Isian / jawaban FAQ wajib diisi.',
        ]);

        if (empty($validated['urutan'])) {
            $maxUrutan = Faq::where('kategori_faq', $validated['kategori_faq'])->max('urutan') ?? 0;
            $validated['urutan'] = $maxUrutan + 1;
        }

        // Jika terdapat placeholder bagian kosong, pakai row tersebut
        $placeholder = Faq::where('kategori_faq', $validated['kategori_faq'])
            ->whereNull('pertanyaan')
            ->first();

        if ($placeholder) {
            $placeholder->update($validated);
        } else {
            Faq::create($validated);
        }

        return redirect()->route('admin.faq.index')->with('success', 'Judul FAQ baru berhasil ditambahkan.');
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'kategori_faq' => 'required|string|max:255',
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
            'urutan' => 'nullable|integer',
        ], [
            'kategori_faq.required' => 'Nama bagian / kategori FAQ wajib diisi.',
            'pertanyaan.required' => 'Judul / pertanyaan FAQ wajib diisi.',
            'jawaban.required' => 'Isian / jawaban FAQ wajib diisi.',
        ]);

        if (!isset($validated['urutan'])) {
            $validated['urutan'] = $faq->urutan;
        }

        $faq->update($validated);

        return redirect()->route('admin.faq.index')->with('success', 'FAQ berhasil diperbarui.');
    }

    /**
     * Update section name (kategori_faq) for all FAQs in a section.
     */
    public function updateSection(Request $request)
    {
        $validated = $request->validate([
            'old_kategori_faq' => 'required|string',
            'new_kategori_faq' => 'required|string|max:255',
        ], [
            'old_kategori_faq.required' => 'Bagian asal tidak ditemukan.',
            'new_kategori_faq.required' => 'Nama bagian baru wajib diisi.',
        ]);

        Faq::where('kategori_faq', $validated['old_kategori_faq'])
            ->update(['kategori_faq' => trim($validated['new_kategori_faq'])]);

        return redirect()->route('admin.faq.index')->with('success', 'Nama Bagian FAQ berhasil diperbarui.');
    }

    /**
     * Remove an entire section and all its FAQs.
     */
    public function destroySection(Request $request)
    {
        $validated = $request->validate([
            'kategori_faq' => 'required|string',
        ]);

        Faq::where('kategori_faq', $validated['kategori_faq'])->delete();

        return redirect()->route('admin.faq.index')->with('success', 'Bagian "' . $validated['kategori_faq'] . '" dan seluruh isinya berhasil dihapus.');
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroy(Faq $faq)
    {
        $section = $faq->kategori_faq;
        $faq->delete();

        // Jika tidak ada FAQ tersisa di bagian ini, sisakan placeholder agar bagian tidak hilang
        $countLeft = Faq::where('kategori_faq', $section)->count();
        if ($countLeft === 0) {
            Faq::create([
                'kategori_faq' => $section,
                'pertanyaan' => null,
                'jawaban' => null,
                'urutan' => 0,
            ]);
        }

        return redirect()->route('admin.faq.index')->with('success', 'FAQ berhasil dihapus.');
    }
}
