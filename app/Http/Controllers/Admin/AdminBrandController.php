<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBrandController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);

        $slug = Str::slug($data['name']);
        $base = $slug;
        $i    = 2;
        while (Brand::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        Brand::create([
            'name'      => $data['name'],
            'slug'      => $slug,
            'is_active' => true,
        ]);

        return back()->with('success', 'Marca creada correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $brand = Brand::findOrFail($id);
        $data  = $request->validate(['name' => 'required|string|max:100']);
        $brand->update(['name' => $data['name']]);
        return back()->with('success', 'Marca actualizada.');
    }

    public function destroy(string $id)
    {
        $brand = Brand::withCount('products')->findOrFail($id);

        if ($brand->products_count > 0) {
            return back()->with('error', "No se puede eliminar \"{$brand->name}\" porque tiene {$brand->products_count} producto(s) asociado(s).");
        }

        $brand->delete();
        return back()->with('success', 'Marca eliminada.');
    }
}
