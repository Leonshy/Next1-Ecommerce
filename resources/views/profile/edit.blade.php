<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 2FA --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">Verificación en dos pasos (2FA)</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Agrega una capa extra de seguridad. Al iniciar sesión, recibirás un código de 6 dígitos en tu correo.
                            </p>
                        </header>

                        <div class="mt-6 flex items-center gap-4">
                            @if(auth()->user()->two_factor_enabled)
                                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-full px-3 py-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Activado
                                </span>

                                <form method="POST" action="{{ route('2fa.disable') }}" x-data="{ open: false }">
                                    @csrf
                                    <button type="button" @click="open = true"
                                            class="text-sm text-red-600 hover:underline font-medium">
                                        Desactivar 2FA
                                    </button>

                                    {{-- Modal confirmación --}}
                                    <div x-show="open" x-cloak
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                                         x-transition>
                                        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4" @click.stop>
                                            <h3 class="text-base font-semibold text-gray-900 mb-1">Desactivar verificación en dos pasos</h3>
                                            <p class="text-sm text-gray-500 mb-4">Ingresá tu contraseña actual para confirmar.</p>

                                            @error('password') <p class="text-sm text-red-500 mb-3">{{ $message }}</p> @enderror

                                            <input type="password" name="password" placeholder="Contraseña actual"
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-400">

                                            <div class="flex justify-end gap-3">
                                                <button type="button" @click="open = false"
                                                        class="text-sm text-gray-500 hover:text-gray-700">Cancelar</button>
                                                <button type="submit"
                                                        class="text-sm bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium">
                                                    Desactivar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 bg-gray-100 border border-gray-200 rounded-full px-3 py-1">
                                    Desactivado
                                </span>

                                <form method="POST" action="{{ route('2fa.enable') }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-sm text-blue-600 hover:underline font-medium">
                                        Activar 2FA (recomendado)
                                    </button>
                                </form>
                            @endif
                        </div>

                        @if(session('success') && str_contains(session('success'), '2FA'))
                            <p class="mt-3 text-sm text-green-600">{{ session('success') }}</p>
                        @endif
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
