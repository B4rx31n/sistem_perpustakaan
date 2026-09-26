<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Petugas = 'petugas';
    case Anggota = 'anggota';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Petugas => 'Petugas',
            self::Anggota => 'Anggota',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Admin => 'Akses penuh ke katalog, anggota, dan laporan',
            self::Petugas => 'Menangani peminjaman, pengembalian, dan katalog',
            self::Anggota => 'Meminjam, mengembalikan, dan memesan buku',
        };
    }

    /**
     * Peran yang boleh masuk ke halaman operasional, yaitu yang mengelola buku.
     *
     * @return array<int, string>
     */
    public static function petugasValues(): array
    {
        return [self::Admin->value, self::Petugas->value];
    }
}
