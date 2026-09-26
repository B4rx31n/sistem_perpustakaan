<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('beranda');

/*
|--------------------------------------------------------------------------
| Tamu
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [LoginController::class, 'create'])->name('login');
    Route::post('/masuk', [LoginController::class, 'store'])->middleware('throttle:login');

    Route::get('/daftar', [RegisterController::class, 'create'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'store'])->middleware('throttle:login');
});

/*
|--------------------------------------------------------------------------
| Sudah masuk
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    | Katalog terbuka untuk semua yang sudah masuk, karena anggota perlu melihat
    | buku yang tersedia sebelum memutuskan untuk datang ke perpustakaan.
    */
    Route::get('/buku', [BookController::class, 'index'])->name('books.index');

    /*
    | Id buku selalu angka, jadi segmen route-nya dibatasi angka. Tanpa ini
    | /buku/tambah akan tertangkap route show dan menghasilkan ModelNotFound.
    */
    Route::get('/buku/{book}', [BookController::class, 'show'])
        ->whereNumber('book')
        ->name('books.show');

    Route::get('/reservasi', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservasi/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservasi', [ReservationController::class, 'store'])->name('reservations.store');
    Route::delete('/reservasi/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

    /*
    |--------------------------------------------------------------------------
    | Petugas dan administrator
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('/peminjaman', [LoanController::class, 'index'])->name('loans.index');
        Route::get('/peminjaman/baru', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/peminjaman', [LoanController::class, 'store'])
            ->middleware('throttle:peminjaman')
            ->name('loans.store');
        Route::get('/peminjaman/{loan}', [LoanController::class, 'show'])->name('loans.show');
        Route::post('/peminjaman/{loan}/kembalikan', [LoanController::class, 'kembalikan'])->name('loans.return');
        Route::post('/peminjaman/{loan}/perpanjang', [LoanController::class, 'perpanjang'])->name('loans.extend');

        Route::get('/anggota', [MemberController::class, 'index'])->name('members.index');
        Route::get('/anggota/{member}', [MemberController::class, 'show'])->name('members.show');
        Route::get('/anggota/{member}/kartu', [MemberController::class, 'kartu'])->name('members.card');
        Route::get('/anggota/{member}/ubah', [MemberController::class, 'edit'])->name('members.edit');
        Route::put('/anggota/{member}', [MemberController::class, 'update'])->name('members.update');

        Route::get('/pembayaran', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/pembayaran/baru', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/pembayaran', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/tagihan-denda', [PaymentController::class, 'piutang'])->name('payments.receivables');

        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/peminjaman', [ReportController::class, 'peminjaman'])->name('reports.loans');
        Route::get('/laporan/koleksi', [ReportController::class, 'koleksi'])->name('reports.collection');
        Route::get('/laporan/keterlambatan', [ReportController::class, 'keterlambatan'])->name('reports.overdue');
        Route::get('/laporan/aktivitas', [ReportController::class, 'aktivitas'])->name('reports.activity');
        Route::get('/laporan/keanggotaan', [ReportController::class, 'keanggotaan'])->name('reports.members');
        Route::get('/laporan/reservasi', [ReportController::class, 'reservasi'])->name('reports.reservations');
    });

    /*
    |--------------------------------------------------------------------------
    | Administrator
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {
        Route::get('/buku/tambah', [BookController::class, 'create'])->name('books.create');
        Route::post('/buku', [BookController::class, 'store'])->name('books.store');
        Route::get('/buku/{book}/ubah', [BookController::class, 'edit'])->whereNumber('book')
            ->name('books.edit');
        Route::put('/buku/{book}', [BookController::class, 'update'])->whereNumber('book')
            ->name('books.update');
        Route::delete('/buku/{book}', [BookController::class, 'destroy'])->whereNumber('book')
            ->name('books.destroy');

        Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/kategori', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/kategori/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
});
