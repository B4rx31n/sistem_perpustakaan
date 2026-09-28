<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sistem Perpustakaan' }}</title>

    {{-- Tema dibaca sebelum halaman digambar supaya tidak berkedip putih saat mode gelap aktif.
         State sidebar juga dibaca lebih awal supaya tidak melompat saat halaman dimuat. --}}
    <script>
        (function () {
            try {
                var simpan = localStorage.getItem('tema-perpustakaan');
                var gelap = simpan === 'gelap'
                    || (!simpan && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', gelap);

                if (localStorage.getItem('sidebar-perpustakaan') === 'ringkas') {
                    document.documentElement.classList.add('sidebar-ringkas');
                }
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

    <a href="#konten-utama"
       class="sr-only rounded-lg focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:border focus:border-pastel-3 focus:bg-grad-inti focus:px-3 focus:py-2 focus:text-sm focus:text-inti-kunci">
        Lompat ke konten utama
    </a>

    <div class="flex min-h-screen">
        {{-- Sidebar menumpuk di atas konten pada layar sempit. Lebarnya dikendalikan
             oleh kelas .sidebar-ringkas pada elemen <html> yang disetel JS. --}}
        <aside id="sidebar"
               data-sidebar
               class="fixed inset-y-0 left-0 z-40 w-[17rem] shrink-0 border-r border-garis bg-permukaan/85 transition-[width,transform] duration-200 ease-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:shadow-naik lg:shadow-transparent max-lg:-translate-x-full max-lg:shadow-tebal"
               aria-label="Navigasi utama">
            <div data-sidebar-kepala
                 class="flex h-16 shrink-0 items-center gap-2.5 border-b border-garis px-5">
                <a href="{{ route('dashboard') }}"
                   class="min-w-0 flex-1 rounded-lg focus-visible:outline-offset-4">
                    <x-logo ukuran="kecil" />
                </a>

                <button type="button"
                        data-sidebar-toggle
                        aria-expanded="false"
                        aria-controls="sidebar"
                        class="ml-auto hidden h-8 w-8 shrink-0 items-center justify-center rounded-lg text-tinta-lembut transition-colors duration-150 hover:bg-permukaan-lembut hover:text-tinta lg:flex"
                        aria-label="Ciutkan atau lebarkan menu">
                    <span data-sidebar-ikon="ciut"><x-icon nama="panel-kanan" /></span>
                    <span data-sidebar-ikon="luas"><x-icon nama="panel-kiri" /></span>
                </button>

                <button type="button"
                        data-sidebar-sembunyi
                        class="ml-auto flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-tinta-lembut transition-colors duration-150 hover:bg-permukaan-lembut hover:text-tinta lg:hidden"
                        aria-label="Tutup menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                         aria-hidden="true" class="h-5 w-5 shrink-0">
                        <path d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @include('partials.nav')
        </aside>

        {{-- Lapisan gelap di belakang sidebar untuk layar sempit. --}}
        <div data-sidebar-lapis
             class="fixed inset-0 z-30 hidden bg-tinta/40 backdrop-blur-[2px] lg:hidden"></div>

        <div class="flex min-w-0 flex-1 flex-col">
            @include('partials.topbar')

            <main id="konten-utama" class="mx-auto w-full max-w-[1400px] flex-1 px-4 py-6 sm:px-6 sm:py-8">
                @include('partials.notifikasi')

                {{ $slot }}
            </main>

            @include('partials.footer')
        </div>
    </div>

    @stack('skrip')
</body>
</html>
