<?php

namespace App\Models;

use App\Notifications\LowStockIngredientsNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class Ingredient extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::updated(function (Ingredient $ingredient) {
            if ($ingredient->current_stock_level <= $ingredient->minimum_stock_level && ! $ingredient->minimum_stock_reached_notification_sent_at) {
                Notification::route('mail', config('mail.from.address'))->notify(new LowStockIngredientsNotification($ingredient));
                $ingredient->update(['minimum_stock_reached_notification_sent_at' => now()]);
            }
        });
    }
}
