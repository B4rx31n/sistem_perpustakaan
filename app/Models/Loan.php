<?php

namespace App\Models;

use App\Enums\LoanStatus;
use Database\Factories\LoanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    /** @use HasFactory<LoanFactory> */
    use HasFactory;

    protected $fillable = [
        'kode',
        'book_id',
        'user_id',
        'petugas_id',
        'dipinjam_at',
        'harus_kembali_at',
        'dikembalikan_at',
        'perpanjangan',
        'status',
        'denda',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'dipinjam_at' => 'datetime',
            'harus_kembali_at' => 'date',
            'dikembalikan_at' => 'datetime',
            'perpanjangan' => 'integer',
            'denda' => 'integer',
            'status' => LoanStatus::class,
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isAktif(): bool
    {
        return $this->status === LoanStatus::Dipinjam;
    }

    public function isTerlambat(): bool
    {
        return $this->isAktif() && $this->harus_kembali_at->isPast();
    }

    public function hariTerlambat(): int
    {
        if (! $this->isAktif()) {
            return 0;
        }

        // Operannya harus dibalik: diffInDays bernilai positif bila tanggal
        // kedua lebih akhir. Karena itu yang dihitung adalah jarak dari jatuh
        // tempo ke hari ini, bukan sebaliknya.
        return max(0, $this->harus_kembali_at->startOfDay()->diffInDays(now()->startOfDay(), false));
    }

    public function bisaDiperpanjang(): bool
    {
        return $this->isAktif()
            && $this->perpanjangan < config('perpustakaan.maks_perpanjangan')
            && ! $this->isTerlambat();
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', LoanStatus::Dipinjam->value);
    }

    public function scopeTerlambat(Builder $query): Builder
    {
        return $query->aktif()->whereDate('harus_kembali_at', '<', now());
    }
}
