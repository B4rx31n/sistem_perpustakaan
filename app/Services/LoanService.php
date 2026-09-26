<?php

namespace App\Services;

use App\Enums\LoanStatus;
use App\Enums\ReservationStatus;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LoanService
{
    public function __construct(private readonly KodeService $kode) {}

    /**
     * Peminjaman baru. Memakai transaksi dan baris buku terkunci supaya dua petugas
     * tidak bisa Minnuti stok yang sama di saat bersamaan.
     *
     * @throws RuntimeException when the book is out of stock or the member already
     *                          has an active loan for the same title
     */
    public function pinjam(Book $book, User $anggota, ?User $petugas = null, ?string $catatan = null): Loan
    {
        return DB::transaction(function () use ($book, $anggota, $petugas, $catatan) {
            $book = Book::query()->lockForUpdate()->findOrFail($book->id);

            $tersedia = $book->stok - $book->loans()
                ->where('status', LoanStatus::Dipinjam->value)
                ->count();

            if ($tersedia < 1) {
                throw new RuntimeException("Stok buku \"{$book->judul}\" habis. Gunakan reservasi untuk mengantre.");
            }

            $sudahPinjam = $anggota->loans()
                ->aktif()
                ->where('book_id', $book->id)
                ->exists();

            if ($sudahPinjam) {
                throw new RuntimeException("{$anggota->name} masih meminjam judul \"{$book->judul}\".");
            }

            $reservasi = $anggota->reservations()
                ->where('book_id', $book->id)
                ->aktif()
                ->orderByDesc('antrean')
                ->first();

            $loan = Loan::create([
                'kode' => $this->kode->buat('PMJ', 'loans'),
                'book_id' => $book->id,
                'user_id' => $anggota->id,
                'petugas_id' => $petugas?->id,
                'dipinjam_at' => now(),
                'harus_kembali_at' => now()->addDays(config('perpustakaan.durasi_peminjaman_hari')),
                'perpanjangan' => 0,
                'status' => LoanStatus::Dipinjam,
                'catatan' => $catatan,
            ]);

            if ($reservasi) {
                $this->tandaiSelesai($reservasi, 'Peminjaman dibuat dari reservasi.');
            }

            ActivityLog::catat(
                $petugas ?? $anggota,
                'pinjam',
                $loan->kode,
                "\"{$book->judul}\" untuk {$anggota->name}, jatuh tempo {$this->formatTanggal($loan->harus_kembali_at)}",
            );

            return $loan->load(['book', 'user']);
        });
    }

    /**
     * Pengembalian. Denda dihitung dari tanggal jatuh tempo sampai hari pengembalian
     * dan langsung disimpan supaya nominal yang dibayar anggota tidak berubah lagi.
     */
    public function kembalikan(Loan $loan, ?User $petugas = null, ?string $catatan = null): Loan
    {
        return DB::transaction(function () use ($loan, $petugas, $catatan) {
            $loan = Loan::query()->lockForUpdate()->findOrFail($loan->id);

            if (! $loan->isAktif()) {
                throw new RuntimeException("Peminjaman {$loan->kode} sudah dikembalikan sebelumnya.");
            }

            $hariTerlambat = $loan->hariTerlambat();

            $loan->update([
                'dikembalikan_at' => now(),
                'status' => LoanStatus::Dikembalikan,
                'denda' => $this->hitungDenda($hariTerlambat),
                'petugas_id' => $petugas?->id ?? $loan->petugas_id,
                'catatan' => $catatan ?? $loan->catatan,
            ]);

            $loan->book->activeReservations()->first()?->update([
                'status' => ReservationStatus::Siap,
                'berlaku_sampai' => now()->addDays(config('perpustakaan.masa_berlaku_reservasi_hari')),
            ]);

            ActivityLog::catat(
                $petugas,
                'kembalikan',
                $loan->kode,
                $loan->denda > 0
                    ? "Terlambat {$hariTerlambat} hari, denda Rp{$this->formatRupiah($loan->denda)}"
                    : 'Tepat waktu, tidak ada denda',
            );

            return $loan->fresh(['book', 'user']);
        });
    }

    /**
     * Perpanjangan satu periode. Ditolak kalau sudah terlambat, supaya anggota tidak
     * bisa memanjangkan masa pinjam yang sudah lewat.
     *
     * @throws RuntimeException
     */
    public function perpanjang(Loan $loan, ?User $petugas = null): Loan
    {
        $loan = Loan::query()->findOrFail($loan->id);

        if (! $loan->isAktif()) {
            throw new RuntimeException('Hanya peminjaman aktif yang bisa diperpanjang.');
        }

        if (! $loan->bisaDiperpanjang()) {
            throw new RuntimeException('Perpanjangan sudah mencapai batas atau peminjaman sudah terlambat.');
        }

        $loan->update([
            'harus_kembali_at' => $loan->harus_kembali_at
                ->addDays(config('perpustakaan.durasi_peminjaman_hari')),
            'perpanjangan' => $loan->perpanjangan + 1,
        ]);

        ActivityLog::catat(
            $petugas,
            'perpanjang',
            $loan->kode,
            'Jatuh tempo baru '.$this->formatTanggal($loan->harus_kembali_at),
        );

        return $loan->fresh(['book', 'user']);
    }

    /**
     * Denda yang sudah jatuh tempo untuk satu peminjaman yang masih aktif.
     */
    public function dendaBerjalan(Loan $loan): int
    {
        return $this->hitungDenda($loan->hariTerlambat());
    }

    public function hitungDenda(int $hariTerlambat): int
    {
        if ($hariTerlambat < 1) {
            return 0;
        }

        return min(
            $hariTerlambat * config('perpustakaan.denda_per_hari'),
            config('perpustakaan.denda_maks_per_peminjaman'),
        );
    }

    /**
     * Sisa hari sebelum jatuh tempo. Negatif berarti sudah lewat.
     */
    public function sisaHari(Loan $loan): int
    {
        return (int) now()->startOfDay()->diffInDays($loan->harus_kembali_at->startOfDay(), false);
    }

    public function tanggalKembali(): Carbon
    {
        return now()->addDays(config('perpustakaan.durasi_peminjaman_hari'));
    }

    private function tandaiSelesai(Reservation $reservasi, string $keterangan): void
    {
        $reservasi->update([
            'status' => ReservationStatus::Selesai,
            'diselesaikan_at' => now(),
        ]);

        ActivityLog::catat(
            $reservasi->user,
            'reservasi-selesai',
            $reservasi->kode,
            $keterangan,
        );
    }

    private function formatTanggal(Carbon $tanggal): string
    {
        return $tanggal->translatedFormat('d M Y');
    }

    private function formatRupiah(int $nilai): string
    {
        return number_format($nilai, 0, ',', '.');
    }
}
