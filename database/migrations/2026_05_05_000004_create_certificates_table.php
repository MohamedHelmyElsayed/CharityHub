<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('donation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('donor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('donor_name');
            $table->decimal('amount', 12, 2);
            $table->string('campaign_title');
            $table->string('certificate_path')->nullable();
            $table->string('status')->default('pending'); // pending, generated, emailed, revoked
            $table->timestamp('emailed_at')->nullable();
            $table->timestamps();

            $table->index('uuid');
            $table->index('donation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
