@extends('layouts.admin')
@section('title', 'Mi Perfil')

@section('content')
<div class="max-w-2xl space-y-6">

    <div>
        <h1 class="text-xl font-bold text-gray-900">Mi perfil y seguridad</h1>
        <p class="text-sm text-gray-500 mt-0.5">Administrá tu información personal y opciones de seguridad.</p>
    </div>

    {{-- Información personal --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Información personal</h2>

        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/20 focus:border-[#1a4a6b] @error('name') border-red-400 @enderror">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/20 focus:border-[#1a4a6b] @error('email') border-red-400 @enderror">
                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="bg-[#1a4a6b] text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-[#1a537a] transition-colors">
                Guardar cambios
            </button>
        </form>
    </div>

    {{-- Contraseña --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-1">Cambiar contraseña</h2>
        <p class="text-sm text-gray-500 mb-4">Mínimo 8 caracteres, mayúscula, minúscula y número.</p>

        @if(session('success_password'))
            <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                {{ session('success_password') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña actual</label>
                <input type="password" name="current_password" autocomplete="current-password"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/20 @error('current_password') border-red-400 @enderror">
                @error('current_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                <input type="password" name="password" autocomplete="new-password"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/20 @error('password') border-red-400 @enderror">
                @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation" autocomplete="new-password"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/20">
            </div>

            <button type="submit"
                    class="bg-[#1a4a6b] text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-[#1a537a] transition-colors">
                Actualizar contraseña
            </button>
        </form>
    </div>

    {{-- 2FA --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Verificación en dos pasos (2FA)</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Al iniciar sesión recibirás un código de 6 dígitos en tu correo para confirmar tu identidad.
                </p>
            </div>
            @if(auth()->user()->two_factor_enabled)
                <span class="shrink-0 inline-flex items-center gap-1.5 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-full px-3 py-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Activo
                </span>
            @else
                <span class="shrink-0 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-3 py-1">
                    Inactivo
                </span>
            @endif
        </div>

        @if(session('success') && str_contains(session('success'), '2FA'))
            <div class="mt-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-5">
            @if(auth()->user()->two_factor_enabled)
                <form method="POST" action="{{ route('2fa.disable') }}" x-data="{ open: false }">
                    @csrf
                    <button type="button" @click="open = true"
                            class="text-sm text-red-600 hover:text-red-700 font-medium border border-red-200 rounded-lg px-4 py-2 hover:bg-red-50 transition-colors">
                        Desactivar verificación en dos pasos
                    </button>

                    <div x-show="open" x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-transition>
                        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4" @click.stop>
                            <h3 class="text-base font-semibold text-gray-900 mb-1">Desactivar 2FA</h3>
                            <p class="text-sm text-gray-500 mb-4">Ingresá tu contraseña para confirmar.</p>
                            @error('password') <p class="text-sm text-red-500 mb-3">{{ $message }}</p> @enderror
                            <input type="password" name="password" placeholder="Contraseña actual"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-red-300">
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="open = false" class="text-sm text-gray-500 hover:text-gray-700">Cancelar</button>
                                <button type="submit" class="text-sm bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium">Desactivar</button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <form method="POST" action="{{ route('2fa.enable') }}">
                    @csrf
                    <button type="submit"
                            class="text-sm text-white bg-[#1a4a6b] hover:bg-[#1a537a] font-medium rounded-lg px-4 py-2 transition-colors">
                        Activar verificación en dos pasos
                    </button>
                </form>
                <p class="mt-2 text-xs text-gray-400">Recomendado para cuentas de administrador.</p>
            @endif
        </div>
    </div>

</div>
@endsection
