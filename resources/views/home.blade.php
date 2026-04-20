<x-app-layout>

    {{-- ========= HERO + CATEGORY SIDEBAR ========= --}}
    <section class="py-4">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-12 gap-0">

                {{-- Category Sidebar (3/12 cols on lg) --}}
                <div class="hidden lg:block col-span-3 h-[200px] sm:h-[280px] md:h-[400px] relative z-20">
                    <div class="bg-white rounded-xl shadow-[var(--shadow-card)] h-full flex flex-col overflow-hidden">
                        {{-- Header --}}
                        <div class="bg-primary text-white p-4 flex items-center gap-3 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            <h3 class="font-bold text-sm uppercase">Categorías</h3>
                        </div>
                        {{-- List --}}
                        <div class="divide-y divide-border flex-1 overflow-y-auto">
                            @foreach($categories as $cat)
                                @if($cat->children_count > 0)
                                    {{-- Con subcategorías: flyout fixed (escapa del overflow) --}}
                                    <div x-data="{ open: false, top: 0, left: 0 }"
                                         @mouseenter="open = true; const r = $el.getBoundingClientRect(); top = r.top; left = r.right"
                                         @mouseleave="open = false">
                                        <a href="{{ route('products.index', ['categoria' => $cat->slug]) }}"
                                           class="w-full px-4 py-3 flex items-center justify-between hover:bg-muted/50 transition-colors text-sm text-foreground uppercase tracking-wide group block">
                                            <span>{{ $cat->name }}</span>
                                            <svg class="w-4 h-4 text-muted-foreground group-hover:text-primary flex-shrink-0"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                        {{-- Flyout con position:fixed para escapar del overflow --}}
                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 translate-x-1"
                                             x-transition:enter-end="opacity-100 translate-x-0"
                                             :style="`position:fixed; top:${top}px; left:${left}px; z-index:9999`"
                                             style="display:none"
                                             class="bg-white shadow-[var(--shadow-soft)] rounded-xl min-w-[220px] overflow-hidden border border-border/40">
                                            <div class="bg-primary/10 text-primary px-4 py-2.5 border-b border-border">
                                                <h4 class="font-semibold text-xs uppercase">{{ $cat->name }}</h4>
                                            </div>
                                            <a href="{{ route('products.index', ['categoria' => $cat->slug]) }}"
                                               class="block px-4 py-2.5 text-xs text-muted-foreground hover:bg-muted/50 hover:text-primary transition-colors uppercase tracking-wide border-b border-border/40">
                                                Ver todos
                                            </a>
                                            @foreach($cat->children as $sub)
                                                <a href="{{ route('products.index', ['categoria' => $sub->slug]) }}"
                                                   class="block px-4 py-2.5 text-sm text-foreground hover:bg-muted/50 hover:text-primary transition-colors uppercase tracking-wide border-b border-border/40">
                                                    {{ $sub->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    {{-- Sin subcategorías: link directo --}}
                                    <a href="{{ route('products.index', ['categoria' => $cat->slug]) }}"
                                       class="w-full px-4 py-3 flex items-center hover:bg-muted/50 transition-colors text-sm text-foreground uppercase tracking-wide block">
                                        {{ $cat->name }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Hero Carousel (9/12 cols on lg, full on mobile) --}}
                <div class="col-span-12 lg:col-span-9 h-[200px] sm:h-[280px] md:h-[400px]">
                    @if($heroSlides->count())
                        <div class="relative h-full w-full overflow-hidden"
                             x-data="{ active: 0, total: {{ $heroSlides->count() }}, timer: null }"
                             x-init="timer = setInterval(() => active = (active + 1) % total, 5000)"
                             @mouseenter="clearInterval(timer)"
                             @mouseleave="timer = setInterval(() => active = (active + 1) % total, 5000)">

                            @foreach($heroSlides as $i => $slide)
                                <div x-show="active === {{ $i }}"
                                     x-transition:enter="transition-opacity duration-700"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     class="absolute inset-0">
                                    <img src="{{ $slide->image_url }}"
                                         alt="{{ $slide->title }}"
                                         class="w-full h-full object-cover"
                                         onerror="this.src='https://placehold.co/1200x400/1a537a/ffffff?text=Next1+Store'">
                                    <div class="absolute inset-0 flex items-center"
                                         style="background:linear-gradient(to right,rgba(0,0,0,.5),transparent)">
                                        <div class="pl-4 sm:pl-8 md:pl-12 text-white">
                                            @if($slide->subtitle)
                                                <p class="text-xs sm:text-sm md:text-lg mb-1 sm:mb-2 opacity-90">{{ $slide->subtitle }}</p>
                                            @endif
                                            @if($slide->title)
                                                <h2 class="text-xl sm:text-3xl md:text-5xl font-black leading-tight mb-2 sm:mb-6">{{ $slide->title }}</h2>
                                            @endif
                                            @if($slide->button_text && $slide->button_link)
                                                <a href="{{ $slide->button_link }}"
                                                   class="inline-block bg-secondary text-white px-4 py-2 sm:px-8 sm:py-3 text-sm sm:text-base font-semibold hover:opacity-90 transition-opacity">
                                                    {{ $slide->button_text }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Prev / Next --}}
                            <button @click="active = (active - 1 + total) % total"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/30 hover:bg-black/50 text-white flex items-center justify-center transition-colors z-10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button @click="active = (active + 1) % total"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/30 hover:bg-black/50 text-white flex items-center justify-center transition-colors z-10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            {{-- Dots --}}
                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                                @foreach($heroSlides as $i => $slide)
                                    <button @click="active = {{ $i }}"
                                            :class="active === {{ $i }} ? 'bg-white w-6' : 'bg-white/50 w-2'"
                                            class="h-2 rounded-full transition-all duration-300"></button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="h-full bg-gradient-to-r from-primary to-primary-light flex items-center justify-center">
                            <div class="text-center text-white px-4">
                                <h2 class="text-2xl sm:text-4xl md:text-5xl font-black mb-2 sm:mb-4">Bienvenido a NEXT1</h2>
                                <p class="text-sm sm:text-xl mb-4 sm:mb-8 opacity-90">Tu tienda online de confianza en Paraguay</p>
                                <a href="{{ route('products.index') }}"
                                   class="inline-block bg-secondary text-white px-4 py-2 sm:px-8 sm:py-3 text-sm sm:text-base font-semibold hover:opacity-90 transition-opacity">
                                    Ver Productos
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

    {{-- ========= OFERTAS DEL DÍA + MÁS VENDIDOS (lado a lado) ========= --}}
    @if($hotDeals->count() || $bestSellers->count())
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-12 gap-6 items-start">

                {{-- ── OFERTAS DEL DÍA (9/12 cols) ── --}}
                @if($hotDeals->count())
                <div class="col-span-12 lg:col-span-9"
                     x-data="{ cur: 0, total: {{ $hotDeals->count() }}, perPage: 4 }"
                     x-init="perPage = window.innerWidth < 640 ? 2 : 4; window.addEventListener('resize', () => { perPage = window.innerWidth < 640 ? 2 : 4; cur = Math.min(cur, Math.max(0, total - perPage)); })">

                    {{-- Header con forma de flecha --}}
                    <div class="flex items-center justify-between mb-6 border-b-2 border-border">
                        <div class="flex items-center">
                            {{-- Título con flecha --}}
                            <h2 class="bg-destructive text-white font-bold text-sm uppercase px-6 py-3 relative">
                                OFERTAS DEL DÍA
                                <span class="absolute -right-3 top-0 h-full w-3 bg-destructive"
                                      style="clip-path: polygon(0 0, 100% 50%, 0 100%)"></span>
                            </h2>
                            {{-- Countdown --}}
                            <div class="flex items-center gap-2 bg-white px-4 py-2 border-b-2 border-transparent -mb-0.5"
                                 x-data="{
                                    h:'00', m:'00', s:'00',
                                    tick(){
                                        const now=new Date(), end=new Date();
                                        end.setHours(23,59,59,999);
                                        let d=Math.max(0,Math.floor((end-now)/1000));
                                        this.h=String(Math.floor(d/3600)).padStart(2,'0');
                                        this.m=String(Math.floor((d%3600)/60)).padStart(2,'0');
                                        this.s=String(d%60).padStart(2,'0');
                                    }
                                 }"
                                 x-init="tick(); setInterval(()=>tick(),1000)">
                                <svg class="w-4 h-4 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-500">Termina en:</span>
                                <div class="flex items-center gap-1 font-mono font-bold">
                                    <span class="bg-destructive text-white px-2 py-1 rounded text-sm" x-text="h"></span>
                                    <span class="text-destructive font-black">:</span>
                                    <span class="bg-destructive text-white px-2 py-1 rounded text-sm" x-text="m"></span>
                                    <span class="text-destructive font-black">:</span>
                                    <span class="bg-destructive text-white px-2 py-1 rounded text-sm" x-text="s"></span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('products.index', ['tag' => 'ofertas']) }}"
                           class="flex items-center gap-1 text-sm font-semibold text-primary hover:text-primary/80 hover:underline transition-colors pb-2">
                            Ver todos
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    {{-- Carrusel con botones circulares en fila --}}
                    <div class="flex items-center gap-2">
                        {{-- Prev --}}
                        <button @click="cur = Math.max(0, cur - 1)"
                                :disabled="cur === 0"
                                class="w-8 h-8 rounded-full border border-border bg-white flex items-center justify-center hover:bg-muted transition-colors flex-shrink-0 disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7-7-7 7 7 7"/></svg>
                        </button>

                        <div class="overflow-hidden flex-1">
                            <div class="flex transition-transform duration-300"
                                 :style="'transform:translateX(-' + (cur * 100 / perPage) + '%)'">
                                @foreach($hotDeals as $product)
                                    <div class="flex-shrink-0 pl-4" :style="'flex:0 0 '+(100/perPage)+'%;max-width:'+(100/perPage)+'%'">
                                        @include('partials.product-card', ['product' => $product])
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Next --}}
                        <button @click="cur = Math.min(total - perPage, cur + 1)"
                                :disabled="cur >= total - perPage"
                                class="w-8 h-8 rounded-full border border-border bg-white flex items-center justify-center hover:bg-muted transition-colors flex-shrink-0 disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7 7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
                @endif

                {{-- ── MÁS VENDIDOS (3/12 cols) ── --}}
                @if($bestSellers->count())
                <div class="col-span-12 lg:col-span-3">
                    <div class="bg-white rounded-xl shadow-[var(--shadow-card)] overflow-hidden"
                         x-data="{ page: 0, perPage: 4, total: {{ $bestSellers->count() }} }">

                        {{-- Header con flecha --}}
                        <div class="flex items-center justify-between border-b-2 border-border">
                            <h2 class="bg-primary text-white font-bold text-sm uppercase px-6 py-3 relative">
                                MÁS VENDIDOS
                                <span class="absolute -right-3 top-0 h-full w-3 bg-primary"
                                      style="clip-path: polygon(0 0, 100% 50%, 0 100%)"></span>
                            </h2>
                            <div class="flex items-center gap-1 pr-2">
                                <button @click="page = Math.max(0, page - 1)"
                                        :disabled="page === 0"
                                        class="p-1 hover:text-primary text-gray-400 hover:bg-muted rounded transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button @click="page = Math.min(Math.ceil(total / perPage) - 1, page + 1)"
                                        :disabled="page >= Math.ceil(total / perPage) - 1"
                                        class="p-1 hover:text-primary text-gray-400 hover:bg-muted rounded transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Lista con paginación Alpine --}}
                        <div class="divide-y divide-border">
                            @foreach($bestSellers as $i => $product)
                                @php
                                    $img       = $product->mainImage?->image_url;
                                    $fullStars = floor(min(5, $product->reviews_avg_rating ?? 4.5));
                                @endphp
                                <div x-show="{{ $i }} >= page * perPage && {{ $i }} < (page + 1) * perPage"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-data="{ qty: 1, added: false }"
                                     class="flex items-start gap-2 p-3 hover:bg-muted/30 transition-colors group">

                                    {{-- Rank global --}}
                                    <span class="text-2xl font-black text-gray-200 w-5 text-center flex-shrink-0 leading-none pt-1">
                                        {{ $i + 1 }}
                                    </span>

                                    {{-- Imagen --}}
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="w-14 h-14 flex-shrink-0 bg-white rounded overflow-hidden border border-border">
                                        <img src="{{ $img ?? 'https://placehold.co/56x56/e8f0fe/1a537a?text=P' }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
                                             onerror="this.src='https://placehold.co/56x56/e8f0fe/1a537a?text=P'">
                                    </a>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        {{-- Estrellas --}}
                                        <div class="flex items-center gap-0.5 mb-0.5">
                                            @for($s = 1; $s <= 5; $s++)
                                                <svg class="w-3 h-3 {{ $s <= $fullStars ? 'fill-yellow-400 text-yellow-400' : 'fill-gray-200 text-gray-200' }}"
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>

                                        {{-- Nombre --}}
                                        <a href="{{ route('products.show', $product->slug) }}"
                                           class="text-xs text-gray-800 line-clamp-2 leading-snug hover:text-primary transition-colors block">
                                            {{ $product->name }}
                                        </a>

                                        {{-- Precio --}}
                                        <p class="text-sm font-bold text-primary mt-0.5">
                                            Gs. {{ number_format($product->price, 0, ',', '.') }}
                                        </p>

                                        {{-- Controles: cantidad + agregar --}}
                                        <div class="flex items-center gap-1 mt-1.5">
                                            {{-- Selector cantidad --}}
                                            <div class="flex items-center border border-border rounded overflow-hidden">
                                                <button type="button"
                                                        @click="qty = Math.max(1, qty - 1)"
                                                        class="w-5 h-6 flex items-center justify-center text-gray-500 hover:bg-muted transition-colors text-xs font-bold">
                                                    −
                                                </button>
                                                <span class="w-6 h-6 flex items-center justify-center text-xs font-semibold border-x border-border"
                                                      x-text="qty"></span>
                                                <button type="button"
                                                        @click="qty = qty + 1"
                                                        class="w-5 h-6 flex items-center justify-center text-gray-500 hover:bg-muted transition-colors text-xs font-bold">
                                                    +
                                                </button>
                                            </div>

                                            {{-- Agregar al carrito --}}
                                            <button type="button"
                                                    @click="
                                                        window.dispatchEvent(new CustomEvent('cart:add', { detail: { productId: {{ $product->id }}, quantity: qty } }));
                                                        added = true;
                                                        setTimeout(() => added = false, 1500);
                                                    "
                                                    class="flex-1 flex items-center justify-center gap-1 h-6 rounded text-xs font-semibold transition-colors"
                                                    :class="added ? 'bg-green-500 text-white' : 'bg-primary text-white hover:bg-primary/90'">
                                                <svg x-show="!added" class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-9H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                                <svg x-show="added" class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span x-text="added ? '¡Listo!' : 'Agregar'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Indicador de página --}}
                        <div class="flex justify-center gap-1.5 py-2 border-t border-border">
                            @for($p = 0; $p < ceil($bestSellers->count() / 4); $p++)
                                <button @click="page = {{ $p }}"
                                        :class="page === {{ $p }} ? 'bg-primary w-4' : 'bg-gray-300 w-2'"
                                        class="h-2 rounded-full transition-all duration-300"></button>
                            @endfor
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </section>
    @endif

    {{-- ========= ANUNCIOS / PROMO BANNERS ========= --}}
    <section class="py-4">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @if($promoBanners->count())
                    @foreach($promoBanners as $banner)
                        @php
                            $bg = $banner->background_gradient ?? '';
                            if (str_starts_with($bg, '{') || str_starts_with($bg, '[')) {
                                $bgData = json_decode($bg, true) ?? [];
                                if (isset($bgData['type']) && $bgData['type'] === 'image' && isset($bgData['url'])) {
                                    $bgStyle = "background-image:url('" . $bgData['url'] . "');background-size:cover;background-position:center";
                                } elseif (isset($bgData['color'])) {
                                    $bgStyle = "background-color:" . $bgData['color'];
                                } elseif (isset($bgData['gradient'])) {
                                    $bgStyle = "background:" . $bgData['gradient'];
                                } else {
                                    $bgStyle = "background:" . $bg;
                                }
                            } else {
                                $bgStyle = "background:" . $bg;
                            }
                        @endphp
                        <a href="{{ $banner->button_link }}"
                           class="promo-banner relative h-32 overflow-hidden group cursor-pointer block rounded-xl shadow-[var(--shadow-card)] hover:shadow-[var(--shadow-soft)] transition-shadow duration-300"
                           style="{{ $bgStyle }}">
                            <div class="absolute inset-0 p-4 flex flex-col justify-center z-10"
                                 style="color:{{ $banner->text_color ?? 'white' }}">
                                <h3 class="font-bold text-lg uppercase leading-tight">{{ $banner->title }}</h3>
                                @if($banner->subtitle)
                                    <p class="text-xs opacity-80 mt-1 uppercase">{{ $banner->subtitle }}</p>
                                @endif
                                @if($banner->button_text)
                                    <span class="mt-3 flex items-center gap-1 text-xs font-semibold border border-white/50 px-3 py-1.5 w-fit hover:bg-white transition-colors"
                                          style="color:{{ $banner->button_text_color ?? 'white' }}">
                                        {{ $banner->button_text }}
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </span>
                                @endif
                            </div>
                            <div class="absolute right-0 top-0 h-full w-1/2 opacity-20">
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 w-20 h-20 border-2 border-white/30 rounded-full"></div>
                            </div>
                        </a>
                    @endforeach
                @else
                    @foreach([
                        ['title'=>'VENTA LAPTOP', 'sub'=>'LA MEJOR TECNOLOGÍA', 'color'=>'bg-cyan-500'],
                        ['title'=>'MEJOR DIGITAL', 'sub'=>'EXPERIENCIA INCREÍBLE', 'color'=>'bg-green-500'],
                        ['title'=>'VENTA SONY', 'sub'=>'SONIDO DE CALIDAD', 'color'=>'bg-orange-500'],
                        ['title'=>'ELECTRODOMÉSTICOS', 'sub'=>'PARA TU HOGAR', 'color'=>'bg-purple-500'],
                    ] as $b)
                        <a href="{{ route('products.index') }}"
                           class="{{ $b['color'] }} promo-banner block">
                            <div class="absolute inset-0 p-4 flex flex-col justify-center text-white z-10">
                                <h3 class="font-bold text-lg uppercase leading-tight">{{ $b['title'] }}</h3>
                                <p class="text-xs opacity-80 mt-1 uppercase">{{ $b['sub'] }}</p>
                                <span class="mt-3 flex items-center gap-1 text-xs font-semibold border border-white/50 px-3 py-1.5 w-fit hover:bg-white hover:text-foreground transition-colors">
                                    COMPRAR
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            </div>
                            <div class="absolute right-0 top-0 h-full w-1/2 opacity-20">
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 w-20 h-20 border-2 border-white/30 rounded-full"></div>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    {{-- ========= CAMPAÑAS ========= --}}
    @foreach($campaigns as $campaign)
    @php
        $campaignProducts = \App\Models\Product::active()
            ->when($campaign->tag, fn($q) => $q->whereJsonContains('tags', $campaign->tag))
            ->with(['category', 'brand'])
            ->limit(8)
            ->get();
    @endphp
    <section class="py-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-12 gap-6 items-stretch"
                 x-data="{ cur: 0, total: {{ $campaignProducts->count() }}, perPage: 4 }"
                 x-init="perPage = window.innerWidth < 640 ? 2 : 4; window.addEventListener('resize', () => { perPage = window.innerWidth < 640 ? 2 : 4; cur = Math.min(cur, Math.max(0, total - perPage)); })">

                {{-- Banner campaña (3 cols) --}}
                <div class="col-span-12 lg:col-span-3 flex flex-col">
                    <a href="{{ route('products.index', ['tag' => $campaign->tag]) }}"
                       class="relative flex flex-col flex-1 rounded-xl overflow-hidden group min-h-[180px] sm:min-h-[260px] md:min-h-[320px]"
                       style="background: linear-gradient(135deg, #1a3a5c 0%, #0f2035 100%)"
                        @if($campaign->banner_image)
                            <img src="{{ $campaign->banner_image }}"
                                 alt="{{ $campaign->name }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 onerror="this.remove()">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                        @endif
                        <div class="relative z-10 flex flex-col justify-end flex-1 p-5">
                            <h3 class="text-white font-black text-xl leading-tight">{{ $campaign->name }}</h3>
                            @if($campaign->description)
                                <p class="text-white/80 text-sm mt-2 leading-snug">{{ $campaign->description }}</p>
                            @endif
                            <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-white border border-white/40 px-4 py-2 rounded-lg hover:bg-white hover:text-gray-900 transition-colors w-fit">
                                Ver todos
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </div>
                    </a>
                </div>

                {{-- Productos de la campaña (9 cols) --}}
                <div class="col-span-12 lg:col-span-9">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-6 border-b-2 border-border">
                        <div class="flex items-center">
                            <h2 class="bg-destructive text-white font-bold text-sm uppercase px-6 py-3 relative">
                                {{ strtoupper($campaign->name) }}
                                <span class="absolute -right-3 top-0 h-full w-3 bg-destructive"
                                      style="clip-path: polygon(0 0, 100% 50%, 0 100%)"></span>
                            </h2>
                        </div>
                        <a href="{{ route('products.index', ['tag' => $campaign->tag]) }}"
                           class="flex items-center gap-1 text-sm font-semibold text-primary hover:text-primary/80 hover:underline transition-colors pb-2">
                            Ver todos
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    @if($campaignProducts->count())
                    {{-- Carrusel --}}
                    <div class="flex items-center gap-2">
                        <button @click="cur = Math.max(0, cur - 1)"
                                :disabled="cur === 0"
                                class="w-8 h-8 rounded-full border border-border bg-white flex items-center justify-center hover:bg-muted transition-colors flex-shrink-0 disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7-7-7 7 7 7"/></svg>
                        </button>

                        <div class="overflow-hidden flex-1">
                            <div class="flex transition-transform duration-300"
                                 :style="'transform:translateX(-' + (cur * 100 / perPage) + '%)'">
                                @foreach($campaignProducts as $product)
                                    <div class="flex-shrink-0 pl-4" :style="'flex:0 0 '+(100/perPage)+'%;max-width:'+(100/perPage)+'%'">
                                        @include('partials.product-card', ['product' => $product])
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button @click="cur = Math.min(total - perPage, cur + 1)"
                                :disabled="cur >= total - perPage"
                                class="w-8 h-8 rounded-full border border-border bg-white flex items-center justify-center hover:bg-muted transition-colors flex-shrink-0 disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7 7 7-7 7"/></svg>
                        </button>
                    </div>
                    @else
                    <p class="text-sm text-gray-400 py-8 text-center">No hay productos asociados a esta campaña.</p>
                    @endif
                </div>

            </div>
        </div>
    </section>
    @endforeach

    {{-- ========= MARCAS + DESTACADOS (lado a lado) ========= --}}
    @if($featured->count())
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-12 gap-6 items-start">

                {{-- ── MARCAS (3/12 cols) ── --}}
                <div class="col-span-12 lg:col-span-3">
                    <div class="bg-white rounded-xl shadow-[var(--shadow-card)] overflow-hidden">

                        {{-- Header --}}
                        <div class="border-b border-border">
                            <h2 class="bg-primary text-white font-bold text-sm uppercase px-6 py-3 relative inline-block">
                                MARCAS
                                <span class="absolute -right-3 top-0 h-full w-3 bg-primary"
                                      style="clip-path: polygon(0 0, 100% 50%, 0 100%)"></span>
                            </h2>
                        </div>

                        {{-- Lista de marcas --}}
                        <div class="divide-y divide-border overflow-y-auto" style="max-height:360px">
                            @forelse($brands as $brand)
                                <a href="{{ route('products.index', ['marca' => $brand->slug]) }}"
                                   class="block px-4 py-2.5 hover:bg-muted/40 hover:text-primary transition-colors text-sm text-foreground">
                                    {{ $brand->name }}
                                </a>
                            @empty
                                @foreach(['Apple','Samsung','Sony','Logitech','Xiaomi','Philips','LG','Huawei','JBL','Bose'] as $b)
                                    <a href="{{ route('products.index') }}"
                                       class="block px-4 py-2.5 hover:bg-muted/40 hover:text-primary transition-colors text-sm text-foreground">
                                        {{ $b }}
                                    </a>
                                @endforeach
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ── DESTACADOS (9/12 cols) ── --}}
                <div class="col-span-12 lg:col-span-9"
                     x-data="{ cur: 0, total: {{ $featured->count() }}, perPage: 4 }"
                     x-init="perPage = window.innerWidth < 640 ? 2 : 4; window.addEventListener('resize', () => { perPage = window.innerWidth < 640 ? 2 : 4; cur = Math.min(cur, Math.max(0, total - perPage)); })">

                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-6 border-b-2 border-border">
                        <div class="flex items-center">
                            <h2 class="bg-primary text-white font-bold text-sm uppercase px-6 py-3 relative">
                                PRODUCTOS DESTACADOS
                                <span class="absolute -right-3 top-0 h-full w-3 bg-primary"
                                      style="clip-path: polygon(0 0, 100% 50%, 0 100%)"></span>
                            </h2>
                        </div>
                        <a href="{{ route('products.index') }}"
                           class="flex items-center gap-1 text-sm font-semibold text-primary hover:text-primary/80 hover:underline transition-colors pb-2">
                            Ver todos
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    {{-- Carrusel --}}
                    <div class="flex items-center gap-2">
                        <button @click="cur = Math.max(0, cur - 1)"
                                :disabled="cur === 0"
                                class="w-8 h-8 rounded-full border border-border bg-white flex items-center justify-center hover:bg-muted transition-colors flex-shrink-0 disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7-7-7 7 7 7"/></svg>
                        </button>

                        <div class="overflow-hidden flex-1">
                            <div class="flex transition-transform duration-300"
                                 :style="'transform:translateX(-' + (cur * 100 / perPage) + '%)'">
                                @foreach($featured as $product)
                                    <div class="flex-shrink-0 pl-4" :style="'flex:0 0 '+(100/perPage)+'%;max-width:'+(100/perPage)+'%'">
                                        @include('partials.product-card', ['product' => $product])
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button @click="cur = Math.min(total - perPage, cur + 1)"
                                :disabled="cur >= total - perPage"
                                class="w-8 h-8 rounded-full border border-border bg-white flex items-center justify-center hover:bg-muted transition-colors flex-shrink-0 disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7 7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>
    @endif

    {{-- ========= NUEVOS PRODUCTOS ========= --}}
    @if($newProducts->count())
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-6 border-b-2 border-border">
                <div class="flex items-center">
                    <h2 class="bg-primary text-white font-bold text-sm uppercase px-6 py-3 relative">
                        PRODUCTOS NUEVOS
                        <span class="absolute -right-3 top-0 h-full w-3 bg-primary"
                              style="clip-path: polygon(0 0, 100% 50%, 0 100%)"></span>
                    </h2>
                </div>
                <a href="{{ route('products.index') }}"
                   class="flex items-center gap-1 text-sm font-semibold text-primary hover:text-primary/80 hover:underline transition-colors pb-2">
                    Ver todos
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($newProducts as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>
    @endif


</x-app-layout>
