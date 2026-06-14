<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('can_manage_inventory')->default(false);
            $table->boolean('can_manage_services')->default(false);
            $table->boolean('can_view_reports')->default(true);
            $table->boolean('can_manage_cash_points')->default(true);
            $table->timestamps();

            $table->unique(['admin_id', 'service_id', 'product_id']);
            $table->index(['admin_id', 'can_manage_inventory']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_assignments');
    }
};