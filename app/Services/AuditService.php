<?php

namespace App\Services;

use App\Models\FinancialLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Log a manual financial adjustment made by an admin.
     */
    public function logManualAdjustment(
        float $amount,
        string $currency,
        string $reason,
        array $oldValues = [],
        array $newValues = []
    ): FinancialLog {
        return FinancialLog::create([
            'user_id' => Auth::id(),
            'transaction_type' => 'manual_admin_adjustment',
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'success',
            'gateway' => 'manual',
            'metadata' => ['reason' => $reason],
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    /**
     * Log a blocked duplicate request.
     */
    public function logBlockedDuplicate(string $key, array $payload, string $ip): FinancialLog
    {
        return FinancialLog::create([
            'transaction_type' => 'duplicate_request_blocked',
            'status' => 'blocked',
            'idempotency_key' => $key,
            'ip_address' => $ip,
            'metadata' => [
                'blocked_payload' => self::mask($payload),
                'reason' => 'Idempotency key collision or duplicate request.'
            ],
        ]);
    }
    
    /**
     * Utility to mask sensitive data before logging.
     */
    public static function mask(array $data): array
    {
        $sensitiveKeys = ['card', 'cvc', 'secret', 'token', 'api_key', 'password', 'number'];
        return self::recursiveMask($data, $sensitiveKeys);
    }

    private static function recursiveMask(array $data, array $sensitiveKeys): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::recursiveMask($value, $sensitiveKeys);
            } else {
                foreach ($sensitiveKeys as $sensitiveKey) {
                    if (str_contains(strtolower((string)$key), $sensitiveKey)) {
                        $data[$key] = '********';
                        break;
                    }
                }
            }
        }
        return $data;
    }
}
