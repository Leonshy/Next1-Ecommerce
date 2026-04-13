@extends('layouts.admin')
@section('title', 'Campañas')

@section('content')

{{-- Flash --}}
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
        <h2 class="text-xl font-bold text-gray-900">Campañas</h2>
        <p class="text-sm text-gray-500 mt-0.5">Banners promocionales que aparecen en el inicio</p>
    </div>
    <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
            style="background:#1a4a6b">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva campaña
    </button>
</div>

{{-- Tabla --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left w-8">#</th>
                <th class="px-5 py-3 text-left">Campaña</th>
                <th class="px-5 py-3 text-left">Etiqueta</th>
                <th class="px-5 py-3 text-left">Período</th>
                <th class="px-5 py-3 text-center">En inicio</th>
                <th class="px-5 py-3 text-center">Estado</th>
                <th class="px-5 py-3 text-left">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($campaigns as $campaign)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $campaign->display_order }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if($campaign->banner_image)
                                <img src="{{ $campaign->banner_image }}"
                                     alt="{{ $campaign->name }}"
                                     class="w-14 h-9 object-cover rounded border border-gray-200 flex-shrink-0"
                                     onerror="this.style.display='none'">
                            @else
                                <div class="w-14 h-9 bg-gray-100 rounded border border-gray-200 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $campaign->name }}</p>
                                @if($campaign->description)
                                    <p class="text-xs text-gray-400 line-clamp-1 max-w-xs">{{ $campaign->description }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        @if($campaign->tag)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                {{ $campaign->tag }}
                            </span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-500">
                        @if($campaign->start_date || $campaign->end_date)
                            {{ $campaign->start_date?->format('d/m/Y') ?? '—' }}
                            →
                            {{ $campaign->end_date?->format('d/m/Y') ?? '—' }}
                        @else
                            <span class="text-gray-300">Sin fechas</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($campaign->display_on_home)
                            <span class="text-green-600 text-xs font-medium">✓ Sí</span>
                        @else
                            <span class="text-gray-300 text-xs">No</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $campaign->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $campaign->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <button onclick="openEdit('{{ $campaign->id }}', {{ json_encode($campaign->toArray()) }})"
                                    class="text-xs font-medium hover:underline" style="color:#1a4a6b">
                                Editar
                            </button>
                            <form method="POST" action="{{ route('admin.campanas.destroy', $campaign->id) }}"
                                  onsubmit="return confirm('¿Eliminar la campaña « {{ addslashes($campaign->name) }} »?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-xs">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">
                        No hay campañas creadas aún.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ───────────── MODAL CREAR ───────────── --}}
<div id="modal-create"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Nueva campaña</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.campanas.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            @include('admin.marketing._campaign-fields')
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 text-white py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity"
                        style="background:#1a4a6b">
                    Crear campaña
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ───────────── MODAL EDITAR ───────────── --}}
<div id="modal-edit"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Editar campaña</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="edit-form" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PATCH')
            @include('admin.marketing._campaign-fields', ['editing' => true])
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 text-white py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity"
                        style="background:#1a4a6b">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, data) {
    const form = document.getElementById('edit-form');
    form.action = `/admin/marketing/campanas/${id}`;

    const set = (name, value) => {
        const el = form.querySelector(`[name="${name}"]`);
        if (!el) return;
        if (el.type === 'checkbox') el.checked = !!value;
        else el.value = value ?? '';
    };

    set('name',            data.name);
    set('tag',             data.tag);
    set('description',     data.description);
    set('start_date',      data.start_date);
    set('end_date',        data.end_date);
    set('display_order',   data.display_order);
    set('display_on_home', data.display_on_home);
    set('is_active',       data.is_active);

    // Enviar imagen al picker vía evento de ventana
    window.dispatchEvent(new CustomEvent('campaign-set-image', {
        detail: { url: data.banner_image ?? '' }
    }));

    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>

@endsection
