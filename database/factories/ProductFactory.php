<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'price' => fake()->randomNumber(4),
        ];
    }

    public function name(string $name)
    {
        return $this->state(['name' => $name]);
    }

    public function price(int $price)
    {
        return $this->state(['price' => $price]);
    }
}
