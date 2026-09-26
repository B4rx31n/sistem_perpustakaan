@props([
    'judul',
    'pesan' => null,
    'warna' => 'bahaya',
])

{{-- Peringatan yang menuntut tindakan. Dipakai hanya untuk hal yang benar-benar
     perlu DICATAT hari ini, bukan untuk informasi umum. Nama kelas ditulis
     utuh karena Tailwind memindai sumber untuk nama lengkap, bukan fragmen. --}}
@php
    $gayaKulit = match ($warna) {
        'inti' => 'bg-inti-lembut text-inti',
        'aksen' => 'bg-aksen-lembut text-aksen',
        default => 'bg-bahaya-lembut text-bahaya',
    };

    $gayaTeks = match ($warna) {
        'inti' => 'text-inti',
        'aksen' => 'text-aksen',
        default => 'text-bahaya',
    };

    $gayaIkon = match ($warna) {
        'inti' => 'bg-inti/15',
        'aksen' => 'bg-aksen/15',
        default => 'bg-bahaya/15',
    };
@endphp

<div role="alert"
     {{ $attributes->merge(['class' => 'flex items-start gap-3.5 rounded-xl border border-transparent px-4 py-3.5 '.$gayaKulit]) }}>
    <span aria-hidden="true" class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $gayaIkon }} {{ $gayaTeks }}">
        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v3.5a.75.75 0 0 0 1.5 0v-3.5ZM10 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
        </svg>
    </span>

    <div class="min-w-0 flex-1">
        <p class="text-[0.875rem] font-semibold {{ $gayaTeks }}">{{ $judul }}</p>

        @if (filled($pesan))
            <p class="mt-0.5 text-[0.8125rem] leading-relaxed opacity-90">{{ $pesan }}</p>
        @endif
    </div>

    @isset($aksi)
        <div class="shrink-0 self-center">{{ $aksi }}</div>
    @endisset
</div>
