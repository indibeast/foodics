<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

class IngredientFactory extends Factory
{
    protected $model = Ingredient::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'current_stock_level' => 10_000,
            'minimum_stock_level' => 5_000,
        ];
    }

    public function name($name)
    {
        return $this->state(['name' => $name]);
    }

    public function currentStockLevel($level)
    {
        return $this->state(['current_stock_level' => $level]);
    }

    public function minimumStockLevel($level)
    {
        return $this->state(['minimum_stock_level' => $level]);
    }
}
