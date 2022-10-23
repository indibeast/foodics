<?php

namespace App\Actions;

use App\DataTransferObjects\OrderData;
use App\DataTransferObjects\OrderItemData;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class SaveOrderAction
{
    public static function execute(OrderData $orderData)
    {
        DB::transaction(function () use ($orderData) {
            $order = Order::create([
                'total' => $orderData->total,
            ]);

            $orderData->orderItems->each(function (OrderItemData $item) use ($order) {
                $order->orderItems()->create([
                    'product_id' => $item->productId,
                    'quantity' => $item->quantity,
                    'price' => $item->productPrice,
                    'total' => $item->total,
                ]);
            });
        });
    }
}
