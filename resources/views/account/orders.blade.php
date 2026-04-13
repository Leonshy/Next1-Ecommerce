<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route('account.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Mis Pedidos</h1>
        </div>

        @if($orders->count())
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-sm transition-shadow">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                            <div>
                                <span class="font-semibold text-gray-900">{{ $order->order_number }}</span>
                                <span class="ml-3 text-xs text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                                    {{ $order->status_label }}
                                </span>
                                <span class="font-bold text-gray-900">Gs. {{ $order->formatted_total }}</span>
                            </div>
                        </div>
                        <div class="px-5 py-3 flex items-center justify-between">
                            <p class="text-sm text-gray-500">
                                {{ $order->items_count ?? $order->items->count() }} producto(s)
                            </p>
                            <a href="{{ route('account.order.show', $order->order_number) }}"
                               class="text-sm text-blue-600 font-medium hover:underline">
                                Ver detalle →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $orders->links() }}</div>
        @else
            <div class="text-center py-16 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-lg font-medium">No tenés pedidos aún</p>
                <a href="{{ route('products.index') }}" class="mt-3 inline-block text-blue-600 font-medium hover:underline">
                    Empezar a comprar
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
