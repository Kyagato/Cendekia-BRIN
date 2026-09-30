<?php

namespace App\Http\Controllers;

use App\Models\Knowledge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    /**
     * Tampilkan halaman statistik dashboard admin dengan single-pass aggregation & caching.
     */
    public function index(Request $request)
    {
        // 1. Dapatkan daftar instansi (di-cache 15 menit agar tidak scan tabel users terus-menerus)
        $instansiList = Cache::remember('stats_instansi_list', 900, function () {
            return User::whereNotNull('instansi')
                ->where('instansi', '!=', '')
                ->pluck('instansi')
                ->unique()
                ->values();
        });

        // 2. Statistik Total Keseluruhan (Single-pass SQL aggregation di-cache 5 menit)
        $globalStats = Cache::remember('stats_global_aggregation', 300, function () {
            return Knowledge::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN tipe = 'Teks' THEN 1 ELSE 0 END) as count_teks,
                SUM(CASE WHEN tipe = 'Gambar' THEN 1 ELSE 0 END) as count_gambar,
                SUM(CASE WHEN tipe = 'Video' THEN 1 ELSE 0 END) as count_video,
                SUM(CASE WHEN tipe = 'Audio' THEN 1 ELSE 0 END) as count_audio
            ")->first();
        });

        $totalKnowledge = (int) ($globalStats->total ?? 0);
        $countTeks      = (int) ($globalStats->count_teks ?? 0);
        $countGambar    = (int) ($globalStats->count_gambar ?? 0);
        $countVideo     = (int) ($globalStats->count_video ?? 0);
        $countAudio     = (int) ($globalStats->count_audio ?? 0);

        $selectedInstansi = $request->get('instansi');

        // 3. Statistik per instansi (jika ada filter instansi yang dipilih)
        if ($selectedInstansi) {
            $cacheKey = 'stats_instansi_' . md5($selectedInstansi);

            $instansiStats = Cache::remember($cacheKey, 300, function () use ($selectedInstansi) {
                return Knowledge::whereHas('user', function ($q) use ($selectedInstansi) {
                    $q->where('instansi', $selectedInstansi);
                })->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN tipe = 'Teks' THEN 1 ELSE 0 END) as count_teks,
                    SUM(CASE WHEN tipe = 'Gambar' THEN 1 ELSE 0 END) as count_gambar,
                    SUM(CASE WHEN tipe = 'Video' THEN 1 ELSE 0 END) as count_video,
                    SUM(CASE WHEN tipe = 'Audio' THEN 1 ELSE 0 END) as count_audio
                ")->first();
            });

            $instansiTeks   = (int) ($instansiStats->count_teks ?? 0);
            $instansiGambar = (int) ($instansiStats->count_gambar ?? 0);
            $instansiVideo  = (int) ($instansiStats->count_video ?? 0);
            $instansiAudio  = (int) ($instansiStats->count_audio ?? 0);
        } else {
            $instansiTeks   = $countTeks;
            $instansiGambar = $countGambar;
            $instansiVideo  = $countVideo;
            $instansiAudio  = $countAudio;
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
