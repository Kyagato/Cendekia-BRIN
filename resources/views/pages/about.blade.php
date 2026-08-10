@extends('layouts.public')
@section('title', 'Tentang')

@section('content')
<!-- Header -->
<section class="hero-gradient py-16">
    <div class="container mx-auto px-4 text-center">
        <nav class="flex justify-center text-sm text-white/70 mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ url('/') }}" class="inline-flex items-center hover:text-white transition">
                        Beranda
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        <span class="text-white font-medium ml-1">Tentang</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Tentang Cendekia BRIN</h1>
        <p class="text-xl text-white/80 max-w-2xl mx-auto">Membangun ekosistem pengetahuan yang kolaboratif dan inovatif</p>
    </div>
</section>

<!-- Apa itu -->
<section class="py-16 container mx-auto px-4">
    <div class="flex flex-col lg:flex-row items-center gap-12">
        <div class="lg:w-1/2 scroll-reveal">
            <h2 class="text-3xl font-bold text-slate-800 dark:text-white mb-6">Apa itu <span class="text-primary-600 dark:text-primary-400">Cendekia BRIN?</span></h2>
            <div class="space-y-4 text-slate-600 dark:text-slate-300 leading-relaxed text-lg">
                <p>
                    Cendekia BRIN (Sistem Informasi Manajemen Pengetahuan) adalah platform repositori digital strategis yang dikembangkan oleh Badan Riset dan Inovasi Nasional. Platform ini bertujuan untuk mengelola, menyimpan, dan mendistribusikan aset pengetahuan yang berharga.
                </p>
                <p>
                    Melalui SIMP@N, para periset, akademisi, dan masyarakat umum dapat dengan mudah menemukan berbagai bentuk pengetahuan—mulai dari dokumen riset, video pembelajaran, galeri gambar, hingga arsip audio—dalam satu portal yang terintegrasi dan mudah diakses.
                </p>
            </div>
        </div>
        <div class="lg:w-1/2 scroll-reveal">
            <div class="relative w-full h-80 rounded-2xl bg-gradient-to-tr from-primary-500 to-secondary-400 p-1 overflow-hidden shadow-2xl">
                <div class="absolute inset-0 bg-white/10 dark:bg-black/10 backdrop-blur-sm z-10"></div>
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/20 rounded-full blur-2xl z-20"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-black/20 rounded-full blur-2xl z-20"></div>
                <div class="relative z-30 w-full h-full bg-white dark:bg-slate-900 rounded-xl flex items-center justify-center p-8">
                    <div class="text-center">
                        <svg class="w-24 h-24 text-primary-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Pusat Pengetahuan Terpadu</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cara Menggunakan -->
<section class="py-16 bg-slate-50 dark:bg-slate-900/50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-slate-800 dark:text-white mb-12 scroll-reveal">Cara Menggunakan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Step 1 -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative scroll-reveal card-hover">
                <div class="w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center text-xl font-bold absolute -top-6 left-6 shadow-lg">1</div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mt-4 mb-2">Daftar Akun</h3>
                <p class="text-slate-600 dark:text-slate-400">Buat akun untuk mengakses fitur penuh, menyimpan favorit, dan berdiskusi.</p>
            </div>
            <!-- Step 2 -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative scroll-reveal card-hover">
                <div class="w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center text-xl font-bold absolute -top-6 left-6 shadow-lg">2</div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mt-4 mb-2">Jelajahi Pengetahuan</h3>
                <p class="text-slate-600 dark:text-slate-400">Cari dan filter konten riset berdasarkan kategori, label, atau tipe media.</p>
            </div>
            <!-- Step 3 -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative scroll-reveal card-hover">
                <div class="w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center text-xl font-bold absolute -top-6 left-6 shadow-lg">3</div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mt-4 mb-2">Unggah Konten</h3>
                <p class="text-slate-600 dark:text-slate-400">Berkontribusi dengan mengunggah karya, dokumen, atau materi edukasi Anda.</p>
            </div>
            <!-- Step 4 -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative scroll-reveal card-hover">
                <div class="w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center text-xl font-bold absolute -top-6 left-6 shadow-lg">4</div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mt-4 mb-2">Kolaborasi di Forum</h3>
                <p class="text-slate-600 dark:text-slate-400">Berinteraksi dengan periset lain melalui forum diskusi yang terstruktur.</p>
            </div>
        </div>
    </div>
</section>

<!-- Fitur Utama -->
<section class="py-16 container mx-auto px-4">
    <h2 class="text-3xl font-bold text-center text-slate-800 dark:text-white mb-12 scroll-reveal">Fitur Utama</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $features = [
                ['title' => 'Repositori Digital', 'desc' => 'Penyimpanan aman dan terpusat untuk semua aset pengetahuan.', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4'],
                ['title' => 'Multi-Tipe Konten', 'desc' => 'Dukungan untuk format teks, gambar, video, dan audio interaktif.', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                ['title' => 'Kategori Terstruktur', 'desc' => 'Organisasi hierarkis memudahkan pencarian topik yang spesifik.', 'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z'],
                ['title' => 'Forum Diskusi', 'desc' => 'Ruang tanya jawab dan kolaborasi antar pengguna dan pakar.', 'icon' => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z'],
                ['title' => 'Pencarian Cerdas', 'desc' => 'Mesin pencari cepat dengan dukungan filter dan label.', 'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                ['title' => 'Mode Gelap', 'desc' => 'Dukungan dark mode penuh untuk kenyamanan membaca di malam hari.', 'icon' => 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z']
            ];
        @endphp

        @foreach($features as $feat)
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-start gap-4 card-hover scroll-reveal">
            <div class="w-12 h-12 bg-primary-50 dark:bg-primary-900/30 rounded-lg flex items-center justify-center text-primary-600 dark:text-primary-400 shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feat['icon'] }}" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">{{ $feat['title'] }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $feat['desc'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Tim Pengembang -->
<section class="py-16 bg-slate-50 dark:bg-slate-900/50">
    <div class="container mx-auto px-4 max-w-3xl text-center scroll-reveal">
        <h2 class="text-3xl font-bold text-slate-800 dark:text-white mb-8">Hubungi Kami</h2>
        <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-md border border-slate-100 dark:border-slate-700">
            <div class="w-20 h-20 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full mx-auto mb-4 flex items-center justify-center text-white">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Tim Pengembang Cendekia BRIN</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-6">Badan Riset dan Inovasi Nasional</p>
            
            <a href="mailto:dev@simpanbrin.go.id" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl font-semibold transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                dev@simpanbrin.go.id
            </a>
        </div>
    </div>
</section>
@endsection
