<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Concerns\PaginaRingkas;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Services\LoanService;
use App\Services\MemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    use PaginaRingkas;

    public function __construct(
        private readonly MemberService $members,
        private readonly LoanService $loans,
    ) {}

    public function index(Request $request): View
    {
        $anggota = User::query()
            ->withCount(['loans as loans_aktif' => fn ($query) => $query->where('status', LoanStatus::Dipinjam->value)])
            ->cari($request->string('q')->toString() ?: null)
            ->when($request->input('role'), fn ($query) => $query->where('role', $request->input('role')))
            ->when($request->input('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->orderBy('name')
            ->orderBy('id')
            ->paginate($this->perHalaman($request))
            ->withQueryString();

        $tagihan = User::query()
            ->anggota()
            ->select('users.*')
            ->selectRaw('COALESCE((SELECT SUM(denda) FROM loans WHERE loans.user_id = users.id), 0) as total_denda')
            ->selectRaw('COALESCE((SELECT SUM(jumlah) FROM payments WHERE payments.user_id = users.id), 0) as total_bayar')
            ->get()
            ->mapWithKeys(fn (User $user) => [$user->id => max(0, (int) $user->total_denda - (int) $user->total_bayar)])
            ->filter(fn (int $sisa) => $sisa > 0);

        return view('members.index', [
            'anggota' => $anggota,
            'totalAnggota' => User::query()->where('role', UserRole::Anggota->value)->count(),
            'totalAktif' => User::query()->where('role', UserRole::Anggota->value)->aktif()->count(),
            'jumlahBerutang' => $tagihan->count(),
            'totalPiutang' => (int) $tagihan->sum(),
        ]);
    }

    public function show(Request $request, User $member): View
    {
        $this->authorize('view', $member);

        $member->loadCount(['loans as loans_aktif' => fn ($query) => $query->where('status', LoanStatus::Dipinjam->value)]);

        return view('members.show', [
            'member' => $member,
            'pinjamanAktif' => $member->loans()
                ->aktif()
                ->with('book:id,judul,isbn')
                ->orderBy('harus_kembali_at')
                ->get(),
            'riwayat' => $member->loans()
                ->where('status', LoanStatus::Dikembalikan->value)
                ->with('book:id,judul,isbn')
                ->latest('dikembalikan_at')
                ->limit(10)
                ->get(),
            'reservasi' => $member->reservations()
                ->with('book:id,judul,isbn')
                ->latest()
                ->limit(10)
                ->get(),
            'pembayaran' => $member->payments()
                ->with('loan:id,kode')
                ->latest('dibayar_pada')
                ->limit(10)
                ->get(),
            'sisaDenda' => $this->members->sisaDenda($member),
            'loanService' => $this->loans,
        ]);
    }

    public function edit(User $member): View
    {
        $this->authorize('update', $member);

        return view('members.edit', [
            'member' => $member,
            'peran' => UserRole::cases(),
        ]);
    }

    public function update(UpdateMemberRequest $request, User $member): RedirectResponse
    {
        $this->authorize('update', $member);

        $data = $request->safe()->except(['role', 'status']);

        $ubahPeran = $request->user()->can('ubahPeran', $member);
        $ubahStatus = $request->user()->can('ubahStatus', $member);

        if ($ubahPeran) {
            $data['role'] = $request->string('role')->toString();
        }

        if ($ubahStatus) {
            $data['status'] = $request->string('status')->toString();
        }

        $member->update($data);

        ActivityLog::catat(
            $request->user(),
            'anggota-ubah',
            $member->nomor_anggota ?? $member->email,
            "Data anggota {$member->name} diperbarui",
        );

        return redirect()
            ->route('members.show', $member)
            ->with('success', "Data anggota {$member->name} diperbarui.");
    }

    /**
     * Riwayat resmi satu anggota, disusun untuk dicetak dan ditandatangani.
     */
    public function kartu(Request $request, User $member): View
    {
        $this->authorize('view', $member);

        return view('members.kartu', [
            'member' => $member,
            'pinjamanAktif' => $member->loans()->aktif()->with('book:id,judul,isbn')->orderBy('harus_kembali_at')->get(),
            'riwayat' => $member->loans()->where('status', LoanStatus::Dikembalikan->value)->with('book:id,judul,isbn')->latest('dikembalikan_at')->limit(20)->get(),
            'sisaDenda' => $this->members->sisaDenda($member),
            'totalPinjaman' => $member->loans()->count(),
            'totalReservasi' => Reservation::query()->where('user_id', $member->id)->count(),
            'totalPembayaran' => Payment::query()->where('user_id', $member->id)->sum('jumlah'),
        ]);
    }
}
