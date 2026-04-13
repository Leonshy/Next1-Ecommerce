@extends('layouts.admin')
@section('title', 'Quiénes Somos')

@section('content')
<form method="POST" action="{{ route('admin.content.about-us.update') }}"
      x-data="{
          values: {{ json_encode($data['values'] ?? []) }},
          addValue() { this.values.push({ icon: 'Heart', title: '', description: '' }); },
          removeValue(i) { this.values.splice(i, 1); }
      }">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Columna izquierda: Título, Misión, Visión, CTA --}}
        <div class="space-y-5">
            <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Información General</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título de la página *</label>
                    <input type="text" name="title" value="{{ $data['title'] ?? 'Sobre Nosotros' }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                    <input type="text" name="subtitle" value="{{ $data['subtitle'] ?? '' }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Misión y Visión</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Misión</label>
                    <textarea name="mission" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $data['mission'] ?? '' }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Visión</label>
                    <textarea name="vision" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $data['vision'] ?? '' }}</textarea>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Call to Action</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título del CTA</label>
                    <input type="text" name="ctaTitle" value="{{ $data['ctaTitle'] ?? '' }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción del CTA</label>
                    <textarea name="ctaDescription" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $data['ctaDescription'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        {{-- Columna derecha: Valores --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                <h2 class="text-base font-semibold text-gray-900">Valores de la empresa</h2>
                <button type="button" @click="addValue()"
                        class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition-colors">
                    + Agregar valor
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(val, i) in values" :key="i">
                    <div class="border border-gray-200 rounded-lg p-4 relative">
                        <button type="button" @click="removeValue(i)"
                                class="absolute top-2 right-2 text-gray-300 hover:text-red-400 transition-colors text-xs">
                            ✕
                        </button>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Ícono</label>
                                <select :name="'value_icon[' + i + ']'" x-model="val.icon"
                                        class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="Heart">Corazón</option>
                                    <option value="Users">Personas</option>
                                    <option value="Award">Premio</option>
                                    <option value="Truck">Envío</option>
                                    <option value="Target">Objetivo</option>
                                    <option value="Eye">Ojo</option>
                                    <option value="Star">Estrella</option>
                                    <option value="Shield">Escudo</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Título</label>
                                <input type="text" :name="'value_title[' + i + ']'" x-model="val.title"
                                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Descripción</label>
                                <textarea :name="'value_description[' + i + ']'" x-model="val.description" rows="2"
                                          class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                            </div>
                        </div>
                    </div>
                </template>
                <p x-show="values.length === 0" class="text-sm text-gray-400 text-center py-8">
                    No hay valores. Hacé clic en "+ Agregar valor" para comenzar.
                </p>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <button type="submit"
                class="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-gray-700 transition-colors">
            Guardar cambios
        </button>
    </div>
</form>
@endsection
