{{-- Riwayat status satu entitas, dipakai untuk kartu anggota, peminjaman, dan
     reservasi. Dipisah dari halaman supaya tiga halaman itu tidak mengulang
     struktur yang sama. --}}
@props(['entitas', 'aksi' => 'dibuat'])

<dl class="grid grid-cols-2 gap-x-4 gap-y-3 sm:grid-cols-4">
    <div>
        <dt class="text-[0.75rem] font-medium text-tinta-samar">Dibuat</dt>
        <dd class="mt-0.5 text-[0.875rem] text-tinta">{{ $entitas->created_at->translatedFormat('d M Y') }}</dd>
        <dd class="font-mono text-[0.75rem] text-tinta-samar">{{ $entitas->created_at->format('H:i') }}</dd>
    </div>

    @isset($entitas->dipinjam_at)
        <div>
            <dt class="text-[0.75rem] font-medium text-tinta-samar">Dipinjam</dt>
            <dd class="mt-0.5 text-[0.875rem] text-tinta">{{ $entitas->dipinjam_at->translatedFormat('d M Y') }}</dd>
            <dd class="font-mono text-[0.75rem] text-tinta-samar">{{ $entitas->dipinjam_at->format('H:i') }}</dd>
        </div>
    @endisset

    @isset($entitas->berlaku_sampai)
        <div>
            <dt class="text-[0.75rem] font-medium text-tinta-samar">Batas ambil</dt>
            <dd class="mt-0.5 text-[0.875rem] text-tinta">{{ $entitas->berlaku_sampai->translatedFormat('d M Y') }}</dd>
            <dd class="font-mono text-[0.75rem] text-tinta-samar">{{ $entitas->berlaku_sampai->format('H:i') }}</dd>
        </div>
    @endisset

    @if (array_key_exists('diselesaikan_at', $entitas->getAttributes()) || isset($entitas->diselesaikan_at))
        <div>
            <dt class="text-[0.75rem] font-medium text-tinta-samar">Selesai</dt>
            <dd class="mt-0.5 text-[0.875rem] text-tinta">{{ $entitas->diselesaikan_at?->translatedFormat('d M Y') ?? '-' }}</dd>
        </div>
    @endif
</dl>
