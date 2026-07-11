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
            $table->foreignId('cash_point_id')->constrained('cash_points')->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->date('opening_date');
            $table->boolean('is_locked')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['cash_point_id', 'provider_id', 'opening_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_openings');
    }
};