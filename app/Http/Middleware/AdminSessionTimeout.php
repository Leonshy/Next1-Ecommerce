<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminSessionTimeout
{
    const TIMEOUT_SECONDS = 30 * 60; // 30 minutos

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            $last = session('admin_last_activity');

            if ($last && (time() - $last > self::TIMEOUT_SECONDS)) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('status', 'Tu sesión de administrador expiró por inactividad.');
            }

            session(['admin_last_activity' => time()]);
        }

        return $next($request);
    }
}
