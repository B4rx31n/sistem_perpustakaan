<x-layouts.app>
    @php
        /**
         * Warna tag mengikuti jenis tindakan, bukan asal beda warna. Tindakan yang
         * menambah data memakai warna inti, perubahan memakai aksen, dan penghapusan
         * memakai warna bahaya.
         */
        $gayaAksi = [
            'pinjam' => ['Peminjaman', 'inti'],
            'kembalikan' => ['Pengembalian', 'inti'],
            'perpanjang' => ['Perpanjangan', 'aksen'],
            'bayar-denda' => ['Pembayaran', 'inti'],
            'reservasi' => ['Reservasi', 'netral'],
            'reservasi-siap' => ['Reservasi siap', 'aksen'],
            'reservasi-selesai' => ['Reservasi selesai', 'inti'],
            'reservasi-batal' => ['Reservasi dibatalkan', 'bahaya'],
            'reservasi-kedaluwarsa' => ['Reservasi kedaluwarsa', 'bahaya'],
            'buku-tambah' => ['Buku ditambah', 'inti'],
            'buku-ubah' => ['Buku diubah', 'aksen'],
            'buku-hapus' => ['Buku dihapus', 'bahaya'],
            'kategori-tambah' => ['Kategori ditambah', 'inti'],
            'kategori-ubah' => ['Kategori diubah', 'aksen'],
            'kategori-hapus' => ['Kategori dihapus', 'bahaya'],
            'anggota-ubah' => ['Data anggota diubah', 'aksen'],
            'profil-ubah' => ['Profil diubah', 'netral'],
            'daftar' => ['Pendaftaran', 'inti'],
            'seed' => ['Data awal', 'netral'],
        ];
    @endphp

    <x-page-heading
        judul="Jejak audit aktivitas"
        deskripsi="Semua tindakan yang mengubah data operasional, dari peminjaman sampai perubahan profil."
    >
        <x-button :href="route('reports.index')" varian="garis" ukuran="kecil">Semua laporan</x-button>
    </x-page-heading>


    <form method="GET" action="{{ route('reports.activity') }}"
          class="mb-4 grid gap-2.5 border border-garis bg-permukaan p-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="lg:col-span-2">
            <label for="cari-aktivitas" class="sr-only">Cari aktor, subjek, atau keterangan</label>
            <input id="cari-aktivitas"
                   name="q"
                   type="search"
                   value="{{ request('q') }}"
                   placeholder="Cari aktor, subjek, atau keterangan"
                   data-cari-otomatis
                   class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 placeholder:text-tinta-samar focus:border-inti focus:shadow-fokus focus:outline-none">
        </div>

        <div>
            <label for="filter-aksi" class="sr-only">Jenis tindakan</label>
            <select id="filter-aksi"
                    name="aksi"
                    class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                <option value="">Semua jenis tindakan</option>
                @foreach ($daftarAksi as $aksi)
                    <option value="{{ $aksi }}" @selected(request('aksi') === $aksi)>
                        {{ $gayaAksi[$aksi][0] ?? $aksi }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <div class="min-w-0 flex-1">
                <label for="jumlah-baris-aktivitas" class="sr-only">Jumlah baris per halaman</label>
                <select id="jumlah-baris-aktivitas"
                        name="halaman"
                        class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                    @foreach ([30 => '30', 50 => '50', 100 => '100'] as $jumlah => $teks)
                        <option value="{{ $jumlah }}" @selected(request('halaman', 30) === $jumlah)>{{ $teks }}</option>
                    @endforeach
                </select>
            </div>
            <x-button type="submit" ukuran="kecil">Terapkan</x-button>
        </div>
    </form>

    @if ($logs->isEmpty())
        <x-empty-state
            judul="Tidak ada catatan yang cocok"
            pesan="Coba kata kunci lain atau pilih semua jenis tindakan."
            ikon="AKTIVITAS"
        >
            <x-slot:aksi>
                <x-button :href="route('reports.activity')" varian="garis" ukuran="kecil">Bersihkan filter</x-button>
            </x-slot:aksi>
        </x-empty-state>
    @else
        <x-panel
            judul="Catatan aktivitas"
            :subjudul="number_format($logs->total(), 0, ',', '.') . ' catatan, terbaru di atas.'"
            padat
        >
            <div class="overflow-x-auto">
                <table class="tabel-dasar">
                    <caption class="sr-only">Jejak audit aktivitas pengguna</caption>
                    <thead>
                        <tr>
                            <th scope="col">Waktu</th>
                            <th scope="col">Aktor</th>
                            <th scope="col">Tindakan</th>
                            <th scope="col">Subjek</th>
                            <th scope="col">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td class="whitespace-nowrap text-[0.8125rem] text-tinta-lembut">
                                    {{ $log->created_at->translatedFormat('d M Y') }}
                                    <span class="block font-mono text-[0.6875rem] text-tinta-samar">
                                        {{ $log->created_at->format('H:i') }}
                                    </span>
                                </td>
                                <td class="max-w-40 truncate text-[0.8125rem] font-medium text-tinta">
                                    {{ $log->nama_aktor }}
                                </td>
                                <td>
                                    <x-status-tag
                                        :label="$gayaAksi[$log->aksi][0] ?? $log->aksi"
                                        :warna="$gayaAksi[$log->aksi][1] ?? 'netral'"
                                    />
                                </td>
                                <td class="max-w-40 font-mono text-[0.75rem] text-tinta-lembut">
                                    {{ $log->subjek ?: '—' }}
                                </td>
                                <td class="max-w-96 text-[0.8125rem] text-tinta-lembut">
                                    {{ $log->keterangan ?: '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-3 pb-3">
                <x-pagination :paginator="$logs" />
            </div>
        </x-panel>
    @endif
</x-layouts.app>
