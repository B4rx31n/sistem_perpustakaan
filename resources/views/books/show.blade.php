<x-layouts.app>
    @php
        $tersedia = $book->jumlahTersedia();
        $dipinjam = $book->jumlahDipinjam();
        $riwayat = $book->relationLoaded('loans') ? $book->loans : collect();
    @endphp

    <x-page-heading
        :judul="$book->judul"
        :deskripsi="$book->penulis . ($book->tahun_terbit ? ', ' . $book->tahun_terbit : '') . ($book->penerbit ? ' — ' . $book->penerbit : '')"
    >
        @if ($book->category)
            <a href="{{ route('books.index', ['kategori' => $book->category_id]) }}"
               class="rounded-lg border border-garis bg-permukaan px-3 py-2 text-[0.75rem] text-tinta shadow-halus transition-[background-color,border-color,transform] duration-150 hover:border-garis-kuat hover:bg-permukaan-lembut active:scale-[0.98]">
                {{ $book->category->nama }}
            </a>
        @endif

        @can('update', $book)
            <x-button :href="route('books.edit', $book)" varian="garis" ukuran="kecil">Ubah data</x-button>
        @endcan

        @if ($tersedia > 0)
            <x-button :href="route('loans.create', ['book_id' => $book->id])" ukuran="kecil">Catat peminjaman</x-button>
        @endif
    </x-page-heading>


    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <section aria-labelledby="rincian-buku" class="kartu overflow-hidden">
                <h2 id="rincian-buku" class="sr-only">Rincian buku</h2>

                @if ($book->cover_path)
                    <img src="{{ $book->coverUrl() }}"
                         alt="Sampul {{ $book->judul }}"
                         class="max-h-72 w-full border-b border-garis object-cover">
                @endif

                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 px-4 py-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">ISBN</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">{{ $book->isbn }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Penulis</dt>
                        <dd class="mt-0.5 text-[0.8125rem]">{{ $book->penulis }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Penerbit</dt>
                        <dd class="mt-0.5 text-[0.8125rem]">{{ $book->penerbit ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Tahun terbit</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">{{ $book->tahun_terbit ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Eksemplar</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">{{ $book->stok }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Masuk katalog</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">{{ $book->created_at->translatedFormat('d M Y') }}</dd>
                    </div>
                </dl>

                @if ($book->deskripsi)
                    <div class="border-t border-garis px-4 py-3">
                        <h3 class="text-[0.75rem] font-medium text-tinta-samar">Deskripsi</h3>
                        <p class="mt-1 text-[0.875rem] text-tinta-lembut">{{ $book->deskripsi }}</p>
                    </div>
                @endif
            </section>

            <section aria-labelledby="riwayat-peminjaman" class="kartu overflow-hidden">
                <div class="kepala-kartu">
                    <h2 id="riwayat-peminjaman" class="text-[0.875rem] font-semibold">Riwayat peminjaman terakhir</h2>
                </div>

                @if ($riwayat->isEmpty())
                    <x-empty-state
                        judul="Buku ini belum pernah dipinjam"
                        pesan="Riwayat akan terisi begitu judul masuk daftar peminjaman."
                        ikon="RIWAYAT"
                    />
                @else
                    <div class="overflow-x-auto">
                        <table class="tabel-dasar">
                            <caption class="sr-only">Sepuluh peminjaman terakhir untuk buku ini</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Kode</th>
                                    <th scope="col">Anggota</th>
                                    <th scope="col">Dipinjam</th>
                                    <th scope="col">Kembali</th>
                                    <th scope="col" class="text-right">Denda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($riwayat as $pinjaman)
                                    <tr>
                                        <td class="font-mono text-[0.75rem] whitespace-nowrap">
                                            <a href="{{ route('loans.show', $pinjaman) }}" class="text-inti hover:underline">
                                                {{ $pinjaman->kode }}
                                            </a>
                                        </td>
                                        <td class="max-w-40 truncate">{{ $pinjaman->user?->name ?? '—' }}</td>
                                        <td class="whitespace-nowrap font-mono text-[0.75rem] text-tinta-lembut">
                                            {{ $pinjaman->dipinjam_at->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="whitespace-nowrap font-mono text-[0.75rem] text-tinta-lembut">
                                            {{ $pinjaman->dikembalikan_at?->translatedFormat('d M Y') ?? ' masih dipinjam' }}
                                        </td>
                                        <td class="angka whitespace-nowrap font-mono">
                                            @if ($pinjaman->denda > 0)
                                                <span class="font-semibold text-bahaya">
                                                    Rp{{ number_format($pinjaman->denda, 0, ',', '.') }}
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

        <div class="space-y-5">
            <section aria-labelledby="ketersediaan-buku" class="kartu overflow-hidden">
                <h2 id="ketersediaan-buku" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Ketersediaan
                </h2>

                <div class="grid grid-cols-3 divide-x divide-garis border-b border-garis text-center">
                    <div class="px-2 py-3">
                        <p class="font-mono text-[1.25rem] leading-none font-semibold">{{ $book->stok }}</p>
                        <p class="mt-1 text-[0.6875rem] text-tinta-samar">Eksemplar</p>
                    </div>
                    <div class="px-2 py-3">
                        <p class="font-mono text-[1.25rem] leading-none font-semibold text-tinta-lembut">{{ $dipinjam }}</p>
                        <p class="mt-1 text-[0.6875rem] text-tinta-samar">Dipinjam</p>
                    </div>
                    <div class="px-2 py-3">
                        <p class="angka font-mono text-[1.25rem] leading-none font-semibold text-inti">{{ $tersedia }}</p>
                        <p class="mt-1 text-[0.6875rem] text-tinta-samar">Tersedia</p>
                    </div>
                </div>

                <div class="px-4 py-3">
                    @if ($tersedia > 0)
                        <p class="text-[0.8125rem] text-tinta-lembut">
                            Buku bisa dipinjam langsung di loket. Masa pinjam
                            {{ config('perpustakaan.durasi_peminjaman_hari') }} hari.
                        </p>
                    @else
                        <p class="text-[0.8125rem] text-tinta-lembut">
                            Semua eksemplar sedang keluar. Pesan antrean agar librarians
                            mengabari saat buku tersedia.
                        </p>

                        <form method="POST" action="{{ route('reservations.store') }}" class="mt-2.5">
                            @csrf
                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                            <x-button type="submit" ukuran="kecil" class="w-full">Pesan buku ini</x-button>
                        </form>
                    @endif
                </div>
            </section>

            <section aria-labelledby="antrean-reservasi" class="kartu overflow-hidden">
                <div class="kepala-kartu">
                    <h2 id="antrean-reservasi" class="text-[0.875rem] font-semibold">Antrean reservasi</h2>
                </div>

                @if ($reservasiAktif->isEmpty())
                    <p class="px-4 py-3 text-[0.8125rem] text-tinta-lembut">Belum ada antrean untuk judul ini.</p>
                @else
                    <ol class="divide-y divide-garis">
                        @foreach ($reservasiAktif as $reservasi)
                            <li class="flex items-center justify-between gap-3 px-4 py-2.5">
                                <div class="min-w-0">
                                    <p class="truncate text-[0.8125rem]">{{ $reservasi->user?->name ?? '—' }}</p>
                                    <p class="text-[0.6875rem] text-tinta-samar">
                                        Berlaku sampai {{ $reservasi->berlaku_sampai->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <span class="font-mono text-[0.75rem] text-tinta-lembut">
                                        #{{ $reservasi->antrean }}
                                    </span>
                                    <x-status-tag
                                        :label="$reservasi->status->label()"
                                        :warna="$reservasi->status->value === 'siap' ? 'inti' : 'netral'"
                                    />
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>

            @can('delete', $book)
                <section aria-labelledby="hapus-buku" class="border border-bahaya bg-permukaan">
                    <h2 id="hapus-buku" class="border-b border-bahaya px-4 py-3 text-[0.875rem] font-semibold text-bahaya">
                        Hapus dari katalog
                    </h2>
                    <div class="px-4 py-3">
                        <p class="text-[0.8125rem] text-tinta-lembut">
                            Judul hanya bisa dihapus bila sedang tidak dipinjam siapa pun.
                        </p>
                        <x-button
                            :href="route('books.destroy', $book)"
                            method="DELETE"
                            varian="bahaya"
                            ukuran="kecil"
                            konfirmasi='Hapus &quot;{{ $book->judul }}&quot; dari katalog? Riwayat peminjaman ikut terhapus.'
                            class="mt-2.5 w-full"
                        >Hapus judul ini</x-button>
                    </div>
                </section>
            @endcan
        </div>
    </div>
</x-layouts.app>
