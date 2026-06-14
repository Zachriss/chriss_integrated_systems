<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashpoint_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Opening balances
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('opening_mpesa_float', 15, 2)->default(0);
            $table->decimal('opening_airtel_float', 15, 2)->default(0);
            $table->decimal('opening_mixx_float', 15, 2)->default(0);
            $table->decimal('opening_halopesa_float', 15, 2)->default(0);

            // Closing balances
            $table->decimal('closing_cash', 15, 2)->default(0);
            $table->decimal('closing_mpesa_float', 15, 2)->default(0);
            $table->decimal('closing_airtel_float', 15, 2)->default(0);
            $table->decimal('closing_mixx_float', 15, 2)->default(0);
            $table->decimal('closing_halopesa_float', 15, 2)->default(0);

            // Automatic calculations
            $table->decimal('cash_difference', 15, 2)->default(0);
            $table->decimal('mpesa_difference', 15, 2)->default(0);
            $table->decimal('airtel_difference', 15, 2)->default(0);
            $table->decimal('mixx_difference', 15, 2)->default(0);
            $table->decimal('halopesa_difference', 15, 2)->default(0);

            // Status
            $table->enum('status', ['Open', 'Closed'])->default('Open');

            // Dates
            $table->date('session_date');
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            // Only one open session per user per day
            $table->unique(['user_id', 'session_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashpoint_sessions');
    }
};