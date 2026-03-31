<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * In local development, use the current request host (e.g. 192.168.x.x:8000) for URL generation
 * so links and redirects work when the app is opened from another device on the LAN.
 */
class TrustLocalLanUrls
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('local')) {
            URL::forceRootUrl($request->getSchemeAndHttpHost());
        }

        return $next($request);
    }
}
