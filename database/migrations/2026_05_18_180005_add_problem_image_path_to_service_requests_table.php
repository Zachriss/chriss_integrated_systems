<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table): void {
            if (! Schema::hasColumn('service_requests', 'problem_image_path')) {
                $table->string('problem_image_path')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table): void {
            if (Schema::hasColumn('service_requests', 'problem_image_path')) {
                $table->dropColumn('problem_image_path');
            }
        });
    }
};
