<x-layouts.app>
    @php
        /** @var \App\Models\Book $book */
        /** @var \Illuminate\Support\Collection $kategori */
        $kategoriOpsi = $kategori->pluck('nama', 'id');
    @endphp

    <x-page-heading
        judul="Ubah data buku"
        :deskripsi="'Kode katalog ' . $book->isbn . '. Perubahan tidak menghapus riwayat peminjaman yang sudah tercatat.'"
    >
        <x-button :href="route('books.show', $book)" varian="garis" ukuran="kecil">Lihat detail</x-button>
    </x-page-heading>


    <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data" class="grid gap-5 lg:grid-cols-3">
        @csrf
        @method('PUT')

        <div class="lg:col-span-2">
            <x-book-fields :buku="$book" :kategori-opsi="$kategoriOpsi" />
        </div>

        <aside class="space-y-5 lg:col-span-1">
            <div class="sticky top-20 border border-garis bg-permukaan p-4">
                <h2 class="text-[0.875rem] font-semibold">Simpan perubahan</h2>

                <dl class="mt-2 space-y-1 text-[0.75rem] text-tinta-lembut">
                    <div class="flex justify-between gap-2">
                        <dt>Sedang dipinjam</dt>
                        <dd class="font-mono text-tinta">{{ $book->jumlahDipinjam() }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt>Tersedia</dt>
                        <dd class="font-mono text-tinta">{{ $book->jumlahTersedia() }}</dd>
                    </div>
                </dl>

                <p class="mt-2 text-[0.75rem] text-tinta-samar">
                    Eksemplar yang sedang dipinjam tidak dihitung sebagai stok tersedia.
                </p>

                <x-button type="submit" class="mt-3 w-full">Simpan perubahan</x-button>
                <x-button :href="route('books.show', $book)" varian="garis" class="mt-2 w-full">Batal</x-button>
            </div>
        </aside>
    </form>
</x-layouts.app>
