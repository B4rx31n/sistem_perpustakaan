<?php

namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'kode' => 'RSV-'.now()->format('Ymd').'-'.str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'book_id' => Book::factory(),
            'user_id' => User::factory(),
            'status' => ReservationStatus::Menunggu,
            'antrean' => 1,
            'berlaku_sampai' => now()->addDays(3),
        ];
    }

    public function kedaluwarsa(): static
    {
        return $this->state(fn () => ['berlaku_sampai' => now()->subDay()]);
    }
}
