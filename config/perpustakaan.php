<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Peminjaman
    |--------------------------------------------------------------------------
    |
    | Aturan operasional peminjaman. Nilai-nilai ini dibaca oleh LoanService dan
    | ditampilkan sebagai teks bantuan di form, supaya pemohon tidak perlu
    | mencari tahu aturannya sendiri.
    |
    */

    'durasi_peminjaman_hari' => (int) env('PERPUSTAKAAN_DURASI_PINJAMAN', 7),

    'maks_perpanjangan' => (int) env('PERPUSTAKAAN_MAKS_PERPANJANGAN', 1),

    /*
    |--------------------------------------------------------------------------
    | Denda
    |--------------------------------------------------------------------------
    */

    'denda_per_hari' => (int) env('PERPUSTAKAAN_DENDA_PER_HARI', 1000),

    'denda_maks_per_peminjaman' => (int) env('PERPUSTAKAAN_DENDA_MAKS', 100000),

    /*
    |--------------------------------------------------------------------------
    | Reservasi
    |--------------------------------------------------------------------------
    |
    | Reservasi yang tidak diambil dalam masa berlaku akan dibatalkan otomatis
    | oleh scheduler, lalu antrean diteruskan ke anggota berikutnya.
    |
    */

    'masa_berlaku_reservasi_hari' => (int) env('PERPUSTAKAAN_MASA_RESERVASI', 3),

];
