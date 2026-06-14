<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('opening_mpesa', 12, 2)->default(0);
            $table->decimal('opening_airtel', 12, 2)->default(0);
            $table->decimal('opening_tigo', 12, 2)->default(0);
            $table->decimal('opening_halo', 12, 2)->default(0);
            $table->decimal('opening_cash', 12, 2)->default(0);
            $table->decimal('closing_mpesa', 12, 2)->default(0);
            $table->decimal('closing_airtel', 12, 2)->default(0);
            $table->decimal('closing_tigo', 12, 2)->default(0);
            $table->decimal('closing_halo', 12, 2)->default(0);
            $table->decimal('closing_cash', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['admin_id', 'date']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_points');
    }
};