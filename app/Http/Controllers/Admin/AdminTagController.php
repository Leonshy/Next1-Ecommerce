<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminTagController extends Controller
{
    /** Slugs that cannot be deleted (linked to product flags) */
    private const PROTECTED = ['destacado', 'nuevo', 'ofertas'];

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);

        $slug = Str::slug($data['name']);

        // If a tag with this slug already exists, return it without creating a duplicate
        $existing = Tag::where('slug', $slug)->first();
        if ($existing) {
            if ($request->wantsJson()) {
                return response()->json(['name' => $existing->name, 'slug' => $existing->slug]);
            }
            return back()->with('success', 'La etiqueta ya existe.');
        }

        $tag = Tag::create(['name' => $data['name'], 'slug' => $slug]);

        if ($request->wantsJson()) {
            return response()->json(['name' => $tag->name, 'slug' => $tag->slug]);
        }

        return back()->with('success', 'Etiqueta creada correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $tag     = Tag::findOrFail($id);
        $data    = $request->validate(['name' => 'required|string|max:100']);
        $oldSlug = $tag->slug;
        $newSlug = Str::slug($data['name']);

        // Si el slug cambia, actualizarlo en todos los productos que la usan
        if ($oldSlug !== $newSlug) {
            Product::whereJsonContains('tags', $oldSlug)->each(function (Product $p) use ($oldSlug, $newSlug) {
                $p->update([
                    'tags' => array_map(fn($t) => $t === $oldSlug ? $newSlug : $t, $p->tags ?? []),
                ]);
            });
        }

        $tag->update(['name' => $data['name'], 'slug' => $newSlug]);

        return back()->with('success', 'Etiqueta actualizada.');
    }

    public function destroy(string $id)
    {
        $tag = Tag::findOrFail($id);

        if (in_array($tag->slug, self::PROTECTED)) {
            return back()->with('error', "La etiqueta \"{$tag->name}\" es permanente y no puede eliminarse.");
        }

        // Quitar la etiqueta de todos los productos que la usan
        Product::whereJsonContains('tags', $tag->slug)->each(function (Product $p) use ($tag) {
            $p->update([
                'tags' => array_values(array_filter($p->tags ?? [], fn($t) => $t !== $tag->slug)),
            ]);
        });

        $tag->delete();
        return back()->with('success', 'Etiqueta eliminada y removida de todos los productos.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120']);

        $rows = $this->parseSimpleFile($request->file('file'));

        if (empty($rows)) {
            return back()->withErrors(['file' => 'El archivo está vacío o no tiene el formato correcto.'])->withFragment('etiquetas');
        }

        $created = 0;
        $skipped = 0;
        $errors  = [];
        $rowNum  = 1;

        foreach ($rows as $data) {
            $rowNum++;
            $name = trim($data['nombre'] ?? $data['name'] ?? '');
            if (!$name) { $errors[] = "Fila {$rowNum}: nombre vacío."; continue; }

            $slug = Str::slug($name);

            try {
                if (Tag::where('slug', $slug)->exists()) {
                    $skipped++;
                } else {
                    Tag::create(['name' => $name, 'slug' => $slug]);
                    $created++;
                }
            } catch (\Exception $e) {
                $errors[] = "Fila {$rowNum} (\"{$name}\"): " . $e->getMessage();
            }
        }

        return redirect()->route('admin.productos.index', ['tab' => 'etiquetas'])
            ->with('import_result_etiquetas', compact('created', 'skipped', 'errors'));
    }

    private function parseSimpleFile($file): array
    {
        $ext  = strtolower($file->getClientOriginalExtension());
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
