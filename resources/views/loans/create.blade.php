<x-layouts.app>
    @php
        /** @var \Illuminate\Support\Collection $anggota */
        /** @var \Illuminate\Support\Collection $buku */
        $bukuOpsi = $buku->mapWithKeys(
            fn ($item) => [$item->id => $item->judul . ' — ' . $item->jumlahTersedia() . ' tersedia']
        );

        $pinjamanAnggotaTerpilih = $anggotaTerpilih?->activeLoans ?? collect();
        $sisaDenda = $memberService->sisaDenda($anggotaTerpilih ?? new \App\Models\User());
    @endphp

    <x-page-heading
        judul="Pencatatan peminjaman"
        deskripsi="Masa pinjam {{ config('perpustakaan.durasi_peminjaman_hari') }} hari, maksimum {{ config('perpustakaan.maks_perpanjangan') }} kali perpanjangan."
    >
        <x-button :href="route('loans.index')" varian="garis" ukuran="kecil">Daftar peminjaman</x-button>
    </x-page-heading>


    <form method="GET" action="{{ route('loans.create') }}" class="mb-4 flex flex-wrap items-end gap-2 border border-garis bg-permukaan p-3">
        <div class="min-w-0 flex-1">
            <label for="pilih-anggota-awal" class="block text-[0.8125rem] font-medium text-tinta">
                Cari anggota lebih dulu
            </label>
            <select id="pilih-anggota-awal"
                    name="user_id"
                    onchange="this.form.requestSubmit()"
                    class="mt-1 block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                <option value="">Pilih nomor anggota atau nama</option>
                @foreach ($anggota as $item)
                    <option value="{{ $item->id }}" @selected((int) request('user_id') === $item->id)>
                        {{ $item->nomor_anggota ?? 'tanpa nomor' }} — {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <x-button type="submit" varian="garis">Tampilkan</x-button>
    </form>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('loans.store') }}" class="kartu overflow-hidden">
                @csrf

                <div class="kepala-kartu">
                    <h2 class="text-[0.875rem] font-semibold">Data peminjaman</h2>
                </div>

                <div class="space-y-4 px-4 py-4">
                    <x-form-select
                        nama="user_id"
                        label="Anggota"
                        wajib
                        :nilai="old('user_id', $anggotaTerpilih?->id)"
                        :pilihan="$anggota->mapWithKeys(fn ($item) => [
                            $item->id => ($item->nomor_anggota ?? 'tanpa nomor') . ' — ' . $item->name,
                        ])"
                        kosong="Pilih anggota peminjam"
                        bantuan="Cari anggota lewat form di atas agar buku yang sedang dipinjamnya terlihat dulu."
                    />

                    <x-form-select
                        nama="book_id"
                        label="Buku"
                        wajib
                        :nilai="old('book_id', request('book_id'))"
                        :pilihan="$bukuOpsi"
                        kosong="Pilih judul yang stoknya tersedia"
                    />

                    <x-form-textarea
                        nama="catatan"
                        label="Catatan"
                        :baris="2"
                        placeholder="Kondisi buku saat diterima, atau hal lain yang perlu diingat."
                    />

                    <div class="rounded-xl border border-garis bg-permukaan-lembut/70 px-3.5 py-3">
                        <p class="text-[0.75rem] font-semibold text-tinta-lembut">Ketentuan peminjaman</p>
                        <ul class="mt-1 space-y-0.5 text-[0.75rem] text-tinta-samar">
                            <li>Denda keterlambatan Rp{{ number_format(config('perpustakaan.denda_per_hari'), 0, ',', '.') }} per hari, maksimal Rp{{ number_format(config('perpustakaan.denda_maks_per_peminjaman'), 0, ',', '.') }}.</li>
                            <li>Anggota dengan sisa denda belum nol tidak bisa meminjam.</li>
                            <li>Satu judul hanya bisa dipinjam satu eksemplar per anggota.</li>
                        </ul>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-garis px-4 py-3">
                    <x-button :href="route('loans.index')" varian="garis">Batal</x-button>
                    <x-button type="submit">Catat peminjaman</x-button>
                </div>
            </form>
        </div>

        <aside class="lg:col-span-1">
            @if ($anggotaTerpilih)
                <div class="sticky top-20 space-y-4">
                    <section aria-labelledby="ringkasan-anggota-terpilih" class="kartu overflow-hidden">
                        <div class="kepala-kartu">
                            <h2 id="ringkasan-anggota-terpilih" class="text-[0.875rem] font-semibold">
                                {{ $anggotaTerpilih->name }}
                            </h2>
                            <p class="font-mono text-[0.6875rem] text-tinta-samar">
                                {{ $anggotaTerpilih->nomor_anggota ?? 'tanpa nomor' }}
                                @if ($anggotaTerpilih->program_studi)
                                    &middot; {{ $anggotaTerpilih->program_studi }}
                                @endif
                            </p>
                        </div>

                        <dl class="divide-y divide-garis text-[0.8125rem]">
                            <div class="flex items-center justify-between px-4 py-2">
                                <dt class="text-tinta-lembut">Sedang dipinjam</dt>
                                <dd class="angka font-mono">{{ $pinjamanAnggotaTerpilih->count() }}</dd>
                            </div>
                            <div class="flex items-center justify-between px-4 py-2">
                                <dt class="text-tinta-lembut">Sisa denda</dt>
                                <dd class="angka font-mono {{ $sisaDenda > 0 ? 'font-semibold text-bahaya' : 'text-inti' }}">
                                    Rp{{ number_format($sisaDenda, 0, ',', '.') }}
                                </dd>
                            </div>
                        </dl>

                        @if ($sisaDenda > 0)
                            <div class="border-t border-garis px-4 py-3">
                                <p class="text-[0.8125rem] text-bahaya">
                                    Denda belum lunas, peminjaman baru akan ditolak sistem.
                                </p>
                                <x-button
                                    :href="route('payments.create', ['user_id' => $anggotaTerpilih->id])"
                                    varian="garis"
                                    ukuran="kecil"
                                    class="mt-2 w-full"
                                >Terima pembayaran denda</x-button>
                            </div>
                        @endif
                    </section>

                    @if ($pinjamanAnggotaTerpilih->isNotEmpty())
                        <section aria-labelledby="buku-sedang-dipinjam" class="kartu overflow-hidden">
                            <h2 id="buku-sedang-dipinjam" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                                Buku yang sedang dipinjam
                            </h2>
                            <ul class="divide-y divide-garis">
                                @foreach ($pinjamanAnggotaTerpilih as $pinjaman)
                                    <li class="px-4 py-2.5">
                                        <p class="truncate text-[0.8125rem]">{{ $pinjaman->book->judul }}</p>
                                        <p class="mt-0.5 font-mono text-[0.6875rem] text-tinta-samar">
                                            Kembali {{ $pinjaman->harus_kembali_at->translatedFormat('d M Y') }}
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                </div>
            @else
                <div class="kartu overflow-hidden">
                    <x-empty-state
                        judul="Anggota belum dipilih"
                        pesan="Pilih anggota di form bagian atas untuk melihat buku yang sedang dipinjam dan sisa dendanya."
                        ikon="ANGGOTA"
                    />
                </div>
            @endif
        </aside>
    </div>
</x-layouts.app>
