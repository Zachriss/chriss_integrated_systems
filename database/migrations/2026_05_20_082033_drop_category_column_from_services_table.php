<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Migrate any remaining string 'category' data to category_id relationship
            DB::statement("UPDATE services s LEFT JOIN service_categories sc ON LOWER(REPLACE(sc.name, ' ', '')) = LOWER(REPLACE(s.category, ' ', '')) SET s.category_id = sc.id WHERE s.category_id IS NULL AND s.category IS NOT NULL AND s.category != ''");

            // Drop the old string category column
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('category')->nullable()->after('name');
        });
    }
};
