@props([
    'judul',
    'pesan',
    'aksi' => null,
    'ikon' => null,
])

{{-- R-27: keadaan tanpa data punya tampilan sendiri, bukan tabel kosong. --}}
<div class="rounded-xl border border-dashed border-garis-kuat/60 bg-permukaan-lembut/40 px-5 py-12 text-center">
    <div class="mx-auto max-w-md">
        <span aria-hidden="true"
              class="mx-auto flex h-11 w-11 items-center justify-center rounded-full border border-garis bg-permukaan text-tinta-samar">
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75h12.5m-12.5 0L3.75 16.5h12.5L16.25 9.75M6.75 3.75h6.5a1 1 0 0 1 1 1v11.5a1 1 0 0 1-1 1h-6.5a1 1 0 0 1-1-1V4.75a1 1 0 0 1 1-1Z" />
            </svg>
        </span>
        <p class="mt-3.5 text-[0.9375rem] font-semibold text-tinta">{{ $judul }}</p>
        <p class="mt-1 text-[0.8125rem] leading-relaxed text-tinta-lembut">{{ $pesan }}</p>
        <p aria-hidden="true" class="mt-3 font-mono text-[0.625rem] tracking-widest text-tinta-samar uppercase">
            {{ $ikon ?? 'KOSONG' }}
        </p>

        @if ($aksi)
            <div class="mt-4 flex flex-wrap items-center justify-center gap-2">{{ $aksi }}</div>
        @endif
    </div>
</div>
