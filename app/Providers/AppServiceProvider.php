<?php

namespace App\Providers;

use App\Models\ProductImage;
use App\Observers\ProductImageObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        ProductImage::observe(ProductImageObserver::class);

        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers()->uncompromised(3));
    }
}
