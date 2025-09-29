<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('daily_activities', function (Blueprint $table) {
            // Remove the legacy JSON column that’s blocking inserts
            if (Schema::hasColumn('daily_activities', 'activities')) {
                $table->dropColumn('activities');
            }
        });
    }

    public function down(): void
    {
        Schema::table('daily_activities', function (Blueprint $table) {
            // Put it back if you ever rollback (kept nullable so it won’t block inserts)
            if (! Schema::hasColumn('daily_activities', 'activities')) {
                $table->json('activities')->nullable();
            }
        });
    }
};
