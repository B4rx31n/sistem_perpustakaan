<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Penjaga untuk halaman yang pernah membalas HTTP 500 karena view hilang atau
 * data yang salah kolom. Daftar rute di sini sengaja mengikuti routes/web.php.
 */
class HalamanAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function halamanAdmin(): array
    {
        return [
            'dashboard' => ['dashboard'],
            'daftar buku' => ['books.index'],
            'form tambah buku' => ['books.create'],
            'daftar kategori' => ['categories.index'],
            'daftar peminjaman' => ['loans.index'],
            'form peminjaman baru' => ['loans.create'],
            'daftar anggota' => ['members.index'],
            'riwayat pembayaran' => ['payments.index'],
            'form terima pembayaran' => ['payments.create'],
            'tagihan denda' => ['payments.receivables'],
            'daftar reservasi' => ['reservations.index'],
            'menu laporan' => ['reports.index'],
            'laporan peminjaman' => ['reports.loans'],
            'laporan koleksi' => ['reports.collection'],
            'laporan keterlambatan' => ['reports.overdue'],
            'laporan aktivitas' => ['reports.activity'],
            'laporan keanggotaan' => ['reports.members'],
            'laporan reservasi' => ['reports.reservations'],
            'profil sendiri' => ['profile.edit'],
        ];
    }

    #[DataProvider('halamanAdmin')]
    public function test_halaman_admin_dapat_dirender(string $namaRute): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route($namaRute))
            ->assertOk();
    }

    /**
     * View yang lupa dibungkus layout hanya mengembalikan fragmen HTML, jadi
     * seluruh CSS dan navigasi lenyap tanpa error sama sekali. Sidebar adalah
     * satu-satunya navigasi, jadi halaman tanpa sidebar dianggap rusak juga.
     */
    #[DataProvider('halamanAdmin')]
    public function test_halaman_admin_memakai_layout_dengan_css(string $namaRute): void
    {
        $tanggapan = $this->actingAs(User::factory()->admin()->create())
            ->get(route($namaRute))
            ->assertOk();

        $tanggapan->assertSee('<html lang="id"', escape: false);
        $tanggapan->assertSee('konten-utama', escape: false);
        $tanggapan->assertSee('rel="stylesheet"', escape: false);
        $tanggapan->assertSee('id="sidebar"', escape: false);
        $tanggapan->assertSee('data-sidebar-toggle', escape: false);
    }

    public function test_halaman_anggota_dan_kartu_anggota_dapat_dirender(): void
    {
        $admin = User::factory()->admin()->create();
        $anggota = User::factory()->create();
        $buku = Book::factory()->for(Category::factory())->create();
        $peminjaman = Loan::factory()->for($buku)->for($anggota)->terlambat()->create();

        Payment::factory()->for($peminjaman)->for($anggota)->create();
        Reservation::factory()->for($buku)->for($anggota)->create();

        $this->actingAs($admin)->get(route('members.show', $anggota))->assertOk();
        $this->actingAs($admin)->get(route('members.edit', $anggota))->assertOk();
        $this->actingAs($admin)->get(route('members.card', $anggota))->assertOk();
        $this->actingAs($admin)->get(route('loans.show', $peminjaman))->assertOk();
        $this->actingAs($admin)->get(route('reservations.show', Reservation::first()))->assertOk();
    }

    public function test_anggota_hanya_melihat_halaman_yang_diizinkan(): void
    {
        $anggota = User::factory()->create();

        $this->actingAs($anggota)->get(route('dashboard'))->assertOk();
        $this->actingAs($anggota)->get(route('profile.edit'))->assertOk();
        $this->actingAs($anggota)->get(route('members.index'))->assertRedirect();
        $this->actingAs($anggota)->get(route('reports.index'))->assertRedirect();
    }
}
