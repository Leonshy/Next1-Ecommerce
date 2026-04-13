<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagsSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Insertar etiquetas de la referencia ────────────────────────────
        $tagsData = [
            ['name' => 'Destacado',    'slug' => 'destacado'],
            ['name' => 'Nuevo',        'slug' => 'nuevo'],
            ['name' => 'Ofertas',      'slug' => 'ofertas'],
            ['name' => 'Premium',      'slug' => 'premium'],
            ['name' => 'Black Friday', 'slug' => 'black-friday'],
            ['name' => 'Verano 2026',  'slug' => 'verano2026'],
        ];

        foreach ($tagsData as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], $tag);
        }

        // ── 2. Asignar etiquetas a productos (por nombre) ─────────────────────
        $productTags = [
            'Apple AirPods Pro 2'     => ['premium', 'black-friday', 'nuevo', 'ofertas', 'destacado'],
            'Logitech MX Master 3S'   => ['destacado', 'premium', 'black-friday'],
            'Logitech G Pro X'        => ['destacado', 'ofertas', 'black-friday'],
            'Sony WH-1000XM5'         => ['premium', 'destacado', 'nuevo', 'black-friday'],
            'JBL Tune 520BT'          => ['nuevo', 'verano2026'],
            'Sony DualSense PS5'      => ['destacado', 'verano2026'],
            'JBL Live Pro 2 TWS'      => ['ofertas', 'verano2026'],
            'Samsung Galaxy S24 Ultra'=> ['premium', 'destacado', 'nuevo'],
        ];

        foreach ($productTags as $productName => $tags) {
            $product = Product::where('name', $productName)->first();
            if ($product) {
                $product->update(['tags' => $tags]);
            }
        }

        $this->command->info('Tags creadas y asignadas correctamente.');
    }
}
