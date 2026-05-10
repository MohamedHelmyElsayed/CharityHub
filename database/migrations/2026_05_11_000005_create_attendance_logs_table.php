<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('volunteer_shifts')->cascadeOnDelete();
            $table->timestamp('check_in');
            $table->timestamp('check_out')->nullable();
            $table->enum('check_in_method', ['manual', 'qr_code', 'self'])->default('manual');
            $table->enum('check_out_method', ['manual', 'qr_code', 'self'])->nullable();
            $table->string('ip_address')->nullable();
            $table->json('location_data')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['checked_in', 'checked_out', 'verified', 'disputed', 'absent'])->default('checked_in');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['volunteer_id', 'shift_id'], 'attendance_volunteer_shift_unique');
            $table->index(['shift_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
