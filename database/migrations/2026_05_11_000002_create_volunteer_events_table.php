<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('event_type')->default('general'); // general, fundraising, cleanup, etc.
            $table->json('required_skills')->nullable();
            $table->integer('max_volunteers')->default(0); // 0 = unlimited
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->dateTime('registration_deadline')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['draft', 'open', 'full', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();

            $table->index('status');
            $table->index('start_date');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_events');
    }
};
