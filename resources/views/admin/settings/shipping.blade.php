@extends('layouts.admin')
@section('title', 'Configuración de Envíos')

@section('content')
<div class="max-w-4xl space-y-6">

    {{-- Opciones generales --}}
    <form method="POST" action="{{ route('admin.settings.shipping.update') }}">
        @csrf @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
            <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Opciones Generales</h2>

            {{-- Envío gratis --}}
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-800">Envío gratis</p>
                    <p class="text-xs text-gray-500 mt-0.5">Habilitar envío gratuito a partir de un monto mínimo</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="free_shipping_enabled" value="0">
                    <input type="checkbox" name="free_shipping_enabled" value="1" class="sr-only peer"
                           {{ $settings->free_shipping_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-300 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            <div x-data="{ enabled: {{ $settings->free_shipping_enabled ? 'true' : 'false' }} }">
                <div class="flex items-start justify-between" @click="enabled = !enabled">
                    {{-- invisible trigger already handled above --}}
                </div>
                <div class="mt-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto mínimo para envío gratis (Gs.)</label>
                    <input type="number" name="free_shipping_min_amount" value="{{ $settings->free_shipping_min_amount ?? 0 }}"
                           class="w-48 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Retiro en tienda --}}
            <div class="flex items-start justify-between pt-3 border-t border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-800">Retiro en tienda</p>
                    <p class="text-xs text-gray-500 mt-0.5">El cliente puede retirar su pedido en el local</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="store_pickup_enabled" value="0">
                    <input type="checkbox" name="store_pickup_enabled" value="1" class="sr-only peer"
                           {{ $settings->store_pickup_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            {{-- Envío propio --}}
            <div class="flex items-start justify-between pt-3 border-t border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-800">Envío propio</p>
                    <p class="text-xs text-gray-500 mt-0.5">La tienda gestiona sus propios repartidores</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="envio_propio_enabled" value="0">
                    <input type="checkbox" name="envio_propio_enabled" value="1" class="sr-only peer"
                           {{ $settings->envio_propio_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
        </div>

        {{-- Zonas de envío --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mt-6" x-data="zonesEditor({{ json_encode($settings->zones ?? []) }})">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                <h2 class="text-base font-semibold text-gray-900">Zonas de Envío</h2>
                <button type="button" @click="addZone()"
                        class="text-sm text-blue-600 hover:underline font-medium">+ Agregar zona</button>
            </div>

            <div class="space-y-3" id="zones-container">
                <template x-for="(zone, i) in zones" :key="i">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <input type="text" :name="`zones[${i}][label]`" x-model="zone.label" placeholder="Ej: Asunción" required
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="w-40">
                            <input type="number" :name="`zones[${i}][price]`" x-model="zone.price" placeholder="Precio (Gs.)" required
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="button" @click="removeZone(i)" class="text-red-400 hover:text-red-600 text-lg leading-none">&times;</button>
                    </div>
                </template>
            </div>

            <p x-show="zones.length === 0" class="text-sm text-gray-400 py-4 text-center">No hay zonas configuradas</p>
        </div>

        {{-- AEX --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
            <div class="flex items-start justify-between border-b border-gray-100 pb-3 mb-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Integración AEX</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Cotización de envíos en tiempo real vía API de AEX</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer mt-1">
                    <input type="hidden" name="aex_enabled" value="0">
                    <input type="checkbox" name="aex_enabled" value="1" class="sr-only peer"
                           {{ $settings->aex_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usuario API</label>
                    <input type="text" name="aex_api_user" value="{{ $settings->aex_api_user }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña API</label>
                    <input type="password" name="aex_api_password" value="{{ $settings->aex_api_password }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Entorno</label>
                    <select name="aex_environment"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="sandbox" {{ $settings->aex_environment === 'sandbox' ? 'selected' : '' }}>Sandbox (pruebas)</option>
                        <option value="production" {{ $settings->aex_environment === 'production' ? 'selected' : '' }}>Producción</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-4">
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                Guardar cambios
            </button>
        </div>
    </form>
</div>

<script>
function zonesEditor(initial) {
    return {
        zones: initial.length ? initial : [],
        addZone() { this.zones.push({ label: '', price: 0 }); },
        removeZone(i) { this.zones.splice(i, 1); },
    };
}
</script>
@endsection
