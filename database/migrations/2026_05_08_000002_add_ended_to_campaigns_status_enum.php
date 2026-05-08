<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Alters the campaigns.status ENUM to include 'ended'
     * so that auto-completed (goal-reached) campaigns can be flagged correctly.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `campaigns` MODIFY `status` ENUM('active', 'completed', 'paused', 'ended') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     * NOTE: any rows with status='ended' will be truncated if you roll back.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `campaigns` MODIFY `status` ENUM('active', 'completed', 'paused') NOT NULL DEFAULT 'active'");
    }
};
