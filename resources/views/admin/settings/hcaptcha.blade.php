@extends('layouts.admin')
@section('title', 'hCaptcha')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('admin.settings.hcaptcha.update') }}">
        @csrf @method('PUT')

        {{-- Credenciales --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between border-b border-gray-100 pb-3 mb-5">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Configuración hCaptcha</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Protección contra bots en formularios públicos</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer mt-1">
                    <input type="hidden" name="is_enabled" value="0">
                    <input type="checkbox" name="is_enabled" value="1" class="sr-only peer"
                           {{ $settings->is_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Key</label>
                    <input type="text" name="site_key" value="{{ $settings->site_key }}"
                           placeholder="Clave del sitio de hCaptcha"
                           autocomplete="off"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                    <input type="password" name="secret_key" value="{{ $settings->secret_key }}"
                           placeholder="Clave secreta de hCaptcha"
                           autocomplete="new-password"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Dónde aplicar --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">Formularios protegidos</h2>
            <div class="space-y-3">
                @foreach([
                    'protect_login'      => ['label' => 'Inicio de sesión',  'desc' => 'Formulario de login'],
                    'protect_register'   => ['label' => 'Registro',          'desc' => 'Formulario de registro de nuevos usuarios'],
                    'protect_newsletter' => ['label' => 'Newsletter',        'desc' => 'Suscripción al boletín'],
                ] as $field => $item)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $item['label'] }}</p>
                        <p class="text-xs text-gray-500">{{ $item['desc'] }}</p>
                    </div>
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

        {{-- Info --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm text-amber-800">
            <strong>Nota:</strong> Si las credenciales son inválidas o hay un error de configuración, hCaptcha se
            deshabilitará automáticamente para no bloquear a los usuarios. Revisá los logs si sospechás errores silenciosos.
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                Guardar hCaptcha
            </button>
        </div>
    </form>
</div>
@endsection
