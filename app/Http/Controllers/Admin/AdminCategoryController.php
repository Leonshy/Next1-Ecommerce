<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
}
