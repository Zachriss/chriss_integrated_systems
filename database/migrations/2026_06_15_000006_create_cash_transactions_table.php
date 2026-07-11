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
            $table->foreignId('cash_point_id')->constrained('cash_points')->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->enum('transaction_type', ['deposit', 'withdraw']);
            $table->decimal('amount', 12, 2);
            $table->decimal('fee', 12, 2)->default(0);
            $table->decimal('agent_commission', 12, 2)->default(0);
            $table->decimal('system_commission', 12, 2)->default(0);
            $table->string('reference_number')->nullable();
            $table->date('transaction_date');
            $table->timestamps();

            $table->index(['cash_point_id', 'transaction_date']);
            $table->index(['provider_id', 'transaction_date']);
            $table->index('staff_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};