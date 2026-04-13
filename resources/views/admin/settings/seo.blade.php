@extends('layouts.admin')
@section('title', 'Configuración SEO')

@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.settings.seo.update') }}">
        @csrf @method('PUT')

        @php
        $pageLabels = [
            'home'     => 'Inicio',
            'products' => 'Productos',
            'about_us' => 'Nosotros',
            'faq'      => 'FAQ',
            'global'   => 'Global (fallback)',
        ];
        @endphp

        <div x-data="{ tab: 'home' }" class="bg-white rounded-xl border border-gray-200 overflow-hidden">

            {{-- Tabs --}}
            <div class="flex border-b border-gray-200 overflow-x-auto">
                @foreach($pageLabels as $key => $label)
                <button type="button" @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                        class="px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Tab panels --}}
            @foreach($pageLabels as $key => $label)
            <div x-show="tab === '{{ $key }}'" class="p-6 space-y-4">
                <p class="text-xs text-gray-500 bg-gray-50 rounded-lg px-3 py-2">
                    Página: <strong>{{ $label }}</strong>
                    @if($key === 'global') — Estos valores se usan como fallback en cualquier página sin SEO propio. @endif
                </p>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="pages[{{ $key }}][title]"
                           value="{{ $settings[$key]->title }}"
                           placeholder="Título de la página (máx. 60 caracteres)"
                           maxlength="60"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta descripción</label>
                    <textarea name="pages[{{ $key }}][description]" rows="3"
                              placeholder="Descripción para buscadores (máx. 160 caracteres)"
                              maxlength="160"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ $settings[$key]->description }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Palabras clave</label>
                    <input type="text" name="pages[{{ $key }}][keywords]"
                           value="{{ $settings[$key]->keywords }}"
                           placeholder="Separadas por coma: tienda, ropa, ofertas"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL canónica</label>
                        <input type="url" name="pages[{{ $key }}][canonical_url]"
                               value="{{ $settings[$key]->canonical_url }}"
                               placeholder="https://next1.com/..."
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <x-admin.media-picker
                            name="pages[{{ $key }}][og_image]"
                            :value="$settings[$key]->og_image ?? ''"
                            label="OG Image"
                        />
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex justify-end mt-4">
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                Guardar SEO
            </button>
        </div>
    </form>
</div>
@endsection
