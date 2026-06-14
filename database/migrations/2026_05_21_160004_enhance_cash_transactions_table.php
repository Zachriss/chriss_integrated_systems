<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->after('cash_point_id')->constrained('users')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->after('staff_id')->constrained('customers')->nullOnDelete();
            $table->foreignId('from_channel_id')->nullable()->after('customer_id')->constrained('payment_channels')->nullOnDelete();
            $table->foreignId('to_channel_id')->nullable()->after('from_channel_id')->constrained('payment_channels')->nullOnDelete();
            $table->foreignId('payment_channel_id')->nullable()->after('to_channel_id')->constrained('payment_channels')->nullOnDelete();
            $table->string('transaction_type')->default('income')->after('payment_channel_id'); // income, transfer, adjustment
            $table->string('reference_number')->nullable()->after('amount');
            $table->date('transaction_date')->nullable()->after('reference_number');
        });
    }

    public function down(): void
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropForeign(['staff_id','customer_id','from_channel_id','to_channel_id','payment_channel_id']);
            $table->dropColumn(['staff_id','customer_id','from_channel_id','to_channel_id','payment_channel_id','transaction_type','reference_number','transaction_date']);
        });
    }
};