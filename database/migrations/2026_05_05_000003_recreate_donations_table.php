<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'donor_id')) {
                $table->foreignId('donor_id')->nullable()->after('user_id')->constrained('donors')->nullOnDelete();
            }
            if (!Schema::hasColumn('donations', 'idempotency_key')) {
                $table->string('idempotency_key')->unique()->nullable()->after('payment_id');
            }
            if (!Schema::hasColumn('donations', 'type')) {
                $table->enum('type', ['one_time', 'recurring'])->default('one_time')->after('amount');
            }
            if (!Schema::hasColumn('donations', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('amount');
            }
            if (!Schema::hasColumn('donations', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')->nullable()->after('payment_id');
            }
            if (!Schema::hasColumn('donations', 'anonymous')) {
                $table->boolean('anonymous')->default(false);
            }
            if (!Schema::hasColumn('donations', 'message')) {
                $table->text('message')->nullable();
            }
            if (!Schema::hasColumn('donations', 'ip_address')) {
                $table->string('ip_address', 45)->nullable();
            }
            if (!Schema::hasColumn('donations', 'certificate_uuid')) {
                $table->string('certificate_uuid')->nullable()->unique();
            }
            if (!Schema::hasColumn('donations', 'certificate_path')) {
                $table->string('certificate_path')->nullable();
            }
            if (!Schema::hasColumn('donations', 'certificate_generated_at')) {
                $table->timestamp('certificate_generated_at')->nullable();
            }
            if (!Schema::hasColumn('donations', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable();
            }
        });

        // Add indexes
        Schema::table('donations', function (Blueprint $table) {
            // Only add if not exist - wrapped in try/catch in seeder
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn([
                'donor_id', 'idempotency_key', 'type', 'currency',
                'stripe_payment_intent_id', 'anonymous', 'message',
                'ip_address', 'certificate_uuid', 'certificate_path',
                'certificate_generated_at', 'refunded_at'
            ]);
        });
    }
};
