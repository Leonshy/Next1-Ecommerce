@extends('layouts.admin')
@section('title', 'Email / SMTP')

@section('content')
<div class="max-w-3xl space-y-6">

    @if(session('success'))
        <div class="rounded-xl border bg-green-50 border-green-200 p-4">
            <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border bg-red-50 border-red-200 p-4">
            <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Configuración SMTP --}}
    <form method="POST" action="{{ route('admin.settings.email.update') }}"
          x-data="{
            enc: '{{ $settings->encryption ?? 'tls' }}',
            setPreset(e) {
                this.enc = e;
                if (e === 'ssl')  document.querySelector('[name=port]').value = 465;
                if (e === 'tls')  document.querySelector('[name=port]').value = 587;
                if (e === 'none') document.querySelector('[name=port]').value = 25;
            }
          }">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start justify-between border-b border-gray-100 pb-4 mb-6">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Configuración SMTP</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Servidor de correo para envío de notificaciones automáticas</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer mt-1">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                           {{ $settings->is_active ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                    <span class="ml-2 text-sm text-gray-600">Activo</span>
                </label>
            </div>

            {{-- Presets de cifrado --}}
            <div class="mb-5">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Configuración rápida</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="setPreset('ssl')"
                            :class="enc === 'ssl' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-400'"
                            class="px-4 py-2 rounded-lg border text-sm font-medium transition-colors">
                        SSL — Puerto 465
                        <span class="ml-1 text-xs opacity-70">(recomendado)</span>
                    </button>
                    <button type="button" @click="setPreset('tls')"
                            :class="enc === 'tls' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-400'"
                            class="px-4 py-2 rounded-lg border text-sm font-medium transition-colors">
                        TLS — Puerto 587
                    </button>
                    <button type="button" @click="setPreset('none')"
                            :class="enc === 'none' ? 'bg-gray-600 text-white border-gray-600' : 'bg-white text-gray-700 border-gray-300 hover:border-gray-400'"
                            class="px-4 py-2 rounded-lg border text-sm font-medium transition-colors">
                        Sin cifrado — Puerto 25
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Host + Puerto en la misma fila --}}
                <div class="sm:col-span-2 grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Host SMTP</label>
                        <input type="text" name="host" value="{{ $settings->host }}"
                               placeholder="mail.tudominio.com"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-400 mt-1">Ej: mail.next1.com.py, smtp.gmail.com, smtp.office365.com</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Puerto</label>
                        <input type="number" name="port" value="{{ $settings->port ?? 465 }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-400 mt-1">465 / 587 / 25</p>
                    </div>
                </div>

                {{-- Cifrado (hidden, controlado por los presets + input hidden) --}}
                <input type="hidden" name="encryption" :value="enc">

                {{-- Usuario --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usuario (email)</label>
                    <input type="text" name="username" value="{{ $settings->username }}"
                           autocomplete="off"
                           placeholder="correo@tudominio.com"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Contraseña --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" name="password"
                           autocomplete="new-password"
                           placeholder="{{ $settings->password ? '••••••••••• (dejar vacío para mantener)' : 'Nueva contraseña' }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @if($settings->password)
                        <p class="text-xs text-gray-400 mt-1">Dejá vacío para no cambiar la contraseña actual.</p>
                    @endif
                </div>

                {{-- Email remitente --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email remitente</label>
                    <input type="email" name="from_email" value="{{ $settings->from_email }}"
                           placeholder="noreply@next1.com.py"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Nombre remitente --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre remitente</label>
                    <input type="text" name="from_name" value="{{ $settings->from_name }}"
                           placeholder="Next1"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

            </div>

            {{-- Config actual resumida --}}
            <div class="mt-5 p-3 bg-gray-50 rounded-lg text-xs text-gray-500 border border-gray-100">
                <span class="font-medium text-gray-700">Configuración activa:</span>
                {{ $settings->host ?: '—' }}:{{ $settings->port ?? 465 }}
                (<span x-text="enc === 'ssl' ? 'SSL' : enc === 'tls' ? 'TLS' : 'Sin cifrado'">{{ strtoupper($settings->encryption === 'none' || !$settings->encryption ? 'Sin cifrado' : $settings->encryption) }}</span>)
                · Desde: {{ $settings->from_email ?: '—' }}
            </div>

            <div class="flex justify-end mt-5">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    Guardar configuración
                </button>
            </div>
        </div>
    </form>

    {{-- Email de prueba --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="border-b border-gray-100 pb-3 mb-5">
            <h2 class="text-base font-semibold text-gray-900">Enviar email de prueba</h2>
            <p class="text-xs text-gray-500 mt-0.5">Verifica que la configuración SMTP funcione correctamente</p>
        </div>
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

    {{-- Info emails automáticos --}}
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
        <h3 class="text-sm font-semibold text-blue-800 mb-3">Emails automáticos configurados</h3>
        <ul class="space-y-2 text-sm text-blue-700">
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><strong>Confirmación de pedido</strong> — se envía cuando el cliente finaliza la compra</span>
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><strong>Cambio de estado</strong> — se envía cuando cambiás el estado del pedido a Confirmado, En preparación, Enviado, Entregado o Cancelado</span>
            </li>
        </ul>
        <p class="text-xs text-blue-600 mt-3">Los emails solo se envían si el SMTP está activo y configurado correctamente.</p>
    </div>

</div>
@endsection
