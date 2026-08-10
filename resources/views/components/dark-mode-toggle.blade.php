<button 
    @click="$store.darkMode.toggle()" 
    type="button" 
    class="relative p-2 rounded-full text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors focus:outline-none"
    aria-label="Toggle dark mode"
>
    <!-- Sun icon -->
    <svg 
        x-show="$store.darkMode.on" 
        x-transition:enter="transition ease-out duration-300 transform" 
        x-transition:enter-start="-rotate-90 opacity-0" 
        x-transition:enter-end="rotate-0 opacity-100" 
        x-transition:leave="transition ease-in duration-300 transform absolute inset-0 m-auto" 
        x-transition:leave-start="rotate-0 opacity-100" 
        x-transition:leave-end="rotate-90 opacity-0"
        class="w-5 h-5" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24" 
        style="display: none;"
    >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>
    
    <!-- Moon icon -->
    <svg 
        x-show="!$store.darkMode.on" 
        x-transition:enter="transition ease-out duration-300 transform" 
        x-transition:enter-start="rotate-90 opacity-0" 
        x-transition:enter-end="rotate-0 opacity-100" 
        x-transition:leave="transition ease-in duration-300 transform absolute inset-0 m-auto" 
        x-transition:leave-start="rotate-0 opacity-100" 
        x-transition:leave-end="-rotate-90 opacity-0"
        class="w-5 h-5" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
    >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
    </svg>
</button>
