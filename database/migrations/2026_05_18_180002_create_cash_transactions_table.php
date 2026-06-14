<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_point_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['income', 'expense']);
            $table->enum('payment_method', ['mpesa', 'airtel', 'tigo', 'halo', 'cash']);
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->string('reference')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['cash_point_id', 'type']);
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};