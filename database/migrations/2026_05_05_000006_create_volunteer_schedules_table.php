<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_volunteers')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('event_date');
            $table->index('status');
        });

        Schema::create('volunteer_schedule_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('volunteer_id')->constrained()->cascadeOnDelete();
            $table->decimal('hours_worked', 5, 2)->nullable();
            $table->enum('status', ['registered', 'attended', 'absent', 'cancelled'])->default('registered');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['volunteer_schedule_id', 'volunteer_id'], 'vol_sched_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_schedule_user');
        Schema::dropIfExists('volunteer_schedules');
    }
};
