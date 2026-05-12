<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            // Extended profile fields
            if (!Schema::hasColumn('volunteers', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('volunteers', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('volunteers', 'address')) {
                $table->text('address')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('volunteers', 'interests')) {
                $table->json('interests')->nullable()->after('skills');
            }
            if (!Schema::hasColumn('volunteers', 'availability')) {
                $table->json('availability')->nullable()->after('interests');
            }
            if (!Schema::hasColumn('volunteers', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('availability');
            }
            if (!Schema::hasColumn('volunteers', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('volunteers', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('emergency_contact_phone');
            }
            if (!Schema::hasColumn('volunteers', 'total_approved_hours')) {
                $table->decimal('total_approved_hours', 10, 2)->default(0)->after('total_hours');
            }
            if (!Schema::hasColumn('volunteers', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('total_approved_hours');
            }
            if (!Schema::hasColumn('volunteers', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('approved_at');
            }
            if (!Schema::hasColumn('volunteers', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('approved_by');
            }
        });

        // Update status enum to support all required statuses
        // We do this via raw SQL because MySQL enums need ALTER TABLE
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE volunteers MODIFY COLUMN status ENUM('pending','approved','active','rejected','suspended','inactive') NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('volunteers', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'date_of_birth', 'gender', 'address', 'interests', 'availability',
                'emergency_contact_name', 'emergency_contact_phone', 'profile_photo',
                'total_approved_hours', 'approved_at', 'approved_by', 'internal_notes',
            ]);
        });
    }
};
