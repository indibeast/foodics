<?php

namespace App\Actions;

use App\Models\Product;

class UpdateStockAmount
{
    public static function execute(Product $product, int $quantity)
    {
        $product->ingredients->each(function ($ingredient) use ($quantity) {
            $currentStockLevel = $ingredient->fresh()->current_stock_level - ($ingredient->pivot->amount * $quantity);
            $ingredient->update([
                'current_stock_level' => $currentStockLevel,
            ]);
        });
    }
}
