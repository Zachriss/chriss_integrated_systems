<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('department'); // IT, Electrical, Stationery, Cashier, Networking
            $table->decimal('salary', 12, 2)->nullable();
            $table->timestamps();

            $table->index('department');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_profiles');
    }
};