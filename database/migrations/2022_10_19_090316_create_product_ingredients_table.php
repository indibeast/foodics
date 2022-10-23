<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_ingredient', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('ingredient_id');
            $table->integer('amount');
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('ingredient_id')->references('id')->on('ingredients');
        });
    }
};
