<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'provider', 'is_enabled', 'is_validated',
        'public_key', 'private_key', 'webhook_secret',
        'environment', 'settings',
    ];

    protected $casts = [
        'is_enabled'   => 'boolean',
        'is_validated' => 'boolean',
        'settings'     => 'array',
    ];

    public static function getProvider(string $provider): ?self
    {
        return static::where('provider', $provider)->first();
    }

    public static function getActive(): array
    {
        return static::where('is_enabled', true)->where('is_validated', true)->get()->toArray();
    }
}
