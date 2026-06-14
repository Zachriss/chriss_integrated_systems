<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('method')->default('cash'); // cash, mobile_money
            $table->string('reference_code')->nullable();
            $table->string('status')->default('pending'); // pending, paid, failed
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('reference_code');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};