<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case Menunggu = 'menunggu';
    case Siap = 'siap';
    case Selesai = 'selesai';
    case Kedaluwarsa = 'kedaluwarsa';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu giliran',
            self::Siap => 'Siap diambil',
            self::Selesai => 'Selesai',
            self::Kedaluwarsa => 'Kedaluwarsa',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    /**
     * Status yang masih memegang tempat dalam antrean.
     *
     * @return array<int, string>
     */
    public static function aktifValues(): array
    {
        return [self::Menunggu->value, self::Siap->value];
    }
}
