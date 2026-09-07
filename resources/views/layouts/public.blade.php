<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data :class="{ 'dark': $store.darkMode.on }" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-dark-mode" content="{{ auth()->user()->dark_mode ?? '0' }}">
        <meta name="user-authenticated" content="1">
    @endauth

    <title>@yield('title', 'MojoPedia') — Sistem Informasi Manajemen Pengetahuan BRIN</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 transition-colors duration-300 text-slate-800 dark:text-slate-100 flex flex-col min-h-screen">
    @include('components.public-navbar')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('components.footer')

    @yield('scripts')
</body>
</html>
