<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\MediaFile;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'mainImage'])->latest();

        if ($search = $request->input('q')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
        }

        $products   = $query->paginate(20)->withQueryString();
        $categories = Category::with('parent')->orderBy('name')->get();
        $brands     = Brand::withCount('products')->orderBy('name')->get();
        $protected  = ['destacado', 'nuevo', 'ofertas'];
        $tags       = Tag::orderBy('name')->get()
                         ->sortBy(fn($t) => [in_array($t->slug, $protected) ? 0 : 1, $t->name])
                         ->values();

        return view('admin.products.index', compact('products', 'categories', 'brands', 'tags'));
    }

    public function create()
    {
        $categories = Category::with('parent')->orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();
        $allTags    = Tag::orderBy('name')->get(['name', 'slug']);
        return view('admin.products.form', compact('categories', 'brands', 'allTags'));
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);
        $data['slug'] = $this->uniqueSlug(Str::slug($data['name']));

        $product = Product::create($data);

        $this->syncImages($product, $request);

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado.');
    }

    public function edit(string $id)
    {
        $product    = Product::with('productImages')->findOrFail($id);
        $categories = Category::with('parent')->orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();
        $allTags    = Tag::orderBy('name')->get(['name', 'slug']);
        return view('admin.products.form', compact('product', 'categories', 'brands', 'allTags'));
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        $data    = $this->validateProduct($request);

        if ($data['name'] !== $product->name) {
            $data['slug'] = $this->uniqueSlug(Str::slug($data['name']), $product->id);
        }

        $product->update($data);

        $this->syncImages($product, $request);

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(string $id)
    {
        Product::findOrFail($id)->delete();
        return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado.');
    }

    // ─── Import / Export ──────────────────────────────────────────────────────

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:5120']);

        $file    = $request->file('file');
        $handle  = fopen($file->getRealPath(), 'r');
        $headers = array_map('trim', fgetcsv($handle));

        $categories = Category::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);
        $brands     = Brand::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);

        $success = 0;
        $errors  = [];
        $row     = 1;

        while (($values = fgetcsv($handle)) !== false) {
            $row++;
            $data = array_combine($headers, array_pad($values, count($headers), ''));

            $name = trim($data['nombre'] ?? $data['name'] ?? '');
            if (!$name) { $errors[] = "Fila {$row}: el campo 'nombre' es obligatorio."; continue; }

            $price = (float) str_replace([',', ' '], '', $data['precio'] ?? $data['price'] ?? '0');
            if ($price <= 0) { $errors[] = "Fila {$row}: precio inválido para \"{$name}\"."; continue; }

            $categoryName = strtolower(trim($data['categoria'] ?? $data['category'] ?? ''));
            $brandName    = strtolower(trim($data['marca']     ?? $data['brand']    ?? ''));

            $originalPrice = trim($data['precio_original'] ?? $data['original_price'] ?? '');

            $tagsRaw = trim($data['etiquetas'] ?? $data['tags'] ?? '');
            $tags    = $tagsRaw ? array_map('trim', explode(',', $tagsRaw)) : [];

            $slug = $this->uniqueSlug(Str::slug($data['slug'] ?? $name));

            $imageUrl = trim($data['imagen'] ?? $data['image'] ?? '');

            try {
                $product = Product::create([
                    'name'           => $name,
                    'slug'           => $slug,
                    'description'    => trim($data['descripcion'] ?? $data['description'] ?? '') ?: null,
                    'price'          => $price,
                    'original_price' => $originalPrice !== '' ? (float) $originalPrice : null,
                    'sku'            => trim($data['sku'] ?? '') ?: null,
                    'stock'          => (int) ($data['stock'] ?? 0),
                    'category_id'    => $categoryName ? ($categories[$categoryName] ?? null) : null,
                    'brand_id'       => $brandName    ? ($brands[$brandName]       ?? null) : null,
                    'badge'          => trim($data['badge'] ?? '') ?: null,
                    'tags'           => $tags,
                    'is_active'      => !in_array(strtolower(trim($data['activo']    ?? $data['is_active']   ?? 'true')),  ['false', '0', 'no']),
                    'is_featured'    =>  in_array(strtolower(trim($data['destacado'] ?? $data['is_featured'] ?? 'false')), ['true',  '1', 'si', 'sí']),
                    'is_new'         =>  in_array(strtolower(trim($data['nuevo']     ?? $data['is_new']      ?? 'false')), ['true',  '1', 'si', 'sí']),
                    'is_hot_deal'    =>  in_array(strtolower(trim($data['oferta']    ?? $data['is_hot_deal'] ?? 'false')), ['true',  '1', 'si', 'sí']),
                ]);

                if ($imageUrl) {
                    // Buscar si la URL corresponde a un archivo de la biblioteca interna
                    $mediaFile = MediaFile::where('file_url', $imageUrl)->first();

                    ProductImage::create([
                        'product_id'    => $product->id,
                        'image_url'     => $mediaFile ? $mediaFile->file_url : $imageUrl,
                        'alt_text'      => $mediaFile?->alt_text ?: $name,
                        'display_order' => 0,
                        'is_main'       => true,
                    ]);
                }

                $success++;
            } catch (\Exception $e) {
                $errors[] = "Fila {$row} (\"{$name}\"): " . $e->getMessage();
            }
        }

        fclose($handle);

        return redirect()->route('admin.productos.index')->with('import_result', compact('success', 'errors'));
    }

    public function export()
    {
        $products = Product::with(['category', 'brand', 'mainImage', 'productImages'])->orderBy('name')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="productos_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($products) {
            $handle = fopen('php://output', 'w');
            // BOM para Excel
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['nombre','slug','descripcion','precio','precio_original','sku','stock','categoria','marca','badge','etiquetas','activo','destacado','nuevo','oferta','imagen']);

            foreach ($products as $p) {
                fputcsv($handle, [
                    $p->name,
                    $p->slug,
                    $p->description ?? '',
                    $p->price,
                    $p->original_price ?? '',
                    $p->sku ?? '',
                    $p->stock,
                    $p->category?->name ?? '',
                    $p->brand?->name    ?? '',
                    $p->badge           ?? '',
                    implode(', ', $p->tags ?? []),
                    $p->is_active   ? 'true' : 'false',
                    $p->is_featured ? 'true' : 'false',
                    $p->is_new      ? 'true' : 'false',
                    $p->is_hot_deal ? 'true' : 'false',
                    $p->mainImage?->image_url
                        ?? $p->productImages->first()?->image_url
                        ?? ($p->images[0] ?? ''),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function template()
    {
        $category = Category::active()->first()?->name ?? 'Electrónica';
        $brand    = Brand::active()->first()?->name    ?? 'Samsung';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_productos.csv"',
        ];

        $callback = function () use ($category, $brand) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['nombre','slug','descripcion','precio','precio_original','sku','stock','categoria','marca','badge','etiquetas','activo','destacado','nuevo','oferta','imagen']);
            fputcsv($handle, [
                'Producto Ejemplo', 'producto-ejemplo', 'Descripción del producto',
                '100000', '120000', 'SKU001', '10',
                $category, $brand, 'NUEVO', 'etiqueta1, etiqueta2',
                'true', 'false', 'true', 'false',
                'https://ejemplo.com/imagen.jpg',
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function validateProduct(Request $request): array
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock'          => 'nullable|integer|min:0',
            'sku'            => 'nullable|string|max:100',
            'category_id'    => 'nullable|exists:categories,id',
            'brand_id'       => 'nullable|exists:brands,id',
            'badge'          => 'nullable|string|max:50',
            'tags'           => 'nullable|array',
            'tags.*'         => 'nullable|string|max:100',
            'is_active'      => 'nullable|boolean',
            'is_featured'    => 'nullable|boolean',
            'is_new'         => 'nullable|boolean',
            'is_hot_deal'    => 'nullable|boolean',
        ]);

        // Checkboxes no enviados = false
        foreach (['is_active', 'is_featured', 'is_new', 'is_hot_deal'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        // Tags: filter empty strings
        $data['tags'] = array_values(array_filter($request->input('tags', []), fn($t) => trim($t) !== ''));

        return $data;
    }

    /** Genera slug único, opcionalmente excluyendo el id actual */
    private function uniqueSlug(string $base, ?string $excludeId = null): string
    {
        $slug = $base;
        $i    = 2;
        while (
            Product::where('slug', $slug)
                   ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                   ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }

    /** Sincroniza las imágenes del producto a partir del form */
    private function syncImages(Product $product, Request $request): void
    {
        $urls = array_filter((array) $request->input('gallery_images', []));
        if (empty($urls)) {
            return;
        }

        $mainUrl = $request->input('gallery_main', $urls[0]);

        // Borrar imágenes antiguas que ya no están en el nuevo listado
        $product->productImages()->whereNotIn('image_url', $urls)->delete();

        // Existentes para no duplicar
        $existing = $product->productImages()->pluck('image_url')->toArray();

        foreach (array_values($urls) as $order => $url) {
            if (in_array($url, $existing)) {
                // Actualizar order e is_main
                $product->productImages()->where('image_url', $url)->update([
                    'display_order' => $order,
                    'is_main'       => $url === $mainUrl,
                ]);
            } else {
                ProductImage::create([
                    'product_id'    => $product->id,
                    'image_url'     => $url,
                    'alt_text'      => $product->name,
                    'display_order' => $order,
                    'is_main'       => $url === $mainUrl,
                ]);
            }
        }
    }
}
