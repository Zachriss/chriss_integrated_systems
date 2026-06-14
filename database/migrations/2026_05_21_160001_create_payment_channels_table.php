<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type')->default('mobile_money');
            $table->string('status')->default('active');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Seed default channels
        DB::table('payment_channels')->insert([
            ['name' => 'Cash', 'code' => 'cash', 'type' => 'cash', 'status' => 'active', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'M-Pesa', 'code' => 'mpesa', 'type' => 'mobile_money', 'status' => 'active', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Airtel Money', 'code' => 'airtel', 'type' => 'mobile_money', 'status' => 'active', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tigo Pesa', 'code' => 'tigo', 'type' => 'mobile_money', 'status' => 'active', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Halotel', 'code' => 'halotel', 'type' => 'mobile_money', 'status' => 'active', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_channels');
    }
};