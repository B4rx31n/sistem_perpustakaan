<x-layouts.app>
    @php
        $warna = match ($reservation->status->value) {
            'siap' => 'inti',
            'kedaluwarsa' => 'bahaya',
            'dibatalkan' => 'netral',
            default => 'aksen',
        };

        $bolehBatalkan = $reservation->status->value === 'menunggu' || $reservation->status->value === 'siap';
        $bolehBatal = $bolehBatalkan && (auth()->user()->isPetugas() || auth()->user()->is($reservation->user));
    @endphp

    <x-page-heading
        :judul="'Reservasi ' . $reservation->kode"
        :deskripsi="$reservation->book->judul . ' — ' . $reservation->user->name"
    >
        <x-button :href="route('reservations.index')" varian="garis" ukuran="kecil">Semua reservasi</x-button>
    </x-page-heading>


    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <section aria-labelledby="rincian-reservasi" class="kartu overflow-hidden">
                <h2 id="rincian-reservasi" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Rincian reservasi
                </h2>

                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 px-4 py-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Kode</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">{{ $reservation->kode }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Status</dt>
                        <dd class="mt-0.5">
                            <x-status-tag :label="$reservation->status->label()" :warna="$warna" />
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Antrean</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">ke-{{ $reservation->antrean }}</dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Dibuat</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">
                            {{ $reservation->created_at->translatedFormat('d M Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Berlaku sampai</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">
                            {{ $reservation->berlaku_sampai->translatedFormat('d M Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[0.75rem] font-medium text-tinta-samar">Selesai</dt>
                        <dd class="mt-0.5 font-mono text-[0.8125rem]">
                            {{ $reservation->diselesaikan_at?->translatedFormat('d M Y') ?? '—' }}
                        </dd>
                    </div>
                </dl>

                @if ($reservation->catatan)
                    <div class="border-t border-garis px-4 py-3">
                        <h3 class="text-[0.75rem] font-medium text-tinta-samar">Catatan</h3>
                        <p class="mt-1 text-[0.875rem] text-tinta-lembut">{{ $reservation->catatan }}</p>
                    </div>
                @endif
            </section>

            <section aria-labelledby="buku-dipesan" class="kartu overflow-hidden">
                <h2 id="buku-dipesan" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">Buku</h2>

                <div class="flex flex-wrap items-center gap-4 px-4 py-4">
                    @if ($reservation->book->cover_path)
                        <img src="{{ $reservation->book->coverUrl() }}"
                             alt="Sampul {{ $reservation->book->judul }}"
                             class="h-24 w-16 shrink-0 border border-garis object-cover">
                    @endif

                    <div class="min-w-0 flex-1">
                        <a href="{{ route('books.show', $reservation->book) }}"
                           class="text-[0.9375rem] font-medium text-tinta hover:underline">
                            {{ $reservation->book->judul }}
                        </a>
                        <p class="mt-0.5 text-[0.8125rem] text-tinta-lembut">{{ $reservation->book->penulis }}</p>
                        <p class="mt-1 font-mono text-[0.6875rem] text-tinta-samar">
                            ISBN {{ $reservation->book->isbn }}
                            @if ($reservation->book->category)
                                &middot; {{ $reservation->book->category->nama }}
                            @endif
                        </p>
                    </div>
                </div>
            </section>

            <section aria-labelledby="penjelasan-status" class="kartu overflow-hidden">
                <h2 id="penjelasan-status" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Apa artinya status ini
                </h2>

                <div class="px-4 py-3">
                    @php
                        $potongan = [
                            'menunggu' => 'Anggota masih menunggu buku dikembalikan oleh peminjam sebelumnya. Petugas akan menandai siap diambil saat buku tiba di loket.',
                            'siap' => 'Buku sudah tersedia dan menunggu diambil. Reservasi berlaku ' . config('perpustakaan.masa_berlaku_reservasi_hari') . ' hari sejak ditandai siap, lewat tanggal itu reservasi otomatis kedaluwarsa.',
                            'selesai' => 'Anggota sudah mengambil buku dan reservasi berubah menjadi peminjaman biasa.',
                            'kedaluwarsa' => 'Batas ambil sudah lewat tanpa diambil. Antrean diteruskan ke anggota berikutnya.',
                            'dibatalkan' => 'Reservasi dibatalkan sebelum buku diambil, sehingga tempat di antrean dilepas untuk anggota berikutnya.',
                        ];
                    @endphp

                    <p class="text-[0.875rem] text-tinta-lembut">
                        {{ $potongan[$reservation->status->value] ?? 'Status reservasi tidak dikenali.' }}
                    </p>
                </div>
            </section>
        </div>

        <aside class="space-y-5">
            <section aria-labelledby="pemilik-reservasi" class="kartu overflow-hidden">
                <h2 id="pemilik-reservasi" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Pemesan
                </h2>

                <div class="px-4 py-3">
                    <p class="text-[0.9375rem] font-medium">{{ $reservation->user->name }}</p>
                    <p class="font-mono text-[0.6875rem] text-tinta-samar">
                        {{ $reservation->user->nomor_anggota ?? 'tanpa nomor' }}
                    </p>

                    <dl class="mt-2.5 space-y-1 text-[0.75rem]">
                        @if ($reservation->user->no_hp)
                            <div class="flex justify-between gap-2">
                                <dt class="text-tinta-samar">Telepon</dt>
                                <dd class="font-mono text-right text-tinta">{{ $reservation->user->no_hp }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between gap-2">
                            <dt class="text-tinta-samar">Surel</dt>
                            <dd class="truncate text-right text-tinta">{{ $reservation->user->email }}</dd>
                        </div>
                    </dl>

                    @if (auth()->user()->isPetugas())
                        <x-button
                            :href="route('members.show', $reservation->user)"
                            varian="garis"
                            ukuran="kecil"
                            class="mt-3 w-full"
                        >Kartu anggota</x-button>
                    @endif
                </div>
            </section>

            @if ($bolehBatal)
                <section aria-labelledby="batalkan-reservasi" class="kartu overflow-hidden">
                    <h2 id="batalkan-reservasi" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                        Batalkan reservasi
                    </h2>

                    <div class="px-4 py-3">
                        <p class="text-[0.8125rem] text-tinta-lembut">
                            Setelah dibatalkan, anggota berikutnya naik satu tingkat antrean.
                        </p>

                        <form method="POST" action="{{ route('reservations.destroy', $reservation) }}" class="mt-2.5">
                            @csrf
                            @method('DELETE')

                            <label for="alasan-batal" class="block text-[0.8125rem] font-medium text-tinta">Alasan</label>
                            <input id="alasan-batal"
                                   name="alasan"
                                   value="{{ old('alasan') }}"
                                   placeholder="Opsional"
                                   class="mt-1 block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 placeholder:text-tinta-samar focus:border-inti focus:shadow-fokus focus:outline-none">

                            <x-button
                                type="submit"
                                varian="bahaya"
                                ukuran="kecil"
                                class="mt-2.5 w-full"
                                konfirmasi="Batalkan reservasi {{ $reservation->kode }}? Antrean anggota lain akan naik."
                            >Batalkan reservasi</x-button>
                        </form>
                    </div>
                </section>
            @else
                <section aria-labelledby="reservasi-tutup" class="kartu overflow-hidden">
                    <h2 id="reservasi-tutup" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                        Reservasi sudah ditutup
                    </h2>
                    <p class="px-4 py-3 text-[0.8125rem] text-tinta-lembut">
                        Status {{ strtolower($reservation->status->label()) }} tidak bisa dibatalkan lagi.
                    </p>
                </section>
            @endif
        </aside>
    </div>
</x-layouts.app>
