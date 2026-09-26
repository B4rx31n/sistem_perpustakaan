<x-layouts.app>
    @php
        $warnaStatus = [
            \App\Enums\ReservationStatus::Menunggu->value => 'netral',
            \App\Enums\ReservationStatus::Siap->value => 'aksen',
            \App\Enums\ReservationStatus::Selesai->value => 'inti',
            \App\Enums\ReservationStatus::Kedaluwarsa->value => 'bahaya',
            \App\Enums\ReservationStatus::Dibatalkan->value => 'bahaya',
        ];

        $statusTerdaftar = \App\Enums\ReservationStatus::cases();
    @endphp

    <x-page-heading
        judul="Status reservasi"
        :deskripsi="number_format($total, 0, ',', '.') . ' reservasi tercatat, ' . number_format($aktif, 0, ',', '.') . ' masih memegang tempat dalam antrean.'"
    >
        <x-button :href="route('reports.index')" varian="garis" ukuran="kecil">Semua laporan</x-button>
        <x-button :href="route('reservations.index')" varian="garis" ukuran="kecil">Daftar reservasi</x-button>
    </x-page-heading>


    @if ($sedangSiap > 0)
        <x-peringatan
            class="mb-5"
            judul="{{ $sedangSiap }} reservasi siap diambil"
            pesan="Buku sudah tersedia dan menunggu anggota datang ke loket sebelum masa ambil habis."
            warna="aksen"
        >
            <x-slot:aksi>
                <x-button :href="route('reservations.index')" ukuran="kecil">Buka antrean</x-button>
            </x-slot:aksi>
        </x-peringatan>
    @endif

    <section aria-labelledby="ringkasan-reservasi" class="mb-5">
        <h2 id="ringkasan-reservasi" class="sr-only">Ringkasan reservasi</h2>

        <div class="grid gap-3 lg:grid-cols-3">
            <x-metrik
                label="Reservasi berjalan"
                :nilai="number_format($aktif, 0, ',', '.')"
                :konteks="$total > 0
                    ? number_format($aktif / $total * 100, 1, ',', '.') . '% dari ' . number_format($total, 0, ',', '.') . ' reservasi masih memegang antrean.'
                    : 'Belum ada reservasi yang sedang berjalan.'"
                arah="Buka daftar"
                :tautan="route('reservations.index')"
            />

            <x-statistik
                label="Siap diambil"
                :nilai="number_format($sedangSiap, 0, ',', '.')"
                :keterangan="$sedangSiap > 0 ? 'Menunggu anggota datang ke loket' : 'Tidak ada yang menunggu diambil'"
            />
            <x-statistik
                label="Kedaluwarsa"
                :nilai="number_format($kedaluwarsa, 0, ',', '.')"
                :keterangan="$kedaluwarsa > 0 ? 'Batas ambil sudah lewat' : 'Tidak ada yang kedaluwarsa'"
            />
        </div>
    </section>

    <x-panel judul="Reservasi per status" subjudul="Seluruh status yang tercatat, termasuk yang sudah selesai." padat>
        <div class="overflow-x-auto">
            <table class="tabel-dasar">
                <caption class="sr-only">Rekapitulasi reservasi per status</caption>
                <thead>
                    <tr>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Jumlah</th>
                        <th scope="col" class="w-72">Porsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($statusTerdaftar as $status)
                        @php
                            $jumlah = (int) ($perStatus[$status->value] ?? 0);
                            $porsi = $total > 0 ? $jumlah / $total * 100 : 0;
                        @endphp
                        <tr>
                            <td>
                                <x-status-tag
                                    :label="$status->label()"
                                    :warna="$warnaStatus[$status->value] ?? 'netral'"
                                />
                            </td>
                            <td class="angka font-mono font-semibold">{{ number_format($jumlah, 0, ',', '.') }}</td>
                            <td>
                                <x-bilah :persen="$porsi" :nilai="number_format($porsi, 1, ',', '.') . '%'" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-garis-kuat bg-permukaan-lembut">
                        <th scope="row" class="px-3.5 py-3 text-left text-[0.8125rem] font-semibold text-tinta">
                            Total
                        </th>
                        <td class="angka font-mono font-semibold">{{ number_format($total, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-panel>
</x-layouts.app>
