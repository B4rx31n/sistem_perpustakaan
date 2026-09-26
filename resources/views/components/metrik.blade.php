@props([
    'label',
    'nilai',
    'konteks' => null,
    'arah' => null,
    'tautan' => null,
    'warna' => 'netral',
])

{{-- Metrik utama. Berbeda dari x-statistik yang padat untuk baris sekunder,
     kartu ini dipakai paling banyak sekali atau dua kali per halaman supaya
     ada titik berat yang jelas. --}}
@php
    $gayaGaris = match ($warna) {
        'inti' => 'bg-inti',
        'aksen' => 'bg-aksen',
        'bahaya' => 'bg-bahaya',
        default => 'bg-garis-kuat',
    };

    $gayaLatik = match ($warna) {
        'inti' => 'bg-inti-lembut',
        'aksen' => 'bg-aksen-lembut',
        'bahaya' => 'bg-bahaya-lembut',
        default => 'bg-permukaan-lembut',
    };

    $gayaAngka = match ($warna) {
        'bahaya' => 'text-bahaya',
        'aksen' => 'text-aksen',
        default => 'text-tinta',
    };

    $tag = filled($tautan) ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if (filled($tautan)) href="{{ $tautan }}" @endif
    {{ $attributes->merge([
        'class' => 'kartu-naik block rounded-xl border border-garis px-4 py-4 '
            .(filled($tautan) ? 'cursor-pointer' : ''),
    ]) }}
>
    <span aria-hidden="true" class="flex h-9 w-9 items-center justify-center rounded-lg {{ $gayaLatik }}">
        <span class="block h-2 w-2 rounded-full {{ $gayaGaris }}"></span>
    </span>

    <span class="mt-3.5 block text-[0.8125rem] font-medium text-tinta-lembut">
        {{ $label }}
    </span>

    <span class="angka mt-1.5 block font-mono text-[1.875rem] leading-none font-semibold tracking-tight {{ $gayaAngka }}">
        {{ $nilai }}
    </span>

    @if (filled($konteks))
        <span class="mt-2 block text-[0.75rem] leading-snug text-tinta-samar">{{ $konteks }}</span>
    @endif

    @if (filled($arah))
        <span class="mt-2.5 inline-flex items-center gap-1 text-[0.75rem] font-medium text-inti">
            {{ $arah }} <span aria-hidden="true">&rarr;</span>
        </span>
    @endif
</{{ $tag }}>
