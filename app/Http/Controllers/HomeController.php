<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\PromoBanner;
use App\Services\AnalyticsService;

class HomeController extends Controller
{
    public function index()
    {
        $heroSlides   = HeroSlide::active()->get();
        $hotDeals     = Product::active()->hotDeal()->with(['category', 'brand'])->limit(8)->get();
        $newProducts  = Product::active()->new()->with(['category', 'brand'])->limit(8)->get();
        $featured     = Product::active()->featured()->with(['category', 'brand'])->limit(8)->get();
        $bestSellers  = Product::active()->orderBy('reviews_count', 'desc')->with(['category', 'brand'])->limit(12)->get();
        $categories   = Category::active()->root()->withCount('children')->with(['children' => function($q) {
            $q->where('is_active', true)->orderBy('display_order');
        }])->orderBy('display_order')->get();
        $campaigns    = Campaign::forHome()->get();
        $promoBanners = PromoBanner::active()->get();
        $brands       = Brand::active()->orderBy('name')->get();
        $analytics    = AnalyticsService::getSettings();

        return view('home', compact(
            'heroSlides', 'hotDeals', 'newProducts', 'featured',
            'bestSellers', 'categories', 'campaigns', 'promoBanners', 'brands', 'analytics'
        ));
    }
}
