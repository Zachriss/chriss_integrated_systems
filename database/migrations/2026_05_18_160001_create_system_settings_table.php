<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('system_name')->default('Chriss Integrated Systems');
            $table->string('system_logo')->nullable();
            $table->string('system_favicon')->nullable();
            $table->string('primary_color', 7)->default('#1a73e8');
            $table->string('secondary_color', 7)->default('#6c757d');
            $table->string('login_background')->nullable();
            $table->string('currency', 10)->default('TZS');
            $table->string('timezone', 50)->default('Africa/Dar_es_Salaam');
            $table->string('email_from_name')->nullable();
            $table->string('email_from_address')->nullable();
            $table->boolean('maintenance_mode')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};