<?php

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Notifications\LowStockIngredientsNotification;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('it can place an order', function () {
    expect(Order::count())->toBe(0)
        ->and(OrderItem::count())->toBe(0)
        ->and(Product::first()->ingredients)->sequence(
            fn ($ingredient) => $ingredient->current_stock_level->toBe(20_000),
            fn ($ingredient) => $ingredient->current_stock_level->toBe(5_000),
            fn ($ingredient) => $ingredient->current_stock_level->toBe(1_000)
        );

    $payload = [
        'products' => [
            ['product_id' => Product::first()->id, 'quantity' => 2],
        ],
    ];

    $response = $this->postJson('api/order', $payload);

    $order = Order::first();

    expect(Order::count())->toBe(1)
        ->and(OrderItem::count())->toBe(1)
        ->and($order->total)->toBe(2000);

    tap(OrderItem::first(), function ($item) use ($order) {
        expect($item->price)->toBe(1000)
            ->and($item->quantity)->toBe(2)
            ->and($item->product_id)->toBe(Product::first()->id)
            ->and($item->order_id)->toBe($order->id)
            ->and($item->total)->toBe(2000);
    });

    expect(Product::first()->ingredients)->sequence(
        fn ($ingredient) => $ingredient->current_stock_level->toBe(19_700),
        fn ($ingredient) => $ingredient->current_stock_level->toBe(4_940),
        fn ($ingredient) => $ingredient->current_stock_level->toBe(960)
    );
});

test('it should send a notification when ingredients stock level reaches 50% threshold', function () {
    config(['mail.from.address' => 'test@test.com']);
    Notification::fake();

    $ingredientBeef = Ingredient::first();
    $ingredientBeef->update(['current_stock_level' => 10050]);

    $payload = [
        'products' => [
            ['product_id' => Product::first()->id, 'quantity' => 2],
        ],
    ];

    $response = $this->postJson('api/order', $payload);

    $payload = [
        'products' => [
            ['product_id' => Product::first()->id, 'quantity' => 2],
        ],
    ];

    $response = $this->postJson('api/order', $payload);

    Notification::assertSentOnDemand(LowStockIngredientsNotification::class, function ($notification, $channels, $notifiable) {
        return $notifiable->routes['mail'] === 'test@test.com';
    });

    Notification::assertCount(1);
});

test('it should not send multiple notifications for low stock levels', function () {
    Notification::fake();
    $ingredientBeef = Ingredient::first();
    $ingredientBeef->update(['current_stock_level' => 10050, 'minimum_stock_reached_notification_sent_at' => now()]);

    $payload = [
        'products' => [
            ['product_id' => Product::first()->id, 'quantity' => 2],
        ],
    ];

    $response = $this->postJson('api/order', $payload);

    $payload = [
        'products' => [
            ['product_id' => Product::first()->id, 'quantity' => 2],
        ],
    ];

    $response = $this->postJson('api/order', $payload);

    Notification::assertNothingSent();
});

test('it should now allow to order when there are no ingredients left', function () {
    $ingredientBeef = Ingredient::first();
    $ingredientBeef->update(['current_stock_level' => 200]);

    $payload = [
        'products' => [
            ['product_id' => Product::first()->id, 'quantity' => 2],
        ],
    ];

    $response = $this->postJson('api/order', $payload);

    $payload = [
        'products' => [
            ['product_id' => Product::first()->id, 'quantity' => 2],
        ],
    ];

    $response = $this->postJson('api/order', $payload);
    $response->assertJsonValidationErrors('products');
    expect(Order::count())->toBe(0);
});

test('it should now allow to order when product id is invalid', function () {
    $payload = [
        'products' => [
            ['product_id' => 'invalid_product', 'quantity' => 2],
        ],
    ];

    $response = $this->postJson('api/order', $payload);
    $response->assertJsonValidationErrors('products.0.product_id');
    expect(Order::count())->toBe(0);
});

test('quantity should be greater than zero', function () {
    $payload = [
        'products' => [
            ['product_id' => 'invalid_product', 'quantity' => 0],
        ],
    ];

    $response = $this->postJson('api/order', $payload);
    $response->assertJsonValidationErrors('products.0.quantity');

    expect(Order::count())->toBe(0);
});

test('product id required', function () {
    $payload = [
        'products' => [
            ['quantity' => 1],
        ],
    ];

    $response = $this->postJson('api/order', $payload);
    $response->assertJsonValidationErrors('products.0.product_id');

    expect(Order::count())->toBe(0);
});

test('quantity is required', function () {
    $payload = [
        'products' => [
            ['product_id' => Product::first()->id],
        ],
    ];

    $response = $this->postJson('api/order', $payload);
    $response->assertJsonValidationErrors('products.0.quantity');

    expect(Order::count())->toBe(0);
});
