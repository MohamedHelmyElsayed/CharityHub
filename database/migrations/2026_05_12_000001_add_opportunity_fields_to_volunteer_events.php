<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteer_events', function (Blueprint $table) {
            $table->string('category')->default('general')->after('event_type');
            $table->text('requirements')->nullable()->after('description');
            $table->text('benefits')->nullable()->after('requirements');
            $table->string('banner_image')->nullable()->after('cover_image');
        });
    }

    public function down(): void
    {
        Schema::table('volunteer_events', function (Blueprint $table) {
            $table->dropColumn(['category', 'requirements', 'benefits', 'banner_image']);
        });
    }
};
