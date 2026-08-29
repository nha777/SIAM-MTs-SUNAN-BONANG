<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestCorrelationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) Str::uuid();
        
        // Add to request instance so it's accessible anywhere the request is bound
        $request->attributes->set('request_id', $requestId);
        
        // Also bind to app container as fallback for non-HTTP contexts that still trigger events
        if (!app()->has('request_id')) {
            app()->instance('request_id', $requestId);
        }

        $response = $next($request);

        // Add to response header
        if (method_exists($response, 'header')) {
            $response->header('X-Request-ID', $requestId);
        }

        return $response;
    }
}
