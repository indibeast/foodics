<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('current_stock_level');
            $table->bigInteger('minimum_stock_level');
            $table->timestamp('minimum_stock_reached_notification_sent_at')->nullable();
            $table->timestamps();
        });
    }
};
