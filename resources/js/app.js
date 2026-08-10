import './bootstrap';
import Alpine from 'alpinejs';

// Dark Mode Store
Alpine.store('darkMode', {
    on: false,
    init() {
        // Check localStorage first, then check user preference from meta tag
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
        
        // Sync to database if authenticated
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

// Scroll Reveal Observer
document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
                entry.target.style.opacity = '1';
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.scroll-reveal').forEach(el => {
        el.style.opacity = '0';
        observer.observe(el);
    });
});

window.Alpine = Alpine;
Alpine.start();
