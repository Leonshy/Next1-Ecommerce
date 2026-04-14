<header class="sticky top-0 z-50">

    {{-- TOP BAR - Primary color --}}
    <div class="bg-primary text-white text-xs py-2">
        <div class="container mx-auto px-4 flex items-center justify-between">
            <div class="hidden md:flex items-center gap-4">
                @php
                    $storeInfo = \App\Models\SiteContent::getByKey('store_info');
                    $info = $storeInfo?->metadata ?? [];
                @endphp
                @if(!empty($info['phone']))
                    <a href="tel:{{ preg_replace('/\s+/', '', $info['phone']) }}" class="topbar-link">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $info['phone'] }}
                    </a>
                @endif
                @if(!empty($info['email']))
                    <a href="mailto:{{ $info['email'] }}" class="topbar-link">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $info['email'] }}
                    </a>
                @endif
            </div>
            <div class="hidden md:flex items-center gap-4 ml-auto">
                @auth
                    <a href="{{ route('account.wishlist') }}" class="topbar-link">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Lista de Deseos
                    </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- MAIN HEADER - White --}}
    <div class="py-3 bg-white border-b border-border shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex items-center gap-4">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex-shrink-0">
                    @if(!empty($info['logoUrl']))
                        <img src="{{ $info['logoUrl'] }}" alt="{{ $info['storeName'] ?? config('app.name') }}"
                             class="h-10 w-auto object-contain">
                    @else
                        <div class="flex items-center gap-1">
                            <div class="w-9 h-9 bg-primary rounded flex items-center justify-center">
                                <span class="text-white font-black text-sm">N1</span>
                            </div>
                            <span class="text-primary font-black text-xl tracking-tight hidden sm:block">NEXT1</span>
                        </div>
                    @endif
                </a>

                {{-- Search --}}
                <div class="flex-1 flex justify-center">
                    <div class="flex w-full max-w-3xl h-10">
                        {{-- Category dropdown (solo desktop) --}}
                        <div class="hidden md:flex items-stretch flex-shrink-0" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                    class="flex items-center gap-1 rounded-l-full border border-r-0 border-border bg-muted px-4 text-sm text-foreground hover:bg-muted/80 transition-colors whitespace-nowrap">
                                Categorías
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>
                        {{-- Predictive Search (ocupa el resto, incluyendo botón de lupa) --}}
                        <div class="flex-1 min-w-0">
                            @livewire('predictive-search')
                        </div>
                    </div>
                </div>

                {{-- Account / Login --}}
                @guest
                    <a href="{{ route('login') }}"
                       class="flex items-center gap-2 text-primary hover:text-primary/60 transition-colors font-semibold text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="hidden md:inline">INICIAR SESIÓN</span>
                    </a>
                @else
                    @if(auth()->user()->isAdmin() || auth()->user()->isVendedor())
                        <a href="{{ route('admin.home') }}"
                           class="flex items-center gap-2 text-primary hover:text-primary/60 transition-colors font-semibold text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            <span class="hidden md:inline">IR A PANEL</span>
                        </a>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                    class="flex items-center gap-2 text-primary hover:text-primary/60 transition-colors font-semibold text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="hidden md:inline">Hola, {{ explode(' ', auth()->user()->name)[0] }}</span>
                                <svg class="w-3 h-3 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-[var(--shadow-soft)] py-1 z-50">
                                <a href="{{ route('account.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-foreground hover:bg-muted/50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Mi Perfil
                                </a>
                                <a href="{{ route('account.orders') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-foreground hover:bg-muted/50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    Mis Pedidos
                                </a>
                                <a href="{{ route('account.wishlist') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-foreground hover:bg-muted/50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    Lista de Deseos
                                </a>
                                <a href="{{ route('account.addresses') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-foreground hover:bg-muted/50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Direcciones
                                </a>
                                <hr class="my-1 border-border">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-destructive hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endguest

                {{-- Cart Button - Accent color, pill shape --}}
                <button onclick="window.dispatchEvent(new CustomEvent('cart:toggle'))"
                        class="relative flex items-center gap-2 bg-accent text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-accent-hover transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-9H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="hidden md:inline">MI CARRITO</span>
                    {{-- Badge --}}
                    @php
                        if (auth()->check()) {
                            $dbCart = \App\Models\Cart::where('user_id', auth()->id())->first();
                            $cartCount = array_sum(array_column($dbCart?->items ?? [], 'quantity'));
                        } else {
                            $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
                        }
                    @endphp
                    <span x-data="{ count: {{ $cartCount }} }"
                          x-on:cart:updated.window="count = $event.detail.count"
                          x-show="count > 0"
                          x-text="count > 99 ? '99+' : count"
                          class="absolute -top-2 -right-2 bg-destructive text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                    </span>
                </button>

            </div>
        </div>
    </div>

</header>
