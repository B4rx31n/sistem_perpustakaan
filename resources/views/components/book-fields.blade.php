@php
    /**
     * Isi form buku, dipakai bersama oleh halaman tambah dan ubah. Nilai lama
     * diambil dari $buku saat mode ubah, dan dari input sebelumnya saat validasi
     * gagal, jadi petugas tidak perlu mengetik ulang setelah kesalahan.
     *
     * @var \App\Models\Book|null $buku
     * @var \Illuminate\Support\Collection $kategoriOpsi
     */
    $nilai = fn (string $kolom, mixed $bawaan = null) => old($kolom, $buku?->{$kolom} ?? $bawaan);
@endphp

<div class="space-y-5">
    <section aria-labelledby="identitas-buku" class="kartu overflow-hidden">
        <h2 id="identitas-buku" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
            Identitas buku
        </h2>

        <div class="grid gap-4 px-4 py-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-form-input
                    nama="judul"
                    label="Judul"
                    wajib
                    :nilai="$nilai('judul')"
                    placeholder="Contoh: Fahrenheit 451"
                />
            </div>

            <x-form-input
                nama="penulis"
                label="Penulis"
                wajib
                :nilai="$nilai('penulis')"
            />

            <x-form-input
                nama="isbn"
                label="ISBN"
                wajib
                :nilai="$nilai('isbn')"
                bantuan="Hanya angka, tanda hubung, dan X. Contoh: 978-602-0000-00-0"
            />

            <x-form-input
                nama="penerbit"
                label="Penerbit"
                :nilai="$nilai('penerbit')"
            />

            <x-form-input
                nama="tahun_terbit"
                label="Tahun terbit"
                tipe="number"
                :nilai="$nilai('tahun_terbit')"
            />

            <x-form-select
                nama="category_id"
                label="Kategori"
                :nilai="$nilai('category_id')"
                :pilihan="$kategoriOpsi"
                kosong="Tanpa kategori"
            />

            <x-form-input
                nama="stok"
                label="Jumlah stok"
                tipe="number"
                wajib
                :nilai="$nilai('stok', 1)"
                bantuan="Jumlah eksemplar fisik yang tersedia untuk dipinjam."
            />
        </div>
    </section>

    <section aria-labelledby="kelengkapan-buku" class="kartu overflow-hidden">
        <h2 id="kelengkapan-buku" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
            Kelengkapan
        </h2>

        <div class="space-y-4 px-4 py-4">
            <x-form-textarea
                nama="deskripsi"
                label="Deskripsi"
                :nilai="$nilai('deskripsi')"
                placeholder="Ringkasan singkat isi dan alumnusinya."
                :baris="4"
            />

            <div>
                <label for="cover" class="block text-[0.8125rem] font-medium text-tinta">Sampul</label>
                <input id="cover"
                       name="cover"
                       type="file"
                       accept="image/jpeg,image/png,image/webp"
                       @error('cover') aria-invalid="true" aria-describedby="cover-galat" @enderror
                       class="mt-1 block w-full text-[0.8125rem] text-tinta-lembut file:mr-3 file:rounded-lg file:border file:border-garis file:shadow-halus file:bg-permukaan-lembut file:px-2.5 file:py-1.5 file:text-[0.75rem] file:font-medium file:text-tinta hover:file:bg-permukaan-lembut">
                <p class="mt-1 text-[0.75rem] text-tinta-samar">
                    JPG, PNG, atau WebP, maksimal 2 MB. Kosongkan bila sampul belum tersedia.
                </p>
                @error('cover')
                    <p id="cover-galat" class="mt-1 text-[0.75rem] text-bahaya">{{ $message }}</p>
                @enderror

                @if ($buku?->cover_path)
                    <div class="mt-2 flex items-center gap-3">
                        <img src="{{ $buku->coverUrl() }}"
                             alt="Sampul {{ $buku->judul }} saat ini"
                             class="h-20 w-14 border border-garis object-cover">
                        <p class="text-[0.75rem] text-tinta-lembut">
                            Sampul saat ini. Unggah file baru untuk menggantinya.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
