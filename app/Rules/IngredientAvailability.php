<?php

namespace App\Rules;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class IngredientAvailability implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $ingredientCollection = Collection::make($value)
            ->filter(fn ($orderItem) => isset($orderItem['product_id'], $orderItem['quantity']) && Product::find($orderItem['product_id']))
            ->map(function ($orderItem) {
                $product = Product::find($orderItem['product_id']);

                $neededIngredients = [];
                $product->ingredients->each(function ($ingredient) use (&$neededIngredients, $orderItem) {
                    $temp['ingredient_id'] = $ingredient->id;
                    $temp['total_amount'] = $ingredient->pivot->amount * $orderItem['quantity'];
                    $neededIngredients = Arr::prepend($neededIngredients, $temp);
                });

                return $neededIngredients;
            });

        $hasIngredients = $ingredientCollection->flatten(1)->groupBy('ingredient_id')->every(function ($value, $key) {
            $ingredient = Ingredient::find($key);
            $totalIngredientNeededInTheOrder = $value->sum('total_amount');

            return $ingredient->current_stock_level >= $totalIngredientNeededInTheOrder;
        });

        if (! $hasIngredients) {
            $fail('Insufficient ingredients');
        }
    }
}
