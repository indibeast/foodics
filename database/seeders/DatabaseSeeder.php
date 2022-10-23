<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $ingredientBeef = Ingredient::factory()
            ->name('Beef')
            ->minimumStockLevel(10_000)
            ->currentStockLevel(20_000)
            ->create();

        $ingredientCheese = Ingredient::factory()
            ->name('Cheese')
            ->minimumStockLevel(2_500)
            ->currentStockLevel(5_000)
            ->create();

        $ingredientOnion = Ingredient::factory()
            ->name('Onion')
            ->minimumStockLevel(500)
            ->currentStockLevel(1_000)
            ->create();

        $productBurger = Product::factory()->name('Burger')
            ->price(1000)
            ->hasAttached($ingredientBeef, ['amount' => 150])
            ->hasAttached($ingredientCheese, ['amount' => 30])
            ->hasAttached($ingredientOnion, ['amount' => 20])
            ->create();
    }
}
