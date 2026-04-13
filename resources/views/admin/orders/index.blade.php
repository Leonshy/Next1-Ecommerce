@extends('layouts.admin')
@section('title', 'Pedidos')
@section('content')

<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">
        <form method="GET" class="flex gap-2 flex-1">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar pedido, cliente..."
                   class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                <option value="">Todos los estados</option>
                @foreach(['pendiente','confirmado','procesando','enviado','entregado','cancelado'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">Buscar</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-5 py-3 text-left">Pedido</th>
                    <th class="px-5 py-3 text-left">Cliente</th>
                    <th class="px-5 py-3 text-left">Total</th>
                    <th class="px-5 py-3 text-left">Estado</th>
                    <th class="px-5 py-3 text-left">Fecha</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium">{{ $order->order_number }}</td>
                        <td class="px-5 py-3">
                            <p class="text-gray-900">{{ $order->customer_name }}</p>
                            <p class="text-xs text-gray-400">{{ $order->customer_email }}</p>
                        </td>
                        <td class="px-5 py-3 font-semibold">Gs. {{ $order->formatted_total }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.pedidos.show', $order->id) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No hay pedidos</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $orders->links() }}</div>
</div>

@endsection
