<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashpoint_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('status', 20)->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Insert default providers
        DB::table('cashpoint_providers')->insert([
            ['name' => 'M-Pesa', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Airtel Money', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mixx by Yas', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HaloPesa', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cashpoint_providers');
    }
};