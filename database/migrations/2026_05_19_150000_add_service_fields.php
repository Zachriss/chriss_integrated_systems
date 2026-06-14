<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('service_categories')) {
            Schema::create('service_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Add category_id column to services if it doesn't exist
        if (!Schema::hasColumn('services', 'category_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable()->after('category');
                $table->foreign('category_id')->references('id')->on('service_categories')->onDelete('set null');
            });
        }

        // Add other columns if they don't exist
        if (!Schema::hasColumn('services', 'short_description')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('short_description')->nullable()->after('category');
                $table->string('featured_image')->nullable()->after('short_description');
                $table->json('gallery_images')->nullable()->after('featured_image');
                $table->integer('duration_hours')->nullable()->after('base_price');
                $table->boolean('is_featured')->default(false)->after('duration_hours');
                $table->enum('status', ['active', 'inactive'])->default('active')->after('is_featured');
            });
        }

        if (!Schema::hasColumn('services', 'created_by')) {
            Schema::table('services', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable()->after('status');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['short_description', 'featured_image', 'gallery_images', 'duration_hours', 'is_featured', 'status', 'created_by'] as $col) {
                if (Schema::hasColumn('services', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropForeign(['category_id']);
                $table->dropForeign(['created_by']);
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::dropIfExists('service_categories');
    }
};