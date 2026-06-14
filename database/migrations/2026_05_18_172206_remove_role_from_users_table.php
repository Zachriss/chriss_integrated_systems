<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration is disabled - role field is needed for authentication
        // Keeping this empty to avoid removing the role column
    }

    public function down(): void
    {
        // This migration is disabled
    }
};
