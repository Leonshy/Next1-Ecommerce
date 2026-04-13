<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PromoBanner extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'promo_banners';

    protected $fillable = [
        'title', 'subtitle', 'description', 'background_gradient',
        'text_color', 'button_text', 'button_link', 'button_text_color',
        'icon_type', 'watermark_text', 'display_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }
}
