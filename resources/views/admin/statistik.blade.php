@extends('layouts.admin')
@section('title', 'Dashboard - Statistik')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 dark:text-slate-200 font-semibold">Statistik</li>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Dashboard - Statistik</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Selamat datang, <span class="font-semibold text-slate-700 dark:text-slate-300">{{ auth()->user()->name }}</span>. Menampilkan statistik terkini.
        </p>
    </div>

    {{-- Top Summary Cards (Statistik Angka) --}}
    <div class="flex flex-col md:flex-row gap-4">
        {{-- Total --}}
        <div class="flex-1 bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Konten</p>
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mt-0.5">{{ number_format($totalKnowledge) }}</h3>
            </div>
        </div>

        {{-- Teks --}}
        <div class="flex-1 bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide">Teks</p>
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mt-0.5">{{ number_format($countTeks) }}</h3>
            </div>
        </div>

        {{-- Gambar --}}
        <div class="flex-1 bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Gambar</p>
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mt-0.5">{{ number_format($countGambar) }}</h3>
            </div>
        </div>

        {{-- Video --}}
        <div class="flex-1 bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wide">Video</p>
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mt-0.5">{{ number_format($countVideo) }}</h3>
            </div>
        </div>

        {{-- Audio --}}
        <div class="flex-1 bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wide">Audio</p>
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mt-0.5">{{ number_format($countAudio) }}</h3>
            </div>
        </div>
    </div>


    {{-- Main Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Left: Grafik Melingkar (Pie / Donut Chart) --}}
        <div class="lg:col-span-5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-100 text-center mb-6">Jumlah Pengetahuan</h2>
                <div class="relative w-full max-w-[260px] mx-auto aspect-square">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>

            {{-- Keterangan Warna (Legend) Di Bawah Grafik Bulat --}}
            <div class="grid grid-cols-2 gap-3 mt-6 pt-6 border-t border-slate-100 dark:border-slate-700 max-w-xs mx-auto w-full">
                <div class="flex items-center gap-2.5">
                    <span class="w-4 h-4 rounded shrink-0 border border-blue-400 shadow-sm" style="background-color: #60A5FA;"></span>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Teks</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-4 h-4 rounded shrink-0 border border-emerald-400 shadow-sm" style="background-color: #4ADE80;"></span>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Gambar</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-4 h-4 rounded shrink-0 border border-rose-400 shadow-sm" style="background-color: #F87171;"></span>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Video</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-4 h-4 rounded shrink-0 border border-amber-400 shadow-sm" style="background-color: #FACC15;"></span>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Audio</span>
                </div>
            </div>
        </div>

        {{-- Right: Grafik Balok Horizontal (Horizontal Bar Chart) --}}
        <div class="lg:col-span-7 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col justify-between">
            <div>
                {{-- Dropdown Select Filter Instansi --}}
                <div class="mb-4">
                    <form method="GET" action="{{ route('admin.statistik') }}" id="instansiForm">
                        <div class="relative">
                            <select name="instansi" onchange="document.getElementById('instansiForm').submit()" class="w-full px-4 py-2.5 appearance-none rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm font-medium focus:ring-red-600 focus:border-red-600 transition pr-10">
                                <option value="">Semua Instansi</option>
                                @foreach($instansiList as $inst)
                                    <option value="{{ $inst }}" {{ $selectedInstansi == $inst ? 'selected' : '' }}>
                                        {{ $inst }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Subtitle Chart --}}
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 text-center mb-6">
                    Statistik Pengetahuan per Instansi {{ $selectedInstansi ? ': ' . $selectedInstansi : '(Keseluruhan)' }}
                </p>

                {{-- Canvas Horizontal Bar Chart --}}
                <div class="relative w-full h-[280px]">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Load Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#475569';
        const gridColor = isDark ? 'rgba(148, 163, 184, 0.15)' : '#f1f5f9';

        // 1. Pie Chart Initialization
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Teks', 'Gambar', 'Video', 'Audio'],
                datasets: [{
                    data: [
                        {{ $countTeks }},
                        {{ $countGambar }},
                        {{ $countVideo }},
                        {{ $countAudio }}
                    ],
                    backgroundColor: [
                        '#60A5FA', // Teks (Blue)
                        '#4ADE80', // Gambar (Green)
                        '#F87171', // Video (Red)
                        '#FACC15'  // Audio (Yellow)
                    ],
                    borderWidth: 2,
                    borderColor: isDark ? '#1e293b' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // We use custom legend matching the prompt design
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.raw;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // 2. Horizontal Bar Chart Initialization
        const barCtx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Teks', 'Gambar', 'Video', 'Audio'],
                datasets: [{
                    label: 'Jumlah Konten',
                    data: [
                        {{ $instansiTeks }},
                        {{ $instansiGambar }},
                        {{ $instansiVideo }},
                        {{ $instansiAudio }}
                    ],
                    backgroundColor: [
                        '#60A5FA', // Teks (Blue)
                        '#4ADE80', // Gambar (Green)
                        '#F87171', // Video (Red)
                        '#FACC15'  // Audio (Yellow)
                    ],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {

                indexAxis: 'y', // Makes the bar chart horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            color: textColor,
                            stepSize: 1,
                            precision: 0
                        },
                        grid: {
                            color: gridColor
                        }
                    },
                    y: {
                        ticks: {
                            color: textColor,
                            font: {
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
