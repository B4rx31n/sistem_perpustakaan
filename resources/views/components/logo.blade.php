@props([
    'ukuran' => 'kartu',
    'tampil_teks' => true,
])

{{--
    Mark dan wordmark perpustakaan.

    Mark-nya buku terbuka dengan satu garis halaman di tiap halaman. Garis halaman
    itu yang membuatnya terbaca sebagai buku dan bukan glyph kotak umum, dan
    digambar dengan bahasa garis yang sama seperti x-icon supaya logo dan ikon
    tidak terlihat berasal dari dua sistem gambar yang berbeda.

    Komponen ini membungkus <div>, bukan <a>, supaya pemanggil yang memang
    membutuhkan tautan (sidebar) bisa membungkusnya dengan tautan sendiri
    tanpa kehilangan isi mark dan wordmark.
--}}
@php
    $ukuranMark = match ($ukuran) {
        'kecil' => 'h-8 w-8 rounded-lg',
        'besar' => 'h-12 w-12 rounded-xl',
        default => 'h-10 w-10 rounded-xl',
    };

    $ukuranGlyph = match ($ukuran) {
        'kecil' => 'h-[1.125rem] w-[1.125rem]',
        'besar' => 'h-6 w-6',
        default => 'h-5 w-5',
    };

    $ukuranTeks = match ($ukuran) {
        'kecil' => 'text-[0.8125rem]',
        'besar' => 'text-[1.0625rem]',
        default => 'text-[0.9375rem]',
    };
@endphp

<div {{ $attributes->merge(['class' => 'flex min-w-0 items-center gap-2.5']) }}>
    <span aria-hidden="true"
          class="flex {{ $ukuranMark }} shrink-0 items-center justify-center bg-grad-inti text-inti-kunci shadow-halus ring-1 ring-pastel-3/60">
        <svg viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="1.7"
             stroke-linecap="round"
             stroke-linejoin="round"
             class="{{ $ukuranGlyph }} shrink-0">
            {{-- Dua halaman yang terbuka dan bertemu di garis tengah. --}}
            <path d="M12 6.75C10.5 5.25 8.4 4.5 5.25 4.5c-.9 0-1.5.1-1.5.1v12.9s.6-.1 1.5-.1c3.15 0 5.25.75 6.75 2.25 1.5-1.5 3.6-2.25 6.75-2.25.9 0 1.5.1 1.5.1V4.6s-.6-.1-1.5-.1c-3.15 0-5.25.75-6.75 2.25Z" />
            <path d="M12 6.75v12.9" />
            {{-- Garis halaman. Masing-masing satu per halaman supaya tetap terbaca
                 pada ukuran kecil di sidebar yang terlipat. --}}
            <path d="M7.5 8.4h1.5M7.5 11.1h1.5" opacity=".55" />
            <path d="M15 8.4h1.5M15 11.1h1.5" opacity=".55" />
        </svg>
    </span>

    @if ($tampil_teks)
        <span class="min-w-0 leading-tight">
            <span data-sidebar-label
                  class="block truncate {{ $ukuranTeks }} font-semibold text-tinta">Perpustakaan Nusantara</span>
            <span data-sidebar-label
                  class="block truncate text-[0.6875rem] text-tinta-samar">Sistem Layanan</span>
        </span>
    @endif
</div>
