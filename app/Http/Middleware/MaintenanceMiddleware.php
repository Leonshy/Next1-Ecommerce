<?php

namespace App\Http\Middleware;

use App\Models\SiteContent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Los admins siempre pasan
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        $maintenance = Cache::remember('maintenance_mode', 60, function () {
            return SiteContent::getByKey('maintenance')?->metadata ?? ['is_active' => false];
        });

        if (!empty($maintenance['is_active'])) {
            return response()->view('maintenance', [
                'message'       => $maintenance['message'] ?? 'Estamos realizando tareas de mantenimiento.',
                'estimated_time' => $maintenance['estimated_time'] ?? '',
            ], 503);
        }

        return $next($request);
    }
}
