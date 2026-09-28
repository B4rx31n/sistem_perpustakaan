@props([
    'persen' => 0,
    'warna' => 'inti',
    'label' => null,
    'nilai' => null,
])

{{-- Bilah proporsi. Dipakai untuk sebaran bulanan dan stok menipis, dengan
     angka yang selalu ditulis di luar bilah supaya tidak perlu ditebak. --}}
@php
    /* Isi bilah memakai gradien dengan tekstur yang sama, bukan warna pekat, supaya
       panjang bilah tetap terbaca sebagai kuantitas dan tidak berubah jadi
       dekoratif. Gradien berhenti pada arah yang sama dengan tombol. */
    $gayaIsi = match ($warna) {
        'aksen' => 'bg-grad-aksen',
        'bahaya' => 'bg-grad-bahaya',
        default => 'bg-grad-inti',
    };

    $lebar = max(0, min(100, round((float) $persen, 1)));
@endphp

<div>
    @if (filled($label) || filled($nilai))
        <div class="flex items-baseline justify-between gap-2">
            @if (filled($label))
                <span class="truncate text-[0.8125rem] text-tinta">{{ $label }}</span>
            @endif

            @if (filled($nilai))
                <span class="angka shrink-0 font-mono text-[0.75rem] font-semibold text-tinta">{{ $nilai }}</span>
            @endif
        </div>
    @endif

    <div @if (filled($label)) role="img" aria-label="{{ $label }}: {{ round($lebar, 1) }} persen" @else aria-hidden="true" @endif
         class="mt-2 h-2 w-full overflow-hidden rounded-full bg-permukaan-lembut ring-1 ring-garis/60 ring-inset">
        <div class="h-full rounded-full {{ $gayaIsi }} transition-[width] duration-200" style="width: {{ $lebar }}%"></div>
    </div>
</div>
