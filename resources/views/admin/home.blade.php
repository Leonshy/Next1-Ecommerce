@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

{{-- Page header --}}
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Bienvenido, {{ auth()->user()->name }}</h2>
    <p class="text-gray-500 text-sm mt-0.5">Resumen del panel de administración de Next1</p>
</div>

{{-- Stats grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-lg flex items-center justify-center" style="background:hsl(207 60% 28% / 0.1)">
                <svg class="w-5 h-5" style="color:#1a4a6b" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">Activos</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
        <p class="text-sm text-gray-500 mt-0.5">Usuarios Totales</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-lg flex items-center justify-center bg-green-50">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Catálogo</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_products']) }}</p>
        <p class="text-sm text-gray-500 mt-0.5">Productos Activos</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-lg flex items-center justify-center bg-orange-50">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">Este mes</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['monthly_orders']) }}</p>
        <p class="text-sm text-gray-500 mt-0.5">Pedidos del Mes</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-lg flex items-center justify-center" style="background:hsl(28 80% 52% / 0.1)">
                <svg class="w-5 h-5" style="color:#e07b1d" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium px-2 py-1 rounded-full" style="color:#e07b1d;background:hsl(28 80% 52% / 0.1)">Este mes</span>
        </div>
        <p class="text-xl font-bold text-gray-900">Gs. {{ number_format($stats['monthly_revenue'], 0, ',', '.') }}</p>
        <p class="text-sm text-gray-500 mt-0.5">Ventas del Mes</p>
    </div>
</div>

{{-- Bottom row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Pedidos recientes --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <h3 class="font-semibold text-gray-900">Pedidos Recientes</h3>
                <p class="text-xs text-gray-400 mt-0.5">Últimos pedidos realizados</p>
            </div>
            <a href="{{ route('admin.pedidos.index') }}"
               class="flex items-center gap-1 text-sm font-medium hover:underline" style="color:#1a4a6b">
                Ver todos
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-3 text-left">Pedido</th>
                        <th class="px-5 py-3 text-left">Cliente</th>
                        <th class="px-5 py-3 text-left">Total</th>
                        <th class="px-5 py-3 text-left">Estado</th>
                        <th class="px-5 py-3 text-left">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.pedidos.show', $order->id) }}"
                                   class="font-medium hover:underline" style="color:#1a4a6b">{{ $order->order_number }}</a>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $order->customer_name }}</td>
                            <td class="px-5 py-3 font-semibold text-gray-900">Gs. {{ $order->formatted_total }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-400 text-xs">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-gray-400">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                No hay pedidos aún
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Acciones rápidas --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Acciones Rápidas</h3>
            <p class="text-xs text-gray-400 mt-0.5">Tareas frecuentes</p>
        </div>
        <div class="p-4 space-y-2">
            <a href="{{ route('admin.productos.create') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 text-sm text-gray-700 hover:border-[#1a4a6b] hover:text-[#1a4a6b] transition-colors group">
                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#1a4a6b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
                Agregar Producto
            </a>
            @can('admin')
            <a href="{{ route('admin.usuarios.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 text-sm text-gray-700 hover:border-[#1a4a6b] hover:text-[#1a4a6b] transition-colors group">
                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#1a4a6b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Gestionar Usuarios
            </a>
            <a href="{{ route('admin.media.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 text-sm text-gray-700 hover:border-[#1a4a6b] hover:text-[#1a4a6b] transition-colors group">
                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#1a4a6b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Biblioteca de Medios
            </a>
            @endcan
            <a href="{{ route('admin.pedidos.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 text-sm text-gray-700 hover:border-[#1a4a6b] hover:text-[#1a4a6b] transition-colors group">
                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#1a4a6b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Ver Pedidos
            </a>
        </div>
    </div>
</div>

@endsection
