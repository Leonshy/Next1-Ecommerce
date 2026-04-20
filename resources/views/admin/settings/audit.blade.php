@extends('layouts.admin')
@section('title', 'Log de Auditoría')

@section('content')
<div class="max-w-7xl">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Log de Auditoría</h1>
            <p class="text-sm text-gray-500 mt-0.5">Acciones realizadas por administradores en el panel.</p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="admin" value="{{ request('admin') }}"
               placeholder="Email del admin..."
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/20 focus:border-[#1a4a6b]">
        <select name="action" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/20">
            <option value="">Todas las acciones</option>
            <option value="POST"   @selected(request('action') === 'POST')>POST (Crear)</option>
            <option value="PUT"    @selected(request('action') === 'PUT')>PUT (Actualizar)</option>
            <option value="PATCH"  @selected(request('action') === 'PATCH')>PATCH (Actualizar)</option>
            <option value="DELETE" @selected(request('action') === 'DELETE')>DELETE (Eliminar)</option>
        </select>
        <input type="text" name="resource" value="{{ request('resource') }}"
               placeholder="Recurso (Product, Order...)"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/20 focus:border-[#1a4a6b]">
        <button type="submit" class="bg-[#1a4a6b] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#1a537a]">
            Filtrar
        </button>
        @if(request()->hasAny(['admin', 'action', 'resource']))
            <a href="{{ route('admin.settings.audit.index') }}" class="text-sm text-gray-500 hover:text-gray-700 self-center">
                Limpiar filtros
            </a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Fecha</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Admin</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Acción</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Descripción</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">IP</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Detalle</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors" x-data="{ open: false }">
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap text-xs">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium text-gray-700">{{ $log->admin_email ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $colors = ['POST' => 'bg-green-100 text-green-700', 'PUT' => 'bg-blue-100 text-blue-700', 'PATCH' => 'bg-yellow-100 text-yellow-700', 'DELETE' => 'bg-red-100 text-red-700'];
                            @endphp
                            <span class="inline-block text-xs font-bold px-2 py-0.5 rounded {{ $colors[$log->action] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 max-w-xs truncate">
                            {{ $log->description ?? $log->url }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->ip_address }}</td>
                        <td class="px-4 py-3">
                            @if($log->payload)
                                <button @click="open = !open"
                                        class="text-xs text-[#1a4a6b] hover:underline">
                                    <span x-text="open ? 'Ocultar' : 'Ver payload'"></span>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @if($log->payload)
                        <tr x-show="open" style="display:none" class="bg-gray-50">
                            <td colspan="6" class="px-4 pb-3">
                                <pre class="text-xs text-gray-600 bg-white border border-gray-200 rounded p-3 overflow-x-auto">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">
                            No hay registros de auditoría todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($logs->hasPages())
        <div class="mt-4">{{ $logs->links() }}</div>
    @endif

</div>
@endsection
