<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'free_shipping_enabled', 'free_shipping_min_amount',
        'store_pickup_enabled', 'envio_propio_enabled', 'zones',
        'aex_enabled', 'aex_api_user', 'aex_api_password',
        'aex_environment', 'aex_is_validated', 'aex_webhook_url',
    ];

    protected $casts = [
        'zones'                   => 'array',
        'free_shipping_enabled'   => 'boolean',
        'store_pickup_enabled'    => 'boolean',
        'envio_propio_enabled'    => 'boolean',
        'aex_enabled'             => 'boolean',
        'aex_is_validated'        => 'boolean',
    ];

    public static function getDefault(): self
    {
        return static::firstOrCreate(['id' => 'default']);
    }

    public function calculateShipping(string $department, string $city, float $subtotal): array
    {
        $settings = $this;
        $zones = $settings->zones ?? [];

        foreach ($zones as $zone) {
            if (strtolower($zone['id']) !== strtolower($department) || !($zone['enabled'] ?? false)) {
                continue;
            }

            // Check custom rates first
            foreach ($zone['customRates'] ?? [] as $rate) {
                $areas = array_map('strtolower', $rate['areas'] ?? []);
                if (in_array(strtolower($city), $areas)) {
                    $freeShipping = ($rate['freeShippingEnabled'] ?? false)
                        && $settings->free_shipping_enabled
                        && $subtotal >= $settings->free_shipping_min_amount;

                    return [
                        'cost'          => $freeShipping ? 0 : (float) $rate['cost'],
                        'delivery_time' => $rate['deliveryTime'] ?? '',
                        'free'          => $freeShipping,
                    ];
                }
            }

            // Fallback to zone base rate
            $freeShipping = ($zone['freeShippingEnabled'] ?? false)
                && $settings->free_shipping_enabled
                && $subtotal >= $settings->free_shipping_min_amount;

            return [
                'cost'          => $freeShipping ? 0 : (float) $zone['cost'],
                'delivery_time' => $zone['deliveryTime'] ?? '',
                'free'          => $freeShipping,
            ];
        }

        return ['cost' => 0, 'delivery_time' => '', 'free' => false];
    }
}
