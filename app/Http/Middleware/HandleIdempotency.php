<?php

namespace App\Http\Middleware;

use App\Models\IdempotencyKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleIdempotency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-Idempotency-Key');

        if (!$key || !$request->isMethod('POST')) {
            return $next($request);
        }

        $requestHash = hash('sha256', serialize($request->all()));

        $idempotency = IdempotencyKey::where('key', $key)->first();

        if ($idempotency) {
            if ($idempotency->request_hash !== $requestHash) {
                app(\App\Services\AuditService::class)->logBlockedDuplicate($key, $request->all(), $request->ip());
                
                return response()->json([
                    'message' => 'Idempotency key collision.',
                    'error' => 'The same key was used with different request parameters.',
                ], 409);
            }

            if ($idempotency->status === 'processed') {
                // Log the replay
                app(\App\Services\AuditService::class)->logBlockedDuplicate($key, $request->all(), $request->ip());

                // Return the previously stored response
                $response = response()->json($idempotency->response_payload);
                $response->header('X-Idempotency-Replay', 'true');
                return $response;
            }

            app(\App\Services\AuditService::class)->logBlockedDuplicate($key, $request->all(), $request->ip());
            
            return response()->json([
                'message' => 'Conflict.',
                'error' => 'A request with this idempotency key is already in progress.',
            ], 409);
        }

        // Initialize as pending
        $idempotency = IdempotencyKey::create([
            'key' => $key,
            'request_hash' => $requestHash,
            'status' => 'pending',
            'ip_address' => $request->ip(),
        ]);

        try {
            $response = $next($request);

            if ($response->isSuccessful() || $response->isRedirection()) {
                $idempotency->update([
                    'status' => 'processed',
                    'response_payload' => $response->isRedirection() 
                        ? ['redirect' => $response->headers->get('Location')] 
                        : (json_decode($response->getContent(), true) ?: ['success' => true]),
                ]);
            } else {
                // If the application logic returned an error, we allow retry by removing the key
                $idempotency->delete();
            }

            return $response;
        } catch (\Throwable $e) {
            $idempotency->delete();
            throw $e;
        }
    }
}
