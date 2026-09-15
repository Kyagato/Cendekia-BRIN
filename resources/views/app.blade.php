<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title font-sans>{{ config('app.name', 'MojoPedia') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-mojopedia.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-[#f8fafc] dark:bg-slate-950 text-[#0f172a] dark:text-slate-100 selection:bg-blue-600 selection:text-white">
    @inertia
</body>
</html>
