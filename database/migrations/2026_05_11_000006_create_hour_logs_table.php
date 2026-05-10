<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hour_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_log_id')->constrained('attendance_logs')->cascadeOnDelete();
            $table->decimal('calculated_hours', 6, 2);
            $table->decimal('approved_hours', 6, 2)->nullable();
            $table->text('adjustment_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['pending_review', 'approved', 'adjusted', 'rejected'])->default('pending_review');
            $table->timestamps();

            $table->index(['volunteer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hour_logs');
    }
};
