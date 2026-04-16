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
            <div class="flex items-center gap-2">
                <span class="text-xs {{ $settings[$key]->is_enabled ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
                    {{ $settings[$key]->is_enabled ? 'Activo' : 'Inactivo' }}
                </span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" form="form-{{ $key }}" name="is_enabled" value="0">
                    <input type="checkbox" form="form-{{ $key }}" name="is_enabled" value="1" class="sr-only peer"
                           {{ $settings[$key]->is_enabled ? 'checked' : '' }}
                           onchange="document.getElementById('form-{{ $key }}').submit()">
                    <div class="relative w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
        </div>

        <form id="form-{{ $key }}" method="POST" action="{{ route('admin.settings.payments.update', $key) }}">
            @csrf

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
                <button type="submit" form="validate-{{ $key }}"
                        class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                    Validar credenciales
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    Guardar
                </button>
            </div>
        </form>

        {{-- Form separado para validar (no puede estar anidado) --}}
        <form id="validate-{{ $key }}" method="POST"
              action="{{ route('admin.settings.payments.validate', $key) }}" class="hidden">
            @csrf
        </form>
    </div>
    @endforeach

    {{-- Transferencia Bancaria --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-4 border-b border-gray-100 pb-3">
            <span class="text-2xl">🏦</span>
            <div>
                <h2 class="text-base font-semibold text-gray-900">Transferencia Bancaria</h2>
                <p class="text-xs text-gray-400">Datos que verá el cliente al elegir este método de pago</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.payments.update', 'transferencia') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Banco</label>
                    <input type="text" name="bank" value="{{ $transferSettings['bank'] ?? '' }}"
                           placeholder="ej: Banco Continental"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del titular</label>
                    <input type="text" name="account_name" value="{{ $transferSettings['account_name'] ?? '' }}"
                           placeholder="ej: NEXT1 S.A."
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número de cuenta</label>
                    <input type="text" name="account_number" value="{{ $transferSettings['account_number'] ?? '' }}"
                           placeholder="ej: 100-123456-0"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RUC</label>
                    <input type="text" name="ruc" value="{{ $transferSettings['ruc'] ?? '' }}"
                           placeholder="ej: 80123456-7"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Información adicional (opcional)</label>
                    <input type="text" name="extra" value="{{ $transferSettings['extra'] ?? '' }}"
                           placeholder="ej: Referencia: número de pedido"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    Guardar datos bancarios
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
