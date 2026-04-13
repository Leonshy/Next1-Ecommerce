<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['product_id', 'image_url', 'alt_text', 'display_order', 'is_main'];

    protected $casts = ['is_main' => 'boolean'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
