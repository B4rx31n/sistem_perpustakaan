<x-layouts.app>
    <x-page-heading
        judul="Rekapitulasi koleksi"
        :deskripsi="number_format($totalJudul, 0, ',', '.') . ' judul, ' . number_format($totalEksemplar, 0, ',', '.') . ' eksemplar, ' . number_format($totalDipinjam, 0, ',', '.') . ' sedang dipinjam.'"
    >
        <x-button :href="route('reports.index')" varian="garis" ukuran="kecil">Semua laporan</x-button>
        <x-button :href="route('books.index')" varian="garis" ukuran="kecil">Buka katalog</x-button>
    </x-page-heading>


    <section aria-labelledby="ringkasan-koleksi" class="mb-5">
        <h2 id="ringkasan-koleksi" class="sr-only">Ringkasan koleksi</h2>

        @php $porsiDipinjam = $totalEksemplar > 0 ? $totalDipinjam / $totalEksemplar * 100 : 0; @endphp

        <div class="grid gap-3 lg:grid-cols-3">
            <x-metrik
                label="Eksemplar di luar"
                :nilai="number_format(max(0, $totalEksemplar - $totalDipinjam), 0, ',', '.')"
                :warna="$porsiDipinjam > 80 ? 'bahaya' : ($porsiDipinjam > 50 ? 'aksen' : 'inti')"
                :konteks="number_format($porsiDipinjam, 1, ',', '.') . '% dari ' . number_format($totalEksemplar, 0, ',', '.') . ' eksemplar sedang dipinjam.'"
                arah="Buka katalog"
                :tautan="route('books.index')"
            />

            <x-statistik label="Judul" :nilai="number_format($totalJudul, 0, ',', '.')" />
            <x-statistik
                label="Sedang dipinjam"
                :nilai="number_format($totalDipinjam, 0, ',', '.')"
                :keterangan="$totalEksemplar > 0
                    ? number_format($porsiDipinjam, 1, ',', '.') . '% dari stok'
                    : null"
            />
        </div>
    </section>

    @if ($koleksi->isEmpty())
        <x-empty-state
            judul="Koleksi masih kosong"
            pesan="Tambahkan buku dan kategori supaya rekapitulasi ini punya isi."
            ikon="KOLEKSI"
        >
            <x-slot:aksi>
                <x-button :href="route('books.create')" ukuran="kecil">Tambah buku</x-button>
            </x-slot:aksi>
        </x-empty-state>
    @else
        <x-panel judul="Koleksi per kategori" subjudul="Porsi peminjaman dihitung dari eksemplar yang sedang keluar." padat>
            <div class="overflow-x-auto">
                <table class="tabel-dasar">
                    <caption class="sr-only">Rekapitulasi koleksi per kategori</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kategori</th>
                            <th scope="col" class="text-right">Judul</th>
                            <th scope="col" class="text-right">Eksemplar</th>
                            <th scope="col" class="text-right">Dipinjam</th>
                            <th scope="col" class="text-right">Tersedia</th>
                            <th scope="col" class="w-48 text-right">Porsi peminjaman</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($koleksi as $baris)
                            @php
                                $tersedia = max(0, $baris['eksemplar'] - $baris['dipinjam']);
                                $porsi = $baris['eksemplar'] > 0 ? $baris['dipinjam'] / $baris['eksemplar'] * 100 : 0;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('categories.index') }}"
                                       class="block max-w-64 truncate text-[0.8125rem] font-medium text-tinta hover:underline">
                                        {{ $baris['kategori']->nama }}
                                    </a>
                                    <span class="block font-mono text-[0.6875rem] text-tinta-samar">
                                        {{ $baris['kategori']->kode }}
                                    </span>
                                </td>
                                <td class="angka font-mono">{{ $baris['judul'] }}</td>
                                <td class="angka font-mono">{{ $baris['eksemplar'] }}</td>
                                <td class="angka font-mono {{ $baris['dipinjam'] > 0 ? 'text-aksen' : 'text-tinta-samar' }}">
                                    {{ $baris['dipinjam'] }}
                                </td>
                                <td class="angka font-mono">{{ $tersedia }}</td>
                                <td class="w-48">
                                    <x-bilah
                                        :persen="$porsi"
                                        warna="aksen"
                                        :nilai="number_format($porsi, 0, ',', '.') . '%'"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-garis-kuat bg-permukaan-lembut">
                            <th scope="row" class="px-3.5 py-3 text-left text-[0.8125rem] font-semibold text-tinta">
                                Total
                            </th>
                            <td class="angka font-mono font-semibold">{{ number_format($totalJudul, 0, ',', '.') }}</td>
                            <td class="angka font-mono font-semibold">{{ number_format($totalEksemplar, 0, ',', '.') }}</td>
                            <td class="angka font-mono font-semibold">{{ number_format($totalDipinjam, 0, ',', '.') }}</td>
                            <td class="angka font-mono font-semibold">
                                {{ number_format(max(0, $totalEksemplar - $totalDipinjam), 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-panel>
    @endif
</x-layouts.app>
