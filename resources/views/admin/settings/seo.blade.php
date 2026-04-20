@extends('layouts.admin')
@section('title', 'Configuración SEO')

@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.settings.seo.update') }}">
        @csrf @method('PUT')

        @php
        $pageLabels = [
            'home'           => 'Inicio',
            'products'       => 'Productos',
            'about_us'       => 'Quiénes somos',
            'faq'            => 'FAQ',
            'terms'          => 'Términos y condiciones',
            'privacy_policy' => 'Políticas de privacidad',
            'gift_cards'     => 'Gift Cards',
            'global'         => 'Global (fallback)',
        ];
        @endphp

        <div x-data="{ tab: window.location.hash.slice(1) || 'home' }" class="bg-white rounded-xl border border-gray-200 overflow-hidden">

            {{-- Tabs --}}
            <div class="flex border-b border-gray-200 overflow-x-auto">
                @foreach($pageLabels as $key => $label)
                <button type="button" @click="tab = '{{ $key }}'; window.location.hash = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-b-2 border-[#1a4a6b] text-[#1a4a6b]' : 'text-gray-500 hover:text-gray-700'"
                        class="px-4 py-3 text-sm font-medium whitespace-nowrap transition-colors flex-shrink-0">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Tab panels --}}
            @foreach($pageLabels as $key => $label)
            @php $s = $settings[$key]; @endphp
            <div x-show="tab === '{{ $key }}'" class="p-6 space-y-5">

                <p class="text-xs text-gray-500 bg-gray-50 rounded-lg px-3 py-2">
                    Página: <strong>{{ $label }}</strong>
                    @if($key === 'global') — Estos valores se usan como fallback en cualquier página sin SEO propio. @endif
                </p>

                {{-- Meta título --}}
                <div x-data="{ len: {{ strlen($s->meta_title ?? '') }} }">
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-sm font-medium text-gray-700">Meta título</label>
                        <span class="text-xs" :class="len > 60 ? 'text-red-500 font-semibold' : 'text-gray-400'">
                            <span x-text="len"></span>/60
                        </span>
                    </div>
                    <input type="text" name="pages[{{ $key }}][meta_title]"
                           value="{{ $s->meta_title }}"
                           placeholder="Título de la página para buscadores"
                           maxlength="70"
                           x-on:input="len = $event.target.value.length"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    <p class="text-xs text-gray-400 mt-1">Recomendado: máx. 60 caracteres.</p>
                </div>

                {{-- Meta descripción --}}
                <div x-data="{ len: {{ strlen($s->meta_description ?? '') }} }">
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-sm font-medium text-gray-700">Meta descripción</label>
                        <span class="text-xs" :class="len > 160 ? 'text-red-500 font-semibold' : 'text-gray-400'">
                            <span x-text="len"></span>/160
                        </span>
                    </div>
                    <textarea name="pages[{{ $key }}][meta_description]" rows="3"
                              placeholder="Descripción para buscadores (máx. 160 caracteres)"
                              maxlength="200"
                              x-on:input="len = $event.target.value.length"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b] resize-none">{{ $s->meta_description }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Recomendado: máx. 160 caracteres.</p>
                </div>

                {{-- Keywords --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Palabras clave</label>
                    <input type="text" name="pages[{{ $key }}][keywords]"
                           value="{{ $s->keywords }}"
                           placeholder="tienda, tecnología, electrónica, paraguay"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    <p class="text-xs text-gray-400 mt-1">Separadas por coma.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- URL canónica --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL canónica</label>
                        <input type="url" name="pages[{{ $key }}][canonical_url]"
                               value="{{ $s->canonical_url }}"
                               placeholder="https://next1.com.py/..."
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                        <p class="text-xs text-gray-400 mt-1">Dejá vacío para usar la URL actual.</p>
                    </div>

                    {{-- OG Image --}}
                    <div x-data="{ ogUrl: '{{ addslashes($s->og_image ?? '') }}' }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Imagen para redes sociales (OG Image)</label>
                        <input type="text" name="pages[{{ $key }}][og_image]"
                               x-model="ogUrl"
                               placeholder="https://next1.com.py/imagen.jpg"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                        <template x-if="ogUrl">
                            <img :src="ogUrl" alt="OG preview"
                                 class="mt-2 h-20 rounded-lg border border-gray-200 object-cover"
                                 x-on:error="$el.style.display='none'" x-on:load="$el.style.display='block'">
                        </template>
                        <p class="text-xs text-gray-400 mt-1">Pegá la URL de la imagen. Recomendado: 1200×630 px.</p>
                    </div>
                </div>

                {{-- Preview SERP --}}
                @php $hasSerpData = $s->meta_title || $s->meta_description; @endphp
                @if($hasSerpData)
                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Vista previa en Google</p>
                    <div class="border border-gray-200 rounded-xl p-4 bg-white">
                        <p class="text-xs text-green-700 mb-0.5 truncate">{{ config('app.url') }}</p>
                        <p class="text-[#1a0dab] text-base font-medium leading-tight mb-1 line-clamp-1">
                            {{ $s->meta_title ?: 'Sin título' }}
                        </p>
                        <p class="text-sm text-gray-600 line-clamp-2">
                            {{ $s->meta_description ?: 'Sin descripción' }}
                        </p>
                    </div>
                </div>
                @endif

            </div>
            @endforeach
        </div>

        <div class="flex justify-end mt-4">
            <button type="submit"
                    class="px-6 py-2 text-white rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity"
                    style="background:#1a4a6b">
                Guardar SEO
            </button>
        </div>
    </form>
</div>
@endsection
