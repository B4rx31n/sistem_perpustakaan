<x-layouts.app>
    <x-page-heading
        judul="Rekapitulasi keanggotaan"
        :deskripsi="number_format($totalAnggota, 0, ',', '.') . ' anggota terdaftar, ' . number_format($reservasiAktif, 0, ',', '.') . ' reservasi sedang berjalan.'"
    >
        <x-button :href="route('reports.index')" varian="garis" ukuran="kecil">Semua laporan</x-button>
        <x-button :href="route('members.index')" varian="garis" ukuran="kecil">Daftar anggota</x-button>
    </x-page-heading>


    <section aria-labelledby="ringkasan-keanggotaan" class="mb-5 space-y-3">
        <h2 id="ringkasan-keanggotaan" class="sr-only">Ringkasan keanggotaan</h2>

        <div class="grid gap-3 lg:grid-cols-3">
            <x-metrik
                label="Nilai piutang"
                :nilai="'Rp' . number_format($totalUtang, 0, ',', '.')"
                :warna="$totalUtang > 0 ? 'bahaya' : 'netral'"
                :konteks="$jumlahUtang > 0
                    ? number_format($jumlahUtang, 0, ',', '.') . ' anggota belum melunasi denda.'
                    : 'Tidak ada anggota dengan tunggakan denda.'"
                arah="Daftar tagihan"
                :tautan="route('payments.receivables')"
            />

            <x-statistik
                label="Total anggota"
                :nilai="number_format($totalAnggota, 0, ',', '.')"
                :keterangan="$totalAnggota > 0
                    ? number_format($totalAktif / $totalAnggota * 100, 1, ',', '.') . '% aktif, ' . number_format($totalNonaktif, 0, ',', '.') . ' nonaktif'
                    : null"
            />
            <x-statistik
                label="Denda diterima"
                :nilai="'Rp' . number_format($totalDiterima, 0, ',', '.')"
                :keterangan="$totalUtang + $totalDiterima > 0
                    ? number_format($totalDiterima / ($totalUtang + $totalDiterima) * 100, 1, ',', '.') . '% dari total denda'
                    : null"
            />
        </div>
    </section>

    <div class="grid gap-5 lg:grid-cols-2">
        <x-panel judul="Anggota per program studi" subjudul="Porsi terhadap seluruh anggota terdaftar.">
            @if ($perProgramStudi->isEmpty())
                <x-empty-state
                    judul="Program studi belum diisi"
                    pesan="Anggota yang mengisi program studi akan dikelompokkan di sini."
                    ikon="PROGRAM STUDI"
                />
            @else
                <ul class="space-y-3">
                    @foreach ($perProgramStudi as $program => $jumlah)
                        @php
                            $porsi = $totalAnggota > 0 ? $jumlah / $totalAnggota * 100 : 0;
                        @endphp
                        <li>
                            <x-bilah
                                :persen="$porsi"
                                :label="$program"
                                :nilai="number_format($jumlah, 0, ',', '.')"
                            />
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-panel>

        <x-panel judul="Denda dan penerimaan" subjudul="Sisa tagihan dibandingkan dengan yang sudah dibayar.">
            <dl class="divide-y divide-garis text-[0.875rem]">
                <div class="flex items-center justify-between px-4 py-2.5">
                    <dt class="text-tinta-lembut">Sisa tagihan belum lunas</dt>
                    <dd class="angka font-mono font-semibold text-bahaya">
                        Rp{{ number_format($totalUtang, 0, ',', '.') }}
                    </dd>
                </div>
                <div class="flex items-center justify-between px-4 py-2.5">
                    <dt class="text-tinta-lembut">Sudah diterima</dt>
                    <dd class="angka font-mono font-semibold text-inti">
                        Rp{{ number_format($totalDiterima, 0, ',', '.') }}
                    </dd>
                </div>
                <div class="flex items-center justify-between px-4 py-2.5">
                    <dt class="font-medium text-tinta">Total denda</dt>
                    <dd class="angka font-mono font-semibold">
                        Rp{{ number_format($totalUtang + $totalDiterima, 0, ',', '.') }}
                    </dd>
                </div>
            </dl>

            <x-slot:aksi>
                <a href="{{ route('payments.receivables') }}" class="font-medium text-inti hover:underline">Daftar tagihan</a>
            </x-slot:aksi>
        </x-panel>
    </div>
</x-layouts.app>
