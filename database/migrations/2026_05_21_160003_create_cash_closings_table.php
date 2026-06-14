<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_closings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_point_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_channel_id')->constrained()->cascadeOnDelete();
            $table->decimal('closing_balance', 12, 2);
            $table->decimal('expected_balance', 12, 2);
            $table->decimal('difference', 12, 2);
            $table->date('closing_date');
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['cash_point_id', 'payment_channel_id', 'closing_date'], 'cp_closings_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_closings');
    }
};