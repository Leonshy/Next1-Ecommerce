<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-6">

        {{-- ===== SIDEBAR FILTROS ===== --}}
        <aside class="w-full md:w-64 flex-shrink-0">
            <form method="GET" action="{{ route('products.index') }}" id="filter-form">

                {{-- Buscar --}}
                <div class="mb-5 bg-white rounded-xl shadow-[var(--shadow-card)] p-4">
                    <h3 class="font-bold text-sm uppercase text-foreground mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Buscar
                    </h3>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Nombre, SKU..."
                           class="input-field">
                </div>

                {{-- Categorías --}}
                <div class="mb-5 bg-white rounded-xl shadow-[var(--shadow-card)] overflow-hidden">
                    <div class="bg-primary text-white px-4 py-3 font-bold text-xs uppercase flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Categorías
                    </div>
                    <div class="p-4 space-y-2 max-h-52 overflow-y-auto">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="categoria" value=""
                                   {{ !request('categoria') ? 'checked' : '' }}
                                   class="text-primary accent-primary">
                            <span class="text-sm text-foreground group-hover:text-primary transition-colors">Todas</span>
                        </label>
                        @foreach($categories as $cat)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="categoria" value="{{ $cat->slug }}"
                                       {{ request('categoria') === $cat->slug ? 'checked' : '' }}
                                       class="text-primary accent-primary">
                                <span class="text-sm text-foreground group-hover:text-primary transition-colors">{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Marcas --}}
                <div class="mb-5 bg-white rounded-xl shadow-[var(--shadow-card)] overflow-hidden">
                    <div class="bg-primary text-white px-4 py-3 font-bold text-xs uppercase flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Marcas
                    </div>
                    <div class="p-4 space-y-2 max-h-44 overflow-y-auto">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="marca" value=""
                                   {{ !request('marca') ? 'checked' : '' }}
                                   class="text-primary accent-primary">
                            <span class="text-sm text-foreground group-hover:text-primary transition-colors">Todas</span>
                        </label>
                        @foreach($brands as $brand)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="marca" value="{{ $brand->slug }}"
                                       {{ request('marca') === $brand->slug ? 'checked' : '' }}
                                       class="text-primary accent-primary">
                                <span class="text-sm text-foreground group-hover:text-primary transition-colors">{{ $brand->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Precio --}}
                <div class="mb-5 bg-white rounded-xl shadow-[var(--shadow-card)] overflow-hidden">
                    <div class="bg-primary text-white px-4 py-3 font-bold text-xs uppercase flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Precio (₲)
                    </div>
                    <div class="p-4 flex gap-2">
                        <input type="number" name="precio_min" value="{{ request('precio_min') }}"
                               placeholder="Mín" class="input-field w-1/2">
                        <input type="number" name="precio_max" value="{{ request('precio_max') }}"
                               placeholder="Máx" class="input-field w-1/2">
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-primary text-white py-2.5 text-sm font-semibold uppercase hover:opacity-90 transition-opacity">
                    Aplicar Filtros
                </button>
                <a href="{{ route('products.index') }}"
                   class="block text-center text-sm text-muted-foreground mt-2 hover:text-primary transition-colors">
                    Limpiar filtros
                </a>
            </form>
        </aside>

        {{-- ===== PRODUCTOS ===== --}}
        <div class="flex-1 min-w-0">

            {{-- Barra superior --}}
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-border">
                <p class="text-sm text-muted-foreground">
                    <span class="font-semibold text-foreground">{{ $products->total() }}</span>
                    producto{{ $products->total() !== 1 ? 's' : '' }} encontrado{{ $products->total() !== 1 ? 's' : '' }}
                </p>
                <select name="orden" onchange="document.getElementById('filter-form').submit()" form="filter-form"
                        class="border border-border px-3 py-1.5 text-sm text-foreground bg-white focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <option value="relevancia" {{ request('orden') === 'relevancia' ? 'selected' : '' }}>Más relevantes</option>
                    <option value="precio_asc" {{ request('orden') === 'precio_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                    <option value="precio_desc" {{ request('orden') === 'precio_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                    <option value="nuevo" {{ request('orden') === 'nuevo' ? 'selected' : '' }}>Más nuevos</option>
                </select>
            </div>

            @if($products->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-20 text-muted-foreground border border-border bg-white">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-lg font-semibold text-foreground">No encontramos productos</p>
                    <p class="text-sm mt-1">Intentá con otros filtros</p>
                    <a href="{{ route('products.index') }}"
                       class="inline-block mt-4 bg-primary text-white px-6 py-2 text-sm font-semibold hover:opacity-90 transition-opacity">
                        Ver todos los productos
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
</x-app-layout>
