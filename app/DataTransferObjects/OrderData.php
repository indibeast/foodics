<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class OrderData extends Data
{
    public function __construct(
        public readonly int $total,
        #[DataCollectionOf(OrderItemData::class)]
        public DataCollection $orderItems
    ) {
    }

    public static function fromRequest(Request $request): static
    {
        $orderItems = $request->collect('products')
            ->map(fn ($item) => OrderItemData::fromProductIdAndQty($item['product_id'], $item['quantity']));

        return new self(
            total: $orderItems->sum('total'),
            orderItems: OrderItemData::collection($orderItems->all())
        );
    }
}
