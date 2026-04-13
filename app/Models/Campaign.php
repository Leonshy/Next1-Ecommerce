<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name', 'tag', 'description', 'banner_image',
        'start_date', 'end_date', 'display_on_home', 'display_order', 'is_active',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'display_on_home' => 'boolean',
        'is_active'      => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForHome($query)
    {
        return $query->where('display_on_home', true)->where('is_active', true)->orderBy('display_order');
    }

    public function products()
    {
        return Product::active()->whereJsonContains('tags', $this->tag)->get();
    }
}
