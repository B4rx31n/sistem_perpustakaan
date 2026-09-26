<x-layouts.app>
    <x-page-heading
        judul="Laporan"
        :deskripsi="'Rekapitulasi operasional perpustakaan. Periode default adalah ' . $bulanIni->translatedFormat('F Y') . ', dan tiap laporan punya filter sendiri.'"
    />


    @php
        $laporanHarian = [
            [
                'rute' => 'reports.loans',
                'judul' => 'Rekapitulasi peminjaman',
                'deskripsi' => 'Peminjaman dan pengembalian satu periode, lengkap dengan total denda keterlambatan.',
                'catatan' => 'Ada filter tanggal',
            ],
            [
                'rute' => 'reports.overdue',
                'judul' => 'Peminjaman terlambat',
                'deskripsi' => 'Daftar yang dipakai saat menagih keterlambatan, dengan denda berjalan per hari.',
                'catatan' => 'Kondisi saat ini',
            ],
            [
                'rute' => 'payments.receivables',
                'judul' => 'Tagihan denda',
                'deskripsi' => 'Anggota yang masih punya sisa denda beserta nominal yang harus ditagih.',
                'catatan' => 'Kondisi saat ini',
            ],
        ];

        $laporanRekap = [
            [
                'rute' => 'reports.collection',
                'judul' => 'Rekapitulasi koleksi',
                'deskripsi' => 'Koleksi per kategori beserta berapa eksemplar yang dipinjam dan tersedia.',
                'catatan' => 'Kondisi saat ini',
            ],
            [
                'rute' => 'reports.members',
                'judul' => 'Rekapitulasi keanggotaan',
                'deskripsi' => 'Anggota per program studi, tunggakan denda, dan penerimaan pembayaran.',
                'catatan' => 'Kondisi saat ini',
            ],
            [
                'rute' => 'reports.reservations',
                'judul' => 'Status reservasi',
                'deskripsi' => 'Antrean yang menunggu, sudah siap diambil, dan yang kedaluwarsa.',
                'catatan' => 'Kondisi saat ini',
            ],
            [
                'rute' => 'reports.activity',
                'judul' => 'Jejak audit aktivitas',
                'deskripsi' => 'Semua tindakan yang mengubah data operasional, dari peminjaman sampai profil.',
                'catatan' => 'Dapat disaring',
            ],
        ];
    @endphp

    <div class="space-y-5">
        <x-panel
            judul="Laporan harian"
            subjudul="Dipakai petugas saat melayani anggota di loket. Semua angka dihitung langsung dari tabel."
            padat
        >
            <ul class="grid gap-px bg-garis sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($laporanHarian as $laporan)
                    <li class="flex flex-col justify-between gap-3 bg-permukaan p-4">
                        <div>
                            <h3 class="text-[0.9375rem] font-semibold text-tinta">{{ $laporan['judul'] }}</h3>
                            <p class="mt-1.5 text-[0.8125rem] leading-snug text-tinta-lembut">{{ $laporan['deskripsi'] }}</p>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-[0.6875rem] text-tinta-samar">
                                {{ $laporan['catatan'] }}
                            </span>
                            <x-button :href="route($laporan['rute'])" ukuran="kecil">Buka</x-button>
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-panel>

        <x-panel
            judul="Rekap dan jejak audit"
            subjudul="Ringkasan periodik dan catatan perubahan data untuk pemeriksaan."
            padat
        >
            <ul class="grid gap-px bg-garis sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($laporanRekap as $laporan)
                    <li class="flex flex-col justify-between gap-3 bg-permukaan p-4">
                        <div>
                            <h3 class="text-[0.875rem] font-semibold text-tinta">{{ $laporan['judul'] }}</h3>
                            <p class="mt-1.5 text-[0.8125rem] leading-snug text-tinta-lembut">{{ $laporan['deskripsi'] }}</p>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-[0.6875rem] text-tinta-samar">
                                {{ $laporan['catatan'] }}
                            </span>
                            <x-button :href="route($laporan['rute'])" varian="garis" ukuran="kecil">Buka</x-button>
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-panel>
    </div>
</x-layouts.app>
