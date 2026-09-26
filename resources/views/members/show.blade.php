<x-layouts.app>
    <x-page-heading
        :judul="$member->name"
        :deskripsi="($member->nomor_anggota ?? 'tanpa nomor anggota') . ' — ' . $member->role->label() . ' — ' . ($member->status === 'aktif' ? 'aktif' : 'nonaktif')"
    >
        <x-button :href="route('members.card', $member)" varian="garis" ukuran="kecil">Kartu cetak</x-button>
        @can('update', $member)
            <x-button :href="route('members.edit', $member)" ukuran="kecil">Ubah data</x-button>
        @endcan
    </x-page-heading>


    @if ($sisaDenda > 0)
        <x-peringatan
            class="mb-5"
            judul="Sisa denda Rp{{ number_format($sisaDenda, 0, ',', '.') }}"
            pesan="Tagihan ini belum dilunasi, jadi peminjaman berikutnya bisa tertahan."
            warna="aksen"
        >
            <x-slot:aksi>
                <x-button :href="route('payments.create', ['user_id' => $member->id])" ukuran="kecil">Terima pembayaran</x-button>
            </x-slot:aksi>
        </x-peringatan>
    @endif

    <section aria-labelledby="ringkasan-anggota" class="mb-5">
        <h2 id="ringkasan-anggota" class="sr-only">Ringkasan aktivitas anggota</h2>
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <x-statistik
                label="Pinjaman aktif"
                :nilai="number_format($pinjamanAktif->count(), 0, ',', '.')"
                :keterangan="$pinjamanAktif->isEmpty() ? 'Tidak memegang buku' : 'Tepat atas nama ' . $member->name"
            />
            <x-statistik
                label="Sisa denda"
                :nilai="'Rp' . number_format($sisaDenda, 0, ',', '.')"
                :keterangan="$sisaDenda > 0 ? 'Belum lunas' : 'Tidak ada tagihan'"
            />
            <x-statistik
                label="Pengembalian terakhir"
                :nilai="number_format($riwayat->count(), 0, ',', '.')"
                :keterangan="$riwayat->isEmpty() ? 'Belum ada' : 'Transaksi terbaru yang tercatat'"
            />
            <x-statistik
                label="Reservasi berjalan"
                :nilai="number_format($reservasi->count(), 0, ',', '.')"
                :keterangan="$reservasi->isEmpty() ? 'Tidak dalam antrean' : 'Menunggu buku tersedia'"
            />
        </div>
    </section>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <section aria-labelledby="data-anggota" class="kartu overflow-hidden">
                <h2 id="data-anggota" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Data anggota
                </h2>

                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 px-4 py-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Nomor anggota</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">{{ $member->nomor_anggota ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Peran</dt>
                        <dd class="mt-0.5 text-[0.8125rem]">{{ $member->role->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Status</dt>
                        <dd class="mt-0.5">
                            <x-status-tag
                                :label="$member->status === 'aktif' ? 'Aktif' : 'Nonaktif'"
                                :warna="$member->status === 'aktif' ? 'inti' : 'bahaya'"
                            />
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Surel</dt>
                        <dd class="mt-0.5 truncate text-[0.8125rem]">{{ $member->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Telepon</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">{{ $member->no_hp ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Program studi</dt>
                        <dd class="mt-0.5 text-[0.8125rem]">{{ $member->program_studi ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Bergabung</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">
                            {{ $member->created_at->translatedFormat('d M Y') }}
                        </dd>
                    </div>
                </dl>

                @if ($member->alamat)
                    <div class="border-t border-garis px-4 py-3">
                        <h3 class="text-[0.75rem] font-medium text-tinta-samar">Alamat</h3>
                        <p class="mt-1 text-[0.875rem] text-tinta-lembut">{{ $member->alamat }}</p>
                    </div>
                @endif
            </section>

            <section aria-labelledby="pinjaman-anggota" class="kartu overflow-hidden">
                <div class="kepala-kartu">
                    <h2 id="pinjaman-anggota" class="text-[0.875rem] font-semibold">Pinjaman aktif</h2>
                    @if ($pinjamanAktif->isNotEmpty())
                        <x-button :href="route('loans.show', $pinjamanAktif->first())" varian="garis" ukuran="kecil">
                            Lihat satu
                        </x-button>
                    @endif
                </div>

                @if ($pinjamanAktif->isEmpty())
                    <x-empty-state
                        judul="Tidak ada buku yang dipinjam"
                        pesan="Anggota ini sedang tidak memegang buku dari perpustakaan."
                        ikon="KOSONG"
                    />
                @else
                    <ul class="divide-y divide-garis">
                        @foreach ($pinjamanAktif as $pinjaman)
                            <li class="flex flex-wrap items-center gap-x-4 gap-y-2 px-4 py-3">
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('books.show', $pinjaman->book) }}"
                                       class="text-[0.875rem] font-medium text-tinta hover:underline">
                                        {{ $pinjaman->book->judul }}
                                    </a>
                                    <p class="font-mono text-[0.6875rem] text-tinta-samar">{{ $pinjaman->kode }}</p>
                                </div>

                                <div class="text-[0.75rem] text-tinta-lembut">
                                    <span class="block">Kembali</span>
                                    <span class="font-mono text-tinta">
                                        {{ $pinjaman->harus_kembali_at->translatedFormat('d M Y') }}
                                    </span>
                                </div>

                                <x-loan-status :loan="$pinjaman" :loan-service="$loanService" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section aria-labelledby="reservasi-anggota" class="kartu overflow-hidden">
                <h2 id="reservasi-anggota" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Reservasi
                </h2>

                @if ($reservasi->isEmpty())
                    <x-empty-state
                        judul="Tidak ada reservasi"
                        pesan="Anggota ini belum pernah memesan buku dalam antrean."
                        ikon="ANTREAN"
                    />
                @else
                    <div class="overflow-x-auto">
                        <table class="tabel-dasar">
                            <caption class="sr-only">Riwayat reservasi anggota</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Kode</th>
                                    <th scope="col">Buku</th>
                                    <th scope="col" class="text-right">Antrean</th>
                                    <th scope="col">Status</th>
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
                                        <td class="max-w-56 truncate">{{ $item->book->judul }}</td>
                                        <td class="angka font-mono">#{{ $item->antrean }}</td>
                                        <td>
                                            <x-status-tag
                                                :label="$item->status->label()"
                                                :warna="match ($item->status->value) {
                                                    'siap' => 'inti',
                                                    'kedaluwarsa' => 'bahaya',
                                                    default => 'netral',
                                                }"
                                            />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section aria-labelledby="riwayat-anggota" class="kartu overflow-hidden">
                <h2 id="riwayat-anggota" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Riwayat pengembalian
                </h2>

                @if ($riwayat->isEmpty())
                    <x-empty-state
                        judul="Riwayat masih kosong"
                        pesan="Belum ada buku yang selesai dikembalikan oleh anggota ini."
                        ikon="RIWAYAT"
                    />
                @else
                    <div class="overflow-x-auto">
                        <table class="tabel-dasar">
                            <caption class="sr-only">Riwayat pengembalian anggota</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Kode</th>
                                    <th scope="col">Buku</th>
                                    <th scope="col">Dikembalikan</th>
                                    <th scope="col" class="text-right">Denda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($riwayat as $item)
                                    <tr>
                                        <td class="font-mono text-[0.75rem] whitespace-nowrap">
                                            <a href="{{ route('loans.show', $item) }}" class="text-inti hover:underline">
                                                {{ $item->kode }}
                                            </a>
                                        </td>
                                        <td class="max-w-64 truncate">{{ $item->book->judul }}</td>
                                        <td class="whitespace-nowrap font-mono text-[0.75rem] text-tinta-lembut">
                                            {{ $item->dikembalikan_at?->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="angka whitespace-nowrap font-mono">
                                            @if ($item->denda > 0)
                                                <span class="font-semibold text-bahaya">
                                                    Rp{{ number_format($item->denda, 0, ',', '.') }}
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
                @endif
            </section>
        </div>

        <aside class="space-y-5">
            <section aria-labelledby="tagihan-anggota" class="kartu overflow-hidden">
                <h2 id="tagihan-anggota" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Tagihan
                </h2>

                <div class="px-4 py-3">
                    <p class="text-[0.75rem] font-medium text-tinta-samar">Sisa denda</p>
                    <p class="mt-1 font-mono text-[1.75rem] leading-none font-semibold {{ $sisaDenda > 0 ? 'text-bahaya' : 'text-inti' }}">
                        Rp{{ number_format($sisaDenda, 0, ',', '.') }}
                    </p>
                    <p class="mt-1.5 text-[0.75rem] text-tinta-samar">
                        {{ $sisaDenda > 0
                            ? 'Denda belum lunas, peminjaman baru akan ditolak sistem.'
                            : 'Tidak ada sisa denda. Anggota boleh meminjam.' }}
                    </p>

                    @if ($sisaDenda > 0)
                        <x-button
                            :href="route('payments.create', ['user_id' => $member->id])"
                            ukuran="kecil"
                            class="mt-3 w-full"
                        >Terima pembayaran</x-button>
                    @endif
                </div>

                <dl class="divide-y divide-garis border-t border-garis text-[0.8125rem]">
                    <div class="flex items-center justify-between px-4 py-2">
                        <dt class="text-tinta-lembut">Sedang dipinjam</dt>
                        <dd class="angka font-mono">{{ $pinjamanAktif->count() }}</dd>
                    </div>
                    <div class="flex items-center justify-between px-4 py-2">
                        <dt class="text-tinta-lembut">Sudah dikembalikan</dt>
                        <dd class="angka font-mono">{{ $riwayat->count() }}</dd>
                    </div>
                </dl>
            </section>

            <section aria-labelledby="pembayaran-anggota" class="kartu overflow-hidden">
                <h2 id="pembayaran-anggota" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Pembayaran terakhir
                </h2>

                @if ($pembayaran->isEmpty())
                    <p class="px-4 py-3 text-[0.8125rem] text-tinta-lembut">Belum ada pembayaran denda tercatat.</p>
                @else
                    <ul class="divide-y divide-garis">
                        @foreach ($pembayaran as $bayar)
                            <li class="flex items-start justify-between gap-3 px-4 py-2.5">
                                <div class="min-w-0">
                                    <p class="font-mono text-[0.75rem] text-tinta-lembut">{{ $bayar->kode }}</p>
                                    <p class="text-[0.6875rem] text-tinta-samar">
                                        {{ $bayar->dibayar_pada->translatedFormat('d M Y') }} &middot; {{ $bayar->metode->label() }}
                                    </p>
                                </div>
                                <span class="angka shrink-0 font-mono text-[0.8125rem] font-semibold text-inti">
                                    Rp{{ number_format($bayar->jumlah, 0, ',', '.') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section aria-labelledby="aksi-anggota" class="kartu overflow-hidden">
                <h2 id="aksi-anggota" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">Aksi</h2>

                <div class="space-y-2 px-4 py-3">
                    <x-button :href="route('loans.create', ['user_id' => $member->id])" varian="garis" class="w-full">
                        Catat peminjaman
                    </x-button>
                    <x-button :href="route('members.card', $member)" varian="garis" class="w-full">
                        Buka kartu cetak
                    </x-button>
                    @can('update', $member)
                        <x-button :href="route('members.edit', $member)" varian="garis" class="w-full">
                            Ubah data anggota
                        </x-button>
                    @endcan
                </div>
            </section>
        </aside>
    </div>
</x-layouts.app>
