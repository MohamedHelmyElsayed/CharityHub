<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteer_slot_requests', function (Blueprint $table) {
            // Make legacy columns nullable so new shift-based requests don't
            // fail when these aren't explicitly provided from the form
            $table->date('requested_date')->nullable()->change();
            $table->time('requested_start_time')->nullable()->change();
            $table->time('requested_end_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('volunteer_slot_requests', function (Blueprint $table) {
            $table->date('requested_date')->nullable(false)->change();
            $table->time('requested_start_time')->nullable(false)->change();
            $table->time('requested_end_time')->nullable(false)->change();
        });
    }
};
