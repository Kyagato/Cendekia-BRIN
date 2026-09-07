import './bootstrap';
import Alpine from 'alpinejs';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Dark Mode Store for Alpine (Legacy Blade support)
Alpine.store('darkMode', {
    on: false,
    init() {
        const saved = localStorage.getItem('darkMode');
        const userPref = document.querySelector('meta[name="user-dark-mode"]');
        if (saved !== null) {
            this.on = saved === 'true';
        } else if (userPref) {
            this.on = userPref.content === '1';
        } else {
            this.on = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        this.apply();
    },
    toggle() {
        this.on = !this.on;
        localStorage.setItem('darkMode', this.on);
        this.apply();
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token && document.querySelector('meta[name="user-authenticated"]')) {
            fetch('/toggle-dark-mode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token.content,
                },
                body: JSON.stringify({ dark_mode: this.on })
            }).catch(() => {});
        }
    },
    apply() {
        if (this.on) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
});

window.Alpine = Alpine;
Alpine.start();

// Initialize Inertia Vue 3 App
createInertiaApp({
    title: (title) => title ? `${title} - MojoPedia` : 'MojoPedia',
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#dc2626', // BRIN Crimson Red progress bar
    },
});
