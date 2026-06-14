<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('system_short_name', 50)->nullable()->after('system_name');
            $table->string('accent_color', 7)->default('#0d6efd')->after('secondary_color');
            $table->string('email', 100)->nullable()->after('accent_color');
            $table->string('phone', 30)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('footer_text', 255)->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'system_short_name',
                'accent_color',
                'email',
                'phone',
                'address',
                'footer_text',
            ]);
        });
    }
};