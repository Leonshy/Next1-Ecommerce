@extends('layouts.admin')
@section('title', 'Pedido ' . $order->order_number)
@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Detalle principal --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Items --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-5 border-b border-gray-100"><h2 class="font-semibold">Productos</h2></div>
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <div class="flex items-center space-x-4 p-4">
                        @if($item->product_image)
                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-12 h-12 object-cover rounded-lg bg-gray-50">
                        @else
                            <div class="w-12 h-12 bg-gray-100 rounded-lg"></div>
                        @endif
                        <div class="flex-1">
                            <p class="font-medium text-sm">{{ $item->product_name }}</p>
                            <p class="text-xs text-gray-400">x{{ $item->quantity }} × Gs. {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-semibold text-sm">Gs. {{ number_format($item->total_price, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
            <div class="p-5 border-t border-gray-100 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>Gs. {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                @if($order->discount > 0)
                    <div class="flex justify-between text-green-600"><span>Descuento</span><span>-Gs. {{ number_format($order->discount, 0, ',', '.') }}</span></div>
                @endif
                <div class="flex justify-between"><span class="text-gray-500">Envío</span><span>Gs. {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                <div class="flex justify-between font-bold text-base border-t pt-2"><span>Total</span><span>Gs. {{ $order->formatted_total }}</span></div>
            </div>
        </div>

        {{-- Notas --}}
        @if($order->notes)
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="font-semibold mb-2">Notas</h2>
                <p class="text-sm text-gray-600">{{ $order->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">

        {{-- Estado --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold mb-3">Estado del Pedido</h3>
            <form method="POST" action="{{ route('admin.pedidos.status', $order->id) }}">
                @csrf @method('PATCH')
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach([
                        'pendiente'               => 'Pendiente',
                        'pendiente_transferencia' => 'Pend. Transferencia',
                        'confirmado'              => 'Confirmado',
                        'procesando'              => 'Procesando',
                        'enviado'                 => 'Enviado',
                        'entregado'               => 'Entregado',
                        'cancelado'               => 'Cancelado',
                    ] as $val => $label)
                        <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                @if($order->transfer_receipt)
                <div class="mb-3 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                    <p class="text-xs font-semibold text-orange-700 mb-1">Comprobante de transferencia:</p>
                    <a href="{{ Storage::url($order->transfer_receipt) }}" target="_blank"
                       class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Ver / Descargar comprobante
                    </a>
                </div>
                @endif

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
                    Actualizar Estado
                </button>
            </form>
        </div>

        {{-- Cliente --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold mb-3">Cliente</h3>
            <div class="space-y-1 text-sm text-gray-600">
                <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                <p>{{ $order->customer_email }}</p>
                @if($order->customer_phone)<p>{{ $order->customer_phone }}</p>@endif
                @if($order->shipping_address)
                    <hr class="my-2">
                    <p class="font-medium text-gray-900">Dirección de envío</p>
                    <p>{{ $order->shipping_address }}</p>
                    @if($order->shipping_city)<p>{{ $order->shipping_city }}</p>@endif
                @endif
            </div>
        </div>

        {{-- Factura --}}
        @if($order->invoice)
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold mb-2">Factura</h3>
                <p class="text-sm text-gray-600 mb-3">{{ $order->invoice->invoice_number }}</p>
            </div>
        @endif
    </div>
</div>

@endsection
