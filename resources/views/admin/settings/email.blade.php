@extends('layouts.admin')
@section('title', 'Email / SMTP')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Configuración SMTP --}}
    <form method="POST" action="{{ route('admin.settings.email.update') }}">
        @csrf @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start justify-between border-b border-gray-100 pb-3 mb-5">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Configuración SMTP</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Servidor de correo para envío de notificaciones</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer mt-1">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                           {{ $settings->is_active ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2 grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Host SMTP</label>
                        <input type="text" name="host" value="{{ $settings->host }}"
                               placeholder="mail.example.com"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Puerto</label>
                        <input type="number" name="port" value="{{ $settings->port ?? 587 }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                    <input type="text" name="username" value="{{ $settings->username }}"
                           autocomplete="off"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" name="password" value="{{ $settings->password }}"
                           autocomplete="new-password"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cifrado</label>
                    <select name="encryption"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="tls"  {{ $settings->encryption === 'tls'  ? 'selected' : '' }}>TLS</option>
                        <option value="ssl"  {{ $settings->encryption === 'ssl'  ? 'selected' : '' }}>SSL</option>
                        <option value=""     {{ !$settings->encryption           ? 'selected' : '' }}>Ninguno</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email remitente</label>
                    <input type="email" name="from_email" value="{{ $settings->from_email }}"
                           placeholder="noreply@next1.com"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre remitente</label>
                    <input type="text" name="from_name" value="{{ $settings->from_name }}"
                           placeholder="Next1"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex justify-end mt-5">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    Guardar SMTP
                </button>
            </div>
        </div>
    </form>

    {{-- Email de prueba --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">Enviar email de prueba</h2>
        <form method="POST" action="{{ route('admin.settings.email.test') }}" class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Destinatario</label>
                <input type="email" name="to" value="{{ auth()->user()->email }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('to')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit"
                    class="px-5 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-900 whitespace-nowrap">
                Enviar prueba
            </button>
        </form>
    </div>
</div>
@endsection
