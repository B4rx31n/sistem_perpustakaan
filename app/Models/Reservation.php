<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;

    protected $fillable = [
        'kode',
        'book_id',
        'user_id',
        'status',
        'antrean',
        'berlaku_sampai',
        'diselesaikan_at',
        'alasan_batal',
    ];

    protected function casts(): array
    {
        return [
            'berlaku_sampai' => 'datetime',
            'diselesaikan_at' => 'datetime',
            'antrean' => 'integer',
            'status' => ReservationStatus::class,
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

    public function isKedaluwarsa(): bool
    {
        return $this->berlaku_sampai->isPast()
            && in_array($this->status, ReservationStatus::aktifValues(), true);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereIn('status', ReservationStatus::aktifValues());
    }
}
