<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = strtolower((string)(Session::get('user.role') ?? Session::get('user')['role'] ?? ''));
        $allowed = ['administrator', 'admin', 'superadmin'];

        if (!in_array($role, $allowed, true)) {
            abort(403, 'Akses khusus administrator.');
        }

        return $next($request);
    }
}
