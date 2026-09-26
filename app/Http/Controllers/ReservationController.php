<?php

namespace App\Http\Controllers;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Concerns\PaginaRingkas;
use App\Models\Book;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class ReservationController extends Controller
{
    use PaginaRingkas;

    public function __construct(private readonly ReservationService $reservasi) {}

    public function index(Request $request): View
    {
        $reservasi = Reservation::query()
            ->with(['book:id,judul,isbn', 'user:id,name,nomor_anggota'])
            ->when($request->input('status') === 'aktif', fn ($query) => $query->aktif())
            ->when($request->input('status') === 'selesai', fn ($query) => $query->whereNotIn('status', ReservationStatus::aktifValues()))
            ->orderByRaw('CASE status WHEN ? THEN 0 WHEN ? THEN 1 ELSE 2 END', [
                ReservationStatus::Siap->value,
                ReservationStatus::Menunggu->value,
            ])
            ->orderBy('antrean')
            ->orderBy('id')
            ->paginate($this->perHalaman($request))
            ->withQueryString();

        return view('reservations.index', [
            'reservasi' => $reservasi,
            'jumlahSiap' => Reservation::query()->where('status', ReservationStatus::Siap->value)->count(),
            'jumlahMenunggu' => Reservation::query()->where('status', ReservationStatus::Menunggu->value)->count(),
            'jumlahKedaluwarsa' => Reservation::query()->where('status', ReservationStatus::Kedaluwarsa->value)->count(),
        ]);
    }

    public function show(Reservation $reservation): View
    {
        $reservation->load(['book.category:id,nama', 'user:id,name,nomor_anggota,email,no_hp']);

        return view('reservations.show', [
            'reservation' => $reservation,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'book_id' => ['required', Rule::exists('books', 'id')],
        ], [
            'book_id.required' => 'Pilih judul buku yang mau dipesan.',
            'book_id.exists' => 'Buku yang dipilih tidak ada di katalog.',
        ]);

        $book = Book::query()->findOrFail($data['book_id']);

        try {
            $reservation = $this->reservasi->pesan(
                $book,
                $request->user(),
                $request->user()->isPetugas() ? $request->user() : null,
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', "Reservasi {$reservation->kode} tercatat. Anda antrean ke-{$reservation->antrean} untuk \"{$book->judul}\".");
    }

    public function destroy(Request $request, Reservation $reservation): RedirectResponse
    {
        $boleh = $request->user()->isPetugas() || $request->user()->is($reservation->user);

        if (! $boleh) {
            return back()->with('error', 'Reservasi ini bukan milik Anda.');
        }

        try {
            $this->reservasi->batalkan(
                $reservation,
                $request->user(),
                $request->string('alasan')->toString() ?: null,
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', "Reservasi {$reservation->kode} dibatalkan.");
    }
}
