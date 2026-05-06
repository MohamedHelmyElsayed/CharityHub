<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            if (!Schema::hasColumn('volunteers', 'total_hours')) {
                $table->decimal('total_hours', 10, 2)->default(0)->after('bio');
            }
        });

        Schema::table('volunteer_hours', function (Blueprint $table) {
            if (!Schema::hasColumn('volunteer_hours', 'status')) {
                $table->string('status')->default('pending')->after('hours');
            }
            if (!Schema::hasColumn('volunteer_hours', 'volunteer_schedule_id')) {
                $table->foreignId('volunteer_schedule_id')->nullable()->constrained()->onDelete('set null')->after('volunteer_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropColumn('total_hours');
        });

        Schema::table('volunteer_hours', function (Blueprint $table) {
            $table->dropColumn(['status', 'volunteer_schedule_id']);
        });
    }
};
