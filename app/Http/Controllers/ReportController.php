<?php

namespace App\Http\Controllers;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Concerns\PaginaRingkas;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Services\LoanService;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    use PaginaRingkas;

    public function __construct(
        private readonly LoanService $loans,
        private readonly MemberService $members,
    ) {}

    public function index(): View
    {
        return view('reports.index', [
            'bulanIni' => now()->startOfMonth(),
        ]);
    }

    /**
     * Rekapitulasi peminjaman satu bulan. Rentang tanggal mengikuti pilihan filter
     * di form, jadi petugas bisa mencetak laporan periode yang sedang diaudit.
     */
    public function peminjaman(Request $request): View
    {
        $data = $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ], [
            'sampai.after_or_equal' => 'Tanggal akhir tidak boleh lebih dulu dari tanggal awal.',
        ]);

        $dari = isset($data['dari']) ? Carbon::parse($data['dari'])->startOfDay() : now()->startOfMonth();
        $sampai = isset($data['sampai']) ? Carbon::parse($data['sampai'])->endOfDay() : now()->endOfMonth();

        $pinjaman = Loan::query()
            ->with(['book:id,judul,isbn', 'user:id,name,nomor_anggota'])
            ->whereBetween('dipinjam_at', [$dari, $sampai])
            ->orderByDesc('dipinjam_at')
            ->get();

        $pengembalian = Loan::query()
            ->whereBetween('dikembalikan_at', [$dari, $sampai])
            ->get();

        $perKategori = Loan::query()
            ->with('book:id,judul,category_id')
            ->whereBetween('dipinjam_at', [$dari, $sampai])
            ->get()
            ->groupBy(fn (Loan $loan) => $loan->book?->category?->nama ?? 'Tanpa kategori')
            ->map->count()
            ->sortDesc();

        return view('reports.pinjaman', [
            'dari' => $dari,
            'sampai' => $sampai,
            'pinjaman' => $pinjaman,
            'jumlahPeminjaman' => $pinjaman->count(),
            'jumlahDikembalikan' => $pengembalian->count(),
            'jumlahTerlambat' => $pengembalian->filter(fn (Loan $loan) => $loan->denda > 0)->count(),
            'totalDenda' => (int) $pengembalian->sum('denda'),
            'perKategori' => $perKategori,
        ]);
    }

    /**
     * Rekapitulasi koleksi per kategori, termasuk berapa eksemplar yang sedang
     * dipinjam dan berapa yang tersedia.
     */
    public function koleksi(): View
    {
        $koleksi = Category::query()
            ->with(['books' => fn ($query) => $query->withCount('activeLoans')])
            ->orderBy('nama')
            ->get()
            ->map(fn (Category $category) => [
                'kategori' => $category,
                'judul' => $category->books->count(),
                'eksemplar' => (int) $category->books->sum('stok'),
                'dipinjam' => (int) $category->books->sum('loans_count'),
            ]);

        return view('reports.koleksi', [
            'koleksi' => $koleksi,
            'totalJudul' => Book::query()->count(),
            'totalEksemplar' => (int) Book::query()->sum('stok'),
            'totalDipinjam' => Loan::query()->aktif()->count(),
        ]);
    }

    /**
     * Daftar peminjaman yang lewat jatuh tempo beserta denda berjalan. Inilah
     * daftar yang biasanya dibutuhkan saat menagih keterlambatan.
     */
    public function keterlambatan(): View
    {
        $terlambat = Loan::query()
            ->terlambat()
            ->with(['book:id,judul,isbn', 'user:id,name,nomor_anggota,no_hp,email'])
            ->orderBy('harus_kembali_at')
            ->get();

        $rincian = $terlambat->map(fn (Loan $loan) => [
            'loan' => $loan,
            'hari' => $loan->hariTerlambat(),
            'denda' => $this->loans->dendaBerjalan($loan),
        ]);

        return view('reports.keterlambatan', [
            'rincian' => $rincian,
            'jumlah' => $terlambat->count(),
            'totalDenda' => (int) $rincian->sum('denda'),
            'totalHari' => (int) $rincian->sum('hari'),
        ]);
    }

    /**
     * Jejak audit seluruh tindakan yang mengubah data operasional.
     */
    public function aktivitas(Request $request): View
    {
        $logs = ActivityLog::query()
            ->when($request->input('aksi'), fn ($query) => $query->where('aksi', $request->input('aksi')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $kata = $request->string('q')->toString();

                $query->where(function ($inner) use ($kata) {
                    $inner->where('nama_aktor', 'like', "%{$kata}%")
                        ->orWhere('subjek', 'like', "%{$kata}%")
                        ->orWhere('keterangan', 'like', "%{$kata}%");
                });
            })
            ->latest()
            ->paginate($this->perHalaman($request, 30))
            ->withQueryString();

        return view('reports.aktivitas', [
            'logs' => $logs,
            'daftarAksi' => ActivityLog::query()->distinct()->orderBy('aksi')->pluck('aksi'),
        ]);
    }

    /**
     * Rekapitulasi keanggotaan dan penerimaan denda.
     */
    public function keanggotaan(): View
    {
        $anggota = User::query()->anggota()->get();

        $tagihan = $anggota->mapWithKeys(fn (User $user) => [$user->id => $this->members->sisaDenda($user)]);

        $perProgramStudi = $anggota
            ->filter(fn (User $user) => filled($user->program_studi))
            ->groupBy('program_studi')
            ->map->count()
            ->sortDesc();

        return view('reports.keanggotaan', [
            'totalAnggota' => $anggota->count(),
            'totalAktif' => $anggota->filter(fn (User $user) => $user->status === 'aktif')->count(),
            'totalNonaktif' => $anggota->filter(fn (User $user) => $user->status !== 'aktif')->count(),
            'jumlahUtang' => $tagihan->filter(fn (int $sisa) => $sisa > 0)->count(),
            'totalUtang' => (int) $tagihan->sum(),
            'totalDiterima' => (int) Payment::query()->sum('jumlah'),
            'totalPeminjaman' => Loan::query()->count(),
            'perProgramStudi' => $perProgramStudi,
            'reservasiAktif' => Reservation::query()->aktif()->count(),
        ]);
    }

    /**
     * Rekapitulasi status reservasi yang sedang berjalan dan yang sudah selesai.
     */
    public function reservasi(): View
    {
        $perStatus = Reservation::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('reports.reservasi', [
            'perStatus' => $perStatus,
            'total' => Reservation::query()->count(),
            'aktif' => Reservation::query()->aktif()->count(),
            'kedaluwarsa' => (int) ($perStatus[ReservationStatus::Kedaluwarsa->value] ?? 0),
            'sedangSiap' => (int) ($perStatus[ReservationStatus::Siap->value] ?? 0),
            'loanService' => $this->loans,
        ]);
    }
}
