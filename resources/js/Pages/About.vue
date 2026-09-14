<template>
  <PublicLayout>
    <!-- Header -->
    <section class="relative py-14 bg-[#1e3a8a] text-white overflow-hidden">

      <div class="container mx-auto px-4 relative z-10 text-center">
        <nav class="flex justify-center text-xs text-blue-200 mb-3" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li>
              <Link href="/" class="hover:text-white transition">Beranda</Link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-3 h-3 mx-1 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-white font-medium ml-1">Tentang</span>
              </div>
            </li>
          </ol>
        </nav>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-3 tracking-tight">Tentang MojoPedia</h1>
        <p class="text-base text-blue-100 max-w-2xl mx-auto">Sistem Manajemen Pengetahuan — Digital Governance System</p>
      </div>
    </section>

    <!-- Apa itu -->
    <section class="py-16 container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row items-center gap-12">
        <div class="lg:w-1/2">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#eff6ff] text-[#2563eb] dark:bg-blue-950 dark:text-blue-300 text-xs font-semibold uppercase tracking-wider mb-4 border border-blue-200/60">
            Profil Sistem
          </div>
          <h2 class="text-2xl sm:text-3xl font-bold text-[#0f172a] dark:text-white mb-6">Mengenal Repositori <span class="text-[#2563eb]">MojoPedia</span></h2>
          <div class="space-y-4 text-[#475569] dark:text-slate-300 leading-relaxed text-sm sm:text-base">
            <p>
              <strong>MojoPedia</strong> adalah platform repositori digital terpadu yang dirancang untuk mengelola, menyimpan, dan mendistribusikan aset pengetahuan yang berharga dalam ekosistem pemerintahan daerah dan riset terbuka.
            </p>
            <p>
              Melalui MojoPedia, aparatur sipil negara, periset, akademisi, dan masyarakat umum dapat dengan mudah menemukan berbagai bentuk aset pengetahuan—mulai dari dokumen kebijakan, kajian strategis SPBE, video pembelajaran, hingga dokumentasi ilmiah terstandarisasi.
            </p>
          </div>
        </div>
        <div class="lg:w-1/2 w-full">
          <div class="rounded-lg border border-[#e2e8f0] dark:border-slate-800 bg-white dark:bg-slate-900 p-8 shadow-sm relative overflow-hidden">
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-[#f1f5f9] dark:border-slate-800">
              <div class="w-12 h-12 rounded-lg bg-[#eff6ff] dark:bg-blue-950/80 border border-blue-200/60 flex items-center justify-center text-[#2563eb] dark:text-blue-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-[#0f172a] dark:text-white">Pusat Pengetahuan Terpadu</h3>
                <p class="text-xs text-[#94a3b8]">Standar Pengarsipan Digital Nasional</p>
              </div>
            </div>
            <ul class="space-y-3 text-xs text-[#475569] dark:text-slate-300">
              <li class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb]"></span>
                <span>Penyimpanan aset terstruktur multi-format (Teks, Video, Audio, Gambar)</span>
              </li>
              <li class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb]"></span>
                <span>Proses review & validasi berjenjang oleh tim moderator kurator</span>
              </li>
              <li class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb]"></span>
                <span>Dukungan interoperabilitas metadata dan keterbukaan informasi SPBE</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- Cara Menggunakan -->
    <section class="py-14 bg-[#f1f5f9]/60 dark:bg-slate-900/60 border-y border-[#e2e8f0] dark:border-slate-800">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-center text-[#0f172a] dark:text-white mb-10">Alur Kerja Pengelolaan Pengetahuan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="(step, idx) in steps" :key="idx" class="bg-white dark:bg-slate-900 p-6 rounded-lg border border-[#e2e8f0] dark:border-slate-800 relative card-hover">
            <div class="w-8 h-8 bg-[#2563eb] text-white rounded-lg flex items-center justify-center text-sm font-bold mb-4 shadow-sm">{{ idx + 1 }}</div>
            <h3 class="text-base font-bold text-[#0f172a] dark:text-white mb-2">{{ step.title }}</h3>
            <p class="text-[#475569] dark:text-slate-400 text-xs leading-relaxed">{{ step.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Fitur Utama -->
    <section class="py-16 container mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-bold text-center text-[#0f172a] dark:text-white mb-10">Fitur Ekosistem Pengetahuan</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="(feat, idx) in features" :key="idx" class="bg-white dark:bg-slate-900 p-6 rounded-lg border border-[#e2e8f0] dark:border-slate-800 flex items-start gap-4 card-hover">
          <div class="w-10 h-10 bg-[#eff6ff] dark:bg-blue-950/80 rounded-lg flex items-center justify-center text-[#2563eb] dark:text-blue-300 shrink-0 border border-blue-200/50">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feat.icon" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-bold text-[#0f172a] dark:text-white mb-1">{{ feat.title }}</h3>
            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">{{ feat.desc }}</p>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '../Layouts/PublicLayout.vue';

const steps = [
  { title: 'Daftar Akun', desc: 'Buat akun untuk mengakses fitur penuh, menyimpan favorit, dan berdiskusi.' },
  { title: 'Jelajahi Pengetahuan', desc: 'Cari dan filter konten riset berdasarkan kategori, label, atau tipe media.' },
  { title: 'Unggah Konten', desc: 'Berkontribusi dengan mengunggah karya, dokumen, atau materi edukasi Anda.' },
  { title: 'Kolaborasi di Forum', desc: 'Berinteraksi dengan periset lain melalui forum diskusi yang terstruktur.' }
];

const features = [
  { title: 'Repositori Digital', desc: 'Penyimpanan aman dan terpusat untuk semua aset pengetahuan.', icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4' },
  { title: 'Multi-Tipe Konten', desc: 'Dukungan untuk format teks, gambar, video, dan audio interaktif.', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' },
  { title: 'Kategori Terstruktur', desc: 'Organisasi hierarkis memudahkan pencarian topik yang spesifik.', icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z' },
  { title: 'Forum Diskusi', desc: 'Ruang tanya jawab dan kolaborasi antar pengguna dan pakar.', icon: 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z' },
  { title: 'Pencarian Cerdas', desc: 'Mesin pencari cepat dengan dukungan filter dan label.', icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' },
  { title: 'Mode Gelap', desc: 'Dukungan dark mode penuh untuk kenyamanan membaca di malam hari.', icon: 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z' }
];
</script>
