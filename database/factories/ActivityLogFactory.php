<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->petugas(),
            'nama_aktor' => $this->faker->name(),
            'aksi' => $this->faker->randomElement(['pinjam', 'kembalikan', 'perpanjang']),
            'subjek' => 'PMJ-'.$this->faker->numerify('20260926-####'),
            'keterangan' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
