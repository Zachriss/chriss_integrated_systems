<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->decimal('buying_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('low_stock_alert_level')->default(5);
            $table->timestamps();

            $table->index('sku');
            $table->index('quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};