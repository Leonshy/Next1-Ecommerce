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
        $zones = $this->zones ?? [];

        foreach ($zones as $zone) {
            // Soporta formato nuevo (departmentId/active) y viejo (id/enabled)
            $zoneId  = $zone['departmentId'] ?? $zone['id'] ?? '';
            $isActive = isset($zone['active'])
                ? (bool) $zone['active']
                : (bool) ($zone['enabled'] ?? false);

            if (strtolower($zoneId) !== strtolower($department) || !$isActive) {
                continue;
            }

            $baseCost         = (float) ($zone['price'] ?? $zone['cost'] ?? 0);
            $baseDelivery     = $zone['deliveryTime'] ?? '';
            $baseFreeEligible = (bool) ($zone['freeShippingEligible'] ?? $zone['freeShippingEnabled'] ?? true);

            // Ciudad deshabilitada → no disponible
            $inactiveCities = array_map('strtolower', $zone['inactiveCities'] ?? []);
            if (in_array(strtolower($city), $inactiveCities)) {
                return ['cost' => 0, 'delivery_time' => '', 'free' => false, 'unavailable' => true];
            }

            // Verifica tarifas personalizadas primero
            foreach ($zone['customRates'] ?? [] as $rate) {
                // Soporta districtIds (nuevo) y areas (viejo)
                $districts = array_map('strtolower', $rate['districtIds'] ?? $rate['areas'] ?? []);
                if (in_array(strtolower($city), $districts)) {
                    $freeEligible = (bool) ($rate['freeShippingEligible'] ?? $baseFreeEligible);
                    $free = $freeEligible
                        && $this->free_shipping_enabled
                        && $subtotal >= $this->free_shipping_min_amount;

                    return [
                        'cost'          => $free ? 0 : (float) ($rate['price'] ?? $rate['cost'] ?? 0),
                        'delivery_time' => $rate['deliveryTime'] ?? $baseDelivery,
                        'free'          => $free,
                    ];
                }
            }

            // Tarifa base del departamento
            $free = $baseFreeEligible
                && $this->free_shipping_enabled
                && $subtotal >= $this->free_shipping_min_amount;

            return [
                'cost'          => $free ? 0 : $baseCost,
                'delivery_time' => $baseDelivery,
                'free'          => $free,
            ];
        }

        return ['cost' => 0, 'delivery_time' => '', 'free' => false];
    }

    /** Retorna los IDs de departamentos con envío activo */
    public function getActiveDepartmentIds(): array
    {
        return collect($this->zones ?? [])
            ->filter(fn($z) => (bool) ($z['active'] ?? $z['enabled'] ?? false))
            ->pluck('departmentId')
            ->filter()
            ->values()
            ->toArray();
    }
}
