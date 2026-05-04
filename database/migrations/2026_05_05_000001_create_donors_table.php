<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->boolean('anonymous')->default(false);
            $table->boolean('gdpr_consent')->default(false);
            $table->timestamp('gdpr_consent_at')->nullable();
            $table->boolean('marketing_opt_in')->default(false);
            $table->timestamp('deleted_at')->nullable(); // soft deletes for GDPR erasure
            $table->timestamps();

            $table->index('email');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
