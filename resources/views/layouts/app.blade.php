<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width-width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('business.business.name', 'Rent Specialist'))</title>
    <meta name="description" content="@yield('description', 'Sewa mobil, SUV, dan sepeda motor di ' . config('business.business.name', 'Rent Specialist') . '. Armada terawat, harga transparan, dan komunikasi langsung via WhatsApp.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @stack('head')
</head>
<body
    class="min-h-screen flex flex-col bg-bone-50"
    data-nav-theme="{{ $navTheme ?? 'light' }}"
>
    @if (($navTheme ?? 'light') === 'dark')
        <x-nav-bar variant="dark" />
    @else
        <x-nav-bar variant="light" />
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <x-site-footer />

    @livewireScripts
    @stack('scripts')
</body>
</html>
