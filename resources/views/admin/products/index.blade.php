@extends('layouts.admin')
@section('title', 'Productos')
@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div class="mb-5 rounded-xl border bg-green-50 border-green-200 p-4">
        <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
    </div>
@endif
@if(session('error'))
    <div class="mb-5 rounded-xl border bg-red-50 border-red-200 p-4">
        <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
    </div>
@endif

{{-- Import result banner --}}
@if(session('import_result'))
    @php $result = session('import_result'); @endphp
    <div class="mb-5 rounded-xl border p-4 {{ count($result['errors']) ? 'bg-yellow-50 border-yellow-200' : 'bg-green-50 border-green-200' }}">
        <p class="text-sm font-medium {{ count($result['errors']) ? 'text-yellow-800' : 'text-green-700' }}">
            {{ $result['success'] }} producto(s) importado(s) correctamente.
        </p>
        @if(count($result['errors']))
            <ul class="mt-2 space-y-0.5 text-xs text-red-600 list-disc list-inside">
                @foreach($result['errors'] as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        @endif
    </div>
@endif

{{-- Page header --}}
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Productos</h2>
    <p class="text-gray-500 text-sm mt-0.5">Gestioná el catálogo de productos, categorías y marcas</p>
</div>

{{-- ── Tabs ───────────────────────────────────────────────────────────────── --}}
<div x-data="{ tab: '{{ request('tab', 'productos') }}' }">

    <div class="flex gap-1 border-b border-gray-200 mb-6">
        @foreach(['productos' => 'Productos', 'categorias' => 'Categorías', 'marcas' => 'Marcas', 'etiquetas' => 'Etiquetas'] as $key => $label)
            <button type="button" @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'border-[#1a4a6b] text-[#1a4a6b]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         TAB: PRODUCTOS
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'productos'">

        {{-- CSV Format Guide --}}
        <div x-data="{ open: false }" class="mb-5 bg-blue-50 border border-blue-200 rounded-xl">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-blue-800">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Guía de formato CSV para importación
                </div>
                <svg class="w-4 h-4 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-transition class="px-4 pb-4 text-sm text-blue-900">
                <p class="mb-3 text-blue-700">El archivo debe ser <strong>.csv</strong> con codificación <strong>UTF-8</strong>. La primera fila debe contener los encabezados. Podés usar la <strong>Plantilla</strong> del botón Exportar como punto de partida.</p>
                <div class="overflow-x-auto rounded-lg border border-blue-200">
                    <table class="w-full text-xs">
                        <thead class="bg-blue-100 text-blue-800">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Columna</th>
                                <th class="px-3 py-2 text-left font-semibold">Obligatorio</th>
                                <th class="px-3 py-2 text-left font-semibold">Descripción</th>
                                <th class="px-3 py-2 text-left font-semibold">Ejemplo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-blue-100 bg-white">
                            @foreach([
                                ['nombre','Sí','Nombre del producto','Sony WH-1000XM5'],
                                ['precio','Sí','Precio de venta (número, sin puntos de miles)','1500000'],
                                ['imagen','No','URL de la imagen principal (biblioteca interna o externa)','https://…/foto.jpg'],
                                ['slug','No','URL amigable. Se genera automáticamente si se omite','sony-wh-1000xm5'],
                                ['descripcion','No','Descripción larga del producto','Auriculares inalámbricos...'],
                                ['precio_original','No','Precio tachado (antes de descuento)','1800000'],
                                ['sku','No','Código interno del producto','SNY-WH1000XM5'],
                                ['stock','No','Cantidad en stock (número entero, por defecto 0)','25'],
                                ['categoria','No','Nombre exacto de la categoría (debe existir en el sistema)','Audio'],
                                ['marca','No','Nombre exacto de la marca (debe existir en el sistema)','Sony'],
                                ['badge','No','Etiqueta visual sobre la imagen (texto libre corto)','NUEVO'],
                                ['etiquetas','No','Tags separados por coma','audio, inalámbrico, premium'],
                                ['activo','No','Visible en la tienda. Por defecto true','true / false'],
                                ['destacado','No','Aparece en sección Destacados','true / false'],
                                ['nuevo','No','Marcado como producto nuevo','true / false'],
                                ['oferta','No','Aparece en sección Ofertas','true / false'],
                            ] as [$col, $req, $desc, $ej])
                                <tr class="even:bg-blue-50/40">
                                    <td class="px-3 py-2 font-mono font-semibold {{ $req === 'Sí' ? 'text-gray-900' : 'text-gray-600' }}">{{ $col }}</td>
                                    <td class="px-3 py-2">
                                        <span class="px-1.5 py-0.5 rounded text-xs {{ $req === 'Sí' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">{{ $req }}</span>
                                    </td>
                                    <td class="px-3 py-2">{{ $desc }}</td>
                                    <td class="px-3 py-2 font-mono text-gray-500 break-all">{{ $ej }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-blue-700 text-xs">Los campos booleanos aceptan: <code class="bg-blue-100 px-1 rounded">true</code>, <code class="bg-blue-100 px-1 rounded">1</code>, <code class="bg-blue-100 px-1 rounded">si</code> para activar; <code class="bg-blue-100 px-1 rounded">false</code>, <code class="bg-blue-100 px-1 rounded">0</code>, <code class="bg-blue-100 px-1 rounded">no</code> para desactivar.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <form method="GET" class="flex gap-2">
                <input type="hidden" name="tab" value="productos">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar productos..."
                       class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b] bg-white">
                <button type="submit" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Buscar</button>
            </form>

            <div class="flex items-center gap-2" x-data="{ exportOpen: false }">
                {{-- Importar --}}
                <button type="button" onclick="document.getElementById('csv-file-input').click()"
                        class="flex items-center gap-1.5 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Importar CSV
                </button>
                <form id="import-form" method="POST" action="{{ route('admin.productos.import') }}" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input id="csv-file-input" type="file" name="file" accept=".csv,text/csv" onchange="document.getElementById('import-form').submit()">
                </form>

                {{-- Exportar (dropdown) --}}
                <div class="relative">
                    <button @click="exportOpen = !exportOpen" @click.outside="exportOpen = false"
                            class="flex items-center gap-1.5 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                        Exportar
                        <svg class="w-3.5 h-3.5" :class="exportOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="exportOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-1 w-52 bg-white border border-gray-200 rounded-xl shadow-lg z-20 py-1">
                        <a href="{{ route('admin.productos.export') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Exportar CSV
                        </a>
                        <a href="{{ route('admin.productos.template') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Descargar Plantilla
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.productos.create') }}" class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-white" style="background:#1a4a6b">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nuevo Producto
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-3 text-left">Producto</th>
                        <th class="px-5 py-3 text-left">Precio</th>
                        <th class="px-5 py-3 text-left">Stock</th>
                        <th class="px-5 py-3 text-left">Estado</th>
                        <th class="px-5 py-3 text-left">Categoría</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    @if($img = ($product->mainImage?->image_url ?? ($product->images[0] ?? null)))
                                        <img src="{{ $img }}" alt="{{ $product->name }}" class="w-11 h-11 object-cover rounded-lg bg-gray-100 shrink-0">
                                    @else
                                        <div class="w-11 h-11 bg-gray-100 rounded-lg shrink-0 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                        @if($product->sku)
                                            <p class="text-xs text-gray-400">SKU: {{ $product->sku }}</p>
                                        @endif
                                        @if(!empty($product->tags))
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach(array_slice($product->tags, 0, 3) as $tag)
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full border border-gray-200 text-gray-500">#{{ $tag }}</span>
                                                @endforeach
                                                @if(count($product->tags) > 3)
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full border border-gray-200 text-gray-500">+{{ count($product->tags) - 3 }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-semibold text-gray-900">Gs. {{ $product->formatted_price }}</p>
                                @if($product->formatted_original_price)
                                    <p class="text-xs text-gray-400 line-through">Gs. {{ $product->formatted_original_price }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="font-medium {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }}">{{ $product->stock }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500 text-sm">{{ $product->category?->name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.productos.edit', $product->id) }}" class="text-xs font-medium hover:underline" style="color:#1a4a6b">Editar</a>
                                    <form method="POST" action="{{ route('admin.productos.destroy', $product->id) }}" onsubmit="return confirm('¿Eliminar este producto?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-500 hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                                No hay productos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-100">{{ $products->links() }}</div>
        </div>
    </div>{{-- /tab productos --}}


    {{-- ══════════════════════════════════════════════════════════════════════
         TAB: CATEGORÍAS
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'categorias'" x-data="{ editId: null, editName: '' }">
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Formulario nueva categoría --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Nueva categoría</h3>
                <form method="POST" action="{{ route('admin.categorias.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre *</label>
                        <input type="text" name="name" required placeholder="Ej: Electrónica"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Categoría padre</label>
                        <select name="parent_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b] bg-white">
                            <option value="">Sin padre (categoría raíz)</option>
                            @foreach($categories->whereNull('parent_id') as $root)
                                <option value="{{ $root->id }}">{{ $root->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2 rounded-lg text-sm font-semibold text-white transition-colors" style="background:#1a4a6b">
                        Agregar categoría
                    </button>
                </form>
            </div>

            {{-- Lista de categorías --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Categorías <span class="text-sm font-normal text-gray-400">({{ $categories->count() }})</span></h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($categories->sortBy(fn($c) => ($c->parent?->name ?? '') . $c->name) as $cat)
                        <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors">
                            <div>
                                <span class="text-sm font-medium text-gray-900">
                                    @if($cat->parent)
                                        <span class="text-gray-400 font-normal">{{ $cat->parent->name }} › </span>
                                    @endif
                                    {{ $cat->name }}
                                </span>
                                @php $count = $cat->products()->count(); @endphp
                                @if($count > 0)
                                    <span class="ml-2 text-xs text-gray-400">{{ $count }} producto(s)</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2" x-data="{ open: false }">
                                {{-- Edit inline --}}
                                <button @click="open = true; $nextTick(() => $refs.editInput{{ $cat->id }}.focus())"
                                        class="text-xs font-medium hover:underline" style="color:#1a4a6b">Editar</button>
                                <form method="POST" action="{{ route('admin.categorias.destroy', $cat->id) }}"
                                      onsubmit="return confirm('¿Eliminar {{ addslashes($cat->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-500 hover:underline">Eliminar</button>
                                </form>

                                {{-- Edit modal --}}
                                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @click.self="open = false">
                                    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop>
                                        <h4 class="font-semibold text-gray-900 mb-4">Editar categoría</h4>
                                        <form method="POST" action="{{ route('admin.categorias.update', $cat->id) }}">
                                            @csrf @method('PATCH')
                                            <input x-ref="editInput{{ $cat->id }}" type="text" name="name" value="{{ $cat->name }}" required
                                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                                            <div class="flex gap-2">
                                                <button type="button" @click="open = false" class="flex-1 py-2 border border-gray-200 rounded-lg text-sm text-gray-600">Cancelar</button>
                                                <button type="submit" class="flex-1 py-2 rounded-lg text-sm font-semibold text-white" style="background:#1a4a6b">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-gray-400 text-sm">No hay categorías</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>{{-- /tab categorias --}}


    {{-- ══════════════════════════════════════════════════════════════════════
         TAB: MARCAS
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'marcas'">
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Formulario nueva marca --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Nueva marca</h3>
                <form method="POST" action="{{ route('admin.marcas.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre *</label>
                        <input type="text" name="name" required placeholder="Ej: Samsung"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    </div>
                    <button type="submit" class="w-full py-2 rounded-lg text-sm font-semibold text-white transition-colors" style="background:#1a4a6b">
                        Agregar marca
                    </button>
                </form>
            </div>

            {{-- Lista de marcas --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Marcas <span class="text-sm font-normal text-gray-400">({{ $brands->count() }})</span></h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($brands as $brand)
                        <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors">
                            <div>
                                <span class="text-sm font-medium text-gray-900">{{ $brand->name }}</span>
                                @if($brand->products_count > 0)
                                    <span class="ml-2 text-xs text-gray-400">{{ $brand->products_count }} producto(s)</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2" x-data="{ open: false }">
                                <button @click="open = true; $nextTick(() => $refs.brandInput{{ $brand->id }}.focus())"
                                        class="text-xs font-medium hover:underline" style="color:#1a4a6b">Editar</button>
                                <form method="POST" action="{{ route('admin.marcas.destroy', $brand->id) }}"
                                      onsubmit="return confirm('¿Eliminar {{ addslashes($brand->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-500 hover:underline">Eliminar</button>
                                </form>

                                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @click.self="open = false">
                                    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop>
                                        <h4 class="font-semibold text-gray-900 mb-4">Editar marca</h4>
                                        <form method="POST" action="{{ route('admin.marcas.update', $brand->id) }}">
                                            @csrf @method('PATCH')
                                            <input x-ref="brandInput{{ $brand->id }}" type="text" name="name" value="{{ $brand->name }}" required
                                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                                            <div class="flex gap-2">
                                                <button type="button" @click="open = false" class="flex-1 py-2 border border-gray-200 rounded-lg text-sm text-gray-600">Cancelar</button>
                                                <button type="submit" class="flex-1 py-2 rounded-lg text-sm font-semibold text-white" style="background:#1a4a6b">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-gray-400 text-sm">No hay marcas</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>{{-- /tab marcas --}}


    {{-- ══════════════════════════════════════════════════════════════════════
         TAB: ETIQUETAS
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'etiquetas'">
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Formulario nueva etiqueta --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Nueva etiqueta</h3>
                <form method="POST" action="{{ route('admin.etiquetas.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre *</label>
                        <input type="text" name="name" required placeholder="Ej: Inalámbrico"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    </div>
                    <button type="submit" class="w-full py-2 rounded-lg text-sm font-semibold text-white transition-colors" style="background:#1a4a6b">
                        Agregar etiqueta
                    </button>
                </form>
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400">Las etiquetas también se pueden crear directamente desde el formulario de cada producto.</p>
                </div>
            </div>

            {{-- Lista de etiquetas --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Etiquetas <span class="text-sm font-normal text-gray-400">({{ $tags->count() }})</span></h3>
                </div>

                @if($tags->isEmpty())
                    <p class="px-5 py-8 text-center text-gray-400 text-sm">No hay etiquetas. Agregá la primera.</p>
                @else
                    <div class="p-4 flex flex-wrap gap-2">
                        @php $protectedSlugs = ['destacado', 'nuevo', 'ofertas']; @endphp
                        @foreach($tags as $tag)
                            @php
                                $usageCount  = \App\Models\Product::whereJsonContains('tags', $tag->slug)->count();
                                $isProtected = in_array($tag->slug, $protectedSlugs);
                            @endphp
                            <div class="flex items-center gap-1.5 pl-3 pr-1.5 py-1.5 rounded-full border group"
                                 style="{{ $isProtected ? 'border-color:rgba(224,123,29,0.35); background:rgba(224,123,29,0.08)' : 'border-color:#e5e7eb; background:#f9fafb' }}"
                                 x-data="{ open: false }">
                                <span class="text-sm font-medium" style="{{ $isProtected ? 'color:#e07b1d' : 'color:#374151' }}">{{ $tag->name }}</span>
                                <span class="text-xs font-mono" style="{{ $isProtected ? 'color:rgba(224,123,29,0.6)' : 'color:#9ca3af' }}">#{{ $tag->slug }}</span>
                                @if($isProtected)
                                    <span title="Etiqueta permanente" style="color:rgba(224,123,29,0.5)">
                                        <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </span>
                                @endif
                                @if($usageCount > 0)
                                    <span class="text-xs text-gray-400 bg-gray-200 px-1.5 py-0.5 rounded-full">{{ $usageCount }}</span>
                                @endif

                                <button @click="open = true; $nextTick(() => $refs.tagInput{{ $tag->id }}.focus())"
                                        class="w-5 h-5 rounded-full flex items-center justify-center text-gray-400 hover:text-[#1a4a6b] hover:bg-[#1a4a6b]/10 transition-colors opacity-0 group-hover:opacity-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                @if(!$isProtected)
                                    <form method="POST" action="{{ route('admin.etiquetas.destroy', $tag->id) }}"
                                          onsubmit="return confirm('¿Eliminar la etiqueta {{ addslashes($tag->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-5 h-5 rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors opacity-0 group-hover:opacity-100">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                @endif

                                {{-- Edit modal --}}
                                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @click.self="open = false">
                                    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop>
                                        <h4 class="font-semibold text-gray-900 mb-4">Editar etiqueta</h4>
                                        <form method="POST" action="{{ route('admin.etiquetas.update', $tag->id) }}">
                                            @csrf @method('PATCH')
                                            <input x-ref="tagInput{{ $tag->id }}" type="text" name="name" value="{{ $tag->name }}" required
                                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                                            <div class="flex gap-2">
                                                <button type="button" @click="open = false" class="flex-1 py-2 border border-gray-200 rounded-lg text-sm text-gray-600">Cancelar</button>
                                                <button type="submit" class="flex-1 py-2 rounded-lg text-sm font-semibold text-white" style="background:#1a4a6b">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>{{-- /tab etiquetas --}}

</div>{{-- /x-data tabs --}}

@endsection
