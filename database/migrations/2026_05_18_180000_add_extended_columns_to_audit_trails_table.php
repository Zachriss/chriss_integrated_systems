<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_trails', function (Blueprint $table) {
            if (! Schema::hasColumn('audit_trails', 'actor_name')) {
                $table->string('actor_name')->nullable()->after('actor_type');
            }

            if (! Schema::hasColumn('audit_trails', 'subject_type')) {
                $table->nullableMorphs('subject');
            }

            if (! Schema::hasColumn('audit_trails', 'subject_name')) {
                $table->string('subject_name')->nullable()->after('subject_id');
            }

            if (! Schema::hasColumn('audit_trails', 'route_name')) {
                $table->string('route_name')->nullable()->after('subject_name');
            }

            if (! Schema::hasColumn('audit_trails', 'method')) {
                $table->string('method', 10)->nullable()->after('route_name');
            }

            if (! Schema::hasColumn('audit_trails', 'url')) {
                $table->text('url')->nullable()->after('method');
            }

            if (! Schema::hasColumn('audit_trails', 'status_code')) {
                $table->unsignedSmallInteger('status_code')->nullable()->after('user_agent');
            }

            if (! Schema::hasColumn('audit_trails', 'old_values')) {
                $table->json('old_values')->nullable()->after('status_code');
            }

            if (! Schema::hasColumn('audit_trails', 'new_values')) {
                $table->json('new_values')->nullable()->after('old_values');
            }

            if (! Schema::hasColumn('audit_trails', 'metadata')) {
                $table->json('metadata')->nullable()->after('new_values');
            }
        });
    }

    public function down(): void
    {
        Schema::table('audit_trails', function (Blueprint $table) {
            if (Schema::hasColumn('audit_trails', 'subject_type') && Schema::hasColumn('audit_trails', 'subject_id')) {
                $table->dropMorphs('subject');
            }

            foreach (['actor_name', 'subject_name', 'route_name', 'method', 'url', 'status_code', 'old_values', 'new_values', 'metadata'] as $column) {
                if (Schema::hasColumn('audit_trails', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
