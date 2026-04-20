<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminCategoryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $slug = Str::slug($data['name']);
        $base = $slug;
        $i    = 2;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        Category::create([
            'name'      => $data['name'],
            'slug'      => $slug,
            'parent_id' => $data['parent_id'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validate(['name' => 'required|string|max:100']);
        $category->update(['name' => $data['name']]);
        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(string $id)
    {
        $category = Category::withCount('products')->findOrFail($id);

        if ($category->products_count > 0) {
            return back()->with('error', "No se puede eliminar \"{$category->name}\" porque tiene {$category->products_count} producto(s) asociado(s).");
        }

        $category->delete();
        return back()->with('success', 'Categoría eliminada.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120']);

        $rows = $this->parseSimpleFile($request->file('file'));

        if (empty($rows)) {
            return back()->withErrors(['file' => 'El archivo está vacío o no tiene el formato correcto.'])->withFragment('categorias');
        }

        $created = 0;
        $updated = 0;
        $errors  = [];
        $rowNum  = 1;

        $rootCategories = Category::whereNull('parent_id')->pluck('id', 'name')
            ->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);

        foreach ($rows as $data) {
            $rowNum++;
            $name = trim($data['nombre'] ?? $data['name'] ?? '');
            if (!$name) { $errors[] = "Fila {$rowNum}: nombre vacío."; continue; }

            $parentName = strtolower(trim($data['padre'] ?? $data['parent'] ?? ''));
            $parentId   = $parentName ? ($rootCategories[$parentName] ?? null) : null;

            $slug = Str::slug($name);
            $existing = Category::where('slug', $slug)->first();

            try {
                if ($existing) {
                    $existing->update(['name' => $name, 'parent_id' => $parentId]);
                    $updated++;
                } else {
                    $base = $slug; $i = 2;
                    while (Category::where('slug', $slug)->exists()) { $slug = "{$base}-{$i}"; $i++; }
                    Category::create(['name' => $name, 'slug' => $slug, 'parent_id' => $parentId, 'is_active' => true]);
                    $created++;
                }
            } catch (\Exception $e) {
                $errors[] = "Fila {$rowNum} (\"{$name}\"): " . $e->getMessage();
            }
        }

        return redirect()->route('admin.productos.index', ['tab' => 'categorias'])
            ->with('import_result_categorias', compact('created', 'updated', 'errors'));
    }

    private function parseSimpleFile($file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        if (in_array($ext, ['xlsx', 'xls'])) {
            $sheet = IOFactory::load($path)->getActiveSheet();
            $data  = $sheet->toArray(null, true, true, false);
            if (empty($data)) return [];
            $headers = array_map(fn($h) => strtolower(trim((string) $h)), array_shift($data));
            $rows = [];
            foreach ($data as $row) {
                $row = array_map(fn($v) => $v === null ? '' : (string) $v, $row);
                if (implode('', $row) === '') continue;
                $rows[] = array_combine($headers, array_pad($row, count($headers), ''));
            }
            return $rows;
        }

        $handle = fopen($path, 'r');
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") rewind($handle);
        $headers = array_map(fn($h) => strtolower(trim($h)), fgetcsv($handle));
        $rows = [];
        while (($values = fgetcsv($handle)) !== false) {
            $rows[] = array_combine($headers, array_pad($values, count($headers), ''));
        }
        fclose($handle);
        return $rows;
    }
}
