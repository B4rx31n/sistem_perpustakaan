{{-- Topbar. Berisi kontrol yang selalu ada di layar mana pun: buka/tutup sidebar,
     nama halaman, dan pengali tema. Menu akun tidak dipindah ke sini supaya
     sidebar tetap menjadi satu-satunya tempat bernavigasi. --}}
<header class="sticky top-0 z-30 border-b border-garis bg-permukaan/80 backdrop-blur-md">
    <div class="flex h-16 items-center gap-3 px-4 sm:px-6">
        {{-- Di layar sempit tidak ada sidebar yang menandai halaman aktif, jadi
             mark buku muncul di sini sebagai penanda posisi dan sebagai jalan
             kembali ke dashboard. --}}
        <a href="{{ route('dashboard') }}"
           class="-ml-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg lg:hidden"
           aria-label="Kembali ke dashboard">
            <span aria-hidden="true"
                  class="flex h-8 w-8 items-center justify-center rounded-lg bg-grad-inti text-inti-kunci ring-1 ring-pastel-3/60">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                     stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0">
                    <path d="M12 6.75C10.5 5.25 8.4 4.5 5.25 4.5c-.9 0-1.5.1-1.5.1v12.9s.6-.1 1.5-.1c3.15 0 5.25.75 6.75 2.25 1.5-1.5 3.6-2.25 6.75-2.25.9 0 1.5.1 1.5.1V4.6s-.6-.1-1.5-.1c-3.15 0-5.25.75-6.75 2.25Z" />
                    <path d="M12 6.75v12.9" />
                </svg>
            </span>
        </a>

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
                      class="flex h-8 w-8 items-center justify-center rounded-full bg-grad-aksen font-mono text-[0.75rem] font-semibold text-aksen">
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
