<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'slug')) {
                $table->string('slug')->unique()->after('title');
            }
            if (!Schema::hasColumn('campaigns', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('description');
            }
            if (!Schema::hasColumn('campaigns', 'short_description')) {
                $table->string('short_description', 300)->nullable()->after('description');
            }
            if (!Schema::hasColumn('campaigns', 'featured')) {
                $table->boolean('featured')->default(false)->after('status');
            }
            if (!Schema::hasColumn('campaigns', 'og_title')) {
                $table->string('og_title')->nullable();
            }
            if (!Schema::hasColumn('campaigns', 'og_description')) {
                $table->text('og_description')->nullable();
            }
            if (!Schema::hasColumn('campaigns', 'facebook_url')) {
                $table->string('facebook_url')->nullable();
            }
            if (!Schema::hasColumn('campaigns', 'twitter_url')) {
                $table->string('twitter_url')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['slug', 'cover_image', 'short_description', 'featured', 'og_title', 'og_description', 'facebook_url', 'twitter_url']);
        });
    }
};
