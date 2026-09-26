<x-layouts.app>
    @php
        /** @var \App\Models\Book $bukuItem */
        $kategoriOpsi = $kategori->pluck('nama', 'id');
        $urutanOpsi = [
            '' => 'Terbaru ditambahkan',
            'judul' => 'Judul A-Z',
            'terlama' => 'Tahun terbit terbaru',
            'stok' => 'Stok paling banyak',
        ];
        $ketersediaanOpsi = [
            '' => 'Semua status stok',
            'tersedia' => 'Stok tersedia',
            'habis' => 'Stok habis',
        ];
    @endphp

    <x-page-heading
        judul="Katalog buku"
        :deskripsi="'Total ' . number_format($totalJudul, 0, ',', '.') . ' judul terdaftar di Perpustakaan Nusantara.'"
    >
        @can('create', \App\Models\Book::class)
            <x-button :href="route('books.create')" ukuran="kecil">Tambah buku</x-button>
        @endcan
    </x-page-heading>


    <form method="GET" action="{{ route('books.index') }}"
          class="mb-4 grid gap-2.5 border border-garis bg-permukaan p-3 sm:grid-cols-2 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <label for="cari-buku" class="sr-only">Cari judul, penulis, atau ISBN</label>
            <input id="cari-buku"
                   name="q"
                   type="search"
                   value="{{ request('q') }}"
                   placeholder="Cari judul, penulis, atau ISBN"
                   data-cari-otomatis
                   class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 placeholder:text-tinta-samar focus:border-inti focus:shadow-fokus focus:outline-none">
        </div>

        <div>
            <label for="filter-kategori" class="sr-only">Kategori</label>
            <select id="filter-kategori"
                    name="kategori"
                    class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                <option value="">Semua kategori</option>
                @foreach ($kategoriOpsi as $idOpsi => $namaKategori)
                    <option value="{{ $idOpsi }}" @selected((int) request('kategori') === $idOpsi)>
                        {{ $namaKategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="filter-ketersediaan" class="sr-only">Ketersediaan</label>
            <select id="filter-ketersediaan"
                    name="ketersediaan"
                    class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                @foreach ($ketersediaanOpsi as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(request('ketersediaan') === $nilai)>{{ $teks }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <label for="filter-urut" class="sr-only">Urutkan</label>
            <select id="filter-urut"
                    name="urut"
                    class="min-w-0 flex-1 rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                @foreach ($urutanOpsi as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(request('urut') === $nilai)>{{ $teks }}</option>
                @endforeach
            </select>
            <x-button type="submit" ukuran="kecil">Terapkan</x-button>
        </div>
    </form>

    @if ($buku->isEmpty())
        <x-empty-state
            judul="Tidak ada buku yang cocok"
            pesan="Coba kata kunci lain, atau longgarkan filter kategori dan ketersediaan."
            ikon="TIDAK DITEMUKAN"
        >
            <x-button :href="route('books.index')" varian="garis" ukuran="kecil">Bersihkan filter</x-button>
            @can('create', \App\Models\Book::class)
                <x-button :href="route('books.create')" ukuran="kecil">Tambah buku baru</x-button>
            @endcan
        </x-empty-state>
    @else
        <x-panel judul="Daftar katalog" subjudul="Seluruh judul yang sudah masuk katalog, urut dari yang terbaru." padat>
            <div class="overflow-x-auto">
                <table class="tabel-dasar">
                    <caption class="sr-only">Daftar buku dalam katalog</caption>
                    <thead>
                        <tr>
                            <th scope="col">ISBN</th>
                            <th scope="col">Judul</th>
                            <th scope="col">Kategori</th>
                            <th scope="col" class="text-right">Stok</th>
                            <th scope="col" class="text-right">Dipinjam</th>
                            <th scope="col" class="text-right">Tersedia</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($buku as $item)
                            @php
                                $tersedia = $item->jumlahTersedia();
                            @endphp
                            <tr>
                                <td class="font-mono text-[0.75rem] whitespace-nowrap text-tinta-lembut">
                                    {{ $item->isbn }}
                                </td>
                                <td class="max-w-72">
                                    <a href="{{ route('books.show', $item) }}"
                                       class="font-medium text-tinta hover:underline">
                                        {{ $item->judul }}
                                    </a>
                                    <span class="block text-[0.75rem] text-tinta-samar">
                                        {{ $item->penulis }}@if ($item->tahun_terbit), {{ $item->tahun_terbit }}@endif
                                    </span>
                                </td>
                                <td class="whitespace-nowrap text-[0.8125rem]">
                                    @if ($item->category)
                                        <span class="rounded-md bg-permukaan-lembut px-2 py-0.5 text-[0.75rem]">
                                            {{ $item->category->nama }}
                                        </span>
                                    @else
                                        <span class="text-tinta-samar">&mdash;</span>
                                    @endif
                                </td>
                                <td class="angka font-mono">{{ $item->stok }}</td>
                                <td class="angka font-mono text-tinta-lembut">{{ $item->jumlahDipinjam() }}</td>
                                <td class="angka font-mono font-semibold">{{ $tersedia }}</td>
                                <td>
                                    <x-status-tag
                                        :label="$tersedia > 0 ? 'Tersedia' : 'Habis'"
                                        :warna="$tersedia > 0 ? 'inti' : 'bahaya'"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-3 pb-3">
                <x-pagination :paginator="$buku" />
            </div>
        </x-panel>
    @endif
</x-layouts.app>
