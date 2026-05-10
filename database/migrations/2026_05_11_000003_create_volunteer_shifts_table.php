<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('volunteer_events')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('shift_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('required_volunteers')->default(1);
            $table->integer('assigned_count')->default(0);
            $table->string('location')->nullable();
            $table->string('qr_code')->nullable(); // stored path
            $table->string('qr_token')->nullable()->unique(); // secure token for QR validation
            $table->timestamp('qr_expires_at')->nullable();
            $table->enum('status', ['open', 'full', 'completed', 'cancelled'])->default('open');
            $table->timestamps();

            $table->index(['shift_date', 'status']);
            $table->index('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_shifts');
    }
};
