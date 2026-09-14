<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds status-attribution columns to vehicles so that the glossary
 * entry for Vehicle Status ("Status changes are timestamped and
 * attributed to a Staff User") is reflected in the schema.
 *
 * Both columns are nullable. They are populated by the Filament
 * status-change row actions on VehicleResource.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->timestamp('status_changed_at')->nullable()->after('status');
            $table->foreignId('status_changed_by')
                ->nullable()
                ->after('status_changed_at')
                ->constrained('staff_users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['status_changed_by']);
            $table->dropColumn(['status_changed_at', 'status_changed_by']);
        });
    }
};
