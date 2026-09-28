@php
    /** @var \App\Models\User|null $pengguna */
    $pengguna = auth()->user();

    /* Menu dikelompokkan supaya sidebar tidak jadi daftar panjang tanpa judul.
       Setiap kelompok hanya berisi satu atau dua tugas yang memang sering dibuka
       berurutan, jadi tidak perlu submenu. */
    $kelompok = [
        'Operasional' => [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'ikon' => 'dashboard', 'match' => 'dashboard'],
            ['route' => 'loans.index', 'label' => 'Peminjaman', 'ikon' => 'peminjaman', 'match' => 'loans.*', 'petugas' => true],
            ['route' => 'reservations.index', 'label' => 'Reservasi', 'ikon' => 'penanda', 'match' => 'reservations.*'],
        ],
        'Katalog' => [
            ['route' => 'books.index', 'label' => 'Buku', 'ikon' => 'buku', 'match' => 'books.*'],
            ['route' => 'categories.index', 'label' => 'Kategori', 'ikon' => 'kategori', 'match' => 'categories.*', 'admin' => true],
        ],
        'Anggota dan keuangan' => [
            ['route' => 'members.index', 'label' => 'Anggota', 'ikon' => 'anggota', 'match' => 'members.*', 'petugas' => true],
            ['route' => 'payments.index', 'label' => 'Pembayaran', 'ikon' => 'pembayaran', 'match' => 'payments.*', 'petugas' => true],
        ],
        'Pelaporan' => [
            ['route' => 'reports.index', 'label' => 'Laporan', 'ikon' => 'laporan', 'match' => 'reports.*', 'petugas' => true],
        ],
    ];

    $kelompok = array_map(function (array $item) use ($pengguna) {
        return array_values(array_filter($item, function (array $entri) use ($pengguna) {
            if (isset($entri['admin']) && ! $pengguna?->isAdmin()) {
                return false;
            }

            if (isset($entri['petugas']) && ! $pengguna?->isPetugas()) {
                return false;
            }

            return true;
        }));
    }, $kelompok);

    $kelompok = array_filter($kelompok);
@endphp

{{-- Sidebar. Lebar penuh saat terbuka dan hanya ikon saat dilipat; pilihan
     disimpan di localStorage oleh JS global. Label teks disembunyikan lewat
     aria-label supaya menu masih terbaca saat hanya ikon yang tampil. --}}
<div data-sidebar-menu class="flex h-full flex-col gap-4 overflow-y-auto px-3 py-4">
    <ul class="space-y-4">
        @foreach ($kelompok as $judulKelompok => $entri)
            <li>
                <p data-sidebar-kelompok class="px-3 pb-1.5 text-[0.6875rem] font-medium text-tinta-samar">
                    {{ $judulKelompok }}
                </p>

                <ul class="space-y-0.5">
                    @foreach ($entri as $item)
                        @php $aktif = request()->routeIs($item['match']); @endphp
                        <li>
                            <a href="{{ route($item['route']) }}"
                               @if ($aktif) aria-current="page" @endif
                               title="{{ $item['label'] }}"
                               aria-label="{{ $item['label'] }}"
                               class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-[0.875rem] transition-[background-image,background-color,color,border-color] duration-150 {{ $aktif
                                   ? 'border border-pastel-3 bg-grad-inti font-medium text-inti-kunci'
                                   : 'text-tinta-lembut hover:bg-permukaan-lembut hover:text-tinta' }}">
                                <x-icon :nama="$item['ikon']" />
                                <span data-sidebar-label class="truncate">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>

    @auth
        <div class="mt-auto space-y-2 border-t border-garis pt-3">
            <a href="{{ route('profile.edit') }}"
               title="Data profil"
               aria-label="Data profil"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[0.875rem] text-tinta-lembut transition-[background-color,color] duration-150 hover:bg-permukaan-lembut hover:text-tinta">
                <x-icon nama="profil" />
                <span data-sidebar-label class="truncate">Data profil</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        title="Keluar dari sistem"
                        aria-label="Keluar dari sistem"
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-[0.875rem] text-bahaya transition-colors duration-150 hover:bg-bahaya-lembut">
                    <x-icon nama="keluar" />
                    <span data-sidebar-label class="truncate">Keluar dari sistem</span>
                </button>
            </form>

            <p data-sidebar-label class="px-3 pt-1 font-mono text-[0.625rem] text-tinta-samar">
                PN &middot; {{ config('perpustakaan.durasi_peminjaman_hari') }} hari
            </p>
        </div>
    @endauth
</div>
