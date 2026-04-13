@extends('layouts.admin')
@section('title', 'Gift Cards')

@section('content')

@if(session('success'))
    <div class="mb-5 rounded-xl border bg-green-50 border-green-200 p-4">
        <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
    </div>
@endif
@if($errors->any())
    <div class="mb-5 rounded-xl border bg-red-50 border-red-200 p-4">
        <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- Header --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Gift Cards</h2>
        <p class="text-sm text-gray-500 mt-0.5">Tarjetas de regalo generadas en la tienda</p>
    </div>
    <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
            style="background:#1a4a6b">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva gift card
    </button>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Total',    'value' => $stats['total'],    'color' => 'text-gray-900'],
        ['label' => 'Activas',  'value' => $stats['activa'],   'color' => 'text-green-600'],
        ['label' => 'Usadas',   'value' => $stats['usada'],    'color' => 'text-blue-600'],
        ['label' => 'Expiradas','value' => $stats['expirada'], 'color' => 'text-red-500'],
    ] as $stat)
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <p class="text-2xl font-bold {{ $stat['color'] }}">{{ number_format($stat['value']) }}</p>
        <p class="text-sm text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Filtros --}}
<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Código o email..."
           class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b] w-64">
    <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30">
        <option value="">Todos los estados</option>
        @foreach(['pendiente','activa','usada','expirada','cancelada'] as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">Filtrar</button>
    @if(request('q') || request('status'))
        <a href="{{ route('admin.gift-cards.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
    @endif
</form>

{{-- Tabla --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Código</th>
                <th class="px-5 py-3 text-left">Monto / Saldo</th>
                <th class="px-5 py-3 text-left">Estado</th>
                <th class="px-5 py-3 text-left">Comprador</th>
                <th class="px-5 py-3 text-left">Destinatario</th>
                <th class="px-5 py-3 text-left">Expira</th>
                <th class="px-5 py-3 text-left">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($giftCards as $gc)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <code class="text-xs font-mono bg-gray-100 px-2 py-1 rounded font-bold tracking-wider">{{ $gc->code }}</code>
                    </td>
                    <td class="px-5 py-3">
                        <p class="font-semibold text-gray-900">Gs. {{ number_format($gc->amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400">Saldo: Gs. {{ number_format($gc->balance, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-5 py-3">
                        @php
                            $colors = [
                                'pendiente' => 'bg-yellow-100 text-yellow-700',
                                'activa'    => 'bg-green-100 text-green-700',
                                'usada'     => 'bg-blue-100 text-blue-700',
                                'expirada'  => 'bg-red-100 text-red-600',
                                'cancelada' => 'bg-gray-100 text-gray-500',
                            ];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$gc->status] ?? 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($gc->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        @if($gc->buyer_name || $gc->buyer_email)
                            <p class="text-xs font-medium text-gray-700">{{ $gc->buyer_name }}</p>
                            <p class="text-xs text-gray-400">{{ $gc->buyer_email }}</p>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @if($gc->recipient_name || $gc->recipient_email)
                            <p class="text-xs font-medium text-gray-700">{{ $gc->recipient_name }}</p>
                            <p class="text-xs text-gray-400">{{ $gc->recipient_email }}</p>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">
                        {{ $gc->expires_at?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <button onclick="openEditGC('{{ $gc->id }}', {{ json_encode($gc->toArray()) }})"
                                    class="text-xs font-medium hover:underline" style="color:#1a4a6b">
                                Editar
                            </button>
                            <form method="POST" action="{{ route('admin.gift-cards.destroy', $gc->id) }}"
                                  onsubmit="return confirm('¿Eliminar gift card {{ $gc->code }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-xs">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No hay gift cards creadas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($giftCards->hasPages())
    <div class="mt-4">{{ $giftCards->links() }}</div>
@endif

{{-- ───── MODAL CREAR ───── --}}
<div id="modal-create"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Nueva gift card</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.gift-cards.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Monto (Gs.) *</label>
                <input type="number" name="amount" required min="1000" step="1000" placeholder="100000"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre comprador</label>
                    <input type="text" name="buyer_name" placeholder="Juan Pérez"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email comprador</label>
                    <input type="email" name="buyer_email" placeholder="juan@email.com"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre destinatario</label>
                    <input type="text" name="recipient_name" placeholder="María García"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email destinatario</label>
                    <input type="email" name="recipient_email" placeholder="maria@email.com"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje personalizado</label>
                <textarea name="message" rows="2" placeholder="¡Feliz cumpleaños!..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de expiración</label>
                <input type="date" name="expires_at"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="flex-1 text-white py-2 rounded-lg text-sm font-semibold hover:opacity-90" style="background:#1a4a6b">Crear gift card</button>
            </div>
        </form>
    </div>
</div>

{{-- ───── MODAL EDITAR ───── --}}
<div id="modal-edit-gc"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Editar gift card</h3>
            <button onclick="document.getElementById('modal-edit-gc').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="edit-form-gc" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    @foreach(['pendiente','activa','usada','expirada','cancelada'] as $s)
                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre comprador</label>
                    <input type="text" name="buyer_name"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email comprador</label>
                    <input type="email" name="buyer_email"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre destinatario</label>
                    <input type="text" name="recipient_name"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email destinatario</label>
                    <input type="email" name="recipient_email"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                <textarea name="message" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de expiración</label>
                <input type="date" name="expires_at"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-gc').classList.add('hidden')"
                        class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="flex-1 text-white py-2 rounded-lg text-sm font-semibold hover:opacity-90" style="background:#1a4a6b">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditGC(id, data) {
    const form = document.getElementById('edit-form-gc');
    form.action = `/admin/marketing/gift-cards/${id}`;
    const set = (name, value) => {
        const el = form.querySelector(`[name="${name}"]`);
        if (!el) return;
        el.value = value ?? '';
    };
    ['status','buyer_name','buyer_email','buyer_phone','recipient_name','recipient_email','message'].forEach(k => set(k, data[k]));
    if (data.expires_at) set('expires_at', data.expires_at.substring(0, 10));
    document.getElementById('modal-edit-gc').classList.remove('hidden');
}
</script>

@endsection
