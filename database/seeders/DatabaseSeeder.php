<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\EmailTemplate;
use App\Models\HeroSlide;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\PromoBanner;
use App\Models\SeoSetting;
use App\Models\SiteContent;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    // ============================================================
    // Convierte clases Tailwind de gradientes en CSS real
    // ============================================================
    private function tailwindGradientToCss(string $classes): string
    {
        $map = [
            // blues
            'from-blue-600'   => '#2563eb', 'via-blue-700'  => '#1d4ed8', 'to-blue-900'   => '#1e3a8a',
            'from-blue-500'   => '#3b82f6', 'to-blue-700'   => '#1d4ed8',
            // orange / red
            'from-orange-500' => '#f97316', 'via-orange-600'=> '#ea580c', 'to-red-600'    => '#dc2626',
            'from-red-500'    => '#ef4444', 'to-red-700'    => '#b91c1c',
            // amber / orange
            'from-amber-500'  => '#f59e0b', 'via-amber-600' => '#d97706', 'to-orange-600' => '#ea580c',
            // green
            'from-green-500'  => '#22c55e', 'to-green-700'  => '#15803d',
            // purple
            'from-purple-500' => '#a855f7', 'to-purple-700' => '#7e22ce',
            // cyan
            'from-cyan-500'   => '#06b6d4', 'to-cyan-700'   => '#0e7490',
        ];

        $parts = explode(' ', trim($classes));
        $from = '#2563eb'; $via = null; $to = '#1e3a8a';
        foreach ($parts as $p) {
            if (str_starts_with($p, 'from-')) $from = $map[$p] ?? $from;
            if (str_starts_with($p, 'via-'))  $via  = $map[$p] ?? null;
            if (str_starts_with($p, 'to-'))   $to   = $map[$p] ?? $to;
        }

        $stops = $via
            ? "{$from}, {$via}, {$to}"
            : "{$from}, {$to}";

        return "linear-gradient(135deg, {$stops})";
    }

    // ============================================================
    // Imagen de producto con picsum (seed basado en slug)
    // ============================================================
    private function productImg(string $slug, int $idx = 0): string
    {
        $seed = preg_replace('/[^a-z0-9]/', '', $slug) . $idx;
        return "https://picsum.photos/seed/{$seed}/600/600";
    }

    public function run(): void
    {
        // ====================================================
        // USERS
        // ====================================================
        $admin = User::firstOrCreate(
            ['email' => 'admin@next1.com.py'],
            ['name' => 'Administrador', 'password' => Hash::make('password')]
        );
        UserRole::firstOrCreate(['user_id' => $admin->id, 'role' => 'admin']);
        UserRole::firstOrCreate(['user_id' => $admin->id, 'role' => 'usuario']);

        $testUser = User::firstOrCreate(
            ['email' => 'leodav.amarilla@gmail.com'],
            ['name' => 'Leonardo Amarilla', 'password' => Hash::make('password')]
        );
        UserRole::firstOrCreate(['user_id' => $testUser->id, 'role' => 'usuario']);

        // ====================================================
        // SITE CONTENT – Store info
        // ====================================================
        SiteContent::updateOrCreate(
            ['key' => 'store_info'],
            [
                'title'    => 'Información de la Tienda',
                'content'  => 'NEXT1 - Tu tienda de tecnología en Paraguay.',
                'metadata' => [
                    'store_name'  => 'NEXT1',
                    'description' => 'La tienda líder en tecnología y electrónica en Paraguay.',
                    'phone'       => '+595 981 953 964',
                    'email'       => 'admin@next1.com.py',
                    'address'     => 'Av. Mariscal López 1234, Asunción, Paraguay',
                    'facebook'    => 'https://facebook.com/next1py',
                    'instagram'   => 'https://instagram.com/next1py',
                    'schedule'    => [
                        'lunes'     => ['start' => '08:00', 'end' => '18:00'],
                        'martes'    => ['start' => '08:00', 'end' => '18:00'],
                        'miercoles' => ['start' => '08:00', 'end' => '18:00'],
                        'jueves'    => ['start' => '08:00', 'end' => '18:00'],
                        'viernes'   => ['start' => '08:00', 'end' => '18:00'],
                        'sabado'    => ['start' => '08:00', 'end' => '13:00'],
                        'domingo'   => ['closed' => true],
                    ],
                ],
            ]
        );

        // ====================================================
        // SEO
        // ====================================================
        SeoSetting::updateOrCreate(
            ['page_key' => 'global'],
            [
                'meta_title'       => 'NEXT1 - Tu Tienda de Tecnología',
                'meta_description' => 'NEXT1 es la tienda líder en tecnología y electrónica en Paraguay. Encuentra los mejores productos al mejor precio.',
            ]
        );

        // ====================================================
        // BRANDS  (UUIDs del JSON)
        // ====================================================
        $brandsRaw = [
            ['id' => '8a221049-5989-4dfe-890e-d0e5dbeb5b45', 'name' => 'Sony',     'slug' => 'sony',     'logo_url' => 'https://placehold.co/120x60/ffffff/1a537a?text=Sony'],
            ['id' => 'bab6c62b-8efd-4088-8b49-3c83d9901fff', 'name' => 'Samsung',  'slug' => 'samsung',  'logo_url' => 'https://placehold.co/120x60/ffffff/1a537a?text=Samsung'],
            ['id' => 'ff09a494-82ca-44c2-812b-09b903e29cc8', 'name' => 'Apple',    'slug' => 'apple',    'logo_url' => 'https://placehold.co/120x60/ffffff/1a537a?text=Apple'],
            ['id' => 'd5743dba-b747-4c1d-81d5-31956c7bba04', 'name' => 'JBL',      'slug' => 'jbl',      'logo_url' => 'https://placehold.co/120x60/ffffff/1a537a?text=JBL'],
            ['id' => '3416e0f9-be28-4e04-9fa6-cb3f2c47487f', 'name' => 'Logitech', 'slug' => 'logitech', 'logo_url' => 'https://placehold.co/120x60/ffffff/1a537a?text=Logitech'],
        ];

        foreach ($brandsRaw as $b) {
            Brand::withoutGlobalScopes()->updateOrCreate(
                ['id' => $b['id']],
                array_merge($b, ['is_active' => true])
            );
        }

        // ====================================================
        // CATEGORIES  (UUIDs del JSON, con jerarquía)
        // ====================================================
        $catsRaw = [
            // Padres
            ['id' => '067fdc27-0445-4d66-95e0-a16172a4f135', 'name' => 'Electrónica',   'slug' => 'electronica',  'display_order' => 1, 'parent_id' => null, 'image_url' => 'https://placehold.co/80x80/e8f0fe/1a537a?text=⚡'],
            ['id' => '3a9f51bb-f4b0-494e-abee-4f43c092547f', 'name' => 'Audio',          'slug' => 'audio',        'display_order' => 2, 'parent_id' => null, 'image_url' => 'https://placehold.co/80x80/e8f0fe/1a537a?text=🎧'],
            ['id' => '1b3c6a82-b1ba-4d8d-8180-6cca11755c97', 'name' => 'Gaming',         'slug' => 'gaming',       'display_order' => 3, 'parent_id' => null, 'image_url' => 'https://placehold.co/80x80/e8f0fe/1a537a?text=🎮'],
            ['id' => '4f91cf25-54a3-4a92-91c6-f79a2869e642', 'name' => 'Computación',    'slug' => 'computacion',  'display_order' => 4, 'parent_id' => null, 'image_url' => 'https://placehold.co/80x80/e8f0fe/1a537a?text=💻'],
            // Hijos de Electrónica
            ['id' => '162ffc3a-c596-46bb-a6dd-9dd9f1ab6981', 'name' => 'Smartphones',   'slug' => 'smartphones',  'display_order' => 1, 'parent_id' => '067fdc27-0445-4d66-95e0-a16172a4f135', 'image_url' => 'https://placehold.co/80x80/e8f0fe/1a537a?text=📱'],
            ['id' => 'fd99f248-79ee-4120-a75a-ac6abd4a9088', 'name' => 'Tablets',        'slug' => 'tablets',      'display_order' => 2, 'parent_id' => '067fdc27-0445-4d66-95e0-a16172a4f135', 'image_url' => 'https://placehold.co/80x80/e8f0fe/1a537a?text=📟'],
            // Hijos de Audio
            ['id' => 'b947890a-33b0-4b98-b78f-1847188797e7', 'name' => 'Auriculares',   'slug' => 'auriculares',  'display_order' => 1, 'parent_id' => '3a9f51bb-f4b0-494e-abee-4f43c092547f', 'image_url' => 'https://placehold.co/80x80/e8f0fe/1a537a?text=🎧'],
            ['id' => '061c67c4-12ce-4428-97b4-5822d8db07a7', 'name' => 'Parlantes',      'slug' => 'parlantes',    'display_order' => 2, 'parent_id' => '3a9f51bb-f4b0-494e-abee-4f43c092547f', 'image_url' => 'https://placehold.co/80x80/e8f0fe/1a537a?text=🔊'],
            // Hijos de Gaming
            ['id' => '813c1981-d313-42d2-bfd0-101659332aeb', 'name' => 'Controles',      'slug' => 'controles',    'display_order' => 1, 'parent_id' => '1b3c6a82-b1ba-4d8d-8180-6cca11755c97', 'image_url' => 'https://placehold.co/80x80/e8f0fe/1a537a?text=🕹️'],
            ['id' => '034bab3e-295b-46ec-87a3-a0ea65914659', 'name' => 'Accesorios Gaming','slug'=>'accesorios-gaming','display_order'=> 2, 'parent_id' => '1b3c6a82-b1ba-4d8d-8180-6cca11755c97', 'image_url' => 'https://placehold.co/80x80/e8f0fe/1a537a?text=🎯'],
            // Hijos de Computación
            ['id' => 'fd0dce7c-72bf-40b9-aa22-485851d74ad5', 'name' => 'Mouse',          'slug' => 'mouse',        'display_order' => 1, 'parent_id' => '4f91cf25-54a3-4a92-91c6-f79a2869e642', 'image_url' => 'https://placehold.co/80x80/e8f0fe/1a537a?text=🖱️'],
            ['id' => '3601a26d-314a-4c76-b185-cc63609a5f8f', 'name' => 'Teclados',       'slug' => 'teclados',     'display_order' => 2, 'parent_id' => '4f91cf25-54a3-4a92-91c6-f79a2869e642', 'image_url' => 'https://placehold.co/80x80/e8f0fe/1a537a?text=⌨️'],
        ];

        // Insertar padres primero, luego hijos
        $parents = array_filter($catsRaw, fn($c) => $c['parent_id'] === null);
        $children = array_filter($catsRaw, fn($c) => $c['parent_id'] !== null);

        foreach (array_merge(array_values($parents), array_values($children)) as $c) {
            Category::withoutGlobalScopes()->updateOrCreate(
                ['id' => $c['id']],
                array_merge($c, ['is_active' => true, 'description' => $c['name']])
            );
        }

        // ====================================================
        // HERO SLIDES  (UUIDs del JSON, imágenes corregidas)
        // ====================================================
        $heroSlidesRaw = [
            [
                'id'            => 'a9adf5ad-ae69-4def-b27b-67ff7b5eea44',
                'title'         => 'Nikon D7100',
                'subtitle'      => 'Calidad de imagen definitiva. Crea sin limitaciones',
                'image_url'     => 'https://picsum.photos/seed/heroslide1/1200/400',
                'button_text'   => 'Ver más',
                'button_link'   => '/productos',
                'display_order' => 1,
                'is_active'     => true,
            ],
            [
                'id'            => 'c6133929-0534-4136-a75c-88e99f773942',
                'title'         => 'Nueva Gama de',
                'subtitle'      => 'TABLETS desde ₲990.000',
                'image_url'     => 'https://picsum.photos/seed/heroslide2/1200/400',
                'button_text'   => 'Ver más',
                'button_link'   => '/productos',
                'display_order' => 2,
                'is_active'     => true,
            ],
            [
                'id'            => '1afbf8a1-8502-40e8-8231-39c64a6b0b27',
                'title'         => 'Nueva Gama de',
                'subtitle'      => 'iMACS disponibles',
                'image_url'     => 'https://picsum.photos/seed/heroslide3/1200/400',
                'button_text'   => 'Ver más',
                'button_link'   => '/productos',
                'display_order' => 3,
                'is_active'     => true,
            ],
            [
                'id'            => 'e800472a-f426-4403-964c-465935bee2aa',
                'title'         => 'GIFT CARDS',
                'subtitle'      => 'El regalo perfecto para cualquier ocasión. Regala tecnología sin complicaciones.',
                'image_url'     => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1920&h=600&fit=crop',
                'button_text'   => 'Comprar Gift Card',
                'button_link'   => '/gift-cards',
                'display_order' => 4,
                'is_active'     => true,
            ],
        ];

        foreach ($heroSlidesRaw as $slide) {
            HeroSlide::withoutGlobalScopes()->updateOrCreate(['id' => $slide['id']], $slide);
        }

        // ====================================================
        // PROMO BANNERS  (UUIDs del JSON, gradientes a CSS)
        // ====================================================
        $promoBannersRaw = [
            [
                'id'                  => '39403731-bf4a-4e47-811a-b316a75a8f9a',
                'title'               => 'Samsung Week',
                'subtitle'            => 'Campaña Especial',
                'description'         => 'Hasta 40% OFF en celulares y accesorios',
                'background_gradient' => $this->tailwindGradientToCss('from-blue-600 via-blue-700 to-blue-900'),
                'text_color'          => 'white',
                'button_text'         => 'Ver Ofertas',
                'button_link'         => '/productos?marca=samsung',
                'button_text_color'   => 'white',
                'display_order'       => 1,
                'is_active'           => true,
            ],
            [
                'id'                  => 'aa00526f-e52f-4199-9eea-a3a57b65aeff',
                'title'               => 'Sony Days',
                'subtitle'            => 'Oferta Exclusiva',
                'description'         => 'Audífonos y controles con descuento',
                'background_gradient' => $this->tailwindGradientToCss('from-orange-500 via-orange-600 to-red-600'),
                'text_color'          => 'white',
                'button_text'         => 'Ver Ofertas',
                'button_link'         => '/productos?marca=sony',
                'button_text_color'   => 'white',
                'display_order'       => 2,
                'is_active'           => true,
            ],
            [
                'id'                  => '40ec7203-b9df-4653-8b69-ce3e64472df1',
                'title'               => 'Gift Cards',
                'subtitle'            => 'Regala Tecnología',
                'description'         => 'El regalo perfecto para cualquier ocasión',
                'background_gradient' => $this->tailwindGradientToCss('from-amber-500 via-amber-600 to-orange-600'),
                'text_color'          => 'white',
                'button_text'         => 'Comprar Ahora',
                'button_link'         => '/gift-cards',
                'button_text_color'   => 'white',
                'display_order'       => 3,
                'is_active'           => true,
            ],
            // 4to banner para completar la grilla de 4 columnas
            [
                'id'                  => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
                'title'               => 'Logitech Sale',
                'subtitle'            => 'Periféricos Pro',
                'description'         => 'Mouse y teclados gaming con descuento',
                'background_gradient' => 'linear-gradient(135deg, #a855f7, #7c3aed)',
                'text_color'          => 'white',
                'button_text'         => 'Ver Ofertas',
                'button_link'         => '/productos?marca=logitech',
                'button_text_color'   => 'white',
                'display_order'       => 4,
                'is_active'           => true,
            ],
        ];

        foreach ($promoBannersRaw as $banner) {
            PromoBanner::withoutGlobalScopes()->updateOrCreate(['id' => $banner['id']], $banner);
        }

        // ====================================================
        // PRODUCTS  (UUIDs del JSON + imágenes via picsum)
        // ====================================================
        $productsRaw = [
            [
                'id'             => '9eed2803-a5a8-4a1f-87d1-7179216514e2',
                'name'           => 'Apple AirPods Pro 2',
                'slug'           => 'apple-airpods-pro-2',
                'description'    => '<p>Auriculares con cancelación activa de ruido, audio espacial personalizado y estuche con carga USB-C. La experiencia de audio premium de Apple.</p>',
                'price'          => 1450000,
                'original_price' => 1600000,
                'sku'            => 'APPLE-APP2',
                'stock'          => 25,
                'category_id'    => 'b947890a-33b0-4b98-b78f-1847188797e7',
                'brand_id'       => 'd5743dba-b747-4c1d-81d5-31956c7bba04',
                'badge'          => '-9%',
                'tags'           => ['premium', 'blackfriday', 'nuevo', 'ofertas', 'destacado'],
                'is_active'      => true,
                'is_featured'    => true,
                'is_hot_deal'    => true,
                'is_new'         => true,
                'rating'         => 4.9,
                'reviews_count'  => 312,
                'images'         => [
                    $this->productImg('apple-airpods-pro-2', 0),
                    $this->productImg('apple-airpods-pro-2', 1),
                    $this->productImg('apple-airpods-pro-2', 2),
                ],
            ],
            [
                'id'             => 'ddf04302-9a73-4337-97b1-fe4f2e2f08da',
                'name'           => 'Logitech MX Master 3S',
                'slug'           => 'logitech-mx-master-3s',
                'description'    => '<p>Mouse inalámbrico de productividad con desplazamiento electromagnético MagSpeed y seguimiento 8K DPI. El mouse definitivo para trabajo creativo.</p>',
                'price'          => 650000,
                'original_price' => null,
                'sku'            => 'LOGI-MXMASTER3S',
                'stock'          => 18,
                'category_id'    => 'fd0dce7c-72bf-40b9-aa22-485851d74ad5',
                'brand_id'       => '3416e0f9-be28-4e04-9fa6-cb3f2c47487f',
                'badge'          => 'TOP',
                'tags'           => ['destacado', 'premium', 'black-friday'],
                'is_active'      => true,
                'is_featured'    => true,
                'is_hot_deal'    => false,
                'is_new'         => false,
                'rating'         => 4.8,
                'reviews_count'  => 142,
                'images'         => [
                    $this->productImg('logitech-mx-master-3s', 0),
                    $this->productImg('logitech-mx-master-3s', 1),
                ],
            ],
            [
                'id'             => '1a7a33bd-2f47-41c3-a8cd-f2b192aa98d1',
                'name'           => 'Logitech G Pro X',
                'slug'           => 'logitech-g-pro-x',
                'description'    => '<p>Mouse gaming profesional con sensor HERO 25K. Diseño ambidiestro ultraligero de 63g. El mouse que usan los esports professionals.</p>',
                'price'          => 450000,
                'original_price' => 520000,
                'sku'            => 'LOGI-GPROX',
                'stock'          => 28,
                'category_id'    => 'fd0dce7c-72bf-40b9-aa22-485851d74ad5',
                'brand_id'       => '3416e0f9-be28-4e04-9fa6-cb3f2c47487f',
                'badge'          => '-13%',
                'tags'           => ['destacado', 'ofertas', 'blackfriday'],
                'is_active'      => true,
                'is_featured'    => true,
                'is_hot_deal'    => true,
                'is_new'         => false,
                'rating'         => 4.9,
                'reviews_count'  => 256,
                'images'         => [
                    $this->productImg('logitech-g-pro-x', 0),
                    $this->productImg('logitech-g-pro-x', 1),
                ],
            ],
            [
                'id'             => '6c488e39-ba05-4682-9953-8f6667576cbd',
                'name'           => 'Sony WH-1000XM5',
                'slug'           => 'sony-wh-1000xm5',
                'description'    => '<p>Auriculares inalámbricos con cancelación de ruido líder en la industria. Audio de alta resolución y hasta 30 horas de batería. El estándar oro de los auriculares.</p>',
                'price'          => 220000,
                'original_price' => null,
                'sku'            => 'SONY-WH1000XM5',
                'stock'          => 15,
                'category_id'    => 'b947890a-33b0-4b98-b78f-1847188797e7',
                'brand_id'       => '8a221049-5989-4dfe-890e-d0e5dbeb5b45',
                'badge'          => '-16%',
                'tags'           => ['premium', 'destacado', 'nuevo', 'black-friday', 'blackfriday'],
                'is_active'      => true,
                'is_featured'    => true,
                'is_hot_deal'    => false,
                'is_new'         => true,
                'rating'         => 4.8,
                'reviews_count'  => 124,
                'images'         => [
                    $this->productImg('sony-wh-1000xm5', 0),
                    $this->productImg('sony-wh-1000xm5', 1),
                    $this->productImg('sony-wh-1000xm5', 2),
                ],
            ],
            [
                'id'             => '4cc7eabc-2a39-4f7e-a6f9-1d6426e9904a',
                'name'           => 'JBL Tune 520BT',
                'slug'           => 'jbl-tune-520bt',
                'description'    => '<p>Auriculares on-ear inalámbricos con sonido JBL Pure Bass. Hasta 57 horas de reproducción con conexión multipunto a 2 dispositivos.</p>',
                'price'          => 320000,
                'original_price' => null,
                'sku'            => 'JBL-TUNE520BT',
                'stock'          => 45,
                'category_id'    => 'b947890a-33b0-4b98-b78f-1847188797e7',
                'brand_id'       => 'd5743dba-b747-4c1d-81d5-31956c7bba04',
                'badge'          => 'NUEVO',
                'tags'           => ['nuevo', 'verano2026'],
                'is_active'      => true,
                'is_featured'    => false,
                'is_hot_deal'    => false,
                'is_new'         => true,
                'rating'         => 4.5,
                'reviews_count'  => 89,
                'images'         => [
                    $this->productImg('jbl-tune-520bt', 0),
                    $this->productImg('jbl-tune-520bt', 1),
                ],
            ],
            [
                'id'             => '77626ee3-684b-4ea4-b6e3-7f2cb022ae5f',
                'name'           => 'Sony DualSense PS5',
                'slug'           => 'sony-dualsense-ps5',
                'description'    => '<p>Control inalámbrico para PlayStation 5 con retroalimentación háptica y gatillos adaptativos. Vibración inmersiva que transforma la experiencia gaming.</p>',
                'price'          => 420000,
                'original_price' => null,
                'sku'            => 'SONY-DUALSENSE',
                'stock'          => 32,
                'category_id'    => '813c1981-d313-42d2-bfd0-101659332aeb',
                'brand_id'       => '8a221049-5989-4dfe-890e-d0e5dbeb5b45',
                'badge'          => null,
                'tags'           => ['destacado', 'verano2026'],
                'is_active'      => true,
                'is_featured'    => true,
                'is_hot_deal'    => false,
                'is_new'         => false,
                'rating'         => 4.7,
                'reviews_count'  => 178,
                'images'         => [
                    $this->productImg('sony-dualsense-ps5', 0),
                    $this->productImg('sony-dualsense-ps5', 1),
                ],
            ],
            [
                'id'             => '60a8de24-0eef-4a43-ab09-de1daf7f4e56',
                'name'           => 'JBL Live Pro 2 TWS',
                'slug'           => 'jbl-live-pro-2-tws',
                'description'    => '<p>Auriculares true wireless con cancelación adaptiva de ruido y hasta 40 horas de batería total. Sonido JBL signature con graves profundos.</p>',
                'price'          => 580000,
                'original_price' => 680000,
                'sku'            => 'JBL-LIVEPRO2',
                'stock'          => 22,
                'category_id'    => 'b947890a-33b0-4b98-b78f-1847188797e7',
                'brand_id'       => 'd5743dba-b747-4c1d-81d5-31956c7bba04',
                'badge'          => '-15%',
                'tags'           => ['ofertas', 'verano2026'],
                'is_active'      => true,
                'is_featured'    => false,
                'is_hot_deal'    => true,
                'is_new'         => false,
                'rating'         => 4.6,
                'reviews_count'  => 93,
                'images'         => [
                    $this->productImg('jbl-live-pro-2-tws', 0),
                    $this->productImg('jbl-live-pro-2-tws', 1),
                ],
            ],
            [
                'id'             => 'ede1feb2-4bff-401e-8950-836f6f01c4c4',
                'name'           => 'Samsung Galaxy S24 Ultra',
                'slug'           => 'samsung-galaxy-s24-ultra',
                'description'    => '<p>Smartphone premium con S Pen integrado, cámara de 200MP y procesador Snapdragon 8 Gen 3. El flagship definitivo de Samsung para 2024.</p>',
                'price'          => 7500000,
                'original_price' => null,
                'sku'            => 'SAM-S24ULTRA',
                'stock'          => 8,
                'category_id'    => '162ffc3a-c596-46bb-a6dd-9dd9f1ab6981',
                'brand_id'       => 'bab6c62b-8efd-4088-8b49-3c83d9901fff',
                'badge'          => 'PREMIUM',
                'tags'           => ['premium', 'destacado', 'nuevo'],
                'is_active'      => true,
                'is_featured'    => true,
                'is_hot_deal'    => false,
                'is_new'         => true,
                'rating'         => 4.9,
                'reviews_count'  => 67,
                'images'         => [
                    $this->productImg('samsung-galaxy-s24-ultra', 0),
                    $this->productImg('samsung-galaxy-s24-ultra', 1),
                    $this->productImg('samsung-galaxy-s24-ultra', 2),
                ],
            ],
        ];

        foreach ($productsRaw as $data) {
            $images = $data['images'];
            unset($data['images']);

            $product = Product::withoutGlobalScopes()->updateOrCreate(
                ['id' => $data['id']],
                $data
            );

            // Regenerar imágenes siempre
            ProductImage::where('product_id', $product->id)->delete();
            foreach ($images as $idx => $imgUrl) {
                ProductImage::create([
                    'product_id'    => $product->id,
                    'image_url'     => $imgUrl,
                    'alt_text'      => $product->name,
                    'display_order' => $idx,
                    'is_main'       => $idx === 0,
                ]);
            }
        }

        // ====================================================
        // ORDERS (datos del JSON, user_id = null para guest orders)
        // ====================================================
        $ordersRaw = [
            ['id'=>'09cb5aad-6293-43a1-a50d-a4877eaeae1d','order_number'=>'ORD-2025-001','status'=>'pendiente',  'customer_name'=>'María García López',    'customer_email'=>'maria.garcia@email.com',       'customer_phone'=>'+595 981 234567','shipping_address'=>'Av. Mariscal López 1234, Asunción','shipping_city'=>'Asunción',         'subtotal'=>2270000,'discount'=>0,      'shipping_cost'=>50000, 'total'=>2320000,'notes'=>'Cliente solicita envío urgente','user_id'=>null],
            ['id'=>'6f8295c7-7ee4-461f-84f9-1ac3f03cd935','order_number'=>'ORD-2025-002','status'=>'confirmado', 'customer_name'=>'Carlos Mendoza',         'customer_email'=>'carlos.mendoza@gmail.com',     'customer_phone'=>'+595 971 987654','shipping_address'=>'Calle Palma 567, Centro',         'shipping_city'=>'Asunción',         'subtotal'=>7500000,'discount'=>500000,'shipping_cost'=>0,     'total'=>7000000,'notes'=>'Descuento por cliente frecuente','user_id'=>null],
            ['id'=>'c243dc60-a69e-4ac7-9f24-45f0287bc336','order_number'=>'ORD-2025-003','status'=>'enviado',    'customer_name'=>'Ana Sofía Rodríguez',    'customer_email'=>'ana.rodriguez@hotmail.com',    'customer_phone'=>'+595 991 456789','shipping_address'=>'Super Carretera Este Km 5',       'shipping_city'=>'Ciudad del Este',  'subtotal'=>770000, 'discount'=>0,      'shipping_cost'=>80000, 'total'=>850000, 'notes'=>'Tracking: PY123456789',        'user_id'=>null],
            ['id'=>'a8a4cd12-2e02-4af0-acde-6fafa60ca0ab','order_number'=>'ORD-2025-004','status'=>'entregado',  'customer_name'=>'Roberto Fernández',      'customer_email'=>'roberto.f@empresa.com.py',     'customer_phone'=>'+595 961 111222','shipping_address'=>'Ruta Transchaco Km 12',           'shipping_city'=>'Mariano Roque Alonso','subtotal'=>420000,'discount'=>0,   'shipping_cost'=>30000, 'total'=>450000, 'notes'=>null,                           'user_id'=>null],
            ['id'=>'23640d58-2b6c-414d-9259-1cdc5ec30561','order_number'=>'ORD-2025-005','status'=>'cancelado',  'customer_name'=>'Laura Benítez',          'customer_email'=>'laura.benitez@yahoo.com',      'customer_phone'=>'+595 982 333444','shipping_address'=>'Barrio San Pablo, Calle 4',       'shipping_city'=>'Encarnación',      'subtotal'=>1850000,'discount'=>185000,'shipping_cost'=>100000,'total'=>1765000,'notes'=>'Cliente canceló por demora',   'user_id'=>null],
            ['id'=>'8978d971-0d0a-4b51-866e-225b92d8be28','order_number'=>'ORD-2025-010','status'=>'entregado',  'customer_name'=>'Leonardo Amarilla',      'customer_email'=>'leodav.amarilla@gmail.com',    'customer_phone'=>'+595 981 123456','shipping_address'=>'Av. Mariscal López 1234',         'shipping_city'=>'Asunción',         'subtotal'=>1100000,'discount'=>0,      'shipping_cost'=>50000, 'total'=>1150000,'notes'=>'Entrega exitosa',               'user_id'=>$testUser->id],
            ['id'=>'cfd5cad8-b699-4f1a-8d49-b0c6d69dd542','order_number'=>'ORD-2025-011','status'=>'entregado',  'customer_name'=>'Leonardo Amarilla',      'customer_email'=>'leodav.amarilla@gmail.com',    'customer_phone'=>'+595 981 123456','shipping_address'=>'Av. Mariscal López 1234',         'shipping_city'=>'Asunción',         'subtotal'=>650000, 'discount'=>65000,  'shipping_cost'=>0,     'total'=>585000, 'notes'=>'Descuento aplicado 10%',       'user_id'=>$testUser->id],
        ];

        foreach ($ordersRaw as $order) {
            Order::withoutGlobalScopes()->updateOrCreate(['id' => $order['id']], $order);
        }

        // ====================================================
        // ORDER ITEMS
        // ====================================================
        $itemsRaw = [
            ['id'=>'028bde63-49d2-4000-aee1-e2119211b2d5','order_id'=>'09cb5aad-6293-43a1-a50d-a4877eaeae1d','product_id'=>'6c488e39-ba05-4682-9953-8f6667576cbd','product_name'=>'Sony WH-1000XM5',    'quantity'=>1,'unit_price'=>1850000,'total_price'=>1850000],
            ['id'=>'de384c4a-2ede-4d36-8089-75d4554d83ef','order_id'=>'09cb5aad-6293-43a1-a50d-a4877eaeae1d','product_id'=>'77626ee3-684b-4ea4-b6e3-7f2cb022ae5f','product_name'=>'Sony DualSense PS5', 'quantity'=>1,'unit_price'=>420000, 'total_price'=>420000],
            ['id'=>'23856473-be3f-4701-9971-b53b55c8a9e0','order_id'=>'6f8295c7-7ee4-461f-84f9-1ac3f03cd935','product_id'=>'ede1feb2-4bff-401e-8950-836f6f01c4c4','product_name'=>'Samsung Galaxy S24 Ultra','quantity'=>1,'unit_price'=>7500000,'total_price'=>7500000],
            ['id'=>'9938da1a-4667-44a0-b1fd-b268baafc18e','order_id'=>'c243dc60-a69e-4ac7-9f24-45f0287bc336','product_id'=>'4cc7eabc-2a39-4f7e-a6f9-1d6426e9904a','product_name'=>'JBL Tune 520BT',     'quantity'=>2,'unit_price'=>320000, 'total_price'=>640000],
            ['id'=>'ea288850-71bf-4def-a787-85bc425e808b','order_id'=>'a8a4cd12-2e02-4af0-acde-6fafa60ca0ab','product_id'=>'77626ee3-684b-4ea4-b6e3-7f2cb022ae5f','product_name'=>'Sony DualSense PS5', 'quantity'=>1,'unit_price'=>420000, 'total_price'=>420000],
            ['id'=>'6752cd92-cdcf-4bd6-8f0f-f1dcbf96a577','order_id'=>'8978d971-0d0a-4b51-866e-225b92d8be28','product_id'=>'ddf04302-9a73-4337-97b1-fe4f2e2f08da','product_name'=>'Logitech MX Master 3S','quantity'=>1,'unit_price'=>650000, 'total_price'=>650000],
            ['id'=>'5f7813c3-321c-4646-b845-3923e0aaba4f','order_id'=>'8978d971-0d0a-4b51-866e-225b92d8be28','product_id'=>'1a7a33bd-2f47-41c3-a8cd-f2b192aa98d1','product_name'=>'Logitech G Pro X',   'quantity'=>1,'unit_price'=>450000, 'total_price'=>450000],
            ['id'=>'61697c1f-2900-4d26-bd37-b5e7df6a0b61','order_id'=>'cfd5cad8-b699-4f1a-8d49-b0c6d69dd542','product_id'=>'ddf04302-9a73-4337-97b1-fe4f2e2f08da','product_name'=>'Logitech MX Master 3S','quantity'=>1,'unit_price'=>650000, 'total_price'=>650000],
        ];

        foreach ($itemsRaw as $item) {
            OrderItem::withoutGlobalScopes()->updateOrCreate(['id' => $item['id']], $item);
        }

        // ====================================================
        // CAMPAIGNS
        // ====================================================
        $campaignsRaw = [
            ['id'=>'0d3f2876-8cbe-4ef6-9635-c48a9527a9be','name'=>'Black Friday 2026','tag'=>'blackfriday','description'=>'Descuentos especiales de Black Friday hasta 50% OFF','banner_image'=>'https://picsum.photos/seed/blackfriday/1200/250','start_date'=>'2026-11-20','end_date'=>'2026-11-30','is_active'=>false,'display_on_home'=>true,'display_order'=>1],
            ['id'=>'0a4cecc5-81ff-4002-8392-0a6afb23edd4','name'=>'Ofertas de Verano','tag'=>'verano2026','description'=>'Los mejores productos para disfrutar el verano','banner_image'=>'https://picsum.photos/seed/verano2026/1200/250','start_date'=>'2026-01-01','end_date'=>'2026-03-31','is_active'=>true,'display_on_home'=>true,'display_order'=>2],
        ];

        foreach ($campaignsRaw as $cam) {
            Campaign::withoutGlobalScopes()->updateOrCreate(['id' => $cam['id']], $cam);
        }

        // ====================================================
        // EMAIL TEMPLATES
        // ====================================================
        $templates = [
            ['template_key'=>'order_confirmation',   'name'=>'Confirmación de Pedido', 'subject'=>'Tu pedido #{{order_number}} ha sido confirmado',  'body_html'=>'<p>Hola {{customer_name}}, tu pedido {{order_number}} fue confirmado.</p>',  'variables'=>['customer_name','order_number','total','order_date'],    'is_active'=>true],
            ['template_key'=>'order_shipped',        'name'=>'Pedido Enviado',          'subject'=>'Tu pedido #{{order_number}} está en camino',         'body_html'=>'<p>Hola {{customer_name}}, tu pedido {{order_number}} fue enviado.</p>',         'variables'=>['customer_name','order_number','shipping_address'],      'is_active'=>true],
            ['template_key'=>'order_delivered',      'name'=>'Pedido Entregado',        'subject'=>'Tu pedido #{{order_number}} ha sido entregado',      'body_html'=>'<p>Hola {{customer_name}}, tu pedido {{order_number}} fue entregado.</p>',      'variables'=>['customer_name','order_number'],                         'is_active'=>true],
            ['template_key'=>'password_reset',       'name'=>'Restablecer Contraseña',  'subject'=>'Restablece tu contraseña',                           'body_html'=>'<p>Hola {{user_name}}, haz clic aquí: {{reset_link}}</p>',                      'variables'=>['user_name','reset_link'],                               'is_active'=>true],
            ['template_key'=>'welcome',              'name'=>'Bienvenida',              'subject'=>'¡Bienvenido a {{store_name}}!',                       'body_html'=>'<p>Bienvenido {{user_name}} a {{store_name}}.</p>',                              'variables'=>['user_name','store_name'],                               'is_active'=>true],
            ['template_key'=>'newsletter_confirmation','name'=>'Confirmación Newsletter','subject'=>'Confirma tu suscripción al newsletter',              'body_html'=>'<p>Confirma tu suscripción: {{confirmation_link}}</p>',                         'variables'=>['confirmation_link'],                                    'is_active'=>true],
        ];

        foreach ($templates as $t) {
            EmailTemplate::updateOrCreate(['template_key' => $t['template_key']], $t);
        }

        $this->command->info('✅ Base de datos poblada correctamente:');
        $this->command->info('   → ' . Product::count()  . ' productos');
        $this->command->info('   → ' . Category::count() . ' categorías');
        $this->command->info('   → ' . Brand::count()    . ' marcas');
        $this->command->info('   → ' . Order::count()    . ' pedidos');
    }
}
