@extends('layouts.admin')
@section('title', 'Analytics')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('admin.settings.analytics.update') }}">
        @csrf @method('PUT')

        {{-- Google Analytics 4 --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4 border-b border-gray-100 pb-3">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">📊</span>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Google Analytics 4</h2>
                        <p class="text-xs text-gray-500">Seguimiento de visitas y eventos</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="ga4_enabled" value="0">
                    <input type="checkbox" name="ga4_enabled" value="1" class="sr-only peer"
                           {{ $settings->ga4_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Measurement ID</label>
                <input type="text" name="ga4_measurement_id" value="{{ $settings->ga4_measurement_id }}"
                       placeholder="G-XXXXXXXXXX"
                       class="w-64 border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        {{-- Google Tag Manager --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4 border-b border-gray-100 pb-3">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">🏷️</span>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Google Tag Manager</h2>
                        <p class="text-xs text-gray-500">Contenedor de etiquetas</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="gtm_enabled" value="0">
                    <input type="checkbox" name="gtm_enabled" value="1" class="sr-only peer"
                           {{ $settings->gtm_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Container ID</label>
                <input type="text" name="gtm_container_id" value="{{ $settings->gtm_container_id }}"
                       placeholder="GTM-XXXXXXX"
                       class="w-64 border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        {{-- Meta Pixel --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4 border-b border-gray-100 pb-3">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">📘</span>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Meta Pixel</h2>
                        <p class="text-xs text-gray-500">Facebook / Instagram Ads tracking</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="meta_pixel_enabled" value="0">
                    <input type="checkbox" name="meta_pixel_enabled" value="1" class="sr-only peer"
                           {{ $settings->meta_pixel_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pixel ID</label>
                <input type="text" name="meta_pixel_id" value="{{ $settings->meta_pixel_id }}"
                       placeholder="123456789012345"
                       class="w-64 border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        {{-- Eventos a rastrear --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">Eventos a rastrear</h2>
            <div class="space-y-3">
                @foreach([
                    'track_view_item'      => 'Ver producto',
                    'track_add_to_cart'    => 'Agregar al carrito',
                    'track_begin_checkout' => 'Iniciar checkout',
                    'track_purchase'       => 'Compra completada',
                ] as $field => $label)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <span class="text-sm text-gray-700">{{ $label }}</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="{{ $field }}" value="0">
                        <input type="checkbox" name="{{ $field }}" value="1" class="sr-only peer"
                               {{ $settings->$field ? 'checked' : '' }}>
                        <div class="w-9 h-5 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-4"></div>
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                Guardar Analytics
            </button>
        </div>
    </form>
</div>
@endsection
