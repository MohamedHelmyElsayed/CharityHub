<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Idempotency Keys Table
        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('request_hash');
            $table->json('response_payload')->nullable();
            $table->string('status'); // processed, failed, pending
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 2. Comprehensive Financial Logs Table
        // We drop the old one if it exists to ensure a clean enterprise schema
        Schema::dropIfExists('financial_logs');
        
        Schema::create('financial_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // The actor (admin/employee)
            $table->foreignId('donor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('donation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_type'); 
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('status'); // success, failed, pending, refunded, blocked
            $table->string('gateway')->nullable(); // stripe, paymob, manual
            $table->string('gateway_transaction_id')->nullable();
            $table->string('idempotency_key')->nullable();
            $table->json('metadata')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('hash')->nullable(); // Transaction integrity hash
            $table->timestamp('created_at')->useCurrent();

            $table->index(['transaction_type', 'status']);
            $table->index('gateway_transaction_id');
            $table->index('idempotency_key');
            $table->index('created_at');
        });

        // 3. Activity Log Table
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->string('event')->nullable();
            $table->timestamps();
            $table->index('log_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('financial_logs');
        Schema::dropIfExists('idempotency_keys');
    }
};
