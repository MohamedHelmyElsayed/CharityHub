<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            if (!Schema::hasColumn('volunteers', 'name')) {
                $table->string('name')->after('user_id');
            }
            if (!Schema::hasColumn('volunteers', 'email')) {
                $table->string('email')->nullable()->after('name');
            }
            if (!Schema::hasColumn('volunteers', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('volunteers', 'skills')) {
                $table->json('skills')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('volunteers', 'bio')) {
                $table->text('bio')->nullable()->after('skills');
            }
            if (!Schema::hasColumn('volunteers', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropColumn(['name', 'email', 'phone', 'skills', 'bio', 'emergency_contact']);
        });
    }
};
