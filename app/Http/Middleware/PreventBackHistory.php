<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces browsers to never serve a cached version of any page.
 * This prevents the "back button shows logged-out account" issue:
 * after logout, pressing Back will trigger a fresh server request,
 * which Laravel's auth middleware will then redirect to /login.
 */
class PreventBackHistory
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Use headers->set() instead of ->withHeaders() because StreamedResponse
        // (used for file downloads) is a raw Symfony class that doesn't have withHeaders().
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');

        return $response;
    }
}
