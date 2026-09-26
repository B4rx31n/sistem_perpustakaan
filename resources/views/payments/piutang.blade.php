<x-layouts.app>
    <x-page-heading
        judul="Tagihan denda belum lunas"
        :deskripsi="number_format($jumlahUtang, 0, ',', '.') . ' anggota masih punya tagihan, total Rp' . number_format($totalUtang, 0, ',', '.') . '.'"
    >
        <x-button :href="route('payments.index')" varian="garis" ukuran="kecil">Riwayat pembayaran</x-button>
        <x-button :href="route('payments.create')" ukuran="kecil">Terima pembayaran</x-button>
    </x-page-heading>


    <section aria-labelledby="ringkasan-piutang" class="mb-5">
        <h2 id="ringkasan-piutang" class="sr-only">Ringkasan piutang denda</h2>

        <div class="grid gap-3 lg:grid-cols-3">
            <x-metrik
                label="Nilai piutang"
                :nilai="'Rp' . number_format($totalUtang, 0, ',', '.')"
                :warna="$totalUtang > 0 ? 'bahaya' : 'netral'"
                :konteks="$jumlahUtang > 0
                    ? number_format($jumlahUtang, 0, ',', '.') . ' anggota belum melunasi denda.'
                    : 'Semua anggota sudah lunas.'"
                arah="Terima pembayaran"
                :tautan="route('payments.create')"
            />

            <x-statistik
                label="Rata-rata per anggota"
                :nilai="$jumlahUtang > 0
                    ? 'Rp' . number_format((int) round($totalUtang / $jumlahUtang), 0, ',', '.')
                    : 'Rp0'"
            />
            <x-statistik
                label="Tagihan terbesar"
                :nilai="$utang->isEmpty()
                    ? '—'
                    : 'Rp' . number_format(max(0, (int) $utang->first()->total_denda - (int) $utang->first()->total_bayar), 0, ',', '.')"
                :keterangan="$utang->isEmpty() ? null : $utang->first()->name"
            />
        </div>
    </section>

    @if ($utang->isEmpty())
        <x-empty-state
            judul="Semua tagihan sudah lunas"
            pesan="Semua anggota sudah melunasi denda. Daftar ini akan terisi otomatis begitu ada peminjaman terlambat."
            ikon="LUNAS"
        >
            <x-slot:aksi>
                <x-button :href="route('loans.index')" varian="garis" ukuran="kecil">Lihat peminjaman</x-button>
            </x-slot:aksi>
        </x-empty-state>
    @else
        <x-panel judul="Daftar tagihan" subjudul="Diurutkan dari total denda terbesar." padat>
            <x-slot:aksi>
                <a href="{{ route('loans.index') }}" class="font-medium text-inti hover:underline">Lihat peminjaman</a>
            </x-slot:aksi>

            <div class="overflow-x-auto">
                <table class="tabel-dasar">
                    <caption class="sr-only">Daftar anggota dengan sisa tagihan denda</caption>
                    <thead>
                        <tr>
                            <th scope="col">Nomor</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Kontak</th>
                            <th scope="col" class="text-right">Total denda</th>
                            <th scope="col" class="text-right">Sudah dibayar</th>
                            <th scope="col" class="text-right">Sisa tagihan</th>
                            <th scope="col" class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($utang as $orang)
                            @php
                                $sisa = max(0, (int) $orang->total_denda - (int) $orang->total_bayar);
                            @endphp
                            <tr>
                                <td class="font-mono text-[0.75rem] whitespace-nowrap text-tinta-lembut">
                                    {{ $orang->nomor_anggota ?? '—' }}
                                </td>
                                <td class="max-w-48">
                                    <a href="{{ route('members.show', $orang) }}"
                                       class="block truncate text-[0.8125rem] font-medium text-tinta hover:underline">
                                        {{ $orang->name }}
                                    </a>
                                    <span class="block truncate text-[0.6875rem] text-tinta-samar">
                                        {{ $orang->program_studi ?: 'Program studi belum diisi' }}
                                    </span>
                                </td>
                                <td class="max-w-48 text-[0.8125rem] text-tinta-lembut">
                                    <span class="block truncate">{{ $orang->email }}</span>
                                    @if ($orang->no_hp)
                                        <span class="block font-mono text-[0.6875rem] text-tinta-samar">{{ $orang->no_hp }}</span>
                                    @endif
                                </td>
                                <td class="angka font-mono text-tinta-lembut">
                                    Rp{{ number_format($orang->total_denda, 0, ',', '.') }}
                                </td>
                                <td class="angka font-mono text-inti">
                                    Rp{{ number_format($orang->total_bayar, 0, ',', '.') }}
                                </td>
                                <td class="angka font-mono font-semibold text-bahaya">
                                    Rp{{ number_format($sisa, 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-button
                                            :href="route('payments.create', ['user_id' => $orang->id])"
                                            ukuran="kecil"
                                        >
                                            Terima bayar
                                        </x-button>
                                        <x-button :href="route('members.show', $orang)" varian="garis" ukuran="kecil">
                                            Detail
                                        </x-button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-3 pb-3">
                <x-pagination :paginator="$utang" />
            </div>
        </x-panel>
    @endif
</x-layouts.app>
