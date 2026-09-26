<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'isbn' => (string) $this->faker->unique()->numerify('978##########'),
            'judul' => rtrim($this->faker->sentence(4), '.'),
            'penulis' => $this->faker->name(),
            'penerbit' => $this->faker->randomElement([
                'Gramedia',
                'Elex Media Komputindo',
                'Kanisius',
                'Gramedia Widiasarana Indonesia',
                'Pearson Indonesia',
                'O Reilly Media',
                'Bentang Pustaka',
            ]),
            'tahun_terbit' => $this->faker->numberBetween(2010, 2024),
            'category_id' => Category::factory(),
            'stok' => $this->faker->numberBetween(1, 6),
            'deskripsi' => $this->faker->paragraph(),
            'cover_path' => null,
        ];
    }

    public function habis(): static
    {
        return $this->state(fn () => ['stok' => 0]);
    }
}
