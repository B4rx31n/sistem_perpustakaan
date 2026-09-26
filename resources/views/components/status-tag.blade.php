@props(['label', 'warna' => 'netral'])

@php
    // Warna tag hanya untuk status nyata, bukan hiasan. Setiap warna punya
    // pasangan latar dan teks yang sudah diuji kontrasnya di dua mode.
    $gaya = match ($warna) {
        'inti' => 'bg-inti-lembut text-inti',
        'aksen' => 'bg-aksen-lembut text-aksen',
        'bahaya' => 'bg-bahaya-lembut text-bahaya',
        default => 'bg-permukaan-lembut text-tinta-lembut',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[0.6875rem] font-medium whitespace-nowrap '.$gaya]) }}>
    <span aria-hidden="true" class="h-1.5 w-1.5 shrink-0 rounded-full bg-current"></span>
    {{ $label }}
</span>
