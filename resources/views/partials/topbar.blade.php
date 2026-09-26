{{-- Topbar. Berisi kontrol yang selalu ada di layar mana pun: buka/tutup sidebar,
     nama halaman, dan pengali tema. Menu akun tidak dipindah ke sini supaya
     sidebar tetap menjadi satu-satunya tempat bernavigasi. --}}
<header class="sticky top-0 z-30 border-b border-garis bg-permukaan/85 backdrop-blur-md">
    <div class="flex h-16 items-center gap-3 px-4 sm:px-6">
        <button type="button"
                data-sidebar-toggle
                aria-expanded="false"
                aria-controls="sidebar"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-tinta-lembut transition-colors duration-150 hover:bg-permukaan-lembut hover:text-tinta"
                aria-label="Buka atau tutup menu navigasi">
            <x-icon nama="panel-kiri" />
        </button>

        <div class="min-w-0 flex-1">
            <p class="truncate text-[0.9375rem] font-semibold text-tinta">{{ $title ?? 'Sistem Perpustakaan' }}</p>
            @isset($subjudul)
                <p class="truncate text-[0.75rem] text-tinta-samar">{{ $subjudul }}</p>
            @endisset
        </div>

        <button type="button"
                data-theme-toggle
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-tinta-lembut transition-colors duration-150 hover:bg-permukaan-lembut hover:text-tinta"
                aria-label="Ganti mode terang dan gelap">
            <span data-theme-icon="terang" aria-hidden="true" class="hidden dark:inline"><x-icon nama="bulan" /></span>
            <span data-theme-icon="gelap" aria-hidden="true" class="dark:hidden"><x-icon nama="matahari" /></span>
        </button>

        @auth
            <span aria-hidden="true"
                  class="hidden h-8 w-px bg-garis sm:block"></span>

            <a href="{{ route('profile.edit') }}"
               class="hidden items-center gap-2.5 rounded-lg px-2 py-1.5 transition-colors duration-150 hover:bg-permukaan-lembut sm:flex">
                <span aria-hidden="true"
                      class="flex h-8 w-8 items-center justify-center rounded-full bg-inti-lembut font-mono text-[0.75rem] font-semibold text-inti">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </span>
                <span class="text-left leading-tight">
                    <span class="block max-w-32 truncate text-[0.8125rem] font-medium text-tinta">{{ auth()->user()->name }}</span>
                    <span class="block text-[0.6875rem] text-tinta-samar">{{ auth()->user()->role->label() }}</span>
                </span>
            </a>
        @endauth
    </div>
</header>
