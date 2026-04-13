<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'brand', 'mainImage']);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categorySlug = $request->input('categoria')) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $childIds = $category->children()->pluck('id')->push($category->id);
                $query->whereIn('category_id', $childIds);
            }
        }

        if ($brandSlug = $request->input('marca')) {
            $brand = Brand::where('slug', $brandSlug)->first();
            if ($brand) $query->where('brand_id', $brand->id);
        }

        if ($tag = $request->input('tag')) {
            $query->whereJsonContains('tags', $tag);
        }

        if ($minPrice = $request->input('precio_min')) {
            $query->where('price', '>=', (float) $minPrice);
        }

        if ($maxPrice = $request->input('precio_max')) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        $sortOptions = [
            'relevancia' => ['reviews_count', 'desc'],
            'precio_asc' => ['price', 'asc'],
            'precio_desc' => ['price', 'desc'],
            'nuevo' => ['created_at', 'desc'],
        ];

        $sort = $request->input('orden', 'relevancia');
        [$sortField, $sortDir] = $sortOptions[$sort] ?? ['reviews_count', 'desc'];
        $query->orderBy($sortField, $sortDir);

        $products   = $query->paginate(24)->withQueryString();
        $categories = Category::active()->root()->with('children')->orderBy('display_order')->get();
        $brands     = Brand::active()->orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function show(string $slug)
    {
        $product = Product::active()->where('slug', $slug)->with(['category', 'brand', 'productImages'])->firstOrFail();
        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'related'));
    }

    public function search(Request $request)
    {
        $q = $request->input('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $products = Product::active()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('sku', 'like', "%{$q}%");
            })
            ->with('mainImage')
            ->limit(8)
            ->get(['id', 'name', 'slug', 'price', 'original_price']);

        return response()->json($products->map(fn($p) => [
            'id'             => $p->id,
            'name'           => $p->name,
            'slug'           => $p->slug,
            'price'          => $p->formatted_price,
            'original_price' => $p->formatted_original_price,
            'image'          => $p->mainImage?->image_url,
            'url'            => route('products.show', $p->slug),
        ]));
    }
}
