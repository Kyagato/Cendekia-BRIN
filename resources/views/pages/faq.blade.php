@extends('layouts.public')
@section('title', 'FAQ')

@section('content')
<!-- Header -->
<section class="hero-gradient py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 drop-shadow">Pertanyaan yang Sering Diajukan</h1>
        <p class="text-xl text-white/80 max-w-2xl mx-auto">Temukan jawaban cepat untuk pertanyaan umum seputar Cendekia BRIN</p>
    </div>
</section>

<section class="py-12 container mx-auto px-4 -mt-12 relative z-10" x-data="{ searchFaq: '' }">
    <!-- Search Bar (Client Side Filter) -->
    <div class="max-w-2xl mx-auto mb-16">
        <div class="glass bg-white/90 dark:bg-slate-800/90 backdrop-blur-md p-2 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 flex items-center">
            <div class="pl-4 text-slate-500">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input type="text" x-model="searchFaq" placeholder="Cari pertanyaan Anda di sini..." class="w-full bg-transparent border-none focus:ring-0 text-slate-800 dark:text-slate-200 px-4 py-3 text-lg placeholder-gray-400">
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        @if(isset($faqs) && count($faqs) > 0)
            @foreach($faqs as $kategori => $items)
            <div class="mb-12" x-data="{ hasVisibleItems: true }">
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-6 border-b border-slate-200 dark:border-slate-700 pb-2">{{ $kategori }}</h2>
                
                <div class="space-y-4">
                    @foreach($items as $faq)
                    <div x-data="{ open: false }" 
                         x-show="searchFaq === '' || '{{ strtolower(addslashes($faq->pertanyaan)) }}'.includes(searchFaq.toLowerCase()) || '{{ strtolower(addslashes($faq->jawaban)) }}'.includes(searchFaq.toLowerCase())"
                         class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-200"
                         :class="open ? 'ring-2 ring-primary-500 shadow-md' : 'hover:border-primary-300 dark:hover:border-slate-600'">
                        <button @click="open = !open" class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none">
                            <span class="font-bold text-lg text-slate-800 dark:text-slate-100 pr-4" :class="open ? 'text-primary-600 dark:text-primary-400' : ''">{{ $faq->pertanyaan }}</span>
                            <svg class="w-6 h-6 text-slate-500 transform transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse x-transition.opacity duration.300ms>
                            <div class="px-6 pb-5 text-slate-600 dark:text-slate-300 leading-relaxed border-t border-slate-100 dark:border-slate-700 pt-4 mt-2">
                                {!! nl2br(e($faq->jawaban)) !!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-12 text-slate-500 dark:text-slate-400">
                Belum ada FAQ yang tersedia.
            </div>
        @endif
    </div>
</section>

<!-- Kontak Support -->
<section class="py-12 bg-slate-50 dark:bg-slate-900/50">
    <div class="container mx-auto px-4 text-center max-w-2xl scroll-reveal">
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-4">Masih punya pertanyaan?</h3>
        <p class="text-slate-600 dark:text-slate-400 mb-8">Tim dukungan kami siap membantu Anda menyelesaikan kendala atau pertanyaan yang tidak ada di daftar FAQ.</p>
        <a href="mailto:support@simpanbrin.go.id" class="inline-flex items-center gap-2 bg-white dark:bg-slate-800 text-primary-600 dark:text-primary-400 font-bold px-8 py-3 rounded-xl shadow-md border border-slate-100 dark:border-slate-700 hover:shadow-lg transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
            Hubungi Support
        </a>
    </div>
</section>
@endsection
