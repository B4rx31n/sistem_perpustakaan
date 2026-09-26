<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sistem Perpustakaan' }}</title>

    {{-- Tema dibaca sebelum halaman digambar supaya tidak berkedip putih saat mode gelap aktif. --}}
    <script>
        (function () {
            try {
                var simpan = localStorage.getItem('tema-perpustakaan');
                var gelap = simpan === 'gelap'
                    || (!simpan && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', gelap);
            } catch (e) {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-full">
    <x-loading-bar />

    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </main>

    <button type="button"
            data-theme-toggle
            class="fixed right-4 bottom-4 z-20 flex h-10 w-10 items-center justify-center rounded-full border border-garis bg-permukaan text-tinta-lembut shadow-naik transition-colors duration-150 hover:bg-permukaan-lembut hover:text-tinta"
            aria-label="Ganti mode terang dan gelap">
        <span data-theme-icon="terang" aria-hidden="true" class="hidden dark:inline"><x-icon nama="bulan" /></span>
        <span data-theme-icon="gelap" aria-hidden="true" class="dark:hidden"><x-icon nama="matahari" /></span>
    </button>

    @stack('skrip')
</body>
</html>
