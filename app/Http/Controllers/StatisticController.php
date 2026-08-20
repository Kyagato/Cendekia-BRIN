<?php

namespace App\Http\Controllers;

use App\Models\Knowledge;
use App\Models\User;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    /**
     * Tampilkan halaman statistik dashboard admin.
     */
    public function index(Request $request)
    {
        // Total keseluruhan
        $totalKnowledge = Knowledge::count();
        $countTeks = Knowledge::where('tipe', 'Teks')->count();
        $countGambar = Knowledge::where('tipe', 'Gambar')->count();
        $countVideo = Knowledge::where('tipe', 'Video')->count();
        $countAudio = Knowledge::where('tipe', 'Audio')->count();

        // Daftar Instansi dari tabel users
        $instansiList = User::whereNotNull('instansi')
            ->where('instansi', '!=', '')
            ->pluck('instansi')
            ->unique()
            ->values();

        $selectedInstansi = $request->get('instansi');

        // Statistik per instansi (jika ada filter instansi yang dipilih)
        if ($selectedInstansi) {
            $instansiQuery = Knowledge::whereHas('user', function ($q) use ($selectedInstansi) {
                $q->where('instansi', $selectedInstansi);
            });

            $instansiTeks = (clone $instansiQuery)->where('tipe', 'Teks')->count();
            $instansiGambar = (clone $instansiQuery)->where('tipe', 'Gambar')->count();
            $instansiVideo = (clone $instansiQuery)->where('tipe', 'Video')->count();
            $instansiAudio = (clone $instansiQuery)->where('tipe', 'Audio')->count();
        } else {
            $instansiTeks = $countTeks;
            $instansiGambar = $countGambar;
            $instansiVideo = $countVideo;
            $instansiAudio = $countAudio;
        }

        return view('admin.statistik', compact(
            'totalKnowledge',
            'countTeks',
            'countGambar',
            'countVideo',
            'countAudio',
            'instansiList',
            'selectedInstansi',
            'instansiTeks',
            'instansiGambar',
            'instansiVideo',
            'instansiAudio'
        ));
    }
}
