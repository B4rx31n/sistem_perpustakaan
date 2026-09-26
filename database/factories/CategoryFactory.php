<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $nama = $this->faker->unique()->randomElement([
            'Fiksi',
            'Nonfiksi',
            'Sains',
            'Teknologi',
            'Sejarah',
            'Ekonomi',
            'Pendidikan',
            'Sastra',
            'Kotlin',
            'Komik',
        ]);

        return [
            'nama' => $nama,
            'kode' => Str::upper(Str::substr($nama, 0, 3)),
            'keterangan' => $this->faker->sentence(),
        ];
    }
}
