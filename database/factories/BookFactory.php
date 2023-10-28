<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template TModel of \App\Models\Book
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class BookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->title,
            'description' => fake()->sentence,
            'author' => fake()->name,
            'user_id' => null,
            'cover_url' => null,
            'price' => fake()->randomFloat(2, 10, 60),
        ];
    }
}
