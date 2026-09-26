@props(['nilai', 'label', 'keterangan' => null])

{{-- Angka besar hanya untuk metrik operasional yang benar-benar dihitung dari
     database. Tidak ada metrik pemanis di halaman mana pun. --}}
<div class="kartu-naik rounded-xl border border-garis bg-permukaan px-4 py-3.5 shadow-halus">
    <p class="text-[0.75rem] font-medium text-tinta-lembut">{{ $label }}</p>
    <p class="angka mt-2 text-[1.625rem] leading-none font-semibold tracking-tight text-tinta">{{ $nilai }}</p>
    @if ($keterangan)
        <p class="mt-2 text-[0.75rem] leading-snug text-tinta-samar">{{ $keterangan }}</p>
    @endif
</div>
