<x-layouts.app>
    @php
        $jumlahAktif = $pinjamanAktif->count();
        $jumlahReservasi = $reservasiAktif->count();
        $palingBuru = $pinjamanAktif->first();
    @endphp

    <x-page-heading
        judul="Dashboard saya"
        :deskripsi="'Pinjaman dan reservasi aktif per ' . now()->translatedFormat('l, d F Y') . '.'"
    >
        <x-button :href="route('books.index')" varian="garis" ukuran="kecil">Cari buku</x-button>
    </x-page-heading>


    @if ($jumlahTerlambat > 0)
        <x-peringatan
            class="mb-5"
            judul="{{ $jumlahTerlambat }} peminjaman lewat jatuh tempo"
            :pesan="'Denda berjalan Rp' . number_format($sisaDenda, 0, ',', '.') . '. Kembalikan di loket atau hubungi petugas untuk perpanjangan.'"
        >
            <x-slot:aksi>
                <x-button :href="route('loans.index')" ukuran="kecil">Lihat peminjaman</x-button>
            </x-slot:aksi>
        </x-peringatan>
    @endif

    <section aria-labelledby="ringkasan-saya" class="mb-5">
        <h2 id="ringkasan-saya" class="sr-only">Ringkasan peminjaman saya</h2>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <x-metrik
                label="Sedang dipinjam"
                :nilai="number_format($jumlahAktif, 0, ',', '.')"
                :konteks="$jumlahAktif > 0
                    ? 'Kembali sebelum ' . $palingBuru->harus_kembali_at->translatedFormat('d M Y') . '.'
                    : 'Tidak ada buku yang sedang keluar dari tanganmu.'"
            />

            <x-metrik
                label="Terlambat"
                :nilai="number_format($jumlahTerlambat, 0, ',', '.')"
                :warna="$jumlahTerlambat > 0 ? 'bahaya' : 'netral'"
                :konteks="$jumlahTerlambat > 0 ? 'Perlu segera dikembalikan.' : 'Semua tepat waktu.'"
            />

            <x-metrik
                label="Sisa denda"
                :nilai="'Rp' . number_format($sisaDenda, 0, ',', '.')"
                :warna="$sisaDenda > 0 ? 'aksen' : 'netral'"
                :konteks="$sisaDenda > 0 ? 'Belum lunas, bisa dibayar di loket.' : 'Tidak ada tagihan.'"
            />

            <x-metrik
                label="Reservasi menunggu"
                :nilai="number_format($jumlahReservasi, 0, ',', '.')"
                :konteks="$jumlahReservasi > 0
                    ? 'Ambil sesuai urutan antrean yang berlaku.'
                    : 'Reservasi dipakai saat semua eksemplar sedang dipinjam.'"
                arah="Cari di katalog"
                :tautan="route('books.index')"
            />
        </div>
    </section>

    <div class="grid gap-5 lg:grid-cols-3">
        <x-panel
            class="lg:col-span-2"
            judul="Buku yang sedang dipinjam"
            subjudul="Urut dari yang paling cepat jatuh tempo."
            padat
        >
            <x-slot:aksi>
                <a href="{{ route('loans.index') }}" class="font-medium text-inti hover:underline">Semua peminjaman</a>
            </x-slot:aksi>

            @if ($pinjamanAktif->isEmpty())
                <x-empty-state
                    judul="Tidak ada buku yang dipinjam"
                    pesan="Cari judul di katalog lalu pesan bila stoknya sedang habis."
                    ikon="KOSONG"
                >
                    <x-slot:aksi>
                        <x-button :href="route('books.index')" ukuran="kecil">Cari di katalog</x-button>
                    </x-slot:aksi>
                </x-empty-state>
            @else
                <ul class="divide-y divide-garis">
                    @foreach ($pinjamanAktif as $pinjaman)
                        <li class="flex flex-wrap items-center gap-x-4 gap-y-2 px-4 py-3">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('books.show', $pinjaman->book) }}"
                                   class="text-[0.875rem] font-medium text-tinta hover:underline">
                                    {{ $pinjaman->book->judul }}
                                </a>
                                <p class="mt-0.5 font-mono text-[0.6875rem] text-tinta-samar">
                                    {{ $pinjaman->kode }} &middot; {{ $pinjaman->book->category?->nama ?? 'Tanpa kategori' }}
                                </p>
                            </div>

                            <div class="shrink-0 text-[0.75rem] text-tinta-lembut">
                                <span class="block">Kembali</span>
                                <span class="angka font-mono text-tinta">
                                    {{ $pinjaman->harus_kembali_at->translatedFormat('d M Y') }}
                                </span>
                            </div>

                            <x-loan-status :loan="$pinjaman" :loan-service="$loanService" />

                            <x-button :href="route('loans.show', $pinjaman)" varian="garis" ukuran="kecil">Detail</x-button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-panel>

        <x-panel judul="Reservasi berjalan" subjudul="Antrean menunggu buku tersedia." padat>
            <x-slot:aksi>
                <a href="{{ route('reservations.index') }}" class="font-medium text-inti hover:underline">Semua</a>
            </x-slot:aksi>

            @if ($reservasiAktif->isEmpty())
                <x-empty-state
                    judul="Tidak ada reservasi"
                    pesan="Reservasi dipakai saat semua eksemplar sedang dipinjam."
                    ikon="ANTREAN"
                />
            @else
                <ul class="divide-y divide-garis">
                    @foreach ($reservasiAktif as $reservasi)
                        <li class="px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ route('books.show', $reservasi->book) }}"
                                       class="block truncate text-[0.8125rem] font-medium hover:underline">
                                        {{ $reservasi->book->judul }}
                                    </a>
                                    <p class="mt-0.5 text-[0.6875rem] text-tinta-samar">
                                        Antrean ke-{{ $reservasi->antrean }} &middot; berlaku sampai
                                        {{ $reservasi->berlaku_sampai->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                                <x-status-tag
                                    :label="$reservasi->status->label()"
                                    :warna="$reservasi->status->value === 'siap' ? 'inti' : 'netral'"
                                />
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-panel>
    </div>

    <x-panel class="mt-5" judul="Riwayat pengembalian terakhir" subjudul="Lima transaksi terakhir." padat>
        @if ($riwayatTerbaru->isEmpty())
            <x-empty-state
                judul="Riwayat masih kosong"
                pesan="Buku yang sudah kamu kembalikan akan tercatat di sini."
                ikon="RIWAYAT"
            />
        @else
            <div class="overflow-x-auto">
                <table class="tabel-dasar">
                    <caption class="sr-only">Lima pengembalian terakhir</caption>
                    <thead>
                        <tr>
                            <th scope="col">Buku</th>
                            <th scope="col">Dipinjam</th>
                            <th scope="col">Dikembalikan</th>
                            <th scope="col" class="text-right">Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayatTerbaru as $riwayat)
                            <tr>
                                <td class="max-w-64 truncate">{{ $riwayat->book->judul }}</td>
                                <td class="angka whitespace-nowrap font-mono text-[0.75rem] text-tinta-lembut">
                                    {{ $riwayat->dipinjam_at->translatedFormat('d M Y') }}
                                </td>
                                <td class="angka whitespace-nowrap font-mono text-[0.75rem] text-tinta-lembut">
                                    {{ $riwayat->dikembalikan_at->translatedFormat('d M Y') }}
                                </td>
                                <td class="angka whitespace-nowrap text-right">
                                    @if ($riwayat->denda > 0)
                                        <span class="font-semibold text-bahaya">
                                            Rp{{ number_format($riwayat->denda, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-tinta-samar">Tanpa denda</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-panel>
</x-layouts.app>
