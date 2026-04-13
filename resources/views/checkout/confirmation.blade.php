<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">¡Pedido recibido!</h1>
            <p class="text-gray-500 mt-2">Tu pedido <span class="font-semibold text-gray-900">{{ $order->order_number }}</span> fue registrado correctamente.</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4 mb-6">
            <h2 class="font-semibold text-gray-900">Detalle del pedido</h2>
            @foreach($order->items as $item)
                <div class="flex items-center space-x-3 py-2 border-b border-gray-50">
                    @if($item->product_image)
                        <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-12 h-12 object-cover rounded-lg bg-gray-50">
                    @endif
                    <div class="flex-1">
                        <p class="text-sm font-medium">{{ $item->product_name }}</p>
                        <p class="text-xs text-gray-400">x{{ $item->quantity }}</p>
                    </div>
                    <p class="text-sm font-semibold">Gs. {{ number_format($item->total_price, 0, ',', '.') }}</p>
                </div>
            @endforeach

            <div class="space-y-1 text-sm pt-2">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>Gs. {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                @if($order->discount > 0)
                    <div class="flex justify-between text-green-600"><span>Descuento</span><span>-Gs. {{ number_format($order->discount, 0, ',', '.') }}</span></div>
                @endif
                <div class="flex justify-between"><span class="text-gray-500">Envío</span><span>Gs. {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                <div class="flex justify-between font-bold text-base border-t pt-2">
                    <span>Total</span><span>Gs. {{ $order->formatted_total }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('home') }}" class="flex-1 text-center border border-gray-200 text-gray-700 py-3 rounded-xl font-medium hover:bg-gray-50">
                Seguir comprando
            </a>
            @auth
                <a href="{{ route('account.orders') }}" class="flex-1 text-center bg-blue-600 text-white py-3 rounded-xl font-medium hover:bg-blue-700">
                    Ver mis pedidos
                </a>
            @endauth
        </div>
    </div>
</x-app-layout>
