<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            // Remove icon column - categories should NOT have images
            $table->dropColumn('icon');
            
            // Add created_by for audit trail
            $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
            $table->string('icon')->nullable()->after('description');
        });
    }
};