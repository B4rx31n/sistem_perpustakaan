@props([
    'judul',
    'deskripsi',
])

{{-- Kepala kartu untuk halaman masuk dan pendaftaran. Dipakai kedua halaman
     supaya identitas visualnya sama persis di titik masuk pertama dan di
     pendaftaran, yang biasanya terpisah dari navigasi utama. --}}
<div class="relative border-b border-garis bg-grad-kepala px-6 py-5">
    <x-logo ukuran="besar" />

    <h1 class="mt-4 text-[1.125rem] leading-tight font-semibold tracking-tight text-tinta">
        {{ $judul }}
    </h1>

    <p class="mt-1 text-[0.8125rem] leading-relaxed text-tinta-lembut">
        {{ $deskripsi }}
    </p>
</div>
