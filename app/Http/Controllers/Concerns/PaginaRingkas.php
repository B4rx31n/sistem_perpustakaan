<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait PaginaRingkas
{
    /**
     * Jumlah baris per halaman untuk tabel. Nilai diambil dari query string supaya
     * pilihan petugas bertahan saat membuka halaman berikutnya, dan dibatasi agar
     * tidak ada permintaan yang menarik ribuan baris sekaligus.
     */
    protected function perHalaman(Request $request, int $bawaan = 20): int
    {
        $diminta = $request->integer('halaman');

        if ($diminta < 1) {
            return $bawaan;
        }

        return min(100, $diminta);
    }
}
