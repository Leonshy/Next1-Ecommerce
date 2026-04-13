<?php

namespace App\Services;

use App\Models\ShippingSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AexService
{
    private string $baseUrl;
    private ?string $apiUser;
    private ?string $apiPassword;

    public function __construct()
    {
        $settings        = ShippingSetting::getDefault();
        $this->apiUser   = $settings->aex_api_user;
        $this->apiPassword = $settings->aex_api_password;
        $this->baseUrl   = $settings->aex_environment === 'production'
            ? 'https://api.aex.com.py'
            : 'https://api-sandbox.aex.com.py';
    }

    public function getToken(): ?string
    {
        try {
            $response = Http::post("{$this->baseUrl}/api/v1/auth/token", [
                'user'     => $this->apiUser,
                'password' => $this->apiPassword,
            ]);

            if ($response->successful() && isset($response['token'])) {
                return $response['token'];
            }
        } catch (\Throwable $e) {
            Log::error('AEX auth failed', ['error' => $e->getMessage()]);
        }

        return null;
    }

    public function validate(string $apiUser, string $apiPassword, string $environment): array
    {
        $baseUrl  = $environment === 'production'
            ? 'https://api.aex.com.py'
            : 'https://api-sandbox.aex.com.py';

        try {
            $response = Http::post("{$baseUrl}/api/v1/auth/token", [
                'user'     => $apiUser,
                'password' => $apiPassword,
            ]);

            $valid = $response->successful() && isset($response['token']);

            ShippingSetting::where('id', 'default')->update([
                'aex_is_validated' => $valid,
                'aex_enabled'      => $valid,
            ]);

            return ['success' => $valid, 'message' => $valid ? 'Credenciales válidas' : 'Credenciales inválidas'];
        } catch (\Throwable $e) {
            // Fallback: validate format only
            $valid = strlen($apiUser) >= 3 && strlen($apiPassword) >= 3;
            return ['success' => false, 'message' => 'No se pudo conectar a AEX: ' . $e->getMessage()];
        }
    }
}
