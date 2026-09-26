<x-layouts.app>
    @php
        /** @var \Illuminate\Support\Collection $kategori */
        $kategoriOpsi = $kategori->pluck('nama', 'id');
    @endphp

    <x-page-heading
        judul="Tambah buku"
        deskripsi="Isi data bibliografis dan jumlah eksemplar. Stok boleh nol untuk judul yang baru dicatat dan belum siap dipinjam."
    >
        <x-button :href="route('books.index')" varian="garis" ukuran="kecil">Batal</x-button>
    </x-page-heading>


    <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data" class="grid gap-5 lg:grid-cols-3">
        @csrf

        <div class="lg:col-span-2">
            <x-book-fields :buku="null" :kategori-opsi="$kategoriOpsi" />
        </div>

        <aside class="lg:col-span-1">
            <div class="sticky top-20 border border-garis bg-permukaan p-4">
                <h2 class="text-[0.875rem] font-semibold">Simpan buku</h2>
                <p class="mt-1 text-[0.8125rem] text-tinta-lembut">
                    Buku langsung masuk katalog setelah disimpan dan bisa dipinjam
                    bila stoknya lebih dari nol.
                </p>

                <x-button type="submit" class="mt-3 w-full">Simpan ke katalog</x-button>
                <x-button :href="route('books.index')" varian="garis" class="mt-2 w-full">Batal</x-button>
            </div>
        </aside>
    </form>
</x-layouts.app>
