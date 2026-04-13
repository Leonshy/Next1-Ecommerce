<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
}
