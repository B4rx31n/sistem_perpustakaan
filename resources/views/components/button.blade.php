@props([
    'varian' => 'utama',
    'tipe' => 'submit',
    'href' => null,
    'method' => null,
    'konfirmasi' => null,
    'memuat' => false,
    'ukuran' => 'normal',
])

@php
    $varianGaya = match ($varian) {
        'bahaya' => 'border border-transparent bg-bahaya-lembut text-bahaya hover:border-bahaya hover:bg-bahaya hover:text-permukaan',
        'garis' => 'border border-garis bg-permukaan text-tinta shadow-halus hover:border-garis-kuat hover:bg-permukaan-lembut',
        default => 'border border-transparent bg-inti text-inti-kunci shadow-halus hover:bg-inti-tua hover:shadow-naik',
    };

    $tinggi = $ukuran === 'kecil' ? 'px-2.5 py-1.5 text-[0.75rem]' : 'px-4 py-2 text-[0.875rem]';
    $kelas = "inline-flex items-center justify-center gap-1.5 rounded-lg font-medium whitespace-nowrap transition-[background-color,border-color,box-shadow,transform] duration-150 active:scale-[0.98] disabled:pointer-events-none disabled:opacity-60 {$tinggi} {$varianGaya}";

    $atributTombol = $attributes->except(['class', 'href', 'method', 'konfirmasi'])->merge([
        'class' => $kelas,
        'data-mengirim' => $memuat ? 'true' : null,
    ]);
@endphp

@if ($href && ! $method)
    <a href="{{ $href }}" {{ $attributes->except(['class'])->merge(['class' => $kelas]) }}>{{ $slot }}</a>
@elseif ($method)
    <form method="POST" action="{{ $href }}"
          @if ($konfirmasi) data-konfirmasi="{{ $konfirmasi }}" @endif
          class="inline">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif
        <button type="submit" {{ $atributTombol }}>{{ $slot }}</button>
    </form>
@else
    <button type="{{ $tipe }}" {{ $atributTombol }}>{{ $slot }}</button>
@endif
