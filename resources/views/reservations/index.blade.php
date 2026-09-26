<x-layouts.app>
    <x-page-heading
        judul="Reservasi"
        deskripsi="Antrean pemesanan buku yang sedang dipinjam. Reservasi siap diambil berlaku {{ config('perpustakaan.masa_berlaku_reservasi_hari') }} hari."
    />


    <section aria-labelledby="ringkasan-reservasi" class="mb-4">
        <h2 id="ringkasan-reservasi" class="sr-only">Ringkasan reservasi</h2>
        <div class="grid grid-cols-3 gap-3">
            <x-statistik
                label="Siap diambil"
                :nilai="number_format($jumlahSiap, 0, ',', '.')"
                keterangan="Tunggu anggota di loket"
            />
            <x-statistik label="Menunggu giliran" :nilai="number_format($jumlahMenunggu, 0, ',', '.')" />
            <x-statistik
                label="Kedaluwarsa"
                :nilai="number_format($jumlahKedaluwarsa, 0, ',', '.')"
                :keterangan="$jumlahKedaluwarsa > 0 ? 'Boleh dianggur ulang' : 'Tidak ada'"
            />
        </div>
    </section>

    <form method="GET" action="{{ route('reservations.index') }}"
          class="mb-4 flex flex-wrap items-center gap-2 border border-garis bg-permukaan p-3">
        <div>
            <label for="filter-status-reservasi" class="sr-only">Status reservasi</label>
            <select id="filter-status-reservasi"
                    name="status"
                    class="rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                @foreach (['' => 'Semua status', 'aktif' => 'Sedang berjalan', 'selesai' => 'Sudah selesai'] as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(request('status') === $nilai)>{{ $teks }}</option>
                @endforeach
            </select>
        </div>

        <x-button type="submit" ukuran="kecil">Terapkan</x-button>

        @if (request()->has('status'))
            <x-button :href="route('reservations.index')" varian="garis" ukuran="kecil">Reset</x-button>
        @endif

        <p class="ml-auto text-[0.75rem] text-tinta-samar">
            Urutan ditampilkan dari yang siap diambil lebih dulu.
        </p>
    </form>

    @if ($reservasi->isEmpty())
        <x-empty-state
            judul="Tidak ada reservasi"
            pesan="Belum ada antrean pemesanan yang cocok dengan filter ini."
            ikon="ANTREAN"
        >
            <x-button :href="route('books.index')" ukuran="kecil">Cari buku di katalog</x-button>
        </x-empty-state>
    @else
        <x-panel judul="Daftar reservasi" subjudul="Antrean pemesanan buku beserta statusnya." padat>
            <div class="overflow-x-auto">
                <table class="tabel-dasar">
                    <caption class="sr-only">Daftar reservasi</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Anggota</th>
                            <th scope="col">Buku</th>
                            <th scope="col" class="text-right">Antrean</th>
                            <th scope="col">Berlaku sampai</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservasi as $item)
                            <tr>
                                <td class="font-mono text-[0.75rem] whitespace-nowrap">
                                    <a href="{{ route('reservations.show', $item) }}" class="text-inti hover:underline">
                                        {{ $item->kode }}
                                    </a>
                                </td>
                                <td class="max-w-40">
                                    <span class="block truncate text-[0.8125rem]">{{ $item->user->name }}</span>
                                    <span class="block font-mono text-[0.6875rem] text-tinta-samar">
                                        {{ $item->user->nomor_anggota ?? '—' }}
                                    </span>
                                </td>
                                <td class="max-w-56 truncate">{{ $item->book->judul }}</td>
                                <td class="angka font-mono font-semibold">#{{ $item->antrean }}</td>
                                <td class="whitespace-nowrap font-mono text-[0.75rem] text-tinta-lembut">
                                    {{ $item->berlaku_sampai->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    <x-status-tag
                                        :label="$item->status->label()"
                                        :warna="match ($item->status->value) {
                                            'siap' => 'inti',
                                            'kedaluwarsa' => 'bahaya',
                                            'dibatalkan' => 'netral',
                                            default => 'aksen',
                                        }"
                                    />
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-button
                                            :href="route('reservations.show', $item)"
                                            varian="garis"
                                            ukuran="kecil"
                                        >Detail</x-button>

                                        @if ($item->status->value === 'menunggu' || $item->status->value === 'siap')
                                            <x-button
                                                :href="route('reservations.destroy', $item)"
                                                method="DELETE"
                                                varian="bahaya"
                                                ukuran="kecil"
                                                konfirmasi="Batalkan reservasi {{ $item->kode }}? Antrean anggota lain akan naik."
                                            >Batalkan</x-button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-3 pb-3">
                <x-pagination :paginator="$reservasi" />
            </div>
        </x-panel>
    @endif
</x-layouts.app>
