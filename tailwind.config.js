import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb', // Primary Vivid
                    700: '#1d4ed8',
                    800: '#1e40af', // Secondary Deep
                    900: '#1e3a8a', // Hero Deep
                    950: '#0f172a'  // Hero Dark
                },
                mojopedia: {
                    surface: '#f8f9ff',
                    'surface-dim': '#cbdbf5',
                    'surface-bright': '#f8f9ff',
                    'surface-container-lowest': '#ffffff',
                    'surface-container-low': '#eff4ff',
                    'surface-container': '#e5eeff',
                    'surface-container-high': '#dce9ff',
                    'surface-container-highest': '#d3e4fe',
                    'on-surface': '#0b1c30',
                    'on-surface-variant': '#434655',
                    'inverse-surface': '#213145',
                    'inverse-on-surface': '#eaf1ff',
                    outline: '#737686',
                    'outline-variant': '#c3c6d7',
                    'surface-tint': '#0053db',
                    primary: '#004ac6',
                    'on-primary': '#ffffff',
                    'primary-container': '#2563eb',
                    'on-primary-container': '#eeefff',
                    'inverse-primary': '#b4c5ff',
                    secondary: '#3755c3',
                    'on-secondary': '#ffffff',
                    'secondary-container': '#708cfd',
                    'on-secondary-container': '#00217a',
                    tertiary: '#0d46d1',
                    'on-tertiary': '#ffffff',
                    'tertiary-container': '#3761ea',
                    'on-tertiary-container': '#efefff',
                    error: '#ba1a1a',
                    'on-error': '#ffffff',
                    'error-container': '#ffdad6',
                    'on-error-container': '#93000a',
                    'primary-fixed': '#dbe1ff',
                    'primary-fixed-dim': '#b4c5ff',
                    'on-primary-fixed': '#00174b',
                    'on-primary-fixed-variant': '#003ea8',
                    'secondary-fixed': '#dde1ff',
                    'secondary-fixed-dim': '#b8c4ff',
                    'on-secondary-fixed': '#001453',
                    'on-secondary-fixed-variant': '#173bab',
                    'tertiary-fixed': '#dce1ff',
                    'tertiary-fixed-dim': '#b7c4ff',
                    'on-tertiary-fixed': '#001551',
                    'on-tertiary-fixed-variant': '#0039b5',
                    background: '#f8f9ff',
                    'on-background': '#0b1c30',
                    'surface-variant': '#d3e4fe',
                    'hero-blue-dark': '#0f172a',
                    'hero-blue-deep': '#1e3a8a',
                    'hero-blue-vivid': '#2563eb',
                    canvas: '#f8fafc',
                    subtle: '#f1f5f9',
                    card: '#ffffff',
                    border: '#e2e8f0',
                    'text-primary': '#0f172a',
                    'text-secondary': '#475569',
                    'text-muted': '#94a3b8',
                    'tag-teks-bg': '#eff6ff',
                    'tag-teks': '#2563eb',
                    'tag-video-bg': '#fef2f2',
                    'tag-video': '#dc2626',
                    'tag-audio-bg': '#f0fdf4',
                    'tag-audio': '#16a34a',
                    'tag-gambar-bg': '#fefce8',
                    'tag-gambar': '#ca8a04',
                    'badge-neutral-bg': '#f1f5f9',
                    'badge-neutral': '#475569',
                },
                secondary: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                    950: '#020617'
                }
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'slide-up': 'slideUp 0.5s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
                'float': 'float 3s ease-in-out infinite'
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideDown: {
                    '0%': { transform: 'translateY(-20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                }
            }
        },
    },

    plugins: [forms],
};
