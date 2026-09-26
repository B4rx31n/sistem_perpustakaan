@props([
    'judul',
    'deskripsi' => null,
    'aksi' => null,
])

{{-- Judul halaman memakai satu ukuran saja. Hierarki di produk ini datang dari
     ketebalan dan posisi, bukan dari membesarkan angka. --}}
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
    <div class="min-w-0">
        <h1 class="text-[1.5rem] leading-tight font-semibold tracking-tight text-tinta">{{ $judul }}</h1>
        @if ($deskripsi)
            <p class="mt-1.5 max-w-3xl text-[0.875rem] leading-relaxed text-tinta-lembut">{{ $deskripsi }}</p>
        @endif
    </div>

    {{-- Aksi halaman boleh lewat prop `aksi` atau langsung isi slot. Semua view
         di proyek ini memakai slot, jadi keduanya harus dirender. --}}
    @if ($aksi || ! $slot->isEmpty())
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            {{ $aksi ?? $slot }}
        </div>
    @endif
</div>
