<x-layouts.app>
    <x-page-heading
        judul="Peminjaman terlambat"
        :deskripsi="number_format($jumlah, 0, ',', '.') . ' peminjaman lewat jatuh tempo, total denda berjalan Rp' . number_format($totalDenda, 0, ',', '.') . '.'"
    >
        <x-button :href="route('reports.index')" varian="garis" ukuran="kecil">Semua laporan</x-button>
        <x-button :href="route('loans.index')" varian="garis" ukuran="kecil">Daftar peminjaman</x-button>
    </x-page-heading>


    <section aria-labelledby="ringkasan-keterlambatan" class="mb-5">
        <h2 id="ringkasan-keterlambatan" class="sr-only">Ringkasan keterlambatan</h2>

        <div class="grid gap-3 lg:grid-cols-3">
            <x-metrik
                label="Denda berjalan"
                :nilai="'Rp' . number_format($totalDenda, 0, ',', '.')"
                warna="bahaya"
                :konteks="'Rp' . number_format(config('perpustakaan.denda_per_hari'), 0, ',', '.') . ' per hari untuk tiap peminjaman yang lewat.'"
            />

            <x-statistik
                label="Peminjaman terlambat"
                :nilai="number_format($jumlah, 0, ',', '.')"
                :keterangan="$jumlah > 0 ? 'Urut dari yang paling lama telat' : 'Tidak ada tunggakan'"
            />
            <x-statistik
                label="Rata-rata keterlambatan"
                :nilai="$jumlah > 0 ? number_format($totalHari / $jumlah, 1, ',', '.') . ' hari' : '—'"
                :keterangan="'Total ' . number_format($totalHari, 0, ',', '.') . ' hari telat'"
            />
        </div>
    </section>

    @if ($rincian->isEmpty())
        <x-empty-state
            judul="Tidak ada peminjaman terlambat"
            pesan="Semua peminjaman masih dalam batas waktu. Daftar ini terisi otomatis begitu ada yang lewat jatuh tempo."
            ikon="TEPAT WAKTU"
        >
            <x-slot:aksi>
                <x-button :href="route('loans.index')" varian="garis" ukuran="kecil">Daftar peminjaman</x-button>
            </x-slot:aksi>
        </x-empty-state>
    @else
        <x-panel judul="Daftar keterlambatan" subjudul="Satu baris per peminjaman, urut dari yang paling lama." padat>
            <div class="overflow-x-auto">
                <table class="tabel-dasar">
                    <caption class="sr-only">Daftar peminjaman yang lewat jatuh tempo</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Judul</th>
                            <th scope="col">Anggota</th>
                            <th scope="col">Kontak</th>
                            <th scope="col">Jatuh tempo</th>
                            <th scope="col" class="text-right">Hari telat</th>
                            <th scope="col" class="text-right">Denda</th>
                            <th scope="col" class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rincian as $baris)
                            @php
                                $pinjaman = $baris['loan'];
                            @endphp
                            <tr>
                                <td class="font-mono text-[0.75rem] whitespace-nowrap text-tinta-lembut">
                                    <a href="{{ route('loans.show', $pinjaman) }}" class="hover:underline">
                                        {{ $pinjaman->kode }}
                                    </a>
                                </td>
                                <td class="max-w-56 truncate text-[0.8125rem]">
                                    {{ $pinjaman->book?->judul ?? 'Buku dihapus' }}
                                </td>
                                <td class="max-w-40">
                                    <a href="{{ route('members.show', $pinjaman->user) }}"
                                       class="block truncate text-[0.8125rem] font-medium text-tinta hover:underline">
                                        {{ $pinjaman->user?->name ?? 'Anggota dihapus' }}
                                    </a>
                                    <span class="block font-mono text-[0.6875rem] text-tinta-samar">
                                        {{ $pinjaman->user?->nomor_anggota ?? '—' }}
                                    </span>
                                </td>
                                <td class="max-w-40 text-[0.75rem] text-tinta-lembut">
                                    <span class="block font-mono">{{ $pinjaman->user?->no_hp ?: '—' }}</span>
                                    <span class="block truncate text-[0.6875rem] text-tinta-samar">
                                        {{ $pinjaman->user?->email ?? '—' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap text-[0.8125rem] text-tinta-lembut">
                                    {{ $pinjaman->harus_kembali_at?->translatedFormat('d M Y') ?? '—' }}
                                </td>
                                <td class="angka font-mono font-semibold text-bahaya">{{ $baris['hari'] }}</td>
                                <td class="angka font-mono font-semibold">
                                    Rp{{ number_format($baris['denda'], 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if ($pinjaman->user)
                                            <x-button
                                                :href="route('payments.create', ['user_id' => $pinjaman->user->id])"
                                                ukuran="kecil"
                                            >
                                                Terima denda
                                            </x-button>
                                        @endif
                                        <x-button :href="route('loans.show', $pinjaman)" varian="garis" ukuran="kecil">
                                            Detail
                                        </x-button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-garis-kuat bg-permukaan-lembut">
                            <th scope="row" colspan="5" class="px-3.5 py-3 text-left text-[0.8125rem] font-semibold text-tinta">
                                Total
                            </th>
                            <td class="angka font-mono font-semibold text-bahaya">{{ number_format($totalHari, 0, ',', '.') }}</td>
                            <td class="angka font-mono font-semibold">Rp{{ number_format($totalDenda, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-panel>
    @endif
</x-layouts.app>
