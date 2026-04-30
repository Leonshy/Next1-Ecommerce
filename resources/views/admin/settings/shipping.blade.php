@extends('layouts.admin')
@section('title', 'Configuración de Envíos')

@section('content')
@php
    $allDepts   = \App\Data\ParaguayLocations::departments();
    $savedZones = $settings->zones ?? [];
@endphp

<div class="max-w-4xl space-y-6">

    <form method="POST" action="{{ route('admin.settings.shipping.update') }}"
          x-data="shippingZonesEditor({{ json_encode($allDepts) }}, {{ json_encode($savedZones) }}, {{ $settings->envio_propio_enabled ? 'true' : 'false' }})"
          x-on:submit="onSubmit">
        @csrf @method('PUT')

        {{-- Mensajes flash --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Opciones Generales ────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
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
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Monto mínimo para envío gratis (Gs.)</label>
                <input type="number" name="free_shipping_min_amount" value="{{ $settings->free_shipping_min_amount ?? 0 }}"
                       class="w-48 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Retiro en tienda --}}
            <div class="flex items-start justify-between pt-4 border-t border-gray-100">
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

            {{-- Envío propio — conectado a Alpine --}}
            <div class="flex items-start justify-between pt-4 border-t border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-800">Envío propio</p>
                    <p class="text-xs text-gray-500 mt-0.5">La tienda gestiona sus propios repartidores</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="envio_propio_enabled" value="0">
                    <input type="checkbox" name="envio_propio_enabled" value="1" class="sr-only peer"
                           x-model="envioPropio"
                           :checked="envioPropio">
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
        </div>

        {{-- ── Zonas de Envío (visible solo si envío propio está activo) ────── --}}
        <div x-show="envioPropio"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start justify-between border-b border-gray-100 pb-4 mb-5">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Zonas de Envío — Paraguay</h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Habilitá los departamentos donde realizás envíos y configurá tarifas base.
                        Podés agregar tarifas personalizadas por ciudad o distrito dentro de cada departamento.
                    </p>
                </div>
                <div class="text-right text-xs text-gray-500 shrink-0 ml-4 mt-0.5">
                    <span x-text="activeDeptCount()"></span> / {{ count($allDepts) }} activos
                    <template x-if="totalCustomRatesCount() > 0">
                        <span class="block text-blue-600">
                            <span x-text="totalCustomRatesCount()"></span> tarifa(s) personalizada(s)
                        </span>
                    </template>
                </div>
            </div>

            {{-- Info --}}
            <div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 text-xs text-blue-700 mb-4 flex gap-2">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>
                    <strong>¿Cómo funciona?</strong> Habilitá un departamento y configurá su tarifa base.
                    Si alguna ciudad tiene un precio diferente, agregá una <strong>Tarifa Personalizada</strong>.
                    Podés <strong>deshabilitar ciudades</strong> para que no aparezcan en el checkout.
                    Los departamentos inactivos aparecen deshabilitados en el checkout.
                </span>
            </div>

            {{-- Hidden input que recibe el JSON serializado de Alpine --}}
            <input type="hidden" name="zones_json" id="zones_json_input">

            {{-- Lista de departamentos --}}
            <div class="space-y-2">
                <template x-for="(zone, zIdx) in zones" :key="zone.departmentId">
                    <div :class="zone.active ? 'border-blue-200 bg-white' : 'border-gray-200 bg-gray-50'"
                         class="border rounded-xl overflow-hidden transition-colors">

                        {{-- Cabecera del departamento --}}
                        <div class="flex items-center gap-3 p-4">
                            {{-- Toggle activo --}}
                            <button type="button"
                                    @click="zone.active = !zone.active; if (!zone.active) zone.open = false"
                                    :class="zone.active ? 'bg-blue-600' : 'bg-gray-300'"
                                    class="relative inline-flex w-10 h-6 rounded-full transition-colors shrink-0 focus:outline-none">
                                <span :class="zone.active ? 'translate-x-5' : 'translate-x-1'"
                                      class="inline-block w-4 h-4 mt-1 bg-white rounded-full shadow transition-transform"></span>
                            </button>

                            {{-- Nombre y resumen --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900" x-text="zone.name"></p>
                                <p class="text-xs text-gray-500">
                                    <span x-text="getActiveCities(zIdx).length"></span> /
                                    <span x-text="zone.districts.length"></span> ciudades activas
                                    <template x-if="zone.customRates.length > 0">
                                        <span class="text-blue-600 ml-1">
                                            · <span x-text="zone.customRates.length"></span>
                                            tarifa(s) personalizada(s)
                                        </span>
                                    </template>
                                </p>
                            </div>

                            {{-- Badge precio cuando activo --}}
                            <template x-if="zone.active && zone.price > 0">
                                <span class="hidden sm:inline-flex text-xs font-medium text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">
                                    Gs. <span x-text="Number(zone.price).toLocaleString('es-PY')"></span>
                                </span>
                            </template>

                            {{-- Badge estado --}}
                            <span :class="zone.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                  class="text-xs font-medium px-2 py-0.5 rounded-full shrink-0"
                                  x-text="zone.active ? 'Activo' : 'Inactivo'"></span>

                            {{-- Expandir --}}
                            <button type="button"
                                    @click="if(zone.active) zone.open = !zone.open"
                                    :disabled="!zone.active"
                                    class="p-1.5 rounded-lg hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                <svg :class="zone.open ? 'rotate-180' : ''"
                                     class="w-4 h-4 text-gray-500 transition-transform"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Contenido expandible --}}
                        <div x-show="zone.open" class="border-t border-gray-100 p-4 space-y-4 bg-white">

                            {{-- ── Ciudades habilitadas / deshabilitadas ── --}}
                            <div class="border border-gray-200 rounded-xl overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-200">
                                    <p class="text-sm font-semibold text-gray-700">Ciudades / Distritos</p>
                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                        <span>
                                            <span x-text="getActiveCities(zIdx).length" class="font-medium text-green-700"></span>
                                            activas ·
                                            <span x-text="zone.inactiveCities.length" class="font-medium text-red-500"></span>
                                            deshabilitadas
                                        </span>
                                        <button type="button" @click="enableAllCities(zIdx)"
                                                class="text-blue-600 hover:underline">Habilitar todas</button>
                                    </div>
                                </div>
                                <div class="p-3 grid grid-cols-2 sm:grid-cols-3 gap-1 max-h-48 overflow-y-auto">
                                    <template x-for="city in zone.districts" :key="city">
                                        <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg cursor-pointer hover:bg-gray-50 select-none"
                                               :class="isCityActive(zIdx, city) ? '' : 'opacity-60'">
                                            <button type="button"
                                                    @click="toggleCity(zIdx, city)"
                                                    :class="isCityActive(zIdx, city) ? 'bg-green-500' : 'bg-gray-300'"
                                                    class="relative inline-flex w-8 h-4 rounded-full transition-colors shrink-0 focus:outline-none">
                                                <span :class="isCityActive(zIdx, city) ? 'translate-x-4' : 'translate-x-0.5'"
                                                      class="inline-block w-3 h-3 mt-0.5 bg-white rounded-full shadow transition-transform"></span>
                                            </button>
                                            <span class="text-xs truncate"
                                                  :class="isCityActive(zIdx, city) ? 'text-gray-800' : 'text-gray-400 line-through'"
                                                  x-text="city"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            {{-- ── Tarifa base del departamento ── --}}
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 space-y-3">
                                <p class="text-sm font-semibold text-blue-900 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Tarifa del Departamento (por defecto)
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Costo de envío (Gs.)</label>
                                        <input type="number" x-model="zone.price" placeholder="25000" min="0"
                                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Tiempo de entrega</label>
                                        <input type="text" x-model="zone.deliveryTime" placeholder="ej: 24-48 horas"
                                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                </div>

                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" x-model="zone.freeShippingEligible"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-xs text-gray-700">Aplica envío gratis por monto mínimo</span>
                                </label>

                                <div x-show="getDistrictsWithDefaultRate(zIdx).length > 0">
                                    <p class="text-xs text-gray-500 mb-1">Aplica a:</p>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="d in getDistrictsWithDefaultRate(zIdx)" :key="d">
                                            <span class="text-xs bg-white border border-blue-200 text-blue-700 px-1.5 py-0.5 rounded" x-text="d"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- ── Tarifas personalizadas ── --}}
                            <template x-if="zone.customRates.length > 0">
                                <div class="space-y-3">
                                    <p class="text-sm font-semibold text-gray-700">Tarifas Personalizadas por Área</p>

                                    <template x-for="(rate, rIdx) in zone.customRates" :key="rate.id">
                                        <div class="border border-dashed border-gray-300 rounded-xl p-4 space-y-3 bg-gray-50">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium text-blue-700">
                                                    Tarifa Personalizada #<span x-text="rIdx + 1"></span>
                                                </span>
                                                <button type="button" @click="removeCustomRate(zIdx, rIdx)"
                                                        class="text-red-400 hover:text-red-600 p-1 rounded-lg hover:bg-red-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Costo de envío (Gs.)</label>
                                                    <input type="number" x-model="rate.price" placeholder="30000" min="0"
                                                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Tiempo de entrega</label>
                                                    <input type="text" x-model="rate.deliveryTime" placeholder="48-72 horas"
                                                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                                </div>
                                            </div>

                                            {{-- Selector de distritos (solo ciudades activas) --}}
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                                    Ciudades / Distritos con esta tarifa
                                                    <span class="text-gray-400 font-normal">(solo ciudades habilitadas)</span>
                                                </label>
                                                <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-2 bg-white grid grid-cols-2 sm:grid-cols-3 gap-1">
                                                    <template x-for="district in getActiveCities(zIdx)" :key="district">
                                                        <label :class="isDistrictInOtherRate(zIdx, rIdx, district) ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer hover:bg-blue-50 rounded'"
                                                               class="flex items-center gap-1.5 px-1.5 py-1 text-xs select-none">
                                                            <input type="checkbox"
                                                                   :checked="rate.districtIds.includes(district)"
                                                                   :disabled="isDistrictInOtherRate(zIdx, rIdx, district)"
                                                                   @change="toggleDistrict(zIdx, rIdx, district)"
                                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-3 h-3 shrink-0">
                                                            <span x-text="district" class="truncate"></span>
                                                        </label>
                                                    </template>
                                                </div>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    <span x-text="rate.districtIds.length"></span> área(s) seleccionada(s)
                                                </p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Botón agregar tarifa personalizada --}}
                            <template x-if="getActiveCities(zIdx).length > 1">
                                <button type="button" @click="addCustomRate(zIdx)"
                                        class="w-full flex items-center justify-center gap-2 py-2.5 border border-dashed border-blue-300 rounded-xl text-sm text-blue-600 hover:bg-blue-50 transition-colors font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Agregar Tarifa Personalizada por Área
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ── AEX ───────────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start justify-between border-b border-gray-100 pb-4 mb-4">
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
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="sandbox" {{ $settings->aex_environment === 'sandbox' ? 'selected' : '' }}>Sandbox (pruebas)</option>
                        <option value="production" {{ $settings->aex_environment === 'production' ? 'selected' : '' }}>Producción</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Guardar --}}
        <div class="flex justify-end">
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                Guardar configuración
            </button>
        </div>
    </form>
