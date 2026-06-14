<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'processed_by')) {
                $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('service_requests', 'processed_at')) {
                $table->timestamp('processed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
            $table->dropColumn(['processed_by', 'processed_at']);
        });
    }
};