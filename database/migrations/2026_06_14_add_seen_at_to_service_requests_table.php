<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->timestamp('seen_at')->nullable()->after('notes');
            $table->timestamp('responded_at')->nullable()->after('seen_at');
            $table->text('staff_response')->nullable()->after('responded_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['seen_at', 'responded_at', 'staff_response']);
        });
    }
};