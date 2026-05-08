<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('impact_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('impact_reports', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('title');
            }
        });

        // Backfill slugs for existing records
        \DB::table('impact_reports')->whereNull('slug')->orderBy('id')->each(function ($report) {
            \DB::table('impact_reports')->where('id', $report->id)->update([
                'slug' => \Illuminate\Support\Str::slug($report->title) . '-' . $report->id,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('impact_reports', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
