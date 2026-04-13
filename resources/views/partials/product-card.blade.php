@php
    $img = $product->mainImage?->image_url ?? ($product->images[0] ?? null);
    $discountPct = $product->discount_percent ?? 0;
@endphp

<div class="bg-card border border-border hover:shadow-soft transition-all duration-300 overflow-hidden group relative">

    {{-- Badge top-left: prioridad Oferta > Destacado > Nuevo > Personalizado --}}
    <div class="absolute top-3 left-3 z-10">
        @if($product->is_hot_deal || $product->original_price)
            <span class="badge-oferta">OFERTA</span>
        @elseif($product->is_featured)
            <span class="badge-destacado">DESTACADO</span>
        @elseif($product->is_new)
            <span class="badge-nuevo">NUEVO</span>
        @elseif($product->badge)
            <span class="badge-custom">{{ $product->badge }}</span>
        @endif
    </div>

    {{-- Descuento % top-right --}}
    @if($discountPct > 0)
        <div class="absolute top-3 right-3 z-10">
            <span class="badge-discount">-{{ $discountPct }}%</span>
        </div>
    @endif

    {{-- Imagen --}}
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <div class="relative aspect-square overflow-hidden bg-background p-4">
            <img src="{{ $img ?? 'https://placehold.co/400x400/e8f0fe/1a537a?text='.urlencode(mb_substr($product->name, 0, 12)) }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500"
                 onerror="this.src='https://placehold.co/400x400/e8f0fe/1a537a?text=Producto'">

            {{-- Hover actions --}}
            <div class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-1 p-2
                        translate-y-full group-hover:translate-y-0 transition-transform duration-300"
                 style="background:linear-gradient(to top,rgba(0,0,0,.2),transparent)">

                @auth
                    <button @click.prevent="$dispatch('wishlist:toggle', { productId: '{{ $product->id }}' })"
                            title="Lista de deseos"
                            class="w-8 h-8 bg-white text-foreground hover:bg-primary hover:text-white transition-colors flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </button>
                @endauth

                <a href="{{ route('products.show', $product->slug) }}"
                   title="Ver detalle"
                   class="w-8 h-8 bg-white text-foreground hover:bg-primary hover:text-white transition-colors flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
            </div>
        </div>
    </a>

    {{-- Content --}}
    <div class="p-4 space-y-2">

        {{-- Stars --}}
        <div class="flex items-center gap-0.5">
            @for($i = 1; $i <= 5; $i++)
                @if($i <= (int)($product->rating ?? 0))
                    <svg class="w-3 h-3 fill-yellow-400 text-yellow-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                @else
                    <svg class="w-3 h-3 text-muted fill-none stroke-current" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                @endif
            @endfor
        </div>

        {{-- Nombre --}}
        <a href="{{ route('products.show', $product->slug) }}">
            <h3 class="text-sm text-foreground line-clamp-2 min-h-[2.5rem] hover:text-primary transition-colors cursor-pointer leading-snug">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Precio --}}
        <div class="flex flex-col min-h-[52px] justify-end">
            @if($product->original_price)
                <p class="text-sm text-muted-foreground line-through">₲ {{ number_format($product->original_price, 0, ',', '.') }}</p>
            @else
                <p class="text-sm invisible select-none">-</p>
            @endif
            <p class="text-lg font-bold text-primary">₲ {{ number_format($product->price, 0, ',', '.') }}</p>
        </div>

        {{-- Quantity selector --}}
        <div x-data="{ qty: 1 }" class="flex items-center justify-center gap-2 py-1">
            <button @click="qty = Math.max(1, qty - 1)"
                    class="w-7 h-7 border border-border flex items-center justify-center hover:border-primary hover:text-primary transition-colors text-foreground">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
            </button>
            <span x-text="qty" class="text-sm font-medium w-8 text-center"></span>
            <button @click="qty++"
                    class="w-7 h-7 border border-border flex items-center justify-center hover:border-primary hover:text-primary transition-colors text-foreground">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
        </div>

        {{-- Agregar al carrito --}}
        <div x-data="{ qty: 1 }">
            <button @click="$dispatch('cart:add', { productId: '{{ $product->id }}', quantity: qty })"
                    class="w-full bg-accent text-white text-xs uppercase font-semibold px-3 py-2.5
                           flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-9H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="truncate">Agregar al Carrito</span>
            </button>
        </div>
    </div>
</div>
