<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impact_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('outcomes_narrative');
            $table->integer('beneficiary_count')->default(0);
            $table->decimal('funds_used', 12, 2)->default(0);
            $table->string('report_period')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->string('pdf_path')->nullable();
            $table->timestamp('pdf_generated_at')->nullable();
            $table->timestamps();

            $table->index('campaign_id');
            $table->index('status');
        });

        Schema::create('beneficiary_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impact_report_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('description')->nullable();
            $table->integer('beneficiaries')->default(0);
            $table->timestamps();
        });

        Schema::create('impact_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impact_report_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impact_photos');
        Schema::dropIfExists('beneficiary_locations');
        Schema::dropIfExists('impact_reports');
    }
};
