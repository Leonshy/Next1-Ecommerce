@extends('layouts.admin')
@section('title', 'Configuración de Pagos')

@section('content')
<div class="max-w-4xl space-y-6">

    @php
    $providers = [
        'bancard'  => ['name' => 'Bancard VPOS', 'icon' => '💳', 'fields' => ['public_key' => 'Public Key', 'private_key' => 'Private Key']],
        'pagopar'  => ['name' => 'PagoPar',      'icon' => '🔵', 'fields' => ['public_key' => 'Public Key', 'private_key' => 'Private Key']],
        'coinbase' => ['name' => 'Coinbase Commerce', 'icon' => '🟡', 'fields' => ['public_key' => 'API Key', 'webhook_secret' => 'Webhook Secret']],
        'coinspaid'=> ['name' => 'CoinsPaid',    'icon' => '🟠', 'fields' => ['public_key' => 'Public Key', 'private_key' => 'Secret Key']],
    ];
    @endphp

    @foreach($providers as $key => $provider)
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start justify-between mb-4 border-b border-gray-100 pb-3">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">{{ $provider['icon'] }}</span>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">{{ $provider['name'] }}</h2>
                    @if($settings[$key]->is_validated)
                        <span class="text-xs text-green-600 font-medium">Credenciales validadas</span>
                    @else
                        <span class="text-xs text-gray-400">Sin validar</span>
                    @endif
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <span class="text-xs text-gray-500 mr-2">{{ $settings[$key]->is_enabled ? 'Activo' : 'Inactivo' }}</span>
                <input type="hidden" form="form-{{ $key }}" name="is_enabled" value="0">
                <input type="checkbox" form="form-{{ $key }}" name="is_enabled" value="1" class="sr-only peer"
                       {{ $settings[$key]->is_enabled ? 'checked' : '' }}
                       onchange="document.getElementById('form-{{ $key }}').submit()">
                <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
            </label>
        </div>

        <form id="form-{{ $key }}" method="POST" action="{{ route('admin.settings.payments.update', $key) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($provider['fields'] as $field => $label)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                    <input type="text" name="{{ $field }}" value="{{ $settings[$key]->$field }}" autocomplete="off"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @endforeach

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Entorno</label>
                    <select name="environment"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="sandbox" {{ $settings[$key]->environment === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                        <option value="production" {{ $settings[$key]->environment === 'production' ? 'selected' : '' }}>Producción</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-end space-x-3">
                <form method="POST" action="{{ route('admin.settings.payments.validate', $key) }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                        Validar credenciales
                    </button>
                </form>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    Guardar
                </button>
            </div>
        </form>
    </div>
    @endforeach

</div>
@endsection
