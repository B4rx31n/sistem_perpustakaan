<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Concerns\PaginaRingkas;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\ActivityLog;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use App\Services\KodeService;
use App\Services\LoanService;
use App\Services\MemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    use PaginaRingkas;

    public function __construct(
        private readonly MemberService $members,
        private readonly LoanService $loans,
        private readonly KodeService $kode,
    ) {}

    public function index(Request $request): View
    {
        $pembayaran = Payment::query()
            ->with(['user:id,name,nomor_anggota', 'loan:id,kode', 'petugas:id,name'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $kata = $request->string('q')->toString();

                $query->where(function ($inner) use ($kata) {
                    $inner->where('kode', 'like', "%{$kata}%")
                        ->orWhereHas('user', fn ($user) => $user->cari($kata));
                });
            })
            ->when($request->input('metode'), fn ($query) => $query->where('metode', $request->input('metode')))
            ->latest('dibayar_pada')
            ->paginate($this->perHalaman($request))
            ->withQueryString();

        return view('payments.index', [
            'pembayaran' => $pembayaran,
            'totalDiterima' => (int) Payment::query()->sum('jumlah'),
            'totalBulanIni' => (int) Payment::query()->whereMonth('dibayar_pada', now()->month)->whereYear('dibayar_pada', now()->year)->sum('jumlah'),
            'jumlahTransaksi' => Payment::query()->count(),
        ]);
    }

    public function create(Request $request): View
    {
        $anggotaTerpilih = null;

        if ($request->filled('user_id')) {
            $anggotaTerpilih = User::query()->anggota()->find($request->integer('user_id'));
        }

        return view('payments.create', [
            'anggota' => User::query()->anggota()->orderBy('name')->get(['id', 'name', 'nomor_anggota']),
            'metode' => PaymentMethod::cases(),
            'anggotaTerpilih' => $anggotaTerpilih,
            'sisaDendaTerpilih' => $anggotaTerpilih ? $this->members->sisaDenda($anggotaTerpilih) : null,
            'pinjamanBerutang' => $anggotaTerpilih
                ? Loan::query()->where('user_id', $anggotaTerpilih->id)->where('denda', '>', 0)->whereDoesntHave('payments')->orderBy('denda')->get(['id', 'kode', 'denda'])
                : collect(),
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $anggota = User::query()->anggota()->findOrFail($request->integer('user_id'));
        $sisaDenda = $this->members->sisaDenda($anggota);

        if ($sisaDenda < 1) {
            return back()->withInput()->with('error', 'Anggota ini tidak punya tagihan denda yang perlu dibayar.');
        }

        $jumlah = $request->integer('jumlah');

        if ($jumlah > $sisaDenda) {
            return back()->withInput()->with('error', 'Nominal melebihi sisa tagihan yang sebesar Rp'.number_format($sisaDenda, 0, ',', '.').'.');
        }

        $loanId = $request->integer('loan_id') ?: null;

        $payment = Payment::create([
            'kode' => $this->kode->buat('BYR', 'payments'),
            'user_id' => $anggota->id,
            'loan_id' => $loanId,
            'petugas_id' => $request->user()->id,
            'jumlah' => $jumlah,
            'metode' => $request->string('metode')->toString(),
            'bukti' => $request->string('bukti')->toString() ?: null,
            'keterangan' => $request->string('keterangan')->toString() ?: null,
            'dibayar_pada' => now(),
        ]);

        ActivityLog::catat(
            $request->user(),
            'bayar-denda',
            $payment->kode,
            'Denda Rp'.number_format($jumlah, 0, ',', '.').' diterima dari '.$anggota->name,
        );

        $sisa = $this->members->sisaDenda($anggota->fresh());

        return redirect()
            ->route('members.show', $anggota)
            ->with('success', $sisa > 0
                ? 'Pembayaran Rp'.number_format($jumlah, 0, ',', '.').' diterima. Sisa tagihan Rp'.number_format($sisa, 0, ',', '.').'.'
                : 'Pembayaran Rp'.number_format($jumlah, 0, ',', '.').' diterima. Tagihan denda anggota sudah lunas.');
    }

    /**
     * Daftar anggota yang masih punya denda, dipakai sebagai titik masuk dari menu
     *-navbar supaya petugas tidak perlu mencari satu per satu.
     */
    public function piutang(Request $request): View
    {
        $utang = User::query()
            ->anggota()
            ->select('users.*')
            ->selectRaw('COALESCE((SELECT SUM(denda) FROM loans WHERE loans.user_id = users.id), 0) as total_denda')
            ->selectRaw('COALESCE((SELECT SUM(jumlah) FROM payments WHERE payments.user_id = users.id), 0) as total_bayar')
            ->whereRaw('COALESCE((SELECT SUM(denda) FROM loans WHERE loans.user_id = users.id), 0) - COALESCE((SELECT SUM(jumlah) FROM payments WHERE payments.user_id = users.id), 0) > 0')
            ->orderByDesc('total_denda')
            ->paginate($this->perHalaman($request))
            ->withQueryString();

        $total = (int) $utang->getCollection()->sum(fn (User $user) => max(
            0,
            (int) $user->total_denda - (int) $user->total_bayar,
        ));

        return view('payments.piutang', [
            'utang' => $utang,
            'totalUtang' => $total,
            'jumlahUtang' => $utang->total(),
            'loanService' => $this->loans,
        ]);
    }
}
