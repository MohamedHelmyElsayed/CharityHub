<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteer_slot_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('volunteer_slot_requests', 'shift_id')) {
                $table->foreignId('shift_id')->nullable()->constrained('volunteer_shifts')->nullOnDelete()->after('volunteer_id');
            }
            if (!Schema::hasColumn('volunteer_slot_requests', 'requested_at')) {
                $table->timestamp('requested_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('volunteer_slot_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('requested_at');
            }
            if (!Schema::hasColumn('volunteer_slot_requests', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('approved_at');
            }
            if (!Schema::hasColumn('volunteer_slot_requests', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('volunteer_slot_requests', 'rejected_by')) {
                $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete()->after('rejected_at');
            }
            if (!Schema::hasColumn('volunteer_slot_requests', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_by');
            }
            if (!Schema::hasColumn('volunteer_slot_requests', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('rejection_reason');
            }
        });

        // Update status enum
        DB::statement("ALTER TABLE volunteer_slot_requests MODIFY COLUMN status ENUM('pending','approved','rejected','cancelled','completed') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('volunteer_slot_requests', function (Blueprint $table) {
            $table->dropForeign(['shift_id', 'approved_by', 'rejected_by']);
            $table->dropColumn(['shift_id', 'requested_at', 'approved_at', 'approved_by', 'rejected_at', 'rejected_by', 'rejection_reason', 'cancelled_at']);
        });
    }
};
