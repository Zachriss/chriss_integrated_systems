<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_tasks', function (Blueprint $table) {
            // Drop the existing foreign key first
            $table->dropForeign(['service_id']);
            // Make it nullable
            $table->unsignedBigInteger('service_id')->nullable()->change();
            // Re-add foreign key with nullOnDelete
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('staff_tasks', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->unsignedBigInteger('service_id')->nullable(false)->change();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }
};