<x-layouts.app>
    <x-page-heading
        judul="Kartu anggota"
        :deskripsi="'Riwayat resmi ' . $member->name . ' untuk dicetak atau ditunjukkan di loket.'"
    >
        <x-button :href="route('members.show', $member)" varian="garis" ukuran="kecil">Detail anggota</x-button>
        <x-button type="button" onclick="window.print()" ukuran="kecil">Cetak kartu</x-button>
    </x-page-heading>


    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <section aria-labelledby="kartu-anggota" class="kartu overflow-hidden">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-garis bg-grad-kepala px-4 py-4">
                    <div class="flex min-w-0 items-start gap-3">
                        {{-- Mark buku di kartu anggota, karena kartu ini adalah
                             kartu perpustakaan fisik yang ditunjukkan di loket,
                             bukan sekadar panel data. --}}
                        <span aria-hidden="true"
                              class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-grad-inti text-inti-kunci ring-1 ring-pastel-3/60">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                                 stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 shrink-0">
                                <path d="M12 6.75C10.5 5.25 8.4 4.5 5.25 4.5c-.9 0-1.5.1-1.5.1v12.9s.6-.1 1.5-.1c3.15 0 5.25.75 6.75 2.25 1.5-1.5 3.6-2.25 6.75-2.25.9 0 1.5.1 1.5.1V4.6s-.6-.1-1.5-.1c-3.15 0-5.25.75-6.75 2.25Z" />
                                <path d="M12 6.75v12.9" />
                                <path d="M7.5 8.4h1.5M7.5 11.1h1.5" opacity=".55" />
                                <path d="M15 8.4h1.5M15 11.1h1.5" opacity=".55" />
                            </svg>
                        </span>

                        <div class="min-w-0">
                            <p class="text-[0.75rem] font-medium text-tinta-samar">
                                Perpustakaan Nusantara
                            </p>
                            <h2 id="kartu-anggota" class="mt-1 text-[1.25rem] leading-tight font-semibold text-tinta">
                                {{ $member->name }}
                            </h2>
                            <p class="mt-1 font-mono text-[0.75rem] text-tinta-samar">
                                {{ $member->nomor_anggota ?? 'Tanpa nomor anggota' }}
                                @if ($member->program_studi)
                                    &middot; {{ $member->program_studi }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0 rounded-lg border border-pastel-3 bg-permukaan/70 px-3 py-2 text-right">
                        <p class="text-[0.6875rem] font-medium text-tinta-samar">Nomor anggota</p>
                        <p class="angka mt-1 font-mono text-[1.25rem] leading-none font-semibold text-inti">
                            {{ $member->nomor_anggota ?? '—' }}
                        </p>
                    </div>
                </div>

                <dl class="grid gap-x-4 gap-y-3 border-b border-garis px-4 py-3.5 sm:grid-cols-2">
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Surel</dt>
                        <dd class="mt-0.5 text-[0.8125rem] break-words text-tinta">{{ $member->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Nomor telepon</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem] text-tinta">{{ $member->no_hp ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Tanggal lahir</dt>
                        <dd class="mt-0.5 text-[0.8125rem] text-tinta">
                            {{ $member->tanggal_lahir?->translatedFormat('d F Y') ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Alamat</dt>
                        <dd class="mt-0.5 text-[0.8125rem] text-tinta">{{ $member->alamat ?: '—' }}</dd>
                    </div>
                </dl>

                <div class="grid grid-cols-2 divide-x divide-garis border-b border-garis sm:grid-cols-4">
                    <div class="px-4 py-3">
                        <p class="text-[0.6875rem] font-medium text-tinta-samar">Total peminjaman</p>
                        <p class="angka mt-1 font-mono text-[1.125rem] font-semibold text-tinta">
                            {{ number_format($totalPinjaman, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="px-4 py-3">
                        <p class="text-[0.6875rem] font-medium text-tinta-samar">Reservasi</p>
                        <p class="angka mt-1 font-mono text-[1.125rem] font-semibold text-tinta">
                            {{ number_format($totalReservasi, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="px-4 py-3">
                        <p class="text-[0.6875rem] font-medium text-tinta-samar">Denda dibayar</p>
                        <p class="angka mt-1 font-mono text-[1.125rem] font-semibold text-inti">
                            Rp{{ number_format((int) $totalPembayaran, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="px-4 py-3">
                        <p class="text-[0.6875rem] font-medium text-tinta-samar">Sisa denda</p>
                        <p class="angka mt-1 font-mono text-[1.125rem] font-semibold {{ $sisaDenda > 0 ? 'text-bahaya' : 'text-inti' }}">
                            Rp{{ number_format($sisaDenda, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-3">
                    <p class="text-[0.75rem] text-tinta-samar">
                        Anggota terdaftar sejak {{ $member->created_at->translatedFormat('d F Y') }}.
                    </p>
                    <x-status-tag
                        :label="$member->status === 'aktif' ? 'Aktif' : 'Nonaktif'"
                        :warna="$member->status === 'aktif' ? 'inti' : 'bahaya'"
                    />
                </div>
            </section>

            <x-panel judul="Sedang dipinjam" :subjudul="$pinjamanAktif->count() . ' buku sedang berada di luar cakupan anggota.'" padat>
                @if ($pinjamanAktif->isEmpty())
                    <x-empty-state
                        judul="Tidak ada peminjaman berjalan"
                        pesan="Anggota ini sedang tidak memegang buku dari perpustakaan."
                        ikon="TIDAK MEMINJAM"
                    />
                @else
                    <div class="overflow-x-auto">
                        <table class="tabel-dasar">
                            <caption class="sr-only">Buku yang sedang dipinjam anggota</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Kode</th>
                                    <th scope="col">Judul</th>
                                    <th scope="col">Dipinjam</th>
                                    <th scope="col">Jatuh tempo</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pinjamanAktif as $pinjaman)
                                    <tr>
                                        <td class="font-mono text-[0.75rem] whitespace-nowrap text-tinta-lembut">
                                            <a href="{{ route('loans.show', $pinjaman) }}" class="hover:underline">
                                                {{ $pinjaman->kode }}
                                            </a>
                                        </td>
                                        <td class="max-w-56 truncate text-[0.8125rem]">
                                            {{ $pinjaman->book?->judul ?? 'Buku dihapus' }}
                                        </td>
                                        <td class="whitespace-nowrap text-[0.8125rem] text-tinta-lembut">
                                            {{ $pinjaman->dipinjam_at?->translatedFormat('d M Y') ?? '—' }}
                                        </td>
                                        <td class="whitespace-nowrap text-[0.8125rem] text-tinta-lembut">
                                            {{ $pinjaman->harus_kembali_at?->translatedFormat('d M Y') ?? '—' }}
                                        </td>
                                        <td>
                                            <x-loan-status :loan="$pinjaman" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-panel>

            <x-panel judul="Riwayat pengembalian" subjudul="Dua puluh transaksi terakhir anggota ini." padat>
                @if ($riwayat->isEmpty())
                    <x-empty-state
                        judul="Riwayat masih kosong"
                        pesan="Belum ada buku yang selesai dipinjam oleh anggota ini."
                        ikon="RIWAYAT"
                    />
                @else
                    <div class="overflow-x-auto">
                        <table class="tabel-dasar">
                            <caption class="sr-only">Riwayat pengembalian buku anggota</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Kode</th>
                                    <th scope="col">Judul</th>
                                    <th scope="col">Dikembalikan</th>
                                    <th scope="col" class="text-right">Denda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($riwayat as $pinjaman)
                                    <tr>
                                        <td class="font-mono text-[0.75rem] whitespace-nowrap text-tinta-lembut">
                                            <a href="{{ route('loans.show', $pinjaman) }}" class="hover:underline">
                                                {{ $pinjaman->kode }}
                                            </a>
                                        </td>
                                        <td class="max-w-64 truncate text-[0.8125rem]">
                                            {{ $pinjaman->book?->judul ?? 'Buku dihapus' }}
                                        </td>
                                        <td class="whitespace-nowrap text-[0.8125rem] text-tinta-lembut">
                                            {{ $pinjaman->dikembalikan_at?->translatedFormat('d M Y') ?? '—' }}
                                        </td>
                                        <td class="angka font-mono {{ $pinjaman->denda > 0 ? 'text-bahaya' : 'text-tinta-samar' }}">
                                            {{ $pinjaman->denda > 0 ? 'Rp' . number_format($pinjaman->denda, 0, ',', '.') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-panel>
        </div>

        <aside class="lg:col-span-1">
            <div class="sticky top-20 space-y-4">
                <x-panel judul="Ringkasan" subjudul="Angka yang sama dengan kartu di samping." padat>
                    <dl class="divide-y divide-garis text-[0.8125rem]">
                        <div class="flex items-center justify-between gap-2 px-4 py-2.5">
                            <dt class="text-tinta-lembut">Peran akun</dt>
                            <dd>
                                <x-status-tag
                                    :label="$member->role->label()"
                                    :warna="match ($member->role->value) {
                                        'admin' => 'inti',
                                        'petugas' => 'aksen',
                                        default => 'netral',
                                    }"
                                />
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-2 px-4 py-2.5">
                            <dt class="text-tinta-lembut">Sedang dipinjam</dt>
                            <dd class="angka font-mono">{{ $pinjamanAktif->count() }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-2 px-4 py-2.5">
                            <dt class="text-tinta-lembut">Sisa denda</dt>
                            <dd class="angka font-mono font-semibold {{ $sisaDenda > 0 ? 'text-bahaya' : 'text-inti' }}">
                                Rp{{ number_format($sisaDenda, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-2 px-4 py-2.5">
                            <dt class="text-tinta-lembut">Riwayat ditampilkan</dt>
                            <dd class="angka font-mono">{{ $riwayat->count() }}</dd>
                        </div>
                    </dl>

                    <div class="space-y-2 border-t border-garis px-4 py-3">
                        <x-button :href="route('members.show', $member)" varian="garis" ukuran="kecil" class="w-full">
                            Halaman anggota
                        </x-button>
                        @if ($member->role->value === 'anggota')
                            <x-button
                                :href="route('payments.create', ['user_id' => $member->id])"
                                varian="garis"
                                ukuran="kecil"
                                class="w-full"
                            >
                                Terima pembayaran
                            </x-button>
                        @endif
                    </div>
                </x-panel>

                <section aria-labelledby="catatan-kartu" class="rounded-xl border border-garis bg-permukaan-lembut/70 px-4 py-3.5">
                    <h2 id="catatan-kartu" class="text-[0.75rem] font-medium text-tinta-samar">
                        Catatan pencetakan
                    </h2>
                    <p class="mt-1 text-[0.75rem] text-tinta-samar">
                        Kartu ini berisi data pribadi anggota. Jangan dibagikan di luar loket perpustakaan.
                    </p>
                </section>
            </div>
        </aside>
    </div>
</x-layouts.app>
