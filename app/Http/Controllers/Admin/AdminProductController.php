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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        if (empty($data['sku'])) {
            $data['sku'] = Product::generateSku($data['category_id'] ?? null);
        }

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

    // ─── Bulk Actions ─────────────────────────────────────────────────────────

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'string',
        ]);

        $ids    = $request->input('ids');
        $action = $request->input('action');

        switch ($action) {
            case 'delete':
                $count = Product::whereIn('id', $ids)->count();
                Product::whereIn('id', $ids)->delete();
                return back()->with('success', "{$count} producto(s) eliminado(s).");

            case 'activate':
                Product::whereIn('id', $ids)->update(['is_active' => true]);
                return back()->with('success', count($ids) . " producto(s) activado(s).");

            case 'deactivate':
                Product::whereIn('id', $ids)->update(['is_active' => false]);
                return back()->with('success', count($ids) . " producto(s) desactivado(s).");
        }
    }

    // ─── Import / Export ──────────────────────────────────────────────────────

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240']);

        $file      = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $rows      = $this->parseFile($file->getRealPath(), $extension);

        if (empty($rows)) {
            return back()->withErrors(['file' => 'El archivo está vacío o no tiene el formato correcto.']);
        }

        // Cache local para evitar queries repetidas y registrar los creados automáticamente
        $categoryCache    = Category::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id])->toArray();
        $brandCache       = Brand::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id])->toArray();
        $tagCache         = Tag::pluck('slug', 'slug')->toArray();

        $created          = 0;
        $updated          = 0;
        $newCategories    = [];
        $newBrands        = [];
        $newTags          = [];
        $errors           = [];
        $rowNum           = 1;

        foreach ($rows as $data) {
            $rowNum++;

            $name = trim($data['nombre'] ?? $data['name'] ?? '');
            if (!$name) { $errors[] = "Fila {$rowNum}: el campo 'nombre' es obligatorio."; continue; }

            $price = (float) str_replace([',', ' '], '', $data['precio'] ?? $data['price'] ?? '0');
            if ($price <= 0) { $errors[] = "Fila {$rowNum}: precio inválido para \"{$name}\"."; continue; }

            $categoryName  = trim($data['categoria'] ?? $data['category'] ?? '');
            $brandName     = trim($data['marca']     ?? $data['brand']    ?? '');
            $originalPrice = trim($data['precio_original'] ?? $data['original_price'] ?? '');
            $tagsRaw       = trim($data['etiquetas'] ?? $data['tags'] ?? '');
            $tagNames      = $tagsRaw ? array_filter(array_map('trim', explode(',', $tagsRaw))) : [];
            $slug          = $this->uniqueSlug(Str::slug($data['slug'] ?? $name));
            $imageUrl      = trim($data['imagen'] ?? $data['image'] ?? '');

            try {
                // Auto-crear categoría si no existe
                $categoryId = null;
                if ($categoryName) {
                    $key = strtolower($categoryName);
                    if (!isset($categoryCache[$key])) {
                        $catSlug = Str::slug($categoryName);
                        $base = $catSlug; $i = 2;
                        while (Category::where('slug', $catSlug)->exists()) { $catSlug = "{$base}-{$i}"; $i++; }
                        $cat = Category::create(['name' => $categoryName, 'slug' => $catSlug, 'is_active' => true]);
                        $categoryCache[$key] = $cat->id;
                        $newCategories[] = $categoryName;
                    }
                    $categoryId = $categoryCache[$key];
                }

                // Auto-crear marca si no existe
                $brandId = null;
                if ($brandName) {
                    $key = strtolower($brandName);
                    if (!isset($brandCache[$key])) {
                        $brandSlug = Str::slug($brandName);
                        $base = $brandSlug; $i = 2;
                        while (Brand::where('slug', $brandSlug)->exists()) { $brandSlug = "{$base}-{$i}"; $i++; }
                        $brand = Brand::create(['name' => $brandName, 'slug' => $brandSlug, 'is_active' => true]);
                        $brandCache[$key] = $brand->id;
                        $newBrands[] = $brandName;
                    }
                    $brandId = $brandCache[$key];
                }

                // Auto-crear etiquetas que no existan y normalizar a slugs
                $tagSlugs = [];
                foreach ($tagNames as $tagName) {
                    $tagSlug = Str::slug($tagName);
                    if (!isset($tagCache[$tagSlug])) {
                        Tag::firstOrCreate(['slug' => $tagSlug], ['name' => $tagName]);
                        $tagCache[$tagSlug] = $tagSlug;
                        $newTags[] = $tagName;
                    }
                    $tagSlugs[] = $tagSlug;
                }

                $skuRaw = trim($data['sku'] ?? '');
                $sku    = $skuRaw ?: null;

                $existing = $sku
                    ? Product::where('sku', $sku)->first()
                    : Product::where('slug', $slug)->first();

                $productData = [
                    'name'           => $name,
                    'description'    => trim($data['descripcion'] ?? $data['description'] ?? '') ?: null,
                    'price'          => $price,
                    'original_price' => $originalPrice !== '' ? (float) $originalPrice : null,
                    'stock'          => (int) ($data['stock'] ?? 0),
                    'category_id'    => $categoryId,
                    'brand_id'       => $brandId,
                    'badge'          => trim($data['badge'] ?? '') ?: null,
                    'tags'           => $tagSlugs,
                    'is_active'      => !in_array(strtolower(trim($data['activo']    ?? $data['is_active']   ?? 'true')),  ['false', '0', 'no']),
                    'is_featured'    =>  in_array(strtolower(trim($data['destacado'] ?? $data['is_featured'] ?? 'false')), ['true',  '1', 'si', 'sí']),
                    'is_new'         =>  in_array(strtolower(trim($data['nuevo']     ?? $data['is_new']      ?? 'false')), ['true',  '1', 'si', 'sí']),
                    'is_hot_deal'    =>  in_array(strtolower(trim($data['oferta']    ?? $data['is_hot_deal'] ?? 'false')), ['true',  '1', 'si', 'sí']),
                ];

                if ($existing) {
                    $existing->update($productData);
                    $product = $existing;
                    $updated++;
                } else {
                    $productData['slug'] = $this->uniqueSlug(Str::slug($data['slug'] ?? $name));
                    $productData['sku']  = $sku ?? Product::generateSku($categoryId);
                    $product = Product::create($productData);
                    $created++;
                }

                if ($imageUrl && !$product->mainImage) {
                    $mediaFile = MediaFile::where('file_url', $imageUrl)->first();
                    ProductImage::create([
                        'product_id'    => $product->id,
                        'image_url'     => $mediaFile ? $mediaFile->file_url : $imageUrl,
                        'alt_text'      => $mediaFile?->alt_text ?: $name,
                        'display_order' => 0,
                        'is_main'       => true,
                    ]);
                }

            } catch (\Exception $e) {
                $errors[] = "Fila {$rowNum} (\"{$name}\"): " . $e->getMessage();
            }
        }

        $newCategories = array_unique($newCategories);
        $newBrands     = array_unique($newBrands);
        $newTags       = array_unique($newTags);

        return redirect()->route('admin.productos.index')->with('import_result', compact('created', 'updated', 'newCategories', 'newBrands', 'newTags', 'errors'));
    }

    /** Parsea CSV o Excel y devuelve array de rows asociativas */
    private function parseFile(string $path, string $extension): array
    {
        if (in_array($extension, ['xlsx', 'xls'])) {
            $spreadsheet = IOFactory::load($path);
            $sheet       = $spreadsheet->getActiveSheet();
            $data        = $sheet->toArray(null, true, true, false);

            if (empty($data)) return [];

            $headers = array_map(fn($h) => strtolower(trim((string) $h)), array_shift($data));
            $rows    = [];
            foreach ($data as $row) {
                $row = array_map(fn($v) => $v === null ? '' : (string) $v, $row);
                if (implode('', $row) === '') continue; // saltar filas vacías
                $rows[] = array_combine($headers, array_pad($row, count($headers), ''));
            }
            return $rows;
        }

        // CSV
        $handle  = fopen($path, 'r');
        // Strip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }
        $headers = array_map(fn($h) => strtolower(trim($h)), fgetcsv($handle));
        $rows    = [];
        while (($values = fgetcsv($handle)) !== false) {
            $rows[] = array_combine($headers, array_pad($values, count($headers), ''));
        }
        fclose($handle);
        return $rows;
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

    public function exportExcel()
    {
        $products = Product::with(['category', 'brand', 'mainImage', 'productImages'])->orderBy('name')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Productos');

        $columns = ['nombre','slug','descripcion','precio','precio_original','sku','stock','categoria','marca','badge','etiquetas','activo','destacado','nuevo','oferta','imagen'];

        foreach ($columns as $i => $col) {
            $sheet->setCellValue(chr(65 + $i) . '1', $col);
        }

        $lastCol = chr(65 + count($columns) - 1);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a4a6b']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        foreach ($products as $rowIdx => $p) {
            $row = $rowIdx + 2;
            $values = [
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
                $p->mainImage?->image_url ?? $p->productImages->first()?->image_url ?? '',
            ];
            foreach ($values as $i => $val) {
                $sheet->setCellValue(chr(65 + $i) . $row, $val);
            }
            if ($rowIdx % 2 === 1) {
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f8fafc']],
                ]);
            }
        }

        $widths = [20,20,35,12,15,10,8,15,12,10,20,8,10,8,8,35];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimension(chr(65 + $i))->setWidth($w);
        }
        $sheet->freezePane('A2');

        $writer = new Xlsx($spreadsheet);
        $filename = 'productos_' . now()->format('Ymd_His') . '.xlsx';

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function template()
    {
        $category = Category::active()->first()?->name ?? 'Electrónica';
        $brand    = Brand::active()->first()?->name    ?? 'Samsung';

        return response()->stream(function () use ($category, $brand) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['nombre','slug','descripcion','precio','precio_original','sku','stock','categoria','marca','badge','etiquetas','activo','destacado','nuevo','oferta','imagen']);
            fputcsv($handle, [
                'Producto Ejemplo', 'producto-ejemplo', 'Descripción del producto',
                '100000', '120000', 'SKU001', '10',
                $category, $brand, 'NUEVO', 'etiqueta1, etiqueta2',
                'true', 'false', 'true', 'false', 'https://ejemplo.com/imagen.jpg',
            ]);
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_productos.csv"',
        ]);
    }

    public function templateExcel()
    {
        $category = Category::active()->first()?->name ?? 'Electrónica';
        $brand    = Brand::active()->first()?->name    ?? 'Samsung';

        $columns = ['nombre','slug','descripcion','precio','precio_original','sku','stock','categoria','marca','badge','etiquetas','activo','destacado','nuevo','oferta','imagen'];
        $sample  = [
            'Producto Ejemplo', 'producto-ejemplo', 'Descripción del producto',
            100000, 120000, 'SKU001', 10,
            $category, $brand, 'NUEVO', 'etiqueta1, etiqueta2',
            'true', 'false', 'true', 'false', 'https://ejemplo.com/imagen.jpg',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Productos');

        // ── Encabezados ──
        foreach ($columns as $i => $col) {
            $cell = chr(65 + $i) . '1';
            $sheet->setCellValue($cell, $col);
        }

        $lastCol = chr(65 + count($columns) - 1);

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a4a6b']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '2563a8']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // ── Fila de ejemplo ──
        foreach ($sample as $i => $val) {
            $sheet->setCellValue(chr(65 + $i) . '2', $val);
        }

        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f0f7ff']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1e3f8']]],
            'font' => ['color' => ['rgb' => '374151']],
        ]);

        // ── Hoja de instrucciones ──
        $info = $spreadsheet->createSheet();
        $info->setTitle('Instrucciones');
        $info->setCellValue('A1', 'GUÍA DE CAMPOS');
        $info->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1a4a6b']],
        ]);

        $guide = [
            ['Campo', 'Obligatorio', 'Descripción', 'Ejemplo'],
            ['nombre',         'Sí',  'Nombre del producto',                      'Mouse Gamer Pro'],
            ['slug',           'No',  'URL amigable (se genera si está vacío)',    'mouse-gamer-pro'],
            ['descripcion',    'No',  'Descripción larga del producto',            'Descripción detallada...'],
            ['precio',         'Sí',  'Precio de venta (sin puntos ni comas)',     '150000'],
            ['precio_original','No',  'Precio antes del descuento',               '180000'],
            ['sku',            'No',  'Código interno del producto',               'MG-001'],
            ['stock',          'No',  'Cantidad disponible',                       '25'],
            ['categoria',      'No',  'Nombre exacto de la categoría',             $category],
            ['marca',          'No',  'Nombre exacto de la marca',                 $brand],
            ['badge',          'No',  'Etiqueta visual (ej: NUEVO, OFERTA)',       'NUEVO'],
            ['etiquetas',      'No',  'Tags separados por coma',                   'gaming, periférico'],
            ['activo',         'No',  'true / false',                              'true'],
            ['destacado',      'No',  'true / false',                              'false'],
            ['nuevo',          'No',  'true / false',                              'true'],
            ['oferta',         'No',  'true / false',                              'false'],
            ['imagen',         'No',  'URL de la imagen principal',               'https://ejemplo.com/img.jpg'],
        ];

        foreach ($guide as $r => $row) {
            foreach ($row as $c => $val) {
                $info->setCellValue(chr(65 + $c) . ($r + 2), $val);
            }
        }

        $info->getStyle('A2:D2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e07b1d']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        foreach (['A','B','C','D'] as $col) {
            $info->getColumnDimension($col)->setAutoSize(true);
        }

        // Anchos columnas hoja principal
        $widths = [20,20,35,12,15,10,8,15,12,10,20,8,10,8,8,35];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimension(chr(65 + $i))->setWidth($w);
        }

        $sheet->freezePane('A2');

        $writer = new Xlsx($spreadsheet);

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="plantilla_productos.xlsx"',
            'Cache-Control'       => 'max-age=0',
        ]);
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
