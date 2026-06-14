<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('read_at');
            $table->timestamp('approved_at')->nullable()->after('is_approved');
            $table->boolean('converted_to_testimonial')->default(false)->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['is_approved', 'approved_at', 'converted_to_testimonial']);
        });
    }
};