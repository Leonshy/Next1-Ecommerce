<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AnalyticsSetting extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'analytics_settings';

    protected $fillable = [
        'ga4_enabled', 'ga4_measurement_id',
        'meta_pixel_enabled', 'meta_pixel_id',
        'gtm_enabled', 'gtm_container_id',
        'track_view_item', 'track_add_to_cart',
        'track_begin_checkout', 'track_purchase',
    ];

    protected $casts = [
        'ga4_enabled'          => 'boolean',
        'meta_pixel_enabled'   => 'boolean',
        'gtm_enabled'          => 'boolean',
        'track_view_item'      => 'boolean',
        'track_add_to_cart'    => 'boolean',
        'track_begin_checkout' => 'boolean',
        'track_purchase'       => 'boolean',
    ];
}
