@props([
    'judul' => null,
    'subjudul' => null,
    'padat' => false,
])

{{-- Permukaan kerja dengan kepala panel yang seragam. Setiap halaman memakai
     komponen ini supaya jarak, radius, bayangan, dan ukuran judul selalu sama. --}}
<section {{ $attributes->merge(['class' => 'kartu overflow-hidden']) }}>
    @if (filled($judul) || filled($subjudul) || isset($aksi))
        <div class="kepala-kartu">
            <div class="min-w-0">
                @if (filled($judul))
                    <h2 class="text-[0.9375rem] leading-tight font-semibold text-tinta">{{ $judul }}</h2>
                @endif

                @if (filled($subjudul))
                    <p class="mt-0.5 text-[0.8125rem] text-tinta-samar">{{ $subjudul }}</p>
                @endif
            </div>

            @isset($aksi)
                <div class="flex shrink-0 flex-wrap items-center gap-2 text-[0.75rem] text-tinta-lembut">{{ $aksi }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $padat ? '' : 'p-5' }}">
        {{ $slot }}
    </div>
</section>
