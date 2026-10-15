<?php

namespace App\Http\Controllers;

use App\Models\Knowledge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatisticController extends Controller
{
    /**
     * Tampilkan halaman statistik dashboard admin dengan filter periode waktu (harian, mingguan, bulanan, tahunan).
     */
    public function index(Request $request)
    {
        // 1. Statistik Total Keseluruhan (Single-pass SQL aggregation di-cache 5 menit)
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

        // 2. Filter Periode Waktu
        $allowedPeriode = ['semua', 'harian', 'mingguan', 'bulanan', 'tahunan'];
        $periode = $request->input('periode', 'semua');
        if (!in_array($periode, $allowedPeriode)) {
            $periode = 'semua';
        }

        $now = Carbon::now();
        $selectedYear = (int) $request->input('tahun', $now->year);

        if ($periode === 'semua') {
            $periodeTeks   = $countTeks;
            $periodeGambar = $countGambar;
            $periodeVideo  = $countVideo;
            $periodeAudio  = $countAudio;
            $periodeTotal  = $totalKnowledge;
            $periodeLabel  = 'Semua Waktu';
        } else {
            switch ($periode) {
                case 'harian':
                    // 1 hari (hari ini)
                    $startDate = Carbon::today()->startOfDay();
                    $endDate = Carbon::today()->endOfDay();
                    $periodeLabel = 'Harian';
                    break;

                case 'mingguan':
                    // 7 hari terakhir
                    $startDate = Carbon::today()->subDays(6)->startOfDay();
                    $endDate = Carbon::today()->endOfDay();
                    $periodeLabel = 'Mingguan';
                    break;

                case 'bulanan':
                    // 1 bulan ini
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    $periodeLabel = 'Bulanan';
                    break;

                case 'tahunan':
                default:
                    // 1 tahun ini
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    $periodeLabel = 'Tahunan';
                    break;
            }

            $periodStats = Knowledge::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN tipe = 'Teks' THEN 1 ELSE 0 END) as count_teks,
                    SUM(CASE WHEN tipe = 'Gambar' THEN 1 ELSE 0 END) as count_gambar,
                    SUM(CASE WHEN tipe = 'Video' THEN 1 ELSE 0 END) as count_video,
                    SUM(CASE WHEN tipe = 'Audio' THEN 1 ELSE 0 END) as count_audio
                ")->first();

            $periodeTotal  = (int) ($periodStats->total ?? 0);
            $periodeTeks   = (int) ($periodStats->count_teks ?? 0);
            $periodeGambar = (int) ($periodStats->count_gambar ?? 0);
            $periodeVideo  = (int) ($periodStats->count_video ?? 0);
            $periodeAudio  = (int) ($periodStats->count_audio ?? 0);
        }

        return view('admin.statistik', compact(
            'totalKnowledge',
            'countTeks',
            'countGambar',
            'countVideo',
            'countAudio',
            'periode',
            'periodeLabel',
            'periodeTotal',
            'periodeTeks',
            'periodeGambar',
            'periodeVideo',
            'periodeAudio'
        ));
    }
}
