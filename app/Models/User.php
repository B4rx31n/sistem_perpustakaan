<?php

namespace App\Models;

use App\Enums\LoanStatus;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nomor_anggota',
        'no_hp',
        'program_studi',
        'tanggal_lahir',
        'alamat',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'tanggal_lahir' => 'date',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isPetugas(): bool
    {
        return $this->role === UserRole::Petugas || $this->role === UserRole::Admin;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Peminjaman yang masih berjalan, dengan buku dan kategori yang sudah dimuat
     * supaya halaman anggota tidak melakukan query per baris.
     */
    public function activeLoans(): HasMany
    {
        return $this->loans()
            ->where('status', LoanStatus::Dipinjam->value)
            ->with(['book:id,judul,isbn', 'book.category:id,nama'])
            ->latest('dipinjam_at');
    }

    public function scopeAnggota(Builder $query): Builder
    {
        return $query->where('role', UserRole::Anggota->value);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function scopeCari(Builder $query, ?string $keyword): Builder
    {
        if (blank($keyword)) {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($keyword) {
            $inner->where('name', 'like', "%{$keyword}%")
                ->orWhere('nomor_anggota', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%")
                ->orWhere('program_studi', 'like', "%{$keyword}%");
        });
    }
}
