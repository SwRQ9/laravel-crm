<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id();

            // Match users.id which is unsigned int(10)
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // one submission per day per user
            $table->date('date');

            // key/value tallies by activity slug: {"calls_email_socmed":5,"cases_closed":1,...}
            $table->json('activities');

            // cached sum of all points for quick reporting
            $table->unsignedMediumInteger('total_points')->default(0);

            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'date']); // enforce one form per user per day
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_activities');
    }
};
