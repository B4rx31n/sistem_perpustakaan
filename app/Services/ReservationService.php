<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReservationService
{
    public function __construct(private readonly KodeService $kode) {}

    /**
     * memesan buku yang sedang dipinjam. Nomor antrean dihitung dari jumlah
     * reservasi aktif pada judul yang sama, sehingga urutan antrean terjaga.
     *
     * @throws RuntimeException
     */
    public function pesan(Book $book, User $anggota, ?User $petugas = null): Reservation
    {
        return DB::transaction(function () use ($book, $anggota, $petugas) {
            $sudahAda = $anggota->reservations()
                ->where('book_id', $book->id)
                ->aktif()
                ->exists();

            if ($sudahAda) {
                throw new RuntimeException("Reservasi \"{$book->judul}\" sudah tercatat di daftar Anda.");
            }

            $sedangDipinjam = $anggota->loans()
                ->aktif()
                ->where('book_id', $book->id)
                ->exists();

            if ($sedangDipinjam) {
                throw new RuntimeException("Anda sedang meminjam \"{$book->judul}\", tidak perlu reservasi.");
            }

            $jumlahAktif = $book->reservations()
                ->aktif()
                ->where('berlaku_sampai', '>', now())
                ->max('antrean') ?? 0;

            $reservation = Reservation::create([
                'kode' => $this->kode->buat('RSV', 'reservations'),
                'book_id' => $book->id,
                'user_id' => $anggota->id,
                'status' => ReservationStatus::Menunggu,
                'antrean' => $jumlahAktif + 1,
                'berlaku_sampai' => now()->addDays(config('perpustakaan.masa_berlaku_reservasi_hari')),
            ]);

            ActivityLog::catat(
                $petugas ?? $anggota,
                'reservasi',
                $reservation->kode,
                "\"{$book->judul}\", antrean ke-{$reservation->antrean}",
            );

            return $reservation->load(['book', 'user']);
        });
    }

    /**
     * Membatalkan reservasi, baik oleh anggota sendiri maupun oleh petugas.
     *
     * @throws RuntimeException
     */
    public function batalkan(Reservation $reservation, ?User $aktor, ?string $alasan = null): Reservation
    {
        if (! in_array($reservation->status, ReservationStatus::aktifValues(), true)) {
            throw new RuntimeException('Reservasi ini sudah tidak aktif.');
        }

        $reservation->update([
            'status' => ReservationStatus::Dibatalkan,
            'diselesaikan_at' => now(),
            'alasan_batal' => $alasan,
        ]);

        ActivityLog::catat(
            $aktor,
            'reservasi-batal',
            $reservation->kode,
            $alasan ?: 'Dibatalkan tanpa keterangan',
        );

        return $reservation->fresh();
    }

    /**
     * Menandai satu exemplar buku sebagai tersedia untuk anggota-antrean pertama.
     * Dipanggil dari halaman detail peminjaman saat mengembalikan buku.
     */
    public function tandaiSiap(Book $book, ?User $aktor = null): ?Reservation
    {
        $berikutnya = $book->activeReservations()->first();

        if (! $berikutnya) {
            return null;
        }

        $berikutnya->update([
            'status' => ReservationStatus::Siap,
            'berlaku_sampai' => now()->addDays(config('perpustakaan.masa_berlaku_reservasi_hari')),
        ]);

        ActivityLog::catat(
            $aktor,
            'reservasi-siap',
            $berikutnya->kode,
            "\"{$book->judul}\" siap diambil {$berikutnya->user->name}",
        );

        return $berikutnya->fresh();
    }

    /**
     * Meneruskan giliran ke reservasi aktif berikutnya. Dipanggil setelah pengembalian
     * supaya antrean tidak berhenti di anggota yang tidak hadir mengambil buku.
     */
    public function teruskanAntrean(Book $book, ?User $aktor = null): void
    {
        $berikutnya = $book->activeReservations()
            ->where('status', ReservationStatus::Menunggu)
            ->first();

        if (! $berikutnya) {
            return;
        }

        $berikutnya->update([
            'status' => ReservationStatus::Siap,
            'berlaku_sampai' => now()->addDays(config('perpustakaan.masa_berlaku_reservasi_hari')),
        ]);

        ActivityLog::catat(
            $aktor,
            'reservasi-siap',
            $berikutnya->kode,
            "\"{$book->judul}\" diteruskan ke antrean ke-{$berikutnya->antrean}",
        );
    }

    /**
     * Membatalkan reservasi yang sudah lewat masa ambilnya. Dijadwalkan harian lewat
     * routes/console.php.
     */
    public function kedaluwarsakan(): int
    {
        $kedaluwarsa = Reservation::query()
            ->aktif()
            ->where('berlaku_sampai', '<=', now())
            ->with(['book:id,judul', 'user:id,name'])
            ->get();

        foreach ($kedaluwarsa as $reservation) {
            $reservation->update([
                'status' => ReservationStatus::Kedaluwarsa,
                'diselesaikan_at' => now(),
                'alasan_batal' => 'Masa ambil habis, giliran diteruskan ke anggota berikutnya.',
            ]);

            ActivityLog::catat(
                null,
                'reservasi-kedaluwarsa',
                $reservation->kode,
                "\"{$reservation->book->judul}\" untuk {$reservation->user->name}",
            );

            $this->teruskanAntrean($reservation->book);
        }

        return $kedaluwarsa->count();
    }
}
