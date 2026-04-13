<div x-data="{ open: false }"
     @cart:add.window="open = true; $wire.addItem($event.detail.productId, $event.detail.quantity ?? 1)"
     @cart:open.window="open = true"
     @cart:toggle.window="open = !open">

    {{-- Overlay --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-black/50 z-40"
         style="display:none"></div>

    {{-- Drawer --}}
    <div class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white z-50 shadow-2xl flex flex-col"
         style="transform:translateX(100%); transition: transform 0.3s ease-in-out;"
         :style="open ? 'transform:translateX(0)' : 'transform:translateX(100%)'">

        {{-- Header --}}
        <div class="flex items-center justify-between p-4 border-b">
            <h2 class="text-lg font-semibold">Carrito ({{ $this->totalItems() }})</h2>
            <button type="button" @click="open = false"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Items --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            @forelse($items as $key => $item)
                <div class="flex items-center gap-3">
                    @if($item['image'])
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                             class="w-16 h-16 object-cover rounded-lg bg-gray-100 shrink-0">
                    @else
                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs shrink-0">Sin imagen</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</p>
                        <p class="text-sm text-blue-600 font-semibold">Gs. {{ number_format($item['price'], 0, ',', '.') }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                                    class="w-6 h-6 bg-gray-100 rounded hover:bg-gray-200 text-sm font-bold flex items-center justify-center">−</button>
                            <span class="text-sm w-6 text-center">{{ $item['quantity'] }}</span>
                            <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                                    class="w-6 h-6 bg-gray-100 rounded hover:bg-gray-200 text-sm font-bold flex items-center justify-center">+</button>
                        </div>
                    </div>
                    <button wire:click="removeItem('{{ $key }}')" class="text-gray-400 hover:text-red-500 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 3h2l.4 2M7 13h10l4-9H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-sm">Tu carrito está vacío</p>
                    <a href="{{ route('products.index') }}" @click="open = false"
                       class="mt-3 inline-block text-blue-600 text-sm font-medium hover:underline">Ver productos</a>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if(count($items) > 0)
        <div class="border-t p-4 space-y-3">
            <div class="flex justify-between items-center text-base font-semibold">
                <span>Subtotal</span>
                <span>Gs. {{ number_format($this->subtotal(), 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('checkout.index') }}" @click="open = false"
               class="block w-full bg-blue-600 text-white text-center py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                Ir al Checkout
            </a>
            <button wire:click="clearCart"
                    class="w-full text-sm text-gray-500 hover:text-red-500 transition-colors">
                Vaciar carrito
            </button>
        </div>
        @endif
    </div>
</div>
