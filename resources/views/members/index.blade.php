<x-layouts.app>
    @php
        $roleOpsi = [
            '' => 'Semua peran',
            'admin' => 'Administrator',
            'petugas' => 'Petugas',
            'anggota' => 'Anggota',
        ];

        $statusOpsi = [
            '' => 'Semua status',
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
        ];
    @endphp

    <x-page-heading
        judul="Anggota"
        :deskripsi="number_format($totalAnggota, 0, ',', '.') . ' anggota terdaftar, ' . number_format($totalAktif, 0, ',', '.') . ' berstatus aktif.'"
    />


    <section aria-labelledby="ringkasan-anggota-petugas" class="mb-4">
        <h2 id="ringkasan-anggota-petugas" class="sr-only">Ringkasan keanggotaan</h2>
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <x-statistik label="Total anggota" :nilai="number_format($totalAnggota, 0, ',', '.')" />
            <x-statistik label="Aktif" :nilai="number_format($totalAktif, 0, ',', '.')" />
            <x-statistik
                label="Punya tagihan"
                :nilai="number_format($jumlahBerutang, 0, ',', '.')"
                :keterangan="$jumlahBerutang > 0 ? 'Denda belum lunas' : 'Tidak ada tunggakan'"
            />
            <x-statistik
                label="Nilai piutang"
                :nilai="'Rp' . number_format($totalPiutang, 0, ',', '.')"
            />
        </div>
    </section>

    <form method="GET" action="{{ route('members.index') }}"
          class="mb-4 grid gap-2.5 border border-garis bg-permukaan p-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="lg:col-span-2">
            <label for="cari-anggota" class="sr-only">Cari nama, nomor anggota, atau surel</label>
            <input id="cari-anggota"
                   name="q"
                   type="search"
                   value="{{ request('q') }}"
                   placeholder="Cari nama, nomor anggota, atau surel"
                   data-cari-otomatis
                   class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 placeholder:text-tinta-samar focus:border-inti focus:shadow-fokus focus:outline-none">
        </div>

        <div>
            <label for="filter-peran" class="sr-only">Peran</label>
            <select id="filter-peran"
                    name="role"
                    class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                @foreach ($roleOpsi as $nilai => $teks)
                    <option value="{{ $nilai }}" @selected(request('role') === $nilai)>{{ $teks }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <div class="min-w-0 flex-1">
                <label for="filter-status-anggota" class="sr-only">Status akun</label>
                <select id="filter-status-anggota"
                        name="status"
                        class="block w-full rounded-lg border border-garis bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
                    @foreach ($statusOpsi as $nilai => $teks)
                        <option value="{{ $nilai }}" @selected(request('status') === $nilai)>{{ $teks }}</option>
                    @endforeach
                </select>
            </div>
            <x-button type="submit" ukuran="kecil">Terapkan</x-button>
        </div>
    </form>

    @if ($anggota->isEmpty())
        <x-empty-state
            judul="Tidak ada anggota yang cocok"
            pesan="Coba kata kunci lain atau longgarkan filter peran dan status."
            ikon="TIDAK DITEMUKAN"
        >
            <x-button :href="route('members.index')" varian="garis" ukuran="kecil">Bersihkan filter</x-button>
        </x-empty-state>
    @else
        <x-panel judul="Daftar anggota" subjudul="Semua akun anggota, petugas, dan administrator." padat>
            <div class="overflow-x-auto">
                <table class="tabel-dasar">
                    <caption class="sr-only">Daftar anggota dan petugas</caption>
                    <thead>
                        <tr>
                            <th scope="col">Nomor</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Surel</th>
                            <th scope="col">Program studi</th>
                            <th scope="col">Peran</th>
                            <th scope="col" class="text-right">Pinjaman</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($anggota as $orang)
                            <tr>
                                <td class="font-mono text-[0.75rem] whitespace-nowrap text-tinta-lembut">
                                    {{ $orang->nomor_anggota ?? '—' }}
                                </td>
                                <td class="max-w-48">
                                    <a href="{{ route('members.show', $orang) }}"
                                       class="block truncate text-[0.8125rem] font-medium text-tinta hover:underline">
                                        {{ $orang->name }}
                                    </a>
                                    @if ($orang->no_hp)
                                        <span class="block font-mono text-[0.6875rem] text-tinta-samar">{{ $orang->no_hp }}</span>
                                    @endif
                                </td>
                                <td class="max-w-48 truncate text-[0.8125rem] text-tinta-lembut">
                                    {{ $orang->email }}
                                </td>
                                <td class="max-w-40 truncate text-[0.8125rem]">
                                    {{ $orang->program_studi ?: '—' }}
                                </td>
                                <td>
                                    <x-status-tag
                                        :label="$orang->role->label()"
                                        :warna="match ($orang->role->value) {
                                            'admin' => 'inti',
                                            'petugas' => 'aksen',
                                            default => 'netral',
                                        }"
                                    />
                                </td>
                                <td class="angka font-mono">{{ $orang->loans_aktif }}</td>
                                <td>
                                    <x-status-tag
                                        :label="$orang->status === 'aktif' ? 'Aktif' : 'Nonaktif'"
                                        :warna="$orang->status === 'aktif' ? 'inti' : 'bahaya'"
                                    />
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-button :href="route('members.show', $orang)" varian="garis" ukuran="kecil">
                                            Detail
                                        </x-button>
                                        <x-button :href="route('members.card', $orang)" varian="garis" ukuran="kecil">
                                            Kartu
                                        </x-button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-3 pb-3">
                <x-pagination :paginator="$anggota" />
            </div>
        </x-panel>
    @endif
</x-layouts.app>
