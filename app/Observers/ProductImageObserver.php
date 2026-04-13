<?php

namespace App\Observers;

use App\Models\ProductImage;

class ProductImageObserver
{
    // Equivalente al trigger ensure_single_main_image
    public function saving(ProductImage $image): void
    {
        if ($image->is_main) {
            ProductImage::where('product_id', $image->product_id)
                ->where('id', '!=', $image->id)
                ->update(['is_main' => false]);
        }
    }
}
