<?php

namespace App\Services;

use App\Models\HcaptchaSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HCaptchaService
{
    public function verify(string $token): array
    {
        $setting = HcaptchaSetting::first();

        if (!$setting || !$setting->is_enabled || !$setting->is_validated) {
            return ['success' => true]; // Si está deshabilitado, pasamos
        }

        $response = Http::asForm()->post('https://api.hcaptcha.com/siteverify', [
            'secret'   => $setting->secret_key,
            'response' => $token,
        ]);

        $data = $response->json();

        if (!($data['success'] ?? false)) {
            $errorCodes = $data['error-codes'] ?? [];
            $configErrors = ['sitekey-secret-mismatch', 'invalid-sitekey', 'invalid-secret-key'];

            if (array_intersect($configErrors, $errorCodes)) {
                // Auto-deshabilitar si hay error de configuración
                $setting->update(['is_enabled' => false, 'is_validated' => false]);
                Log::warning('hCaptcha auto-deshabilitado por error de configuración', ['errors' => $errorCodes]);
                return ['success' => false, 'auto_disabled' => true, 'error' => 'Configuración inválida de hCaptcha'];
            }

            return ['success' => false, 'error' => 'Verificación de captcha fallida'];
        }

        return ['success' => true];
    }

    public function isProtected(string $form): bool
    {
        $setting = HcaptchaSetting::first();

        if (!$setting || !$setting->is_enabled || !$setting->is_validated) {
            return false;
        }

        return match($form) {
            'login'      => $setting->protect_login,
            'register'   => $setting->protect_register,
            'newsletter' => $setting->protect_newsletter,
            default      => false,
        };
    }

    public function getSiteKey(): ?string
    {
        return HcaptchaSetting::first()?->site_key;
    }
}
