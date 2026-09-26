<?php

namespace Database\Factories;

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        $dipinjam = $this->faker->dateTimeBetween('-40 days', '-1 day');

        return [
            'kode' => 'PMJ-'.now()->format('Ymd').'-'.str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'book_id' => Book::factory(),
            'user_id' => User::factory(),
            'petugas_id' => null,
            'dipinjam_at' => $dipinjam,
            'harus_kembali_at' => (clone $dipinjam)->modify('+7 days'),
            'dikembalikan_at' => null,
            'perpanjangan' => 0,
            'status' => LoanStatus::Dipinjam,
            'denda' => 0,
            'catatan' => null,
        ];
    }

    public function terlambat(int $hari = 5): static
    {
        return $this->state(fn () => [
            'dipinjam_at' => now()->subDays(7 + $hari),
            'harus_kembali_at' => now()->subDays($hari),
        ]);
    }

    public function dikembalikan(?int $hariTerlambat = 0): static
    {
        return $this->state(fn () => [
            'harus_kembali_at' => now()->subDays($hariTerlambat + 1),
            'dikembalikan_at' => now()->subDays($hariTerlambat),
            'status' => LoanStatus::Dikembalikan,
            'denda' => $hariTerlambat * config('perpustakaan.denda_per_hari'),
        ]);
    }
}
