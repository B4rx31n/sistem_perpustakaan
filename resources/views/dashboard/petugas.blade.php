<x-layouts.app>
    @php
        $puncakGrafik = max(1, $grafik->max('total'));
        $maksEksemplar = max(1, $statistik['eksemplar']);
        $porsiDipinjam = round($statistik['sedang_dipinjam'] / $maksEksemplar * 100, 1);
    @endphp

    <x-page-heading
        judul="Dashboard petugas"
        :deskripsi="'Ringkasan layanan ' . now()->translatedFormat('l, d F Y') . '. Semua angka dihitung langsung dari data peminjaman.'"
    >
        <x-button :href="route('loans.create')" varian="garis" ukuran="kecil">Pencatatan peminjaman</x-button>
        <x-button :href="route('reports.loans')" ukuran="kecil">Rekap bulan ini</x-button>
    </x-page-heading>


    @if ($statistik['terlambat'] > 0)
        <x-peringatan
            class="mb-5"
            judul="{{ $statistik['terlambat'] }} peminjaman lewat jatuh tempo"
            :pesan="'Denda yang belum dibayar mencapai Rp' . number_format($totalDendaBelumLunas, 0, ',', '.') . '. Menagih di loket lebih cepat daripada menunggu anggota datang.'"
        >
            <x-slot:aksi>
                <x-button :href="route('reports.overdue')" ukuran="kecil">Buka daftar</x-button>
            </x-slot:aksi>
        </x-peringatan>
    @endif

    <section aria-labelledby="ringkasan-dashboard" class="mb-5">
        <h2 id="ringkasan-dashboard" class="sr-only">Ringkasan operasional hari ini</h2>

        <div class="grid gap-3 md:grid-cols-3">
            <x-metrik
                label="Perlu ditindaklanjuti"
                :nilai="number_format($statistik['terlambat'], 0, ',', '.')"
                :warna="$statistik['terlambat'] > 0 ? 'bahaya' : 'netral'"
                :konteks="$statistik['terlambat'] > 0
                    ? 'Peminjaman lewat jatuh tempo, urut dari yang paling lama.'
                    : 'Semua peminjaman masih dalam batas waktu pengembalian.'"
                arah="Lihat daftar"
                :tautan="route('reports.overdue')"
            />

            <x-metrik
                label="Piutang denda"
                :nilai="'Rp' . number_format($totalDendaBelumLunas, 0, ',', '.')"
                :warna="$totalDendaBelumLunas > 0 ? 'aksen' : 'netral'"
                :konteks="$totalDendaBelumLunas > 0
                    ? number_format($statistik['terlambat'], 0, ',', '.') . ' peminjaman masih punya sisa denda.'
                    : 'Semua denda yang pernah timbul sudah dilunasi.'"
                arah="Terima pembayaran"
                :tautan="route('payments.receivables')"
            />

            <x-metrik
                label="Buku sedang keluar"
                :nilai="number_format($statistik['sedang_dipinjam'], 0, ',', '.')"
                :konteks="'Dari ' . number_format($statistik['eksemplar'], 0, ',', '.') . ' eksemplar, ' . number_format($porsiDipinjam, 1, ',', '.') . '% sedang dipinjam.'"
                arah="Buka peminjaman"
                :tautan="route('loans.index')"
            />
        </div>
    </section>

    <div class="grid gap-5 xl:grid-cols-3">
        <x-panel
            class="xl:col-span-2"
            judul="Peminjaman lewat jatuh tempo"
            subjudul="Sepuluh peminjaman terlama yang harus ditangani di loket."
            padat
        >
            <x-slot:aksi>
                <a href="{{ route('reports.overdue') }}" class="font-medium text-inti hover:underline">Laporan lengkap</a>
            </x-slot:aksi>

            @if ($pinjamanTerlambat->isEmpty())
                <x-empty-state
                    judul="Tidak ada peminjaman terlambat"
                    pesan="Semua peminjaman masih dalam batas waktu pengembalian."
                    ikon="TEPAT WAKTU"
                />
            @else
                <div class="overflow-x-auto">
                    <table class="tabel-dasar">
                        <caption class="sr-only">Daftar peminjaman yang lewat jatuh tempo</caption>
                        <thead>
                            <tr>
                                <th scope="col">Kode</th>
                                <th scope="col">Buku</th>
                                <th scope="col">Anggota</th>
                                <th scope="col">Jatuh tempo</th>
                                <th scope="col" class="text-right">Telat</th>
                                <th scope="col" class="w-px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pinjamanTerlambat as $pinjaman)
                                <tr>
                                    <td class="font-mono text-[0.75rem] whitespace-nowrap">
                                        <a href="{{ route('loans.show', $pinjaman) }}" class="text-inti hover:underline">
                                            {{ $pinjaman->kode }}
                                        </a>
                                    </td>
                                    <td class="max-w-56 truncate">{{ $pinjaman->book->judul }}</td>
                                    <td class="max-w-40 truncate">{{ $pinjaman->user->name }}</td>
                                    <td class="whitespace-nowrap text-[0.8125rem]">
                                        {{ $pinjaman->harus_kembali_at->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="angka whitespace-nowrap text-right font-semibold text-bahaya">
                                        {{ $pinjaman->hariTerlambat() }} hr
                                    </td>
                                    <td class="text-right">
                                        <x-button :href="route('loans.show', $pinjaman)" varian="garis" ukuran="kecil">
                                            Proses
                                        </x-button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-panel>

        <div class="space-y-5">
            <x-panel judul="Kondisi koleksi" subjudul="Porsi eksemplar yang sedang keluar.">
                <x-bilah
                    :persen="$porsiDipinjam"
                    :label="number_format($statistik['sedang_dipinjam'], 0, ',', '.') . ' dari ' . number_format($statistik['eksemplar'], 0, ',', '.') . ' eksemplar'"
                    :nilai="number_format($porsiDipinjam, 1, ',', '.') . '%'"
                    :warna="$porsiDipinjam > 80 ? 'bahaya' : ($porsiDipinjam > 50 ? 'aksen' : 'inti')"
                />

                <dl class="mt-3.5 divide-y divide-garis border-t border-garis text-[0.8125rem]">
                    <div class="flex items-center justify-between gap-2 py-2 first:pt-0">
                        <dt class="text-tinta-lembut">Judul terdaftar</dt>
                        <dd class="angka font-mono font-semibold">{{ number_format($statistik['judul_koleksi'], 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2 py-2">
                        <dt class="text-tinta-lembut">Anggota aktif</dt>
                        <dd class="angka font-mono font-semibold">{{ number_format($statistik['anggota_aktif'], 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2 py-2 last:pb-0">
                        <dt class="text-tinta-lembut">Reservasi berjalan</dt>
                        <dd class="angka font-mono font-semibold">{{ number_format($statistik['reservasi_aktif'], 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </x-panel>

            <x-panel judul="Stok menipis" subjudul="Eksemplar yang tinggal satu atau sudah habis." padat>
                @if ($stokMenipis->isEmpty())
                    <p class="px-4 py-3 text-[0.8125rem] text-tinta-lembut">
                        Semua judul masih punya minimal dua eksemplar tersedia.
                    </p>
                @else
                    <ul class="divide-y divide-garis">
                        @foreach ($stokMenipis as $buku)
                            <li class="flex items-center justify-between gap-3 px-4 py-2.5">
                                <a href="{{ route('books.show', $buku) }}"
                                   class="min-w-0 truncate text-[0.8125rem] text-tinta hover:underline">
                                    {{ $buku->judul }}
                                </a>
                                <x-status-tag
                                    :label="$buku->jumlahTersedia() . ' tersedia'"
                                    :warna="$buku->jumlahTersedia() === 0 ? 'bahaya' : 'aksen'"
                                />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-panel>
        </div>
    </div>

    <div class="mt-5 grid gap-5 xl:grid-cols-3">
        <x-panel
            class="xl:col-span-2"
            judul="Peminjaman 6 bulan terakhir"
            subjudul="Jumlah transaksi peminjaman per bulan, dihitung dari tanggal peminjaman."
        >
            <x-slot:aksi>
                <span class="text-tinta-samar">Puncak {{ number_format($puncakGrafik, 0, ',', '.') }}</span>
            </x-slot:aksi>

            {{-- Grafik batang dibuat dari div, bukan canvas atau pustaka chart: datanya
                 cuma enam batang, dan cara ini tetap bisa dibaca screen reader. --}}
            <ul class="flex items-end gap-2 sm:gap-3">
                @foreach ($grafik as $baris)
                    @php $tinggi = max(2, round($baris['total'] / $puncakGrafik * 100)); @endphp
                    <li class="flex min-w-0 flex-1 flex-col items-center gap-2">
                        <span class="angka font-mono text-[0.6875rem] font-semibold text-tinta-lembut">
                            {{ number_format($baris['total'], 0, ',', '.') }}
                        </span>
                        <div class="flex h-28 w-full items-end border-b border-garis">
                            <div class="w-full bg-grad-inti transition-[filter] duration-[120ms] hover:brightness-95"
                                 style="height: {{ $tinggi }}%"
                                 role="img"
                                 aria-label="{{ $baris['total'] }} peminjaman pada {{ $baris['label'] }} {{ $baris['tahun'] }}"></div>
                        </div>
                        <span class="text-[0.75rem] text-tinta-lembut">{{ $baris['label'] }}</span>
                    </li>
                @endforeach
            </ul>
        </x-panel>

        <x-panel judul="Buku paling banyak dipinjam" subjudul="Delapan judul teratas." padat>
            @if ($bukuTerlaris->isEmpty())
                <x-empty-state
                    judul="Belum ada peminjaman"
                    pesan="Peringkat muncul setelah ada transaksi peminjaman tercatat."
                    ikon="PERINGKAT"
                />
            @else
                <ol class="divide-y divide-garis">
                    @foreach ($bukuTerlaris as $buku)
                        <li class="flex items-center gap-3 px-4 py-2.5">
                            <span aria-hidden="true" class="angka w-5 shrink-0 font-mono text-[0.75rem] text-tinta-samar">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <a href="{{ route('books.show', $buku) }}" class="min-w-0 flex-1 truncate text-[0.8125rem] hover:underline">
                                {{ $buku->judul }}
                            </a>
                            <span class="angka shrink-0 font-mono text-[0.75rem] text-tinta-lembut">
                                {{ number_format($buku->loans_count, 0, ',', '.') }}
                            </span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </x-panel>
    </div>

    <x-panel class="mt-5" judul="Aktivitas terbaru" subjudul="Dua belas tindakan terakhir di sistem." padat>
        <x-slot:aksi>
            <a href="{{ route('reports.activity') }}" class="font-medium text-inti hover:underline">Jejak audit lengkap</a>
        </x-slot:aksi>

        @if ($aktivitas->isEmpty())
            <x-empty-state
                judul="Belum ada aktivitas"
                pesan="Setiap tindakan petugas dan anggota akan tercatat di sini."
                ikon="LOG"
            />
        @else
            <ul class="grid gap-x-6 divide-y divide-garis xl:grid-cols-2 xl:divide-y-0">
                @foreach ($aktivitas->chunk(6) as $kelompok)
                    @foreach ($kelompok as $log)
                        <li class="flex flex-wrap items-baseline gap-x-3 gap-y-1 border-b border-garis px-4 py-2.5 last:border-b-0 xl:border-b">
                            <span class="angka shrink-0 font-mono text-[0.6875rem] text-tinta-samar">
                                {{ $log->created_at->format('d/m H:i') }}
                            </span>
                            <span class="min-w-0 flex-1 text-[0.8125rem]">
                                <span class="font-medium">{{ $log->nama_aktor }}</span>
                                <span class="text-tinta-lembut"> {{ $log->keterangan }}</span>
                            </span>
                            <span class="shrink-0 rounded-md bg-permukaan-lembut px-2 py-0.5 font-mono text-[0.6875rem] text-tinta-lembut">
                                {{ $log->aksi }}
                            </span>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        @endif
    </x-panel>
</x-layouts.app>
