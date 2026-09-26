<?php

namespace App\Models;

use App\Enums\LoanStatus;
use App\Enums\ReservationStatus;
use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    protected $fillable = [
        'isbn',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'category_id',
        'stok',
        'deskripsi',
        'cover_path',
    ];

    protected function casts(): array
    {
        return [
            'tahun_terbit' => 'integer',
            'stok' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeLoans(): HasMany
    {
        return $this->loans()->where('status', LoanStatus::Dipinjam->value);
    }

    public function activeReservations(): HasMany
    {
        return $this->reservations()
            ->whereIn('status', ReservationStatus::aktifValues())
            ->orderBy('antrean');
    }

    public function coverUrl(): ?string
    {
        return $this->cover_path ? Storage::disk('public')->url($this->cover_path) : null;
    }

    /**
     * Memakai nilai dari withCount('activeLoans') bila tersedia, supaya daftar
     * katalog tidak menghitung ulang tiap baris.
     */
    public function jumlahDipinjam(): int
    {
        return (int) ($this->active_loans_count ?? $this->activeLoans()->count());
    }

    public function jumlahTersedia(): int
    {
        return max(0, $this->stok - $this->jumlahDipinjam());
    }

    public function isTersedia(): bool
    {
        return $this->jumlahTersedia() > 0;
    }

    public function scopeTersedia(Builder $query): Builder
    {
        $pinjamanAktif = Loan::query()
            ->selectRaw('count(*)')
            ->whereColumn('loans.book_id', 'books.id')
            ->where('loans.status', LoanStatus::Dipinjam->value);

        return $query->where('stok', '>', $pinjamanAktif);
    }

    public function scopeCari(Builder $query, ?string $keyword): Builder
    {
        if (blank($keyword)) {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($keyword) {
            $inner->where('judul', 'like', "%{$keyword}%")
                ->orWhere('penulis', 'like', "%{$keyword}%")
                ->orWhere('isbn', 'like', "%{$keyword}%")
                ->orWhere('penerbit', 'like', "%{$keyword}%");
        });
    }
}
