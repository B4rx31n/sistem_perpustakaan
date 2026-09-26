<x-layouts.app>
    @php
        $metodeOpsi = collect($metode)->mapWithKeys(fn ($item) => [$item->value => $item->label()]);

        $pinjamanOpsi = $pinjamanBerutang->mapWithKeys(
            fn ($pinjaman) => [$pinjaman->id => $pinjaman->kode . ' — denda Rp' . number_format($pinjaman->denda, 0, ',', '.')]
        );
    @endphp

    <x-page-heading
        judul="Terima pembayaran denda"
        deskripsi="Pembayaran dicatat terhadap anggota, lalu ikut mengurangi sisa tagihan dendanya."
    >
        <x-button :href="route('payments.index')" varian="garis" ukuran="kecil">Riwayat pembayaran</x-button>
        <x-button :href="route('payments.receivables')" varian="garis" ukuran="kecil">Tagihan belum lunas</x-button>
    </x-page-heading>


    <form method="GET" action="{{ route('payments.create') }}" class="mb-4 flex flex-wrap items-end gap-2 border border-garis bg-permukaan p-3">
        <div class="min-w-0 flex-1">
            <label for="pilih-anggota-bayar" class="block text-[0.8125rem] font-medium text-tinta">
                Cari anggota lebih dulu
            </label>
            <select id="pilih-anggota-bayar"
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
            <x-panel judul="Data pembayaran" subjudul="Nominal tidak boleh melebihi sisa tagihan anggota.">
                <form method="POST" action="{{ route('payments.store') }}" class="space-y-4">
                    @csrf
                    <x-form-select
                        nama="user_id"
                        label="Anggota"
                        wajib
                        :nilai="old('user_id', $anggotaTerpilih?->id)"
                        :pilihan="$anggota->mapWithKeys(fn ($item) => [
                            $item->id => ($item->nomor_anggota ?? 'tanpa nomor') . ' — ' . $item->name,
                        ])"
                        kosong="Pilih anggota peminjam"
                        bantuan="Pilih anggota lewat form di atas supaya sisa dendanya terlihat dulu."
                    />

                    <x-form-select
                        nama="loan_id"
                        label="Peminjaman yang dibayar"
                        :nilai="old('loan_id')"
                        :pilihan="$pinjamanOpsi"
                        kosong="Tanpa menunjuk peminjaman tertentu"
                        bantuan="Kosongkan kalau pembayaran hanya mengurangi total tagihan anggota."
                    />

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-form-input
                            nama="jumlah"
                            label="Jumlah pembayaran"
                            tipe="number"
                            wajib
                            min="1"
                            step="1000"
                            placeholder="10000"
                            :nilai="old('jumlah', $sisaDendaTerpilih ?: null)"
                            bantuan="Dalam rupiah penuh, tanpa titik."
                        />

                        <x-form-select
                            nama="metode"
                            label="Metode pembayaran"
                            wajib
                            :nilai="old('metode')"
                            :pilihan="$metodeOpsi"
                            kosong="Pilih metode pembayaran"
                        />
                    </div>

                    <x-form-input
                        nama="bukti"
                        label="Nomor bukti"
                        placeholder="TRX-2026-0001"
                        bantuan="Isi nomor transfer atau nota kasir. Kosongkan untuk pembayaran tunai."
                    />

                    <x-form-textarea
                        nama="keterangan"
                        label="Keterangan"
                        :baris="2"
                        placeholder="Catatan yang perlu diingat petugas, misalnya cicilan sebagian."
                    />

                    <div class="rounded-xl border border-garis bg-permukaan-lembut/70 px-3.5 py-3">
                        <p class="text-[0.75rem] font-semibold text-tinta-lembut">Catatan pencatatan</p>
                        <ul class="mt-1 space-y-0.5 text-[0.75rem] text-tinta-samar">
                            <li>Nominal boleh lebih kecil dari sisa tagihan untuk pembayaran bertahap.</li>
                            <li>Setiap pembayaran langsung masuk ke halaman riwayat dan laporan.</li>
                        </ul>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-garis pt-4">
                        <x-button :href="route('payments.index')" varian="garis">Batal</x-button>
                        <x-button type="submit">Catat pembayaran</x-button>
                    </div>
                </form>
            </x-panel>
        </div>

        <aside class="lg:col-span-1">
            @if ($anggotaTerpilih)
                <div class="sticky top-20 space-y-4">
                    <x-panel judul="Ringkasan tagihan" subjudul="Semua sisa denda anggota yang tercatat." padat>
                        <div>
                            <h2 class="sr-only">
                                {{ $anggotaTerpilih->name }}
                            </h2>
                            <p class="font-mono text-[0.6875rem] text-tinta-samar">
                                {{ $anggotaTerpilih->nomor_anggota ?? 'tanpa nomor' }}
                                @if ($anggotaTerpilih->no_hp)
                                    &middot; {{ $anggotaTerpilih->no_hp }}
                                @endif
                            </p>
                        </div>

                        <dl class="divide-y divide-garis text-[0.8125rem]">
                            <div class="flex items-center justify-between px-4 py-2">
                                <dt class="text-tinta-lembut">Sisa tagihan</dt>
                                <dd class="angka font-mono font-semibold {{ $sisaDendaTerpilih > 0 ? 'text-bahaya' : 'text-inti' }}">
                                    Rp{{ number_format($sisaDendaTerpilih, 0, ',', '.') }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between px-4 py-2">
                                <dt class="text-tinta-lembut">Peminjaman berkewajiban</dt>
                                <dd class="angka font-mono">{{ $pinjamanBerutang->count() }}</dd>
                            </div>
                        </dl>

                        <div class="border-t border-garis px-4 py-3">
                            <x-button
                                :href="route('members.show', $anggotaTerpilih)"
                                varian="garis"
                                ukuran="kecil"
                                class="w-full"
                            >Buka halaman anggota</x-button>
                        </div>
                    </x-panel>

                    @if ($pinjamanBerutang->isNotEmpty())
                        <x-panel judul="Peminjaman belum dibayar" :subjudul="$pinjamanBerutang->count() . ' pinjaman menyumbang sisa denda di atas.'" padat>
                            <ul class="divide-y divide-garis">
                                @foreach ($pinjamanBerutang as $pinjaman)
                                    <li class="flex items-center justify-between gap-2 px-4 py-2.5">
                                        <a href="{{ route('loans.show', $pinjaman) }}"
                                           class="font-mono text-[0.75rem] text-tinta-lembut hover:underline">
                                            {{ $pinjaman->kode }}
                                        </a>
                                        <span class="angka font-mono text-[0.8125rem] font-semibold">
                                            Rp{{ number_format($pinjaman->denda, 0, ',', '.') }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </x-panel>
                    @else
                        <x-panel judul="Tidak ada tagihan" subjudul="Anggota ini tidak punya denda yang perlu dibayar." padat>
                            <x-empty-state
                                judul="Semua lunas"
                                pesan="Tidak ada sisa denda untuk anggota terpilih."
                                ikon="LUNAS"
                            />
                        </x-panel>
                    @endif
                </div>
            @else
                <div class="kartu overflow-hidden">
                    <x-empty-state
                        judul="Anggota belum dipilih"
                        pesan="Pilih anggota di form bagian atas untuk melihat sisa tagihan dan peminjaman yang perlu dilunasi."
                        ikon="ANGGOTA"
                    />
                </div>
            @endif
        </aside>
    </div>
</x-layouts.app>
