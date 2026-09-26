<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'role' => UserRole::Anggota,
            'nomor_anggota' => 'ANG-'.str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 5, '0', STR_PAD_LEFT),
            'no_hp' => '08'.$this->faker->numerify('##########'),
            'program_studi' => $this->faker->randomElement([
                'Teknik Informatika',
                'Sistem Informasi',
                'Ilmu Ekonomi',
                'Ilmu Komunikasi',
                'Pendidikan Bahasa Inggris',
                'Manajemen',
                'Akuntansi',
                'Sastra Inggris',
            ]),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-25 years', '-18 years'),
            'alamat' => 'Jl. '.$this->faker->streetName().' No. '.$this->faker->numberBetween(1, 200),
            'status' => 'aktif',
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Admin,
            'nomor_anggota' => null,
            'program_studi' => null,
        ]);
    }

    public function petugas(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Petugas,
            'nomor_anggota' => null,
            'program_studi' => null,
        ]);
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => ['status' => 'nonaktif']);
    }
}
