<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('account.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Mis Direcciones</h1>
            </div>
            <button x-data @click="$dispatch('open-address-form')"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
                + Nueva dirección
            </button>
        </div>

        {{-- Lista de direcciones --}}
        @if($addresses->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                @foreach($addresses as $address)
                    <div class="bg-white rounded-xl border {{ $address->is_default ? 'border-blue-500' : 'border-gray-200' }} p-5 relative">
                        @if($address->is_default)
                            <span class="absolute top-3 right-3 bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-medium">
                                Principal
                            </span>
                        @endif
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="font-semibold text-gray-900">{{ $address->label }}</span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p class="font-medium text-gray-900">{{ $address->recipient_name }}</p>
                            <p>{{ $address->phone }}</p>
                            <p>{{ $address->street_address }}
                                @if($address->house_number) {{ $address->house_number }}@endif
                            </p>
                            @if($address->neighborhood)
                                <p>{{ $address->neighborhood }}</p>
                            @endif
                            <p>{{ $address->city }}, {{ $address->department }}</p>
                            @if($address->reference)
                                <p class="text-gray-400 text-xs mt-1">Ref: {{ $address->reference }}</p>
                            @endif
                        </div>
                        <div class="mt-4 flex space-x-3">
                            <form method="POST" action="{{ route('account.addresses.delete', $address->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Eliminar esta dirección?')"
                                        class="text-xs text-red-500 hover:underline">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-400 bg-white rounded-xl border border-gray-200 mb-8">
                <p class="text-lg font-medium">No tenés direcciones guardadas</p>
                <p class="text-sm mt-1">Agregá una para agilizar tus próximas compras</p>
            </div>
        @endif

        {{-- Formulario nueva dirección --}}
        <div x-data="{ open: false }"
             x-on:open-address-form.window="open = true">
            <div x-show="open" class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-5">Nueva Dirección</h2>
                <form method="POST" action="{{ route('account.addresses.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Etiqueta *</label>
                        <input type="text" name="label" value="{{ old('label', 'Casa') }}" placeholder="Casa, Oficina..."
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('label')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del destinatario *</label>
                        <input type="text" name="recipient_name" value="{{ old('recipient_name') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('recipient_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departamento *</label>
                        <input type="text" name="department" value="{{ old('department') }}" placeholder="ej: Central"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('department')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad *</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="ej: Luque"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Barrio</label>
                        <input type="text" name="neighborhood" value="{{ old('neighborhood') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección (calle) *</label>
                        <input type="text" name="street_address" value="{{ old('street_address') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('street_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Entre calles</label>
                        <input type="text" name="cross_street_1" value="{{ old('cross_street_1') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número / Depto</label>
                        <input type="text" name="house_number" value="{{ old('house_number') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Referencias</label>
                        <input type="text" name="reference" value="{{ old('reference') }}"
                               placeholder="ej: Casa color verde, portón negro"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="sm:col-span-2 flex items-center space-x-2">
                        <input type="checkbox" name="is_default" value="1" id="is_default"
                               {{ old('is_default') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        <label for="is_default" class="text-sm text-gray-700">Establecer como dirección principal</label>
                    </div>

                    <div class="sm:col-span-2 flex justify-end space-x-3">
                        <button type="button" @click="open = false"
                                class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                            Guardar dirección
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
