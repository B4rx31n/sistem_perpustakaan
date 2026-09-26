<x-layouts.app>
    <x-page-heading
        judul="Peminjaman"
        deskripsi="Catat, perpanjang, dan terima pengembalian buku di loket."
    >
        <x-button :href="route('loans.create')" ukuran="kecil">Pencatatan baru</x-button>
    </x-page-heading>


    <section aria-labelledby="ringkasan-peminjaman" class="mb-4">
        <h2 id="ringkasan-peminjaman" class="sr-only">Ringkasan peminjaman</h2>
        <div class="grid grid-cols-3 gap-3">
            <x-statistik label="Sedang dipinjam" :nilai="number_format($jumlahAktif, 0, ',', '.')" />
            <x-statistik
                label="Terlambat"
                :nilai="number_format($jumlahTerlambat, 0, ',', '.')"
                :keterangan="$jumlahTerlambat > 0 ? 'Perlu ditindaklanjuti' : 'Tidak ada tunggakan'"
            />
            <x-statistik label="Sudah kembali" :nilai="number_format($jumlahSelesai, 0, ',', '.')" />
        </div>
    </section>

    <form method="GET" action="{{ route('loans.index') }}"
          class="mb-4 grid gap-2.5 border border-garis bg-permukaan p-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="lg:col-span-2">
            <label for="cari-pinjaman" class="sr-only">Cari kode, judul, atau nama anggota</label>
            <input id="cari-pinjaman"
                   name="q"
                   type="search"
                   value="{{ request('q') }}"
                   placeholder="Cari kode, judul, atau nama anggota"
                   data-cari-otomatis
                   class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 placeholder:text-tinta-samar focus:border-inti focus:shadow-fokus focus:outline-none">
        </div>

        <div>
            <label for="filter-status-pinjaman" class="sr-only">Status</label>
            <select id="filter-status-pinjaman"
                    name="status"
                    class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                @foreach (['' => 'Semua status', 'aktif' => 'Sedang dipinjam', 'terlambat' => 'Terlambat', 'selesai' => 'Sudah kembali'] as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(request('status') === $nilai)>{{ $teks }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <x-button type="submit" ukuran="kecil">Terapkan</x-button>
            @if (request()->hasAny(['q', 'status']))
                <x-button :href="route('loans.index')" varian="garis" ukuran="kecil">Reset</x-button>
            @endif
        </div>
    </form>

    @if ($pinjaman->isEmpty())
        <x-empty-state
            judul="Tidak ada data peminjaman"
            pesan="Belum ada peminjaman yang cocok dengan filter, atau belum ada transaksi sama sekali."
            ikon="PEMINJAMAN"
        >
            <x-button :href="route('loans.create')" ukuran="kecil">Catat peminjaman pertama</x-button>
        </x-empty-state>
    @else
        <x-panel judul="Daftar peminjaman" subjudul="Transaksi peminjaman dan pengembalian yang tercatat." padat>
            <div class="overflow-x-auto">
                <table class="tabel-dasar">
                    <caption class="sr-only">Daftar peminjaman</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Anggota</th>
                            <th scope="col">Buku</th>
                            <th scope="col">Petugas</th>
                            <th scope="col">Jatuh tempo</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-right">Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pinjaman as $pinjamanItem)
                            <tr>
                                <td class="font-mono text-[0.75rem] whitespace-nowrap">
                                    <a href="{{ route('loans.show', $pinjamanItem) }}" class="text-inti hover:underline">
                                        {{ $pinjamanItem->kode }}
                                    </a>
                                </td>
                                <td class="max-w-40">
                                    <a href="{{ route('members.show', $pinjamanItem->user) }}"
                                       class="block truncate text-[0.8125rem] hover:underline">
                                        {{ $pinjamanItem->user->name }}
                                    </a>
                                    <span class="block font-mono text-[0.6875rem] text-tinta-samar">
                                        {{ $pinjamanItem->user->nomor_anggota ?? '—' }}
                                    </span>
                                </td>
                                <td class="max-w-56 truncate">{{ $pinjamanItem->book->judul }}</td>
                                <td class="max-w-32 truncate text-[0.8125rem] text-tinta-lembut">
                                    {{ $pinjamanItem->petugas?->name ?? '—' }}
                                </td>
                                <td class="whitespace-nowrap font-mono text-[0.75rem]">
                                    {{ $pinjamanItem->harus_kembali_at->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    @if ($pinjamanItem->status->value === 'dikembalikan')
                                        <x-status-tag label="Dikembalikan" warna="netral" />
                                    @else
                                        <x-loan-status :loan="$pinjamanItem" :loan-service="$loanService" />
                                    @endif
                                </td>
                                <td class="angka whitespace-nowrap font-mono">
                                    @if ($pinjamanItem->denda > 0)
                                        <span class="font-semibold text-bahaya">
                                            Rp{{ number_format($pinjamanItem->denda, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-tinta-samar">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-3 pb-3">
                <x-pagination :paginator="$pinjaman" />
            </div>
        </x-panel>
    @endif
</x-layouts.app>