</div>

<script>
function shippingZonesEditor(allDepts, savedZones, envioPropio) {
    const initZones = allDepts.map(dept => {
        const saved = savedZones.find(z => z.departmentId === dept.id) || {};
        return {
            departmentId: dept.id,
            name:         dept.name,
            districts:    dept.districts,
            active:               saved.active               ?? false,
            price:                saved.price                ?? 0,
            deliveryTime:         saved.deliveryTime         ?? '',
            freeShippingEligible: saved.freeShippingEligible ?? true,
            inactiveCities:       saved.inactiveCities       ?? [],
            customRates: (saved.customRates || []).map(r => ({
                id:           r.id || Math.random().toString(36).slice(2, 11),
                price:        r.price        ?? 0,
                deliveryTime: r.deliveryTime ?? '',
                districtIds:  r.districtIds  ?? [],
            })),
            open: false,
        };
    });

    return {
        zones: initZones,
        envioPropio: envioPropio,

        activeDeptCount() {
            return this.zones.filter(z => z.active).length;
        },

        totalCustomRatesCount() {
            return this.zones.reduce((sum, z) => sum + z.customRates.length, 0);
        },

        // ── Ciudades ──────────────────────────────────────────────────────────

        isCityActive(zIdx, city) {
            return !this.zones[zIdx].inactiveCities.includes(city);
        },

        toggleCity(zIdx, city) {
            const zone = this.zones[zIdx];
            const idx  = zone.inactiveCities.indexOf(city);
            if (idx === -1) {
                zone.inactiveCities.push(city);
                // Quitar la ciudad de todas las tarifas personalizadas si estaba ahí
                zone.customRates.forEach(r => {
                    const di = r.districtIds.indexOf(city);
                    if (di !== -1) r.districtIds.splice(di, 1);
                });
            } else {
                zone.inactiveCities.splice(idx, 1);
            }
        },

        enableAllCities(zIdx) {
            this.zones[zIdx].inactiveCities = [];
        },

        getActiveCities(zIdx) {
            const zone = this.zones[zIdx];
            return zone.districts.filter(d => !zone.inactiveCities.includes(d));
        },

        // ── Tarifas personalizadas ────────────────────────────────────────────

        addCustomRate(zIdx) {
            this.zones[zIdx].customRates.push({
                id:           Math.random().toString(36).slice(2, 11),
                price:        0,
                deliveryTime: '',
                districtIds:  [],
            });
        },

        removeCustomRate(zIdx, rIdx) {
            this.zones[zIdx].customRates.splice(rIdx, 1);
        },

        toggleDistrict(zIdx, rIdx, district) {
            const rate = this.zones[zIdx].customRates[rIdx];
            const idx  = rate.districtIds.indexOf(district);
            if (idx === -1) rate.districtIds.push(district);
            else            rate.districtIds.splice(idx, 1);
        },

        isDistrictInOtherRate(zIdx, currentRateIdx, district) {
            return this.zones[zIdx].customRates.some((r, i) =>
                i !== currentRateIdx && r.districtIds.includes(district)
            );
        },

        getDistrictsWithDefaultRate(zIdx) {
            const zone        = this.zones[zIdx];
            const allInCustom = zone.customRates.flatMap(r => r.districtIds);
            // Solo muestra ciudades activas que no tienen tarifa personalizada
            return this.getActiveCities(zIdx).filter(d => !allInCustom.includes(d));
        },

        getSerializedZones() {
            return JSON.stringify(
                this.zones.map(({ name, districts, open, ...zone }) => zone)
            );
        },

        onSubmit() {
            document.getElementById('zones_json_input').value = this.getSerializedZones();
        },
    };
}
</script>
@endsection
