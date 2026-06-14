<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->foreignId('category_id')->nullable()->after('sku')->constrained('product_categories')->nullOnDelete();
            $table->string('brand')->nullable()->after('category_id');
            $table->text('short_description')->nullable()->after('brand');
            $table->text('description')->nullable()->after('short_description');
            $table->string('image')->nullable()->after('low_stock_alert_level');
            $table->string('barcode')->nullable()->after('image');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('barcode');
            $table->boolean('is_featured')->default(false)->after('status');
            $table->foreignId('created_by')->nullable()->after('is_featured')->constrained('users')->nullOnDelete();
            
            $table->index('category_id');
            $table->index('status');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'slug', 'category_id', 'brand', 'short_description',
                'description', 'image', 'barcode', 'status', 'is_featured', 'created_by'
            ]);
        });
    }
};