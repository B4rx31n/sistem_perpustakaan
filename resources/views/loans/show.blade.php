<x-layouts.app>
    @php
        $aktif = $loan->isAktif();
        $hariTelat = $loan->hariTerlambat();
        $dendaBerjalan = $loanService->dendaBerjalan($loan);
        $totalDenda = (int) $loan->denda;
        $dibayar = (int) $pembayaran->sum('jumlah');
        $sisaTagihan = max(0, $totalDenda - $dibayar);
    @endphp

    <x-page-heading
        :judul="'Peminjaman ' . $loan->kode"
        :deskripsi="$loan->book->judul . ' — dipinjam oleh ' . $loan->user->name"
    >
        <x-button :href="route('loans.index')" varian="garis" ukuran="kecil">Semua peminjaman</x-button>
    </x-page-heading>


    @if ($hariTelat > 0)
        <x-peringatan
            class="mb-5"
            judul="Terlambat {{ $hariTelat }} hari dari batas pengembalian"
            :pesan="'Denda berjalan Rp' . number_format($dendaBerjalan, 0, ',', '.') . '. Kembalikan di lokat agar denda tidak bertambah.'"
        />
    @elseif ($sisaTagihan > 0)
        <x-peringatan
            class="mb-5"
            judul="Sisa denda Rp{{ number_format($sisaTagihan, 0, ',', '.') }} belum dilunasi"
            pesan="Buku sudah kembali, tinggal menyelesaikan pembayaran denda."
            warna="aksen"
        >
            <x-slot:aksi>
                <x-button :href="route('payments.create', ['user_id' => $loan->user_id])" ukuran="kecil">Terima pembayaran</x-button>
            </x-slot:aksi>
        </x-peringatan>
    @endif

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <section aria-labelledby="rincian-peminjaman" class="kartu overflow-hidden">
                <h2 id="rincian-peminjaman" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Rincian transaksi
                </h2>

                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 px-4 py-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Kode</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">{{ $loan->kode }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Status</dt>
                        <dd class="mt-0.5">
                            @if ($aktif)
                                <x-loan-status :loan="$loan" :loan-service="$loanService" />
                            @else
                                <x-status-tag label="Dikembalikan" warna="netral" />
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Petugas</dt>
                        <dd class="mt-0.5 text-[0.8125rem]">{{ $loan->petugas?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Dipinjam</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">
                            {{ $loan->dipinjam_at->translatedFormat('d M Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Harus kembali</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">
                            {{ $loan->harus_kembali_at->translatedFormat('d M Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Dikembalikan</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">
                            {{ $loan->dikembalikan_at?->translatedFormat('d M Y') ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Perpanjangan</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">
                            {{ $loan->perpanjangan }} dari {{ config('perpustakaan.maks_perpanjangan') }} kali
                        </dd>
                    </div>
                </dl>

                @if ($loan->catatan)
                    <div class="border-t border-garis px-4 py-3">
                        <h3 class="text-[0.75rem] font-medium text-tinta-samar">Catatan</h3>
                        <p class="mt-1 text-[0.875rem] text-tinta-lembut">{{ $loan->catatan }}</p>
                    </div>
                @endif
            </section>

            <section aria-labelledby="buku-dipinjam" class="kartu overflow-hidden">
                <h2 id="buku-dipinjam" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">Buku</h2>

                <div class="flex flex-wrap items-center gap-4 px-4 py-4">
                    @if ($loan->book->cover_path)
                        <img src="{{ $loan->book->coverUrl() }}"
                             alt="Sampul {{ $loan->book->judul }}"
                             class="h-24 w-16 shrink-0 border border-garis object-cover">
                    @endif

                    <div class="min-w-0 flex-1">
                        <a href="{{ route('books.show', $loan->book) }}"
                           class="text-[0.9375rem] font-medium text-tinta hover:underline">
                            {{ $loan->book->judul }}
                        </a>
                        <p class="mt-0.5 text-[0.8125rem] text-tinta-lembut">{{ $loan->book->penulis }}</p>
                        <p class="mt-1 font-mono text-[0.6875rem] text-tinta-samar">
                            ISBN {{ $loan->book->isbn }}
                            @if ($loan->book->category)
                                &middot; {{ $loan->book->category->nama }}
                            @endif
                        </p>
                    </div>
                </div>
            </section>

            <section aria-labelledby="pembayaran-denda" class="kartu overflow-hidden">
                <div class="kepala-kartu">
                    <h2 id="pembayaran-denda" class="text-[0.875rem] font-semibold">Pembayaran denda</h2>
                </div>

                @if ($pembayaran->isEmpty())
                    <x-empty-state
                        judul="Belum ada pembayaran"
                        pesan="Pembayaran denda akan muncul di sini setelah diterima di loket."
                        ikon="PEMBAYARAN"
                    />
                @else
                    <div class="overflow-x-auto">
                        <table class="tabel-dasar">
                            <caption class="sr-only">Riwayat pembayaran denda peminjaman ini</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Kode</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Metode</th>
                                    <th scope="col">Petugas</th>
                                    <th scope="col" class="text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pembayaran as $bayar)
                                    <tr>
                                        <td class="font-mono text-[0.75rem] whitespace-nowrap">{{ $bayar->kode }}</td>
                                        <td class="whitespace-nowrap font-mono text-[0.75rem] text-tinta-lembut">
                                            {{ $bayar->dibayar_pada->translatedFormat('d M Y H:i') }}
                                        </td>
                                        <td class="whitespace-nowrap text-[0.8125rem]">{{ $bayar->metode->label() }}</td>
                                        <td class="whitespace-nowrap text-[0.8125rem] text-tinta-lembut">
                                            {{ $bayar->petugas?->name ?? '—' }}
                                        </td>
                                        <td class="angka whitespace-nowrap font-mono font-semibold">
                                            Rp{{ number_format($bayar->jumlah, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-permukaan-lembut">
                                    <td colspan="4" class="text-[0.75rem] font-medium text-tinta-samar">
                                        Sudah dibayar
                                    </td>
                                    <td class="angka whitespace-nowrap font-mono font-semibold text-inti">
                                        Rp{{ number_format($dibayar, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <aside class="space-y-5">
            <section aria-labelledby="anggota-peminjam" class="kartu overflow-hidden">
                <h2 id="anggota-peminjam" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Peminjam
                </h2>

                <div class="px-4 py-3">
                    <p class="text-[0.9375rem] font-medium">{{ $loan->user->name }}</p>
                    <p class="font-mono text-[0.6875rem] text-tinta-samar">
                        {{ $loan->user->nomor_anggota ?? 'tanpa nomor' }}
                    </p>

                    <dl class="mt-2.5 space-y-1 text-[0.75rem]">
                        @if ($loan->user->program_studi)
                            <div class="flex justify-between gap-2">
                                <dt class="text-tinta-samar">Program studi</dt>
                                <dd class="text-right text-tinta">{{ $loan->user->program_studi }}</dd>
                            </div>
                        @endif
                        @if ($loan->user->no_hp)
                            <div class="flex justify-between gap-2">
                                <dt class="text-tinta-samar">Telepon</dt>
                                <dd class="font-mono text-right text-tinta">{{ $loan->user->no_hp }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between gap-2">
                            <dt class="text-tinta-samar">Surel</dt>
                            <dd class="truncate text-right text-tinta">{{ $loan->user->email }}</dd>
                        </div>
                    </dl>

                    <x-button
                        :href="route('members.show', $loan->user)"
                        varian="garis"
                        ukuran="kecil"
                        class="mt-3 w-full"
                    >Kartu anggota</x-button>
                </div>
            </section>

            <section aria-labelledby="tagihan-denda" class="kartu overflow-hidden">
                <h2 id="tagihan-denda" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Denda
                </h2>

                <dl class="divide-y divide-garis text-[0.8125rem]">
                    @if ($aktif)
                        <div class="flex items-center justify-between px-4 py-2">
                            <dt class="text-tinta-lembut">Hari terlambat</dt>
                            <dd class="angka font-mono {{ $hariTelat > 0 ? 'font-semibold text-bahaya' : '' }}">
                                {{ $hariTelat }} hari
                            </dd>
                        </div>
                        <div class="flex items-center justify-between px-4 py-2">
                            <dt class="text-tinta-lembut">Denda berjalan</dt>
                            <dd class="angka font-mono {{ $dendaBerjalan > 0 ? 'font-semibold text-bahaya' : 'text-inti' }}">
                                Rp{{ number_format($dendaBerjalan, 0, ',', '.') }}
                            </dd>
                        </div>
                    @else
                        <div class="flex items-center justify-between px-4 py-2">
                            <dt class="text-tinta-lembut">Hari terlambat</dt>
                            <dd class="angka font-mono">{{ $hariTelat }} hari</dd>
                        </div>
                        <div class="flex items-center justify-between px-4 py-2">
                            <dt class="text-tinta-lembut">Denda tercatat</dt>
                            <dd class="angka font-mono {{ $totalDenda > 0 ? 'font-semibold text-bahaya' : 'text-inti' }}">
                                Rp{{ number_format($totalDenda, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between px-4 py-2">
                            <dt class="text-tinta-lembut">Sudah dibayar</dt>
                            <dd class="angka font-mono text-inti">Rp{{ number_format($dibayar, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between bg-permukaan-lembut px-4 py-2">
                            <dt class="font-semibold text-tinta">Sisa tagihan</dt>
                            <dd class="angka font-mono font-semibold {{ $sisaTagihan > 0 ? 'text-bahaya' : 'text-inti' }}">
                                Rp{{ number_format($sisaTagihan, 0, ',', '.') }}
                            </dd>
                        </div>
                    @endif
                </dl>

                @if ($sisaTagihan > 0)
                    <div class="border-t border-garis px-4 py-3">
                        <x-button
                            :href="route('payments.create', ['user_id' => $loan->user_id, 'loan_id' => $loan->id])"
                            ukuran="kecil"
                            class="w-full"
                        >Terima pembayaran</x-button>
                    </div>
                @endif
            </section>

            @if ($aktif)
                <section aria-labelledby="aksi-peminjaman" class="kartu overflow-hidden">
                    <h2 id="aksi-peminjaman" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                        Aksi
                    </h2>

                    <div class="space-y-4 px-4 py-3">
                        <form method="POST" action="{{ route('loans.return', $loan) }}">
                            @csrf
                            <label for="catatan-kembalikan" class="block text-[0.8125rem] font-medium text-tinta">
                                Catatan pengembalian
                            </label>
                            <textarea id="catatan-kembalikan"
                                      name="catatan"
                                      rows="2"
                                      placeholder="Kondisi buku saat diterima kembali."
                                      class="mt-1 block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 placeholder:text-tinta-samar focus:border-inti focus:shadow-fokus focus:outline-none">{{ old('catatan') }}</textarea>

                            <x-button
                                type="submit"
                                class="mt-2 w-full"
                                konfirmasi="Terima pengembalian buku ini? Denda akan dihitung otomatis dari tanggal jatuh tempo."
                            >Terima pengembalian</x-button>
                        </form>

                        <div class="border-t border-garis pt-3">
                            @if ($loan->bisaDiperpanjang())
                                <form method="POST" action="{{ route('loans.extend', $loan) }}">
                                    @csrf
                                    <p class="text-[0.75rem] text-tinta-lembut">
                                        Perpanjangan menambah
                                        {{ config('perpustakaan.durasi_peminjaman_hari') }} hari dari tanggal jatuh tempo.
                                    </p>
                                    <x-button
                                        type="submit"
                                        varian="garis"
                                        ukuran="kecil"
                                        class="mt-2 w-full"
                                        konfirmasi="Perpanjang peminjaman ini? Jatuh tempo akan bergeser."
                                    >Perpanjang {{ config('perpustakaan.durasi_peminjaman_hari') }} hari</x-button>
                                </form>
                            @else
                                <p class="text-[0.75rem] text-tinta-samar">
                                    @if (! $aktif)
                                        Peminjaman sudah dikembalikan.
                                    @elseif ($loan->isTerlambat())
                                        Peminjaman yang terlambat tidak bisa diperpanjang. Minta anggota mengembalikannya.
                                    @else
                                        Batas perpanjangan {{ config('perpustakaan.maks_perpanjangan') }} kali sudah tercapai.
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </section>
            @endif
        </aside>
    </div>
</x-layouts.app>
