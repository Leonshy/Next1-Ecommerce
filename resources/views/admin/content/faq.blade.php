@extends('layouts.admin')
@section('title', 'Preguntas Frecuentes')

@section('content')
<form method="POST" action="{{ route('admin.content.faq.update') }}"
      x-data="{
          items: {{ json_encode(array_values($faqs)) }},
          add() { this.items.push({ question: '', answer: '' }); },
          remove(i) { this.items.splice(i, 1); }
      }">
    @csrf

    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-6">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Preguntas y Respuestas</h2>
                <p class="text-xs text-gray-400 mt-0.5">Estas se muestran en la página pública de Preguntas Frecuentes.</p>
            </div>
            <button type="button" @click="add()"
                    class="text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                + Agregar pregunta
            </button>
        </div>

        <div class="space-y-4">
            <template x-for="(item, i) in items" :key="i">
                <div class="border border-gray-200 rounded-xl p-5 relative">
                    <button type="button" @click="remove(i)"
                            class="absolute top-3 right-3 w-6 h-6 flex items-center justify-center text-gray-300 hover:text-red-400 hover:bg-red-50 rounded transition-colors text-xs font-bold">
                        ✕
                    </button>
                    <div class="space-y-3 pr-8">
                        <div class="flex items-center gap-2 text-xs text-gray-400 font-medium">
                            <span class="w-5 h-5 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 font-semibold" x-text="i + 1"></span>
                            Pregunta
                        </div>
                        <input type="text" :name="'question[' + i + ']'" x-model="item.question"
                               placeholder="Ej: ¿Cuánto tarda el envío?"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <label class="block text-xs text-gray-400 font-medium">Respuesta</label>
                        <textarea :name="'answer[' + i + ']'" x-model="item.answer" rows="3"
                                  placeholder="Escribí la respuesta aquí..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>
            </template>

            <div x-show="items.length === 0"
                 class="text-center py-16 text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                <p class="text-lg mb-2">Sin preguntas</p>
                <p class="text-sm">Hacé clic en "+ Agregar pregunta" para comenzar.</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                    class="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-gray-700 transition-colors">
                Guardar cambios
            </button>
        </div>
    </div>
</form>
@endsection
