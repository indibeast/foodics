<?php

namespace App\Models;

use App\Actions\UpdateStockAmount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class OrderItem extends Model
{
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::created(function ($orderItem) {
            DB::afterCommit(function () use ($orderItem) {
                UpdateStockAmount::execute($orderItem->product, $orderItem->quantity);
            });
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
