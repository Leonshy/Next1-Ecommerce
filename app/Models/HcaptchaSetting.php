<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class HcaptchaSetting extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'hcaptcha_settings';

    protected $fillable = [
        'is_enabled', 'is_validated', 'site_key', 'secret_key',
        'protect_login', 'protect_register', 'protect_newsletter',
    ];

    protected $casts = [
        'is_enabled'         => 'boolean',
        'is_validated'       => 'boolean',
        'protect_login'      => 'boolean',
        'protect_register'   => 'boolean',
        'protect_newsletter' => 'boolean',
    ];

    public static function verifyToken(?string $token): bool
    {
        $settings = static::first();
        if (!$settings || !$settings->is_enabled || !$settings->secret_key) {
            return true; // hCaptcha no configurado → dejar pasar
        }
        if (!$token) return false;

        $response = \Illuminate\Support\Facades\Http::asForm()->post(
            'https://hcaptcha.com/siteverify',
            ['secret' => $settings->secret_key, 'response' => $token]
        );

        return (bool) ($response->json('success') ?? false);
    }
}
