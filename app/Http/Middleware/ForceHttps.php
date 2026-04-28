<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!(bool) config('app.force_https', false) || $request->isSecure()) {
            return $next($request);
        }

        $target = 'https://'.$request->getHttpHost().$request->getRequestUri();

        return response('', Response::HTTP_PERMANENTLY_REDIRECT, [
            'Location' => $target,
        ]);
    }
}
