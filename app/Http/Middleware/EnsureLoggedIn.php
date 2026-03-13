<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class EnsureLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        $idleTimeoutMinutes = max(1, (int) config('session.idle_timeout', 60));
        $idleTimeoutSeconds = $idleTimeoutMinutes * 60;
        $lastActivityAt = (int) Session::get('last_activity_at', 0);
        $now = time();

        if ($lastActivityAt > 0 && ($now - $lastActivityAt) >= $idleTimeoutSeconds) {
            Session::forget(['user', 'last_activity_at']);
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login.form')
                ->with('logout', 'Sesi berakhir karena tidak ada aktivitas selama '.$idleTimeoutMinutes.' menit.');
        }

        Session::put('last_activity_at', $now);

        return $next($request);
    }
}
