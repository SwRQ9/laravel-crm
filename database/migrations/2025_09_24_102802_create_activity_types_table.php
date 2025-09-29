<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // e.g., "Calls / Email / SocMed"
            $table->string('slug')->unique();                // e.g., "calls_email_socmed"
            $table->string('category')->nullable();          // e.g., "Sales", "Training", "Mindset"
            $table->unsignedSmallInteger('point_value')->default(1);
            $table->unsignedSmallInteger('weight')->default(0); // for UI ordering
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_types');
    }
};
