<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'original_price',
        'stock', 'sku', 'category_id', 'brand_id', 'images', 'tags',
        'badge', 'is_active', 'is_featured', 'is_new', 'is_hot_deal',
        'rating', 'reviews_count',
    ];

    protected $casts = [
        'images'       => 'array',
        'tags'         => 'array',
        'is_active'    => 'boolean',
        'is_featured'  => 'boolean',
        'is_new'       => 'boolean',
        'is_hot_deal'  => 'boolean',
        'price'        => 'decimal:2',
        'original_price' => 'decimal:2',
        'rating'       => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('display_order');
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id')->where('is_main', true);
    }

    public function wishlistedBy()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    public function scopeHotDeal($query)
    {
        return $query->where('is_hot_deal', true);
    }

    public static function generateSku(?string $categoryId): string
    {
        $prefix = 'GEN';
        if ($categoryId) {
            $catName = \App\Models\Category::find($categoryId)?->name ?? 'GEN';
            $prefix  = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $catName), 0, 3)) ?: 'GEN';
        }

        $last = static::where('sku', 'like', "{$prefix}-%")
            ->orderByRaw("CAST(SUBSTR(sku, " . (strlen($prefix) + 2) . ") AS UNSIGNED) DESC")
            ->value('sku');

        $next = $last ? ((int) substr($last, strlen($prefix) + 1)) + 1 : 1;

        return $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return (int) round((1 - $this->price / $this->original_price) * 100);
        }
        return null;
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', '.');
    }

    public function getFormattedOriginalPriceAttribute(): ?string
    {
        if ($this->original_price) {
            return number_format($this->original_price, 0, ',', '.');
        }
        return null;
    }
}
