<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route('account.orders') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Pedido {{ $order->order_number }}</h1>
        </div>

        {{-- Estado --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Estado actual</p>
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-semibold bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Fecha</p>
                    <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            {{-- Timeline de estados --}}
            <div class="mt-5 flex items-center justify-between relative">
                <div class="absolute top-3 left-0 right-0 h-0.5 bg-gray-200 z-0"></div>
                @php
                    $estados = ['pendiente', 'confirmado', 'procesando', 'enviado', 'entregado'];
                    $currentIdx = array_search($order->status, $estados);
                @endphp
                @foreach($estados as $i => $estado)
                    @php $done = $currentIdx !== false && $i <= $currentIdx; @endphp
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center text-xs
                            {{ $done ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-gray-300 text-gray-400' }}">
                            {{ $done ? '✓' : ($i + 1) }}
                        </div>
                        <span class="text-xs mt-1 {{ $done ? 'text-blue-600 font-medium' : 'text-gray-400' }}">
                            {{ ucfirst($estado) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Productos --}}
        <div class="bg-white rounded-xl border border-gray-200 mb-4">
            <div class="p-5 border-b border-gray-100">
                <h2 class="font-semibold">Productos</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <div class="flex items-center space-x-4 p-4">
                        @if($item->product_image)
                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}"
                                 class="w-14 h-14 object-cover rounded-lg bg-gray-50">
                        @else
                            <div class="w-14 h-14 bg-gray-100 rounded-lg"></div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-900">{{ $item->product_name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $item->quantity }} × Gs. {{ number_format($item->unit_price, 0, ',', '.') }}
                            </p>
                        </div>
                        <p class="font-semibold text-sm">Gs. {{ number_format($item->total_price, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
            <div class="p-5 border-t border-gray-100 space-y-2 text-sm">
                <div class="flex justify-between text-gray-500">
                    <span>Subtotal</span>
                    <span>Gs. {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($order->discount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Descuento</span>
                        <span>-Gs. {{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-gray-500">
                    <span>Envío</span>
                    <span>{{ $order->shipping_cost > 0 ? 'Gs. ' . number_format($order->shipping_cost, 0, ',', '.') : 'Gratis' }}</span>
                </div>
                <div class="flex justify-between font-bold text-base border-t pt-2">
                    <span>Total</span>
                    <span>Gs. {{ $order->formatted_total }}</span>
                </div>
            </div>
        </div>

        {{-- Dirección --}}
        @if($order->shipping_address)
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
                <h2 class="font-semibold mb-2">Dirección de envío</h2>
                <p class="text-sm text-gray-600">{{ $order->shipping_address }}</p>
                @if($order->shipping_city)
                    <p class="text-sm text-gray-600">{{ $order->shipping_city }}</p>
                @endif
            </div>
        @endif

        {{-- Factura --}}
        @if($order->invoice)
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold">Factura</h2>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $order->invoice->invoice_number }}</p>
                    </div>
                    <button onclick="window.print()"
                            class="flex items-center space-x-2 text-sm text-blue-600 hover:underline font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Descargar PDF</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
