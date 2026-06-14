<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration is now redundant as these fields are in the base users table migration
        // Keeping this empty to avoid conflicts
    }

    public function down(): void
    {
        // This migration is now redundant
    }
};