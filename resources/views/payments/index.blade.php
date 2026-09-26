<x-layouts.app>
    @php
        $metodeOpsi = ['' => 'Semua metode'] + collect(\App\Enums\PaymentMethod::cases())
            ->mapWithKeys(fn ($metode) => [$metode->value => $metode->label()])
            ->all();
    @endphp

    <x-page-heading
        judul="Pembayaran denda"
        :deskripsi="number_format($jumlahTransaksi, 0, ',', '.') . ' transaksi tercatat, total penerimaan Rp' . number_format($totalDiterima, 0, ',', '.') . '.'"
    >
        <x-button :href="route('payments.receivables')" varian="garis" ukuran="kecil">Tagihan belum lunas</x-button>
        <x-button :href="route('payments.create')" ukuran="kecil">Terima pembayaran</x-button>
    </x-page-heading>


    <section aria-labelledby="ringkasan-pembayaran" class="mb-4">
        <h2 id="ringkasan-pembayaran" class="sr-only">Ringkasan penerimaan</h2>
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <x-statistik
                label="Diterima bulan ini"
                :nilai="'Rp' . number_format($totalBulanIni, 0, ',', '.')"
                :keterangan="now()->translatedFormat('F Y')"
            />
            <x-statistik
                label="Total diterima"
                :nilai="'Rp' . number_format($totalDiterima, 0, ',', '.')"
            />
            <x-statistik
                label="Jumlah transaksi"
                :nilai="number_format($jumlahTransaksi, 0, ',', '.')"
            />
            <x-statistik
                label="Rata-rata transaksi"
                :nilai="$jumlahTransaksi > 0
                    ? 'Rp' . number_format((int) round($totalDiterima / $jumlahTransaksi), 0, ',', '.')
                    : 'Rp0'"
            />
        </div>
    </section>

    <form method="GET" action="{{ route('payments.index') }}"
          class="mb-4 grid gap-2.5 border border-garis bg-permukaan p-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="lg:col-span-2">
            <label for="cari-pembayaran" class="sr-only">Cari kode, nama anggota, atau nomor anggota</label>
            <input id="cari-pembayaran"
                   name="q"
                   type="search"
                   value="{{ request('q') }}"
                   placeholder="Cari kode, nama, atau nomor anggota"
                   data-cari-otomatis
                   class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 placeholder:text-tinta-samar focus:border-inti focus:shadow-fokus focus:outline-none">
        </div>

        <div>
            <label for="filter-metode" class="sr-only">Metode pembayaran</label>
            <select id="filter-metode"
                    name="metode"
                    class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                @foreach ($metodeOpsi as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(request('metode') === $nilai)>{{ $teks }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <div class="min-w-0 flex-1">
                <label for="jumlah-baris-pembayaran" class="sr-only">Jumlah baris per halaman</label>
                <select id="jumlah-baris-pembayaran"
                        name="halaman"
                        class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                    @foreach ([20 => '20', 50 => '50', 100 => '100'] as $jumlah => $teks)
                        <option value="{{ $jumlah }}" @selected(request('halaman', 20) === $jumlah)>{{ $teks }}</option>
                    @endforeach
                </select>
            </div>
            <x-button type="submit" ukuran="kecil">Terapkan</x-button>
        </div>
    </form>

    @if ($pembayaran->isEmpty())
        <x-empty-state
            judul="Belum ada pembayaran"
            pesan="Transaksi pembayaran denda akan muncul di sini setelah petugas menerimanya di loket."
            ikon="PEMBAYARAN"
        >
            <x-slot:aksi>
                <x-button :href="route('payments.create')" ukuran="kecil">Terima pembayaran</x-button>
            </x-slot:aksi>
        </x-empty-state>
    @else
        <x-panel judul="Riwayat pembayaran" subjudul="Setiap penerimaan pembayaran denda di loket." padat>
            <div class="overflow-x-auto">
                <table class="tabel-dasar">
                    <caption class="sr-only">Riwayat pembayaran denda</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Anggota</th>
                            <th scope="col">Peminjaman</th>
                            <th scope="col">Metode</th>
                            <th scope="col">Bukti</th>
                            <th scope="col" class="text-right">Jumlah</th>
                            <th scope="col">Petugas</th>
                            <th scope="col">Dibayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembayaran as $bayar)
                            <tr>
                                <td class="font-mono text-[0.75rem] whitespace-nowrap text-tinta-lembut">
                                    {{ $bayar->kode }}
                                </td>
                                <td class="max-w-48">
                                    <a href="{{ route('members.show', $bayar->user) }}"
                                       class="block truncate text-[0.8125rem] font-medium text-tinta hover:underline">
                                        {{ $bayar->user?->name ?? 'Anggota dihapus' }}
                                    </a>
                                    <span class="block font-mono text-[0.6875rem] text-tinta-samar">
                                        {{ $bayar->user?->nomor_anggota ?? '—' }}
                                    </span>
                                </td>
                                <td class="font-mono text-[0.75rem] whitespace-nowrap text-tinta-lembut">
                                    @if ($bayar->loan)
                                        <a href="{{ route('loans.show', $bayar->loan) }}" class="hover:underline">
                                            {{ $bayar->loan->kode }}
                                        </a>
                                    @else
                                        <span class="text-tinta-samar">—</span>
                                    @endif
                                </td>
                                <td>
                                    <x-status-tag
                                        :label="$bayar->metode->label()"
                                        :warna="$bayar->metode === \App\Enums\PaymentMethod::Transfer ? 'inti' : 'aksen'"
                                    />
                                </td>
                                <td class="font-mono text-[0.75rem] text-tinta-lembut">
                                    {{ $bayar->bukti ?: '—' }}
                                </td>
                                <td class="angka font-mono font-semibold">
                                    Rp{{ number_format($bayar->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="max-w-40 truncate text-[0.8125rem] text-tinta-lembut">
                                    {{ $bayar->petugas?->name ?? '—' }}
                                </td>
                                <td class="whitespace-nowrap text-[0.8125rem] text-tinta-lembut">
                                    {{ $bayar->dibayar_pada?->translatedFormat('d M Y') ?? '—' }}
                                    <span class="block font-mono text-[0.6875rem] text-tinta-samar">
                                        {{ $bayar->dibayar_pada?->format('H:i') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-3 pb-3">
                <x-pagination :paginator="$pembayaran" />
            </div>
        </x-panel>
    @endif
</x-layouts.app>
