<?php

namespace AngeArsene\Chat\Tests\Database\Factories;

use AngeArsene\Chat\Tests\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name()
        ];
    }

    public function modelName()
    {
        return Book::class;
    }
}
