<x-layouts.app>
    <x-page-heading
        judul="Rekapitulasi peminjaman"
        :deskripsi="'Periode ' . $dari->translatedFormat('d M Y') . ' sampai ' . $sampai->translatedFormat('d M Y') . '.'"
    >
        <x-button :href="route('reports.index')" varian="garis" ukuran="kecil">Semua laporan</x-button>
    </x-page-heading>


    <section aria-labelledby="ringkasan-peminjaman" class="mb-5">
        <h2 id="ringkasan-peminjaman" class="sr-only">Ringkasan periode ini</h2>

        <div class="grid gap-3 lg:grid-cols-3">
            <x-metrik
                label="Pinjaman keluar"
                :nilai="number_format($jumlahPeminjaman, 0, ',', '.')"
                :konteks="$jumlahPeminjaman > 0
                    ? 'Rata-rata ' . number_format($jumlahPeminjaman / max(1, $dari->diffInDays($sampai) + 1), 1, ',', '.') . ' per hari selama periode ini.'
                    : 'Belum ada peminjaman pada rentang tanggal yang dipilih.'"
            />

            <x-statistik
                label="Sudah kembali"
                :nilai="number_format($jumlahDikembalikan, 0, ',', '.')"
            />
            <x-statistik
                label="Kembali terlambat"
                :nilai="number_format($jumlahTerlambat, 0, ',', '.')"
                :keterangan="$jumlahDikembalikan > 0
                    ? number_format($jumlahTerlambat / $jumlahDikembalikan * 100, 1, ',', '.') . '% dari pengembalian'
                    : null"
            />
        </div>
    </section>

    <form method="GET" action="{{ route('reports.loans') }}" class="mb-4 flex flex-wrap items-end gap-2 border border-garis bg-permukaan p-3">
        <div>
            <label for="filter-dari" class="block text-[0.8125rem] font-medium text-tinta">Dari tanggal</label>
            <input id="filter-dari"
                   name="dari"
                   type="date"
                   value="{{ $dari->format('Y-m-d') }}"
                   class="mt-1 block rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
        </div>

        <div>
            <label for="filter-sampai" class="block text-[0.8125rem] font-medium text-tinta">Sampai tanggal</label>
            <input id="filter-sampai"
                   name="sampai"
                   type="date"
                   value="{{ $sampai->format('Y-m-d') }}"
                   class="mt-1 block rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
        </div>

        <x-button type="submit">Terapkan</x-button>
        <x-button :href="route('reports.loans')" varian="garis">Bulan ini</x-button>
    </form>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @if ($pinjaman->isEmpty())
                <x-empty-state
                    judul="Tidak ada peminjaman pada periode ini"
                    pesan="Ubah rentang tanggal di atas, atau kembali ke rekapitulasi bulan berjalan."
                    ikon="PEMINJAMAN"
                />
            @else
                <x-panel judul="Rincian peminjaman" :subjudul="$dari->translatedFormat('d M Y') . ' sampai ' . $sampai->translatedFormat('d M Y') . '.'" padat>
                    <div class="overflow-x-auto">
                        <table class="tabel-dasar">
                            <caption class="sr-only">Daftar peminjaman pada periode terpilih</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Kode</th>
                                    <th scope="col">Judul</th>
                                    <th scope="col">Anggota</th>
                                    <th scope="col">Dipinjam</th>
                                    <th scope="col">Jatuh tempo</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pinjaman as $pinjaman)
                                    <tr>
                                        <td class="font-mono text-[0.75rem] whitespace-nowrap text-tinta-lembut">
                                            <a href="{{ route('loans.show', $pinjaman) }}" class="hover:underline">
                                                {{ $pinjaman->kode }}
                                            </a>
                                        </td>
                                        <td class="max-w-56 truncate text-[0.8125rem]">
                                            {{ $pinjaman->book?->judul ?? 'Buku dihapus' }}
                                        </td>
                                        <td class="max-w-40 truncate text-[0.8125rem] text-tinta-lembut">
                                            {{ $pinjaman->user?->name ?? 'Anggota dihapus' }}
                                        </td>
                                        <td class="whitespace-nowrap text-[0.8125rem] text-tinta-lembut">
                                            {{ $pinjaman->dipinjam_at?->translatedFormat('d M Y') ?? '—' }}
                                        </td>
                                        <td class="whitespace-nowrap text-[0.8125rem] text-tinta-lembut">
                                            {{ $pinjaman->harus_kembali_at?->translatedFormat('d M Y') ?? '—' }}
                                        </td>
                                        <td>
                                            <x-loan-status :loan="$pinjaman" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-panel>
            @endif
        </div>

        <aside class="lg:col-span-1">
            <x-panel judul="Peminjaman per kategori" subjudul="Porsi terhadap total periode ini.">
                @if ($perKategori->isEmpty())
                    <x-empty-state
                        judul="Belum ada data"
                        pesan="Tidak ada peminjaman pada periode ini, jadi sebaran kategori masih kosong."
                        ikon="KATEGORI"
                    />
                @else
                    <ul class="space-y-3">
                        @foreach ($perKategori as $nama => $jumlah)
                            @php
                                $porsi = $jumlahPeminjaman > 0 ? $jumlah / $jumlahPeminjaman * 100 : 0;
                            @endphp
                            <li>
                                <x-bilah
                                    :persen="$porsi"
                                    :label="$nama"
                                    :nilai="number_format($jumlah, 0, ',', '.')"
                                />
                                <p class="mt-1 font-mono text-[0.6875rem] text-tinta-samar">
                                    {{ number_format($porsi, 1, ',', '.') }}% dari periode ini
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-panel>
        </aside>
    </div>
</x-layouts.app>
