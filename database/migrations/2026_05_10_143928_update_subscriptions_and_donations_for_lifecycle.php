<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'stripe_id')) {
                $table->renameColumn('stripe_id', 'gateway_subscription_id');
            }
            if (Schema::hasColumn('subscriptions', 'stripe_status')) {
                $table->renameColumn('stripe_status', 'status');
            }
            
            if (!Schema::hasColumn('subscriptions', 'gateway')) {
                $table->string('gateway')->nullable()->after('campaign_id');
            }
            if (!Schema::hasColumn('subscriptions', 'gateway_customer_id')) {
                $table->string('gateway_customer_id')->nullable()->after('gateway_subscription_id');
            }
            if (!Schema::hasColumn('subscriptions', 'gateway_plan_id')) {
                $table->string('gateway_plan_id')->nullable()->after('gateway_customer_id');
            }
            if (!Schema::hasColumn('subscriptions', 'quantity')) {
                $table->integer('quantity')->default(1)->after('status');
            }
            if (!Schema::hasColumn('subscriptions', 'amount')) {
                $table->decimal('amount', 15, 2)->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('subscriptions', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('amount');
            }
            if (!Schema::hasColumn('subscriptions', 'billing_interval')) {
                $table->string('billing_interval')->default('month')->after('currency');
            }
            if (!Schema::hasColumn('subscriptions', 'next_billing_date')) {
                $table->timestamp('next_billing_date')->nullable()->after('billing_interval');
            }
            if (!Schema::hasColumn('subscriptions', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable()->after('next_billing_date');
            }
            if (!Schema::hasColumn('subscriptions', 'ends_at')) {
                $table->timestamp('ends_at')->nullable()->after('trial_ends_at');
            }
            if (!Schema::hasColumn('subscriptions', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('ends_at');
            }
        });

        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'subscription_id')) {
                $table->foreignId('subscription_id')->nullable()->after('campaign_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('donations', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('type');
            }
            if (!Schema::hasColumn('donations', 'gateway')) {
                $table->string('gateway')->nullable()->after('status');
            }
            if (!Schema::hasColumn('donations', 'gateway_transaction_id')) {
                $table->string('gateway_transaction_id')->nullable()->after('gateway');
            }
            if (!Schema::hasColumn('donations', 'gateway_refund_id')) {
                $table->string('gateway_refund_id')->nullable()->after('gateway_transaction_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropColumn(['subscription_id', 'is_recurring', 'gateway', 'gateway_transaction_id', 'gateway_refund_id']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'gateway_customer_id', 
                'gateway_plan_id', 
                'quantity', 
                'amount', 
                'currency', 
                'billing_interval', 
                'next_billing_date', 
                'trial_ends_at', 
                'ends_at', 
                'cancelled_at'
            ]);
        });
    }
};
