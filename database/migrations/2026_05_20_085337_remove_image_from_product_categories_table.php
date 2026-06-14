<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_categories') && Schema::hasColumn('product_categories', 'image')) {
            Schema::table('product_categories', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('product_categories') && !Schema::hasColumn('product_categories', 'image')) {
            Schema::table('product_categories', function (Blueprint $table) {
                $table->string('image')->nullable()->after('description');
            });
        }
    }
};