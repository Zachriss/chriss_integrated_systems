<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();

            // Who did the action
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('actor_type')->nullable();
            $table->string('actor_name')->nullable();

            // What happened
            $table->string('action')->nullable();
            $table->string('module')->nullable();
            $table->text('description')->nullable();
            $table->nullableMorphs('subject');
            $table->string('subject_name')->nullable();
            $table->string('route_name')->nullable();
            $table->string('method', 10)->nullable();
            $table->text('url')->nullable();

            // System tracking
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();

            // FIX: required by your system (notifications/log filtering)
            $table->string('status')->default('unread');

            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // Optional but recommended index for performance
            $table->index(['actor_id', 'actor_type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
