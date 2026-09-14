<div class="bg-slate-50 dark:bg-slate-900 py-16 relative overflow-hidden font-sans transition-colors duration-300">
    
    <!-- Background Watermark Text (Optional, for aesthetics) -->
    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none flex justify-center pointer-events-none opacity-[0.03] dark:opacity-[0.02] select-none" style="font-size: 14vw; font-weight: 900; color: #0f172a; transform: translateY(30%);">
        MOJOKERTO
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Floating Card -->
        <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-slate-100 dark:border-slate-700 p-8 sm:p-12 lg:p-14 transition-colors duration-300">
            
            <!-- Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8">
                
                <!-- Column 1: Brand (Spans 5 cols) -->
                <div class="lg:col-span-5">
                    <div class="flex items-start gap-5">
                        <!-- Logo -->
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <img src="/images/logo-kominfo.png" alt="Logo Kabupaten Mojokerto" class="w-full h-full object-contain drop-shadow-sm" />
                        </div>
                        <div class="mt-1">
                            <h4 class="text-[10px] font-bold text-blue-600 dark:text-blue-400 tracking-[0.2em] uppercase mb-1.5 transition-colors">Website Resmi</h4>
                            <h2 class="text-[26px] font-serif font-bold text-slate-900 dark:text-white leading-tight transition-colors">
                                Dinas komunikasi dan<br>informatika
                            </h2>
                            <p class="text-[13px] italic text-slate-500 dark:text-slate-400 mt-2 font-serif transition-colors">Kabupaten Mojokerto</p>
                        </div>
                    </div>
                    
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-8 max-w-sm transition-colors">
                        Dinas komunikasi dan informatika website diskominfo
                    </p>
                    
                    <!-- Socials -->
                    <div class="flex items-center gap-3 mt-6">
                        <a href="#" class="w-10 h-10 rounded-full border border-blue-100 dark:border-slate-600 flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full border border-blue-100 dark:border-slate-600 flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Column 2: Navigasi (Spans 3 cols) -->
                <div class="lg:col-span-3">
                    <h4 class="text-[10px] font-bold text-blue-600 dark:text-blue-400 tracking-[0.2em] uppercase mb-6 transition-colors">Navigasi</h4>
                    <ul class="space-y-4">
                        @php
                            $navItems = [
                                ['label' => 'Beranda', 'url' => '/', 'arrow' => false],
                                ['label' => 'Tentang', 'url' => '/tentang', 'arrow' => false],
                                ['label' => 'Kategori', 'url' => '/kategori', 'arrow' => false],
                                ['label' => 'Forum', 'url' => '/forum', 'arrow' => false],
                                ['label' => 'FAQs', 'url' => '/faq', 'arrow' => false],
                            ];
                        @endphp
                        @foreach($navItems as $item)
                        <li>
                            <a href="{{ $item['url'] }}" class="flex items-center group">
                                <span class="w-1 h-1 rounded-full bg-blue-400 dark:bg-blue-500 mr-3 group-hover:bg-blue-600 dark:group-hover:bg-blue-300 transition-colors"></span>
                                <span class="text-sm text-slate-600 dark:text-slate-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $item['label'] }}</span>
                                @if($item['arrow'])
                                <svg class="w-3 h-3 ml-auto text-slate-300 dark:text-slate-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                @endif
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Column 3: Hubungi Kami (Spans 4 cols) -->
                <div class="lg:col-span-4">
                    <h4 class="text-[10px] font-bold text-blue-600 dark:text-blue-400 tracking-[0.2em] uppercase mb-6 transition-colors">Hubungi Kami</h4>
                    <div class="space-y-6">
                        
                        <!-- Address -->
                        <div class="flex items-start gap-4">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-slate-700 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed pt-1 transition-colors">
                                Jl. RA. Basuni Nomor 14, Jampirogo, Kecamatan Sooko, Kabupaten Mojokerto, Kode Pos 61361, Jawa Timur.
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start gap-4">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-slate-700 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div class="pt-0.5">
                                <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1 transition-colors">Telepon</span>
                                <div class="flex items-center gap-2 group cursor-pointer" onclick="navigator.clipboard.writeText('(0321) 391268')">
                                    <span class="text-sm text-slate-600 dark:text-slate-300 transition-colors">(0321) 391268</span>
                                    <svg class="w-3.5 h-3.5 text-slate-300 dark:text-slate-500 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start gap-4">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-slate-700 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="pt-0.5">
                                <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1 transition-colors">Email</span>
                                <div class="flex items-center gap-2 group cursor-pointer" onclick="navigator.clipboard.writeText('diskominfo@mojokertokab.go.id')">
                                    <span class="text-sm text-slate-600 dark:text-slate-300 transition-colors">diskominfo@mojokertokab.go.id</span>
                                    <svg class="w-3.5 h-3.5 text-slate-300 dark:text-slate-500 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Divider & Copyright -->
            <div class="mt-14 pt-8 border-t border-blue-50 dark:border-slate-700 text-center transition-colors">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] transition-colors">
                    &copy; {{ date('Y') }} DISKOMINFO KABUPATEN MOJOKERTO
                </p>
            </div>
        </div>
    </div>

      <!-- Scroll to Top Button -->
      <button id="scrollToTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-white dark:bg-slate-800 border-[1.5px] border-blue-600 dark:border-blue-400 flex flex-col items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-700 hover:shadow-[0_4px_15px_rgb(37,99,235,0.2)] transition-all duration-300 focus:outline-none shadow-sm group opacity-0 pointer-events-none translate-y-4">
          <svg class="w-4 h-4 mb-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
          <span class="text-[8px] font-bold uppercase tracking-wider">Top</span>
      </button>
</div>
<script>
    window.addEventListener('scroll', function() {
        const btn = document.getElementById('scrollToTopBtn');
        if (btn) {
            if (window.scrollY > 300) {
                btn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
                btn.classList.add('opacity-100', 'translate-y-0');
            } else {
                btn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                btn.classList.remove('opacity-100', 'translate-y-0');
            }
        }
    });
</script>
