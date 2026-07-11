<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_profit_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->date('report_date');
            $table->integer('total_transactions')->default(0);
            $table->decimal('total_fees', 12, 2)->default(0);
            $table->decimal('agent_profit', 12, 2)->default(0);
            $table->decimal('system_profit', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['provider_id', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_profit_summaries');
    }
};