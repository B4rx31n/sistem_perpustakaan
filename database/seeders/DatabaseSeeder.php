<?php

namespace Database\Seeders;

use App\Enums\LoanStatus;
use App\Enums\PaymentMethod;
use App\Enums\ReservationStatus;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Services\KodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    /**
     * Daftar judul dan penulis nyata, dikelompokkan ke kategori katalog.
     *
     * ISBN sengaja dibangkitkan berurutan sebagai data contoh, bukan ISBN asli,
     * supaya tidak disalahbaca sebagai katalog produksi.
     *
     * @var list<array{judul: string, penulis: string, penerbit: string|null, tahun: int, kategori: string}>
     */
    private const DAFTAR_BUKU = [
        ['judul' => 'Bumi Manusia', 'penulis' => 'Pramoedya Ananta Toer', 'penerbit' => 'Lentera Mali', 'tahun' => 1988, 'kategori' => 'Fiksi'],
        ['judul' => 'Rumah Kaca', 'penulis' => 'Pramoedya Ananta Toer', 'penerbit' => 'Lentera Mali', 'tahun' => 1988, 'kategori' => 'Fiksi'],
        ['judul' => 'Gajah Putih', 'penulis' => 'Pramoedya Ananta Toer', 'penerbit' => 'Lentera Mali', 'tahun' => 1993, 'kategori' => 'Fiksi'],
        ['judul' => 'Laskar Pelangi', 'penulis' => 'Andrea Hirata', 'penerbit' => 'Bentang Pustaka', 'tahun' => 2005, 'kategori' => 'Fiksi'],
        ['judul' => 'Sang Pemimpi', 'penulis' => 'Andrea Hirata', 'penerbit' => 'Bentang Pustaka', 'tahun' => 2006, 'kategori' => 'Fiksi'],
        ['judul' => 'Maryamah Karpov', 'penulis' => 'Andrea Hirata', 'penerbit' => 'Bentang Pustaka', 'tahun' => 2008, 'kategori' => 'Fiksi'],
        ['judul' => 'Dilan 1990', 'penulis' => 'Pidi Baiq', 'penerbit' => 'M&C', 'tahun' => 2009, 'kategori' => 'Fiksi'],
        ['judul' => 'Dilan 1991', 'penulis' => 'Pidi Baiq', 'penerbit' => 'M&C', 'tahun' => 2010, 'kategori' => 'Fiksi'],
        ['judul' => 'Ronggeng Dukuh Paruk', 'penulis' => 'Ahmad Tohari', 'penerbit' => null, 'tahun' => 2009, 'kategori' => 'Fiksi'],
        ['judul' => 'Kentang Goreng untuk Ansible', 'penulis' => 'Eka Kurniawan', 'penerbit' => 'Gramedia', 'tahun' => 2015, 'kategori' => 'Fiksi'],
        ['judul' => 'Pulang', 'penulis' => 'Tere Liye', 'penerbit' => 'Gramedia', 'tahun' => 2015, 'kategori' => 'Fiksi'],
        ['judul' => 'Sapiens: Sejarah Ringkas Umat Manusia', 'penulis' => 'Yuval Noah Harari', 'penerbit' => 'Kanisius', 'tahun' => 2017, 'kategori' => 'Sains'],
        ['judul' => '21 Lessons for the 21st Century', 'penulis' => 'Yuval Noah Harari', 'penerbit' => 'Kanisius', 'tahun' => 2018, 'kategori' => 'Sains'],
        ['judul' => 'Homo Deus', 'penulis' => 'Yuval Noah Harari', 'penerbit' => 'Kanisius', 'tahun' => 2019, 'kategori' => 'Sains'],
        ['judul' => 'The Psychology of Money', 'penulis' => 'Morgan Housel', 'penerbit' => 'Kanisius', 'tahun' => 2021, 'kategori' => 'Ekonomi'],
        ['judul' => 'The Richest Man in Babylon', 'penulis' => 'George S. Clason', 'penerbit' => 'Bentang Pustaka', 'tahun' => 2017, 'kategori' => 'Ekonomi'],
        ['judul' => 'The Intelligent Investor', 'penulis' => 'Benjamin Graham', 'penerbit' => 'Kanisius', 'tahun' => 2014, 'kategori' => 'Ekonomi'],
        ['judul' => 'Deep Work', 'penulis' => 'Cal Newport', 'penerbit' => 'Elex Media Komputindo', 'tahun' => 2017, 'kategori' => 'Nonfiksi'],
        ['judul' => 'Make Time', 'penulis' => 'Jake Knapp', 'penerbit' => 'Kanisius', 'tahun' => 2019, 'kategori' => 'Nonfiksi'],
        ['judul' => 'Atomic Habits', 'penulis' => 'James Clear', 'penerbit' => 'Bentang Pustaka', 'tahun' => 2019, 'kategori' => 'Nonfiksi'],
        ['judul' => 'A Walk in the Park', 'penulis' => 'H.K. Rowbotham', 'penerbit' => 'Gramedia', 'tahun' => 2018, 'kategori' => 'Nonfiksi'],
        ['judul' => 'Clean Code: A Handbook of Agile Software Craftsmanship', 'penulis' => 'Robert C. Martin', 'penerbit' => 'Elex Media Komputindo', 'tahun' => 2008, 'kategori' => 'Teknologi'],
        ['judul' => 'Refactoring: Improving the Design of Existing Code', 'penulis' => 'Martin Fowler', 'penerbit' => 'Elex Media Komputindo', 'tahun' => 2019, 'kategori' => 'Teknologi'],
        ['judul' => 'Design Patterns', 'penulis' => 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides', 'penerbit' => 'Elex Media Komputindo', 'tahun' => 2002, 'kategori' => 'Teknologi'],
        ['judul' => 'Kotlin in Action', 'penulis' => 'Dmitry Jemerov dan Stephen Samuel', 'penerbit' => 'Elex Media Komputindo', 'tahun' => 2017, 'kategori' => 'Teknologi'],
        ['judul' => 'The Pragmatic Programmer', 'penulis' => 'David Thomas dan Andrew Hunt', 'penerbit' => 'Kanisius', 'tahun' => 2020, 'kategori' => 'Teknologi'],
        ['judul' => 'JavaScript: The Definitive Guide', 'penulis' => 'Matt Frisbie', 'penerbit' => 'Elex Media Komputindo', 'tahun' => 2016, 'kategori' => 'Teknologi'],
        ['judul' => 'Sejarah Indonesia Modern', 'penulis' => 'R.M. Soedarsono', 'penerbit' => 'Universitas Indonesia Press', 'tahun' => 2019, 'kategori' => 'Sejarah'],
        ['judul' => 'Kalangwan: Sastra Jawa Selayang Pandang', 'penulis' => 'Zoetmulder', 'penerbit' => 'Kanisius', 'tahun' => 2014, 'kategori' => 'Sejarah'],
        ['judul' => 'Kamus Besar Bahasa Indonesia', 'penulis' => 'Pusat Bahasa', 'penerbit' => 'Balai Pustaka', 'tahun' => 2016, 'kategori' => 'Pendidikan'],
        ['judul' => 'Tenggelamnya Kapal van der Wijck', 'penulis' => 'Chairil Anwar', 'penerbit' => 'Gramedia', 'tahun' => 2012, 'kategori' => 'Sastra'],
        ['judul' => 'Lirik Cekelan', 'penulis' => 'Chairil Anwar', 'penerbit' => 'Gramedia', 'tahun' => 2012, 'kategori' => 'Sastra'],
        ['judul' => 'Doraemon 01', 'penulis' => 'Fujiko F. Fujio', 'penerbit' => 'Elex Media Komputindo', 'tahun' => 2005, 'kategori' => 'Komik'],
    ];

    public function run(): void
    {
        $kode = new KodeService;

        $admin = User::factory()->admin()->create([
            'name' => 'Rina Handayani',
            'email' => 'admin@perpustakaan.id',
        ]);

        $petugas = User::factory()->petugas()->create([
            'name' => 'Bagus Prasetyo',
            'email' => 'petugas@perpustakaan.id',
        ]);

        $petugasKedua = User::factory()->petugas()->create([
            'name' => 'Dewi Anggraini',
            'email' => 'dewi@perpustakaan.id',
        ]);

        /*
         * Satu anggota contoh dengan surel yang mudah ditebak, karena halaman
         * masuk menautkannya sebagai akun percobaan. Tanpa akun ini, tamu
         * hanya bisa menebak surel anggota faker yang tidak terduga.
         */
        User::factory()->create([
            'name' => 'Ayu Larasati',
            'email' => 'anggota@perpustakaan.id',
            'nomor_anggota' => 'ANG-00001',
        ]);

        $kategori = $this->buatKategori();
        $buku = $this->buatBuku($kategori);

        $anggota = User::factory()
            ->count(28)
            ->create()
            ->each(function (User $user) {
                $user->forceFill(['nomor_anggota' => 'ANG-'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT)])->save();
            });

        $anggotaAcak = $anggota->shuffle()->values();

        $this->buatPeminjaman($buku, $anggotaAcak, $petugas, $kode);
        $this->buatReservasi($buku, $anggotaAcak, $kode);
        $this->buatPembayaran($petugasKedua, $kode);

        ActivityLog::catat($admin, 'seed', 'data awal', 'Katalog, keanggotaan, dan riwayat peminjaman dimuat.');
    }

    /**
     * @return Collection<string, Category>
     */
    private function buatKategori(): Collection
    {
        $deskripsi = [
            'Fiksi' => 'Novel, cerita pendek, dan karya imajinatif.',
            'Sains' => 'Buku populer sains dan kajian saintifik.',
            'Ekonomi' => 'Ekonomi, keuangan, dan manajemen.',
            'Nonfiksi' => 'Karya umum, esai, dan pengembangan diri.',
            'Teknologi' => 'Komputer, rekayasa, dan teknik.',
            'Sejarah' => 'Sejarah Indonesia dan kajian sejarah',
            'Pendidikan' => 'Bahasa, pedagogi, dan ensiklopedia.',
            'Sastra' => 'Puisi, drama, dan kritik sastra.',
            'Komik' => 'Manga dan komik berbahasa Indonesia.',
        ];

        return collect($deskripsi)->map(fn (string $catatan, string $nama) => Category::factory()->create([
            'nama' => $nama,
            'kode' => strtoupper(substr($nama, 0, 3)),
            'keterangan' => $catatan,
        ]));
    }

    /**
     * @param  Collection<string, Category>  $kategori
     * @return Collection<int, Book>
     */
    private function buatBuku(Collection $kategori): Collection
    {
        $stok = [2, 3, 4, 5, 6, 3, 2, 4, 5, 2, 3, 4, 2, 5, 3, 4, 2, 3, 5, 4, 3, 2, 4, 3, 5, 2, 6, 3, 4, 2, 3, 2, 2, 3];

        return collect(self::DAFTAR_BUKU)->values()->map(function (array $baris, int $index) use ($kategori, $stok) {
            return Book::factory()->create([
                'isbn' => sprintf('978979%s%02d0', (string) (1000000 + $index * 137), $index % 100),
                'judul' => $baris['judul'],
                'penulis' => $baris['penulis'],
                'penerbit' => $baris['penerbit'],
                'tahun_terbit' => $baris['tahun'],
                'category_id' => $kategori[$baris['kategori']]->id,
                'stok' => $stok[$index] ?? 3,
                'deskripsi' => sprintf(
                    'Koleksi "%s" karya %s. Tersimpan di rak %s-%s.',
                    $baris['judul'],
                    $baris['penulis'],
                    strtoupper(substr($baris['kategori'], 0, 1)),
                    str_pad((string) (($index % 6) + 1), 2, '0', STR_PAD_LEFT),
                ),
            ]);
        });
    }

    /**
     * @param  Collection<int, Book>  $buku
     * @param  Collection<int, User>  $anggota
     */
    private function buatPeminjaman(Collection $buku, Collection $anggota, User $petugas, KodeService $kode): void
    {
        $dendaPerHari = config('perpustakaan.denda_per_hari');

        $buku->each(function (Book $bukuItem, int $index) use ($anggota, $petugas, $kode) {
            // Setiap judul kedelapan sengaja dikosongkan supaya katalog punya
            // contoh nyata status stok habis dan reservasi yang menunggu.
            if ($index % 8 === 7) {
                return;
            }

            $meminjam = $anggota[($index * 3) % $anggota->count()];

            // Tiga dari empat peminjaman aktif sengaja masih berjalan, sisanya
            // terlambat supaya dashboard menampilkan kedua kondisi sekaligus.
            $terlambat = $index % 4 === 3;
            $selisih = ($index % 8) + 1;

            $loan = Loan::create([
                'kode' => $kode->buat('PMJ', 'loans'),
                'book_id' => $bukuItem->id,
                'user_id' => $meminjam->id,
                'petugas_id' => $petugas->id,
                'dipinjam_at' => $terlambat
                    ? now()->subDays(7 + $selisih)
                    : now()->subDays(7 - ($index % 4)),
                'harus_kembali_at' => $terlambat
                    ? now()->subDays($selisih)
                    : now()->addDays(1 + ($index % 5)),
                'status' => LoanStatus::Dipinjam,
            ]);

            ActivityLog::catat(
                $petugas,
                'pinjam',
                $loan->kode,
                sprintf(
                    '"%s" untuk %s, jatuh tempo %s',
                    $bukuItem->judul,
                    $meminjam->name,
                    $loan->harus_kembali_at->translatedFormat('d M Y'),
                ),
            );
        });

        $hariTerlambat = [0, 0, 2, 0, 4, 1, 0, 3, 0, 6, 0, 2, 0, 5, 0];

        $buku->take(15)->each(function (Book $bukuItem, int $index) use ($anggota, $petugas, $kode, $hariTerlambat, $dendaPerHari) {
            $meminjam = $anggota[($index * 5) % $anggota->count()];
            $telat = $hariTerlambat[$index];

            $loan = Loan::create([
                'kode' => $kode->buat('PMJ', 'loans'),
                'book_id' => $bukuItem->id,
                'user_id' => $meminjam->id,
                'petugas_id' => $petugas->id,
                'dipinjam_at' => now()->subDays(30 + $index),
                'harus_kembali_at' => now()->subDays(23 + $index),
                'dikembalikan_at' => now()->subDays(23 + $index - $telat),
                'status' => LoanStatus::Dikembalikan,
                'denda' => $telat * $dendaPerHari,
            ]);

            ActivityLog::catat(
                $petugas,
                'kembalikan',
                $loan->kode,
                $loan->denda > 0
                    ? sprintf('Terlambat %d hari, denda Rp%s', $telat, number_format($loan->denda, 0, ',', '.'))
                    : 'Tepat waktu, tidak ada denda',
            );
        });
    }

    /**
     * @param  Collection<int, Book>  $buku
     * @param  Collection<int, User>  $anggota
     */
    private function buatReservasi(Collection $buku, Collection $anggota, KodeService $kode): void
    {
        $buku->take(8)->each(function (Book $bukuItem, int $index) use ($anggota, $kode) {
            $pemesan = $anggota[($index * 7) % $anggota->count()];

            $sudahMeminjam = $bukuItem->loans()
                ->where('user_id', $pemesan->id)
                ->where('status', LoanStatus::Dipinjam->value)
                ->exists();

            if ($sudahMeminjam) {
                return;
            }

            $reservation = Reservation::create([
                'kode' => $kode->buat('RSV', 'reservations'),
                'book_id' => $bukuItem->id,
                'user_id' => $pemesan->id,
                'status' => $index < 3 ? ReservationStatus::Siap : ReservationStatus::Menunggu,
                'antrean' => $index + 1,
                'berlaku_sampai' => now()->addDays(3),
            ]);

            ActivityLog::catat(
                $pemesan,
                'reservasi',
                $reservation->kode,
                sprintf('"%s", antrean ke-%d', $bukuItem->judul, $reservation->antrean),
            );
        });
    }

    private function buatPembayaran(User $petugas, KodeService $kode): void
    {
        Loan::query()
            ->where('status', LoanStatus::Dikembalikan->value)
            ->where('denda', '>', 0)
            ->inRandomOrder()
            ->take(6)
            ->get()
            ->each(function (Loan $loan) use ($petugas, $kode) {
                $payment = Payment::create([
                    'kode' => $kode->buat('BYR', 'payments'),
                    'user_id' => $loan->user_id,
                    'loan_id' => $loan->id,
                    'petugas_id' => $petugas->id,
                    'jumlah' => $loan->denda,
                    'metode' => fake()->boolean(70) ? PaymentMethod::Tunai : PaymentMethod::Transfer,
                    'bukti' => null,
                    'keterangan' => 'Pelunasan denda peminjaman '.$loan->kode,
                    'dibayar_pada' => $loan->dikembalikan_at?->copy()->addDay(),
                ]);

                ActivityLog::catat(
                    $petugas,
                    'bayar-denda',
                    $payment->kode,
                    sprintf('Denda Rp%s diterima dari %s', number_format($payment->jumlah, 0, ',', '.'), $loan->user->name),
                );
            });
    }
}
