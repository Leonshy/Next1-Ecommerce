@extends('layouts.admin')
@section('title', 'Modo Mantenimiento')

@section('content')
<div class="max-w-xl">

    @if($data['is_active'])
    <div class="mb-6 flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm font-medium">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        El sitio está actualmente en modo mantenimiento. Los visitantes ven la página de mantenimiento.
    </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.maintenance.update') }}">
        @csrf
        <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-6">
            <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Configuración de Mantenimiento</h2>

            {{-- Toggle principal --}}
            <div x-data="{ active: {{ $data['is_active'] ? 'true' : 'false' }} }"
                 class="flex items-center justify-between p-4 rounded-xl border-2 transition-colors"
                 :class="active ? 'border-amber-400 bg-amber-50' : 'border-gray-200 bg-gray-50'">
                <div>
                    <p class="text-sm font-semibold text-gray-900">Activar modo mantenimiento</p>
                    <p class="text-xs text-gray-500 mt-0.5">Los visitantes verán la página de mantenimiento. Los admins pueden seguir navegando con normalidad.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer ml-4 flex-shrink-0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                           @change="active = $event.target.checked"
                           {{ $data['is_active'] ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                peer-checked:after:translate-x-full peer-checked:after:border-white
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:border-gray-300 after:border after:rounded-full
                                after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                </label>
            </div>

            {{-- Mensaje --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje para los visitantes</label>
                <textarea name="message" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Estamos realizando tareas de mantenimiento. Volvemos pronto.">{{ $data['message'] ?? '' }}</textarea>
            </div>

            {{-- Tiempo estimado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tiempo estimado <span class="text-gray-400 font-normal">(opcional)</span></label>
                <input type="text" name="estimated_time" value="{{ $data['estimated_time'] ?? '' }}"
                       placeholder="Ej: Volvemos en 2 horas"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-1">Se muestra debajo del mensaje en la página de mantenimiento.</p>
            </div>

            {{-- Preview link --}}
            <div class="bg-gray-50 rounded-lg p-3 flex items-center justify-between">
                <p class="text-xs text-gray-500">Vista previa de la página de mantenimiento</p>
                <a href="{{ route('admin.settings.maintenance.preview') }}" target="_blank"
                   class="text-xs text-blue-600 hover:underline font-medium flex items-center gap-1">
                    Ver preview
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                    class="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-gray-700 transition-colors">
                Guardar cambios
            </button>
        </div>
    </form>
</div>
@endsection
