<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Http\Controllers\Concerns\PaginaRingkas;
use App\Http\Requests\Loan\StoreLoanRequest;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Services\LoanService;
use App\Services\MemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use RuntimeException;

class LoanController extends Controller
{
    use PaginaRingkas;

    public function __construct(
        private readonly LoanService $loans,
        private readonly MemberService $members,
    ) {}

    public function index(Request $request): View
    {
        $pinjaman = Loan::query()
            ->with(['book:id,judul,isbn', 'user:id,name,nomor_anggota', 'petugas:id,name'])
            ->when($request->input('status') === 'aktif', fn ($query) => $query->aktif())
            ->when($request->input('status') === 'terlambat', fn ($query) => $query->terlambat())
            ->when($request->input('status') === 'selesai', fn ($query) => $query->where('status', LoanStatus::Dikembalikan->value))
            ->when($request->filled('q'), fn ($query) => $query->where(function ($inner) use ($request) {
                $kata = $request->string('q')->toString();

                $inner->where('kode', 'like', "%{$kata}%")
                    ->orWhereHas('book', fn ($buku) => $buku->where('judul', 'like', "%{$kata}%"))
                    ->orWhereHas('user', fn ($user) => $user->cari($kata));
            }))
            ->orderByRaw('CASE status WHEN ? THEN 0 ELSE 1 END', [LoanStatus::Dipinjam->value])
            ->orderBy('harus_kembali_at')
            ->orderBy('id')
            ->paginate($this->perHalaman($request))
            ->withQueryString();

        return view('loans.index', [
            'pinjaman' => $pinjaman,
            'loanService' => $this->loans,
            'jumlahAktif' => Loan::query()->aktif()->count(),
            'jumlahTerlambat' => Loan::query()->terlambat()->count(),
            'jumlahSelesai' => Loan::query()->where('status', 'dikembalikan')->count(),
        ]);
    }

    public function create(Request $request): View
    {
        $anggotaTerpilih = null;

        if ($request->filled('user_id')) {
            $anggotaTerpilih = User::query()
                ->anggota()
                ->with(['activeLoans' => fn ($query) => $query->with('book:id,judul')])
                ->find($request->integer('user_id'));
        }

        return view('loans.create', [
            'anggota' => User::query()->anggota()->aktif()->orderBy('name')->get(['id', 'name', 'nomor_anggota', 'program_studi']),
            'buku' => $this->daftarBuku(),
            'anggotaTerpilih' => $anggotaTerpilih,
            'loanService' => $this->loans,
            'memberService' => $this->members,
        ]);
    }

    public function store(StoreLoanRequest $request): RedirectResponse
    {
        $anggota = User::query()->anggota()->findOrFail($request->integer('user_id'));
        $book = Book::query()->withCount('activeLoans')->findOrFail($request->integer('book_id'));

        $sisaDenda = $this->members->sisaDenda($anggota);

        if ($sisaDenda > 0) {
            return back()
                ->withInput()
                ->with('error', 'Denda anggota belum lunas sisa Rp'.number_format($sisaDenda, 0, ',', '.').'. Lunasi dulu di menu Pembayaran.');
        }

        try {
            $loan = $this->loans->pinjam(
                $book,
                $anggota,
                $request->user(),
                $request->string('catatan')->toString() ?: null,
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('loans.show', $loan)
            ->with('success', "Peminjaman {$loan->kode} tercatat. Harus kembali {$loan->harus_kembali_at->translatedFormat('d F Y')}.");
    }

    public function show(Loan $loan): View
    {
        $loan->load(['book.category:id,nama', 'user:id,name,nomor_anggota,email,no_hp,program_studi', 'petugas:id,name']);

        return view('loans.show', [
            'loan' => $loan,
            'loanService' => $this->loans,
            'pembayaran' => $loan->payments()->with('petugas:id,name')->latest('dibayar_pada')->get(),
        ]);
    }

    public function kembalikan(Request $request, Loan $loan): RedirectResponse
    {
        try {
            $loan = $this->loans->kembalikan(
                $loan,
                $request->user(),
                $request->string('catatan')->toString() ?: null,
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $pesan = $loan->denda > 0
            ? 'Buku dikembalikan. Denda keterlambatan Rp'.number_format($loan->denda, 0, ',', '.').' tercatat.'
            : 'Buku dikembalikan tepat waktu, tidak ada denda.';

        return redirect()->route('loans.show', $loan)->with('success', $pesan);
    }

    public function perpanjang(Request $request, Loan $loan): RedirectResponse
    {
        try {
            $loan = $this->loans->perpanjang($loan, $request->user());
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('loans.show', $loan)
            ->with('success', "Peminjaman diperpanjang. Jatuh tempo baru {$loan->harus_kembali_at->translatedFormat('d F Y')}.");
    }

    /**
     * Daftar buku yang masih bisa dipinjam, dengan sisa stok per judul. Buku yang
     * stoknya habis sengaja tidak muncul supaya petugas tidak memilih buku yang
     * pasti gagal saat disimpan.
     *
     * @return Collection<int, Book>
     */
    private function daftarBuku(): Collection
    {
        return Book::query()
            ->with('category:id,nama')
            ->withCount('activeLoans')
            ->orderBy('judul')
            ->get()
            ->filter(fn ($book) => $book->jumlahTersedia() > 0)
            ->values();
    }
}
