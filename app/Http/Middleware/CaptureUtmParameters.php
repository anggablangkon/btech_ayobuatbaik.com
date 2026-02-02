<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureUtmParameters
{
    /**
     * UTM parameters to capture from URL.
     */
    protected array $utmParams = [
        'utm_source',
        'utm_medium', 
        'utm_campaign',
        'utm_term',
        'utm_content',
    ];

    /**
     * Handle an incoming request.
     * Captures UTM parameters from URL and stores them in session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        foreach ($this->utmParams as $param) {
            if ($request->has($param)) {
                // Store in session - overwrite if new UTM detected
                session([$param => $request->get($param)]);
            }
        }

        return $next($request);
    }
}
