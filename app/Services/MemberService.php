<?php

namespace App\Services;

use App\Enums\LoanStatus;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;

class MemberService
{
    public function __construct(private readonly LoanService $loans) {}

    /**
     * Total denda yang sudah tercatat, termasuk denda yang masih berjalan pada
     * peminjaman aktif yang sudah lewat jatuh tempo.
     */
    public function totalDenda(User $anggota): int
    {
        $dendaSelesai = (int) Loan::query()
            ->where('user_id', $anggota->id)
            ->sum('denda');

        $dendaBerjalan = (int) Loan::query()
            ->where('user_id', $anggota->id)
            ->where('status', LoanStatus::Dipinjam->value)
            ->whereDate('harus_kembali_at', '<', now())
            ->get()
            ->sum(fn (Loan $loan) => $this->loans->dendaBerjalan($loan));

        return $dendaSelesai + $dendaBerjalan;
    }

    public function totalPembayaran(User $anggota): int
    {
        return (int) Payment::query()
            ->where('user_id', $anggota->id)
            ->sum('jumlah');
    }

    /**
     * Sisa tagihan denda. Nilai negatif tidak mungkin terjadi karena form pembayaran
     * dibatasi tidak melebihi sisa tagihan.
     */
    public function sisaDenda(User $anggota): int
    {
        return max(0, $this->totalDenda($anggota) - $this->totalPembayaran($anggota));
    }

    public function jumlahPinjamanAktif(User $anggota): int
    {
        return Loan::query()
            ->where('user_id', $anggota->id)
            ->where('status', LoanStatus::Dipinjam->value)
            ->count();
    }

    public function jumlahTerlambat(User $anggota): int
    {
        return Loan::query()
            ->where('user_id', $anggota->id)
            ->where('status', LoanStatus::Dipinjam->value)
            ->whereDate('harus_kembali_at', '<', now())
            ->count();
    }
}
