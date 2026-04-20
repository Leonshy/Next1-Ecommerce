<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name', 'tag', 'filter_type', 'category_id', 'brand_id',
        'description', 'banner_image',
        'start_date', 'end_date', 'display_on_home', 'display_order', 'is_active',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'end_date'        => 'date',
        'display_on_home' => 'boolean',
        'is_active'       => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForHome($query)
    {
        return $query->where('display_on_home', true)->where('is_active', true)->orderBy('display_order');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function products()
    {
        return match($this->filter_type) {
            'category' => $this->category_id
                ? Product::active()->where('category_id', $this->category_id)->get()
                : collect(),
            'brand'    => $this->brand_id
                ? Product::active()->where('brand_id', $this->brand_id)->get()
                : collect(),
            default    => $this->tag
                ? Product::active()->whereJsonContains('tags', $this->tag)->get()
                : collect(),
        };
    }
}
