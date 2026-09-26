<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Http\Controllers\Concerns\PaginaRingkas;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookController extends Controller
{
    use PaginaRingkas;

    public function index(Request $request): View
    {
        $kategori = Category::query()->orderBy('nama')->get();

        $buku = Book::query()
            ->with('category:id,nama,kode')
            ->withCount('activeLoans')
            ->cari($request->string('q')->toString() ?: null)
            ->when($request->filled('kategori'), fn ($query) => $query->where('category_id', $request->integer('kategori')))
            ->when($request->input('ketersediaan') === 'tersedia', fn ($query) => $query->tersedia())
            ->when($request->input('ketersediaan') === 'habis', fn ($query) => $query->whereColumn('stok', '<=', $this->subqueryPinjamanAktif()))
            ->when($request->input('urut') === 'judul', fn ($query) => $query->orderBy('judul'))
            ->when($request->input('urut') === 'terlama', fn ($query) => $query->orderByDesc('tahun_terbit'))
            ->when($request->input('urut') === 'stok', fn ($query) => $query->orderByDesc('stok'))
            ->orderBy('id')
            ->paginate($this->perHalaman($request))
            ->withQueryString();

        return view('books.index', [
            'buku' => $buku,
            'kategori' => $kategori,
            'totalJudul' => Book::query()->count(),
        ]);
    }

    public function create(): View
    {
        return view('books.create', [
            'kategori' => Category::query()->orderBy('nama')->get(),
        ]);
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('cover');

        $book = Book::create($data + [
            'cover_path' => $this->simpanSampul($request),
        ]);

        ActivityLog::catat(
            $request->user(),
            'buku-tambah',
            $book->isbn,
            "\"{$book->judul}\" masuk katalog dengan stok {$book->stok}",
        );

        return redirect()
            ->route('books.show', $book)
            ->with('success', "Buku \"{$book->judul}\" ditambahkan ke katalog.");
    }

    public function show(Book $book): View
    {
        $book->load(['category:id,nama,kode', 'loans' => fn ($query) => $query->latest('dipinjam_at')->limit(10)->with('user:id,name,nomor_anggota')])
            ->loadCount('activeLoans');

        return view('books.show', [
            'book' => $book,
            'reservasiAktif' => $book->reservations()->aktif()->with('user:id,name,nomor_anggota')->orderBy('antrean')->get(),
        ]);
    }

    public function edit(Book $book): View
    {
        return view('books.edit', [
            'book' => $book,
            'kategori' => Category::query()->orderBy('nama')->get(),
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $data = $request->safe()->except('cover');
        $data['cover_path'] = $this->simpanSampul($request) ?? $book->cover_path;

        $book->update($data);

        ActivityLog::catat(
            $request->user(),
            'buku-ubah',
            $book->isbn,
            "Data \"{$book->judul}\" diperbarui",
        );

        return redirect()
            ->route('books.show', $book)
            ->with('success', "Data buku \"{$book->judul}\" diperbarui.");
    }

    public function destroy(Request $request, Book $book): RedirectResponse
    {
        $masihDipinjam = $book->loans()->aktif()->exists();

        if ($masihDipinjam) {
            return back()->with('error', "\"{$book->judul}\" masih dipinjam, tidak bisa dihapus. Kembalikan dulu atau batalkan peminjaman.");
        }

        $this->hapusSampul($book);
        $judul = $book->judul;
        $book->delete();

        ActivityLog::catat($request->user(), 'buku-hapus', null, "\"{$judul}\" dihapus dari katalog");

        return redirect()
            ->route('books.index')
            ->with('success', "Buku \"{$judul}\" dihapus dari katalog.");
    }

    private function simpanSampul(StoreBookRequest $request): ?string
    {
        if (! $request->hasFile('cover')) {
            return null;
        }

        return $request->file('cover')->store('sampul-buku', 'public');
    }

    private function hapusSampul(Book $book): void
    {
        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }
    }

    private function subqueryPinjamanAktif(): QueryBuilder
    {
        return Loan::query()
            ->selectRaw('count(*)')
            ->whereColumn('loans.book_id', 'books.id')
            ->where('loans.status', LoanStatus::Dipinjam->value);
    }
}
