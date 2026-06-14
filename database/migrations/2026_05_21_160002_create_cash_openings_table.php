<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_openings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_point_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_channel_id')->constrained()->cascadeOnDelete();
            $table->decimal('opening_balance', 12, 2);
            $table->date('opening_date');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_locked')->default(true);
            $table->timestamps();

            $table->unique(['cash_point_id', 'payment_channel_id', 'opening_date'], 'cp_openings_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_openings');
    }
};