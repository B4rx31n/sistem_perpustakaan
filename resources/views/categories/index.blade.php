<x-layouts.app>
    <x-page-heading
        judul="Kategori buku"
        deskripsi="Kategori mengelompokkan katalog dan dipakai sebagai filter di halaman peminjaman dan laporan koleksi."
    />


    <div class="grid gap-5 lg:grid-cols-3">
        <x-panel judul="Tambah kategori" subjudul="Kategori dipakai sebagai filter katalog dan laporan.">
            <form method="POST" action="{{ route('categories.store') }}" class="space-y-3.5">
                @csrf

                <x-form-input nama="nama" label="Nama kategori" wajib placeholder="Contoh: Filsafat" />
                <x-form-input
                    nama="kode"
                    label="Kode kategori"
                    wajib
                    placeholder="Contoh: FIL"
                    bantuan="Dipakai untuk pengurutan dan cetak katalog."
                />
                <x-form-textarea nama="keterangan" label="Keterangan" :baris="2" />

                <x-button type="submit" class="w-full">Simpan kategori</x-button>
            </form>
        </x-panel>

        <x-panel class="lg:col-span-2" judul="Daftar kategori" subjudul="Ubah kode atau nama langsung di tabel." padat>
            @if ($kategori->isEmpty())
                <x-empty-state
                    judul="Belum ada kategori"
                    pesan="Tambahkan kategori di samping supaya buku lebih mudah dicari."
                    ikon="KATEGORI"
                />
            @else
                <div class="overflow-x-auto">
                    <table class="tabel-dasar">
                        <caption class="sr-only">Daftar kategori dalam katalog</caption>
                        <thead>
                            <tr>
                                <th scope="col">Kode</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col" class="text-right">Judul</th>
                                <th scope="col" class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategori as $item)
                                <tr>
                                    <td class="font-mono text-[0.75rem] whitespace-nowrap">
                                        <label for="kategori-kode-{{ $item->id }}" class="sr-only">Kode {{ $item->nama }}</label>
                                        <input id="kategori-kode-{{ $item->id }}"
                                               form="form-kategori-{{ $item->id }}"
                                               name="kode"
                                               value="{{ old('kode', $item->kode) }}"
                                               class="w-20 rounded-md border border-garis bg-permukaan px-2 py-1.5 font-mono text-[0.75rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                                    </td>

                                    <td>
                                        <label for="kategori-nama-{{ $item->id }}" class="sr-only">Nama kategori</label>
                                        <input id="kategori-nama-{{ $item->id }}"
                                               form="form-kategori-{{ $item->id }}"
                                               name="nama"
                                               value="{{ old('nama', $item->nama) }}"
                                               class="w-full rounded-md border border-garis bg-permukaan px-2 py-1.5 text-[0.8125rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                                    </td>

                                    <td class="max-w-56">
                                        <label for="kategori-keterangan-{{ $item->id }}" class="sr-only">Keterangan {{ $item->nama }}</label>
                                        <input id="kategori-keterangan-{{ $item->id }}"
                                               form="form-kategori-{{ $item->id }}"
                                               name="keterangan"
                                               value="{{ old('keterangan', $item->keterangan) }}"
                                               class="w-full rounded-md border border-garis bg-permukaan px-2 py-1.5 text-[0.8125rem] text-tinta-lembut transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                                    </td>

                                    <td class="angka font-mono">
                                        <a href="{{ route('books.index', ['kategori' => $item->id]) }}"
                                           class="text-inti hover:underline">
                                            {{ number_format($item->books_count, 0, ',', '.') }}
                                        </a>
                                    </td>

                                    <td>
                                        {{-- Formulirnya diletakkan di dalam sel, bukan di antara
                                             <tr> dan <td>, karena form bukan anak yang sah
                                             dari <tr>. Input memakai atribut form= supaya
                                             tetap terhubung ke form tersebut. --}}
                                        <div class="flex items-center justify-end gap-1.5">
                                            <form method="POST" action="{{ route('categories.update', $item) }}"
                                                  id="form-kategori-{{ $item->id }}" class="contents">
                                                @csrf
                                                @method('PUT')
                                                <x-button type="submit" varian="garis" ukuran="kecil">Simpan</x-button>
                                            </form>

                                            <x-button
                                                :href="route('categories.destroy', $item)"
                                                method="DELETE"
                                                varian="bahaya"
                                                ukuran="kecil"
                                                konfirmasi="Hapus kategori &quot;{{ $item->nama }}&quot;? Kategori yang masih dipakai buku tidak bisa dihapus."
                                            >Hapus</x-button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-panel>
    </div>
</x-layouts.app>
