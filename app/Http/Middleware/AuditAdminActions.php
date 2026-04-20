<?php

namespace App\Http\Middleware;

use App\Models\AdminAuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditAdminActions
{
    // Solo registrar métodos que modifican datos
    private const TRACKED_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    // Campos que nunca se guardan en el log
    private const SENSITIVE_FIELDS = ['password', 'password_confirmation', 'current_password', '_token', 'h-captcha-response'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (in_array($request->method(), self::TRACKED_METHODS) && Auth::check()) {
            try {
                $this->log($request, $response);
            } catch (\Throwable) {}
        }

        return $response;
    }

    private function log(Request $request, Response $response): void
    {
        $user    = Auth::user();
        $payload = collect($request->except(self::SENSITIVE_FIELDS))
            ->filter(fn($v) => ! ($v instanceof \Illuminate\Http\UploadedFile))
            ->toArray();

        // Detectar tipo de recurso y su ID desde la URL
        [$resourceType, $resourceId] = $this->extractResource($request);

        AdminAuditLog::create([
            'admin_id'      => $user->id,
            'admin_email'   => $user->email,
            'action'        => $request->method(),
            'route'         => $request->route()?->getName(),
            'url'           => $request->fullUrl(),
            'resource_type' => $resourceType,
            'resource_id'   => $resourceId,
            'description'   => $this->buildDescription($request, $resourceType),
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
            'payload'       => empty($payload) ? null : $payload,
        ]);
    }

    private function extractResource(Request $request): array
    {
        $route      = $request->route();
        $parameters = $route?->parameters() ?? [];

        // Buscar el primer parámetro de ruta que parezca un ID de recurso
        foreach ($parameters as $key => $value) {
            if (is_object($value)) {
                return [class_basename($value), $value->getKey()];
            }
        }

        // Inferir del nombre de la ruta (ej: admin.products.update → Product)
        $routeName = $route?->getName() ?? '';
        $parts     = explode('.', $routeName);

        if (count($parts) >= 2) {
            $resource = \Illuminate\Support\Str::studly(\Illuminate\Support\Str::singular($parts[1]));
            $id       = $parameters[array_key_first($parameters)] ?? null;
            return [$resource ?: null, is_string($id) || is_int($id) ? $id : null];
        }

        return [null, null];
    }

    private function buildDescription(Request $request, ?string $resourceType): string
    {
        $method = match ($request->method()) {
            'POST'   => 'Creó',
            'PUT', 'PATCH' => 'Actualizó',
            'DELETE' => 'Eliminó',
            default  => $request->method(),
        };

        $resource = $resourceType ? " {$resourceType}" : '';
        $path     = parse_url($request->url(), PHP_URL_PATH);

        return "{$method}{$resource} — {$path}";
    }
}
