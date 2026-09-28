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

    {{-- Motif identitas. Mark buku dibuka ulang sebagai tanda air besar di
         belakang layar, karena ini satu-satunya halaman yang tidak padat data dan
         tidak ada tabel yang bisa tertutup dekorasi. Opasitasnya rendah supaya
         tidak pernah jadi gambar yang dibaca lebih dulu sebelum judul. --}}
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.1"
             stroke-linecap="round" stroke-linejoin="round"
             class="absolute -top-24 -left-24 h-[34rem] w-[34rem] text-pastel-3/35">
            <path d="M12 6.75C10.5 5.25 8.4 4.5 5.25 4.5c-.9 0-1.5.1-1.5.1v12.9s.6-.1 1.5-.1c3.15 0 5.25.75 6.75 2.25 1.5-1.5 3.6-2.25 6.75-2.25.9 0 1.5.1 1.5.1V4.6s-.6-.1-1.5-.1c-3.15 0-5.25.75-6.75 2.25Z" />
            <path d="M12 6.75v12.9" />
            <path d="M7.5 8.4h1.5M7.5 11.1h1.5M7.5 13.8h1.5" opacity=".5" />
            <path d="M15 8.4h1.5M15 11.1h1.5M15 13.8h1.5" opacity=".5" />
        </svg>

        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.1"
             stroke-linecap="round" stroke-linejoin="round"
             class="absolute -right-20 -bottom-20 h-[26rem] w-[26rem] text-pastel-aksen-2/40">
            <path d="M12 6.75C10.5 5.25 8.4 4.5 5.25 4.5c-.9 0-1.5.1-1.5.1v12.9s.6-.1 1.5-.1c3.15 0 5.25.75 6.75 2.25 1.5-1.5 3.6-2.25 6.75-2.25.9 0 1.5.1 1.5.1V4.6s-.6-.1-1.5-.1c-3.15 0-5.25.75-6.75 2.25Z" />
            <path d="M12 6.75v12.9" />
        </svg>
    </div>

    <main class="relative z-10 flex min-h-screen items-center justify-center px-4 py-10">
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
