<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'kode' => 'BYR-'.now()->format('Ymd').'-'.str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'loan_id' => Loan::factory()->dikembalikan(),
            'petugas_id' => null,
            'jumlah' => $this->faker->numberBetween(1, 20) * 1000,
            'metode' => PaymentMethod::Tunai,
            'bukti' => null,
            'keterangan' => null,
            'dibayar_pada' => now(),
        ];
    }
}
