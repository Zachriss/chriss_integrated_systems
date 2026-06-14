<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('role_permission')) {
            Schema::create('role_permission', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
                $table->primary(['role_id', 'permission_id']);
            });
        }

        if (! Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->primary(['user_id', 'role_id']);
            });
        }

        if (Schema::hasTable('role_has_permissions')) {
            DB::table('role_has_permissions')
                ->orderBy('role_id')
                ->get()
                ->each(function ($row): void {
                    DB::table('role_permission')->updateOrInsert([
                        'role_id' => $row->role_id,
                        'permission_id' => $row->permission_id,
                    ]);
                });
        }

        if (Schema::hasTable('user_has_roles')) {
            DB::table('user_has_roles')
                ->orderBy('user_id')
                ->get()
                ->each(function ($row): void {
                    DB::table('role_user')->updateOrInsert([
                        'user_id' => $row->user_id,
                        'role_id' => $row->role_id,
                    ]);
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('role_permission');
    }
};
