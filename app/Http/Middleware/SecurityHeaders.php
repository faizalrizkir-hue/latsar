<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (!(bool) config('security_headers.enabled', true)) {
            return $response;
        }

        $headers = $response->headers;
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), microphone=(), payment=(), usb=()');
        $headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        $hstsEnabled = (bool) config('security_headers.hsts.enabled', true);
        if ($hstsEnabled && $request->isSecure()) {
            $maxAge = max(0, (int) config('security_headers.hsts.max_age', 31536000));
            $value = 'max-age='.$maxAge;
            if ((bool) config('security_headers.hsts.include_subdomains', true)) {
                $value .= '; includeSubDomains';
            }
            if ((bool) config('security_headers.hsts.preload', false)) {
                $value .= '; preload';
            }

            $headers->set('Strict-Transport-Security', $value);
        }

        $reportOnlyCsp = trim((string) config('security_headers.csp.report_only', ''));
        if ($reportOnlyCsp !== '') {
            $headers->set('Content-Security-Policy-Report-Only', $reportOnlyCsp);
        }

        return $response;
    }
}
