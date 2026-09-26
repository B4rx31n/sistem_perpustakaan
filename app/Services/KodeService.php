<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KodeService
{
    /**
     * Nomor dokumen berurutan per hari, misalnya PMJ-20260926-0007.
     *
     * Prefix dipisah per tabel supaya kode yang sama tidak pernah ambigu di log
     * aktivitas, dan bagian tanggal membuat nomor tetap urut dibaca manusia.
     */
    public function buat(string $prefix, string $tabel, string $kolom = 'kode'): string
    {
        $tanggal = Carbon::now()->format('Ymd');
        $dasar = $prefix.'-'.$tanggal.'-';

        $terakhir = DB::table($tabel)
            ->where($kolom, 'like', $dasar.'%')
            ->orderByDesc($kolom)
            ->value($kolom);

        $urutan = $terakhir ? ((int) substr($terakhir, -4)) + 1 : 1;

        return $dasar.str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }
}
