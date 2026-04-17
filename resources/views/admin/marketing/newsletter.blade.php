@extends('layouts.admin')
@section('title', 'Newsletter')

@section('content')

@if(session('success'))
    <div class="mb-5 rounded-xl border bg-green-50 border-green-200 p-4">
        <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
    </div>
@endif

{{-- Header --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Newsletter</h2>
        <p class="text-sm text-gray-500 mt-0.5">Suscriptores al boletín de la tienda</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.newsletter.export') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-green-600 text-white hover:bg-green-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Exportar verificados
        </a>
        <a href="{{ route('admin.newsletter.export') }}?all=1"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Exportar todos
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
        <p class="text-sm text-gray-500 mt-0.5">Total</p>
    </div>
    <div class="bg-white rounded-xl border border-green-100 p-4 shadow-sm">
        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['verificado']) }}</p>
        <p class="text-sm text-gray-500 mt-0.5">Verificados ✓ (exportables)</p>
    </div>
    <div class="bg-white rounded-xl border border-yellow-100 p-4 shadow-sm">
        <p class="text-2xl font-bold text-yellow-500">{{ number_format($stats['pendiente']) }}</p>
        <p class="text-sm text-gray-500 mt-0.5">Pendientes de verificación</p>
    </div>
</div>

{{-- Nota sobre el proceso --}}
<div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5 text-sm text-blue-700">
    <strong>¿Cómo funciona?</strong> Al suscribirse el usuario recibe un email de verificación válido por 30 días.
    Después de 72hs sin verificar, quedan excluidos de la exportación (pero siguen en la lista hasta que verifiquen o los elimines).
    <strong>El botón "Exportar verificados"</strong> genera el CSV listo para importar en Mailchimp o Brevo.
</div>

{{-- Filtros --}}
<form method="GET" class="flex flex-wrap gap-2 mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por email..."
           class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400/30 w-64">
    <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400/30">
        <option value="">Todos los estados</option>
        <option value="verificado" @selected(request('status') === 'verificado')>Verificados</option>
        <option value="pendiente"  @selected(request('status') === 'pendiente')>Pendientes</option>
    </select>
    <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">Filtrar</button>
    @if(request('q') || request('status'))
        <a href="{{ route('admin.newsletter.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
    @endif
</form>

{{-- Tabla --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Email</th>
                <th class="px-5 py-3 text-left">Estado</th>
                <th class="px-5 py-3 text-left">Suscripto el</th>
                <th class="px-5 py-3 text-left">Verificado el</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($subscribers as $sub)
                @php $expired = $sub->status === 'pendiente' && $sub->subscribed_at->lt(now()->subHours(72)); @endphp
                <tr class="hover:bg-gray-50 {{ $expired ? 'opacity-60' : '' }}">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $sub->email }}</td>
                    <td class="px-5 py-3">
                        @if($sub->status === 'verificado')
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Verificado ✓
                            </span>
                        @elseif($expired)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">
                                Pendiente · expirado
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                Pendiente · {{ $sub->subscribed_at->diffForHumans() }}
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $sub->subscribed_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $sub->verified_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td class="px-5 py-3 text-right">
                        <form method="POST" action="{{ route('admin.newsletter.destroy', $sub->id) }}"
                              onsubmit="return confirm('¿Eliminar este suscriptor?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">No hay suscriptores aún.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($subscribers->hasPages())
    <div class="mt-4">{{ $subscribers->links() }}</div>
@endif

@endsection
