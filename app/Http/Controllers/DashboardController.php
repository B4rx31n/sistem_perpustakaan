<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Services\LoanService;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly LoanService $loans,
        private readonly MemberService $members,
    ) {}

    public function index(Request $request): View
    {
        if ($request->user()->isPetugas()) {
            return $this->dashboardPetugas();
        }

        return $this->dashboardAnggota($request);
    }

    /**
     * Ringkasan operasional. Setiap angka dihitung langsung dari tabel, jadi tidak
     * ada statistik yang perlu dipercaya begitu saja.
     */
    private function dashboardPetugas(): View
    {
        $jumlahDipinjam = Loan::query()->aktif()->count();
        $jumlahTerlambat = Loan::query()->terlambat()->count();

        $statistik = [
            'judul_koleksi' => Book::query()->count(),
            'eksemplar' => (int) Book::query()->sum('stok'),
            'sedang_dipinjam' => $jumlahDipinjam,
            'terlambat' => $jumlahTerlambat,
            'anggota_aktif' => User::query()->anggota()->aktif()->count(),
            'reservasi_aktif' => Reservation::query()->aktif()->count(),
        ];

        $pinjamanTerlambat = Loan::query()
            ->terlambat()
            ->with(['book:id,judul,isbn', 'user:id,name,nomor_anggota,no_hp'])
            ->orderBy('harus_kembali_at')
            ->limit(10)
            ->get();

        $pinjamanPerBulan = Loan::query()
            ->where('dipinjam_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw('DATE_FORMAT(dipinjam_at, "%Y-%m") as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $grafik = collect(range(5, 0))->map(function (int $mundur) use ($pinjamanPerBulan) {
            $bulan = now()->subMonths($mundur);

            return [
                'label' => $bulan->translatedFormat('M'),
                'tahun' => $bulan->format('Y'),
                'total' => (int) ($pinjamanPerBulan[$bulan->format('Y-m')] ?? 0),
            ];
        });

        $puncak = max(1, $grafik->max('total'));

        $bukuTerlaris = Book::query()
            ->withCount('loans')
            ->with('category:id,nama')
            ->orderByDesc('loans_count')
            ->orderBy('id')
            ->limit(8)
            ->get();

        $stokMenipis = Book::query()
            ->withCount('activeLoans')
            ->get()
            ->filter(fn (Book $buku) => $buku->jumlahTersedia() <= 1)
            // Kunci sort harus closure. sortBy('jumlahTersedia') akan meminta
            // Eloquent membaca atribut bernama itu, dan Eloquent mengira method
            // dengan nama yang sama adalah relasi.
            ->sortBy(fn (Book $buku) => $buku->jumlahTersedia())
            ->take(8)
            ->values();

        $aktivitas = ActivityLog::query()
            ->latest()
            ->limit(12)
            ->get();

        return view('dashboard.petugas', [
            'statistik' => $statistik,
            'pinjamanTerlambat' => $pinjamanTerlambat,
            'grafik' => $grafik,
            'puncakGrafik' => $puncak,
            'bukuTerlaris' => $bukuTerlaris,
            'stokMenipis' => $stokMenipis,
            'aktivitas' => $aktivitas,
            'totalDendaBelumLunas' => $this->totalDendaBelumLunas(),
        ]);
    }

    /**
     * Halaman anggota hanya berisi hal yang bisa dia lakukan sendiri: buku yang
     * sedang dipinjam, yang harus segera dikembalikan, dan tagihan denda.
     */
    private function dashboardAnggota(Request $request): View
    {
        $user = $request->user();

        $pinjamanAktif = $user->loans()
            ->aktif()
            ->with(['book:id,judul,isbn,cover_path', 'book.category:id,nama'])
            ->orderBy('harus_kembali_at')
            ->get();

        $reservasiAktif = $user->reservations()
            ->aktif()
            ->with('book:id,judul,isbn,cover_path')
            ->orderBy('antrean')
            ->get();

        $riwayatTerbaru = $user->loans()
            ->where('status', 'dikembalikan')
            ->with('book:id,judul,isbn')
            ->latest('dikembalikan_at')
            ->limit(5)
            ->get();

        return view('dashboard.anggota', [
            'pinjamanAktif' => $pinjamanAktif,
            'reservasiAktif' => $reservasiAktif,
            'riwayatTerbaru' => $riwayatTerbaru,
            'jumlahPinjamanAktif' => $this->members->jumlahPinjamanAktif($user),
            'jumlahTerlambat' => $this->members->jumlahTerlambat($user),
            'sisaDenda' => $this->members->sisaDenda($user),
            'loanService' => $this->loans,
        ]);
    }

    private function totalDendaBelumLunas(): int
    {
        $denda = (int) Loan::query()->sum('denda');
        $terbayar = (int) Payment::query()->sum('jumlah');

        return max(0, $denda - $terbayar);
    }
}
