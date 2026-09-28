@props([
    'judul',
    'pesan',
    'aksi' => null,
    'ikon' => null,
])

{{-- R-27: keadaan tanpa data punya tampilan sendiri, bukan tabel kosong.

     Lambangnya rak buku, bukan glyph kotak generik, karena di aplikasi ini
     hampir setiap keadaan kosong soal koleksi: tidak ada buku dipinjam, tidak
     ada judul terdaftar, tidak ada reservasi berjalan. Rak yang kosong
     mengatakan hal yang sama dengan apa pun yang sedang hilang. --}}
<div class="rounded-xl border border-dashed border-pastel-3 bg-pastel-1/40 px-5 py-12 text-center">
    <div class="mx-auto max-w-md">
        <span aria-hidden="true"
              class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl border border-pastel-3 bg-grad-inti text-inti-kunci">
            <x-icon nama="rak" ukuran="besar" />
        </span>
        <p class="mt-3.5 text-[0.9375rem] font-semibold text-tinta">{{ $judul }}</p>
        <p class="mt-1 text-[0.8125rem] leading-relaxed text-tinta-lembut">{{ $pesan }}</p>
        <p aria-hidden="true" class="mt-3 font-mono text-[0.625rem] text-tinta-samar uppercase">
            {{ $ikon ?? 'KOSONG' }}
        </p>

        @if ($aksi)
            <div class="mt-4 flex flex-wrap items-center justify-center gap-2">{{ $aksi }}</div>
        @endif
    </div>
</div>
