<x-app-layout>
<div class="container mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Inicio</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-primary transition-colors">Productos</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('products.index', ['categoria' => $product->category->slug]) }}"
               class="hover:text-primary transition-colors">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span class="text-foreground font-medium">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        {{-- ===== GALERÍA ===== --}}
        @php
            $mainImg = $product->mainImage?->image_url ?? ($product->productImages->first()?->image_url ?? null);
            $fallbackImg = 'https://placehold.co/600x600/e8f0fe/1a537a?text='.urlencode(mb_substr($product->name,0,15));
        @endphp
        <div x-data="{ activeImg: '{{ $mainImg ?? $fallbackImg }}' }">
            <div class="aspect-square bg-background border border-border overflow-hidden mb-4 p-4">
                <img :src="activeImg"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-contain"
                     x-on:error="$event.target.src='{{ $fallbackImg }}'">
            </div>
            @if($product->productImages->count() > 1)
                <div class="flex gap-2 overflow-x-auto pb-2">
                    @foreach($product->productImages as $img)
                        <button @click="activeImg = '{{ $img->image_url }}'"
                                class="flex-shrink-0 w-16 h-16 overflow-hidden border-2 transition-colors"
                                :class="activeImg === '{{ $img->image_url }}' ? 'border-primary' : 'border-border'">
                            <img src="{{ $img->image_url }}" alt="" class="w-full h-full object-contain p-1">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== INFO ===== --}}
        <div>
            @if($product->brand)
                <p class="text-sm text-muted-foreground mb-1 uppercase tracking-wide">{{ $product->brand->name }}</p>
            @endif

            <h1 class="text-2xl font-bold text-foreground mb-3 leading-tight">{{ $product->name }}</h1>

            {{-- Rating --}}
            @if($product->rating > 0)
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($product->rating))
                                <svg class="w-4 h-4 fill-yellow-400 text-yellow-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-muted fill-none stroke-current" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            @endif
                        @endfor
                    </div>
                    <span class="text-sm text-muted-foreground">{{ number_format($product->rating, 1) }} ({{ $product->reviews_count }} reseñas)</span>
                </div>
            @endif

            {{-- Precio --}}
            <div class="mb-6 border-t border-b border-border py-4">
                @if($product->original_price)
                    <p class="text-sm text-muted-foreground line-through">₲ {{ number_format($product->original_price, 0, ',', '.') }}</p>
                @endif
                <div class="flex items-center gap-3">
                    <span class="text-3xl font-black text-primary">₲ {{ number_format($product->price, 0, ',', '.') }}</span>
                    @if($product->discount_percent)
                        <span class="bg-destructive text-white text-sm px-2 py-0.5 font-bold">-{{ $product->discount_percent }}%</span>
                    @endif
                </div>
            </div>

            {{-- Stock / SKU --}}
            <div class="mb-4 space-y-1">
                <p class="text-sm font-medium {{ $product->stock > 0 ? 'text-green-600' : 'text-destructive' }} flex items-center gap-1">
                    @if($product->stock > 0)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        En stock ({{ $product->stock }} disponibles)
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Sin stock
                    @endif
                </p>
                @if($product->sku)
                    <p class="text-xs text-muted-foreground">SKU: <span class="font-mono">{{ $product->sku }}</span></p>
                @endif
            </div>

            {{-- Botones --}}
            @if($product->stock > 0)
                <div x-data="{ qty: 1 }" class="space-y-3 mb-6">
                    {{-- Quantity selector --}}
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-muted-foreground">Cantidad:</span>
                        <div class="flex items-center border border-border">
                            <button @click="qty = Math.max(1, qty - 1)"
                                    class="w-9 h-9 flex items-center justify-center hover:bg-muted transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <span x-text="qty" class="w-12 text-center text-sm font-semibold border-x border-border py-2"></span>
                            <button @click="qty++"
                                    class="w-9 h-9 flex items-center justify-center hover:bg-muted transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button @click="$dispatch('cart:add', { productId: '{{ $product->id }}', quantity: qty })"
                                class="flex-1 bg-accent text-white py-3 font-semibold uppercase text-sm flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-9H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Agregar al Carrito
                        </button>
                        @livewire('wishlist-button', ['productId' => $product->id])
                    </div>
                </div>
            @endif

            {{-- Descripción corta --}}
            @if($product->description)
                <div class="prose prose-sm max-w-none text-muted-foreground border-t border-border pt-5">
                    {!! $product->description !!}
                </div>
            @endif
        </div>
    </div>

    {{-- ===== PRODUCTOS RELACIONADOS ===== --}}
    @if($related->count())
        <section class="mt-14 pt-8 border-t border-border">
            <div class="section-title mb-6">
                <div class="flex items-center">
                    <h2 class="section-title-bar bg-primary">Productos Relacionados</h2>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($related as $relatedProduct)
                    @include('partials.product-card', ['product' => $relatedProduct])
                @endforeach
            </div>
        </section>
    @endif

</div>
</x-app-layout>
