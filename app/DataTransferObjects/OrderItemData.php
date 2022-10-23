<?php

namespace App\DataTransferObjects;

use App\Models\Product;
use Spatie\LaravelData\Data;

class OrderItemData extends Data
{
    public function __construct(
        public readonly int $productId,
        public readonly int $productPrice,
        public readonly int $quantity,
        public readonly int $total,
    ) {
    }

    public static function fromProductIdAndQty($productId, $quantity): static
    {
        $product = Product::find($productId);

        return new self(
            productId: $product->id,
            productPrice: $product->price,
            quantity: $quantity,
            total: $product->price * $quantity
        );
    }
}
