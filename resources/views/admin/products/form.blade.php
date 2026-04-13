@extends('layouts.admin')
@section('title', isset($product) ? 'Editar Producto' : 'Nuevo Producto')
@section('content')

@php
    $existingImages  = isset($product) ? $product->productImages->toArray() : [];
    $mainUrl         = isset($product) ? ($product->mainImage?->image_url ?? ($existingImages[0]['image_url'] ?? '')) : '';
    $existingTags    = isset($product) ? ($product->tags ?? []) : [];
    $tagsCatalog     = isset($allTags) ? $allTags->toArray() : [];
@endphp

{{-- Page header --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.productos.index') }}"
       class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h2 class="text-xl font-bold text-gray-900">{{ isset($product) ? 'Editar Producto' : 'Nuevo Producto' }}</h2>
        <p class="text-sm text-gray-500">{{ isset($product) ? 'Modificá los datos del producto' : 'Completá los datos del nuevo producto' }}</p>
    </div>
</div>

<form method="POST"
      action="{{ isset($product) ? route('admin.productos.update', $product->id) : route('admin.productos.store') }}"
      x-data="productForm({{ json_encode($existingImages) }}, {{ json_encode($mainUrl) }}, {{ json_encode($existingTags) }}, {{ json_encode($tagsCatalog) }})">
    @csrf
    @if(isset($product)) @method('PUT') @endif

    {{-- Errores globales --}}
    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-red-700 mb-1">Por favor corregí los errores:</p>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Columna principal ────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Información básica --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-900 text-base">Información básica</h3>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]" required>
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
                    <textarea name="description" rows="4"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b] resize-y">{{ old('description', $product->description ?? '') }}</textarea>
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">Gs.</span>
                            <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" step="1" min="0"
                                   class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]" required>
                        </div>
                        @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio original</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">Gs.</span>
                            <input type="number" name="original_price" value="{{ old('original_price', $product->original_price ?? '') }}" step="1" min="0"
                                   class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]"
                                   placeholder="Precio tachado">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Stock</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Categoría</label>
                        <select name="category_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b] bg-white">
                            <option value="">Sin categoría</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->parent ? $cat->parent->name . ' › ' : '' }}{{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Marca</label>
                        <select name="brand_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b] bg-white">
                            <option value="">Sin marca</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Badge</label>
                        <input type="text" name="badge" value="{{ old('badge', $product->badge ?? '') }}"
                               placeholder="Ej: NUEVO, -20%"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    </div>
                </div>
            </div>

            {{-- Etiquetas (tags) --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 text-base mb-1">Etiquetas</h3>
                <p class="text-xs text-gray-500 mb-3">Escribí para buscar etiquetas existentes o crear nuevas. Enter o coma para confirmar.</p>

                {{-- Hidden inputs --}}
                <template x-for="tag in tags" :key="tag">
                    <input type="hidden" name="tags[]" :value="tag">
                </template>

                {{-- Chip container + input --}}
                <div class="relative">
                    <div class="min-h-[42px] w-full border border-gray-200 rounded-lg px-2 py-1.5 flex flex-wrap gap-1.5 focus-within:ring-2 focus-within:ring-[#1a4a6b]/30 focus-within:border-[#1a4a6b] transition-colors cursor-text"
                         @click="$refs.tagInput.focus()">
                        <template x-for="(tag, i) in tags" :key="tag">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium"
                                  :style="['destacado','nuevo','ofertas'].includes(tag)
                                      ? 'background:rgba(224,123,29,0.12); color:#e07b1d'
                                      : 'background:hsl(207 60% 28% / 0.1); color:#1a4a6b'">
                                <span x-text="tagLabel(tag)"></span>
                                <button type="button" @click.stop="removeTag(i)"
                                        class="flex items-center justify-center w-3.5 h-3.5 rounded-full hover:bg-[#1a4a6b]/20 transition-colors leading-none">
                                    ×
                                </button>
                            </span>
                        </template>
                        <input x-ref="tagInput"
                               type="text"
                               x-model="tagInput"
                               @keydown="handleTagKey($event)"
                               @input="showSuggestions = true; activeSuggestion = -1"
                               @focus="showSuggestions = true"
                               @blur="setTimeout(() => showSuggestions = false, 150)"
                               autocomplete="off"
                               placeholder="Buscar o agregar etiqueta..."
                               style="outline:none !important; box-shadow:none !important; border:none !important;"
                               class="flex-1 min-w-[140px] outline-none ring-0 border-0 bg-transparent text-sm text-gray-700 placeholder-gray-400 py-0.5">
                    </div>

                    {{-- Suggestions dropdown --}}
                    <div x-show="showSuggestions && filteredSuggestions.length > 0"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute z-30 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                        <ul class="max-h-52 overflow-y-auto py-1">
                            <template x-for="(s, idx) in filteredSuggestions" :key="s.slug ?? s.name">
                                <li @mousedown.prevent="pickSuggestion(s)"
                                    :class="idx === activeSuggestion ? 'bg-[#1a4a6b]/8 text-[#1a4a6b]' : 'text-gray-700 hover:bg-gray-50'"
                                    class="flex items-center gap-2 px-3 py-2 text-sm cursor-pointer transition-colors">
                                    <template x-if="!s._new">
                                        <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a0 0 0 014-4z"/>
                                        </svg>
                                    </template>
                                    <template x-if="s._new">
                                        <svg class="w-3.5 h-3.5 shrink-0 text-[#e07b1d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </template>
                                    <span x-text="s._new ? 'Crear: ' + s.name : s.name"></span>
                                    <template x-if="s._new">
                                        <span class="ml-auto text-xs text-[#e07b1d] font-medium">Nueva</span>
                                    </template>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Visibilidad --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 text-base mb-1">Visibilidad</h3>
                <p class="text-xs text-gray-500 mb-4">Activar Destacado, Nuevo u Oferta agrega automáticamente la etiqueta correspondiente.</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

                    {{-- Activo (sin tag) --}}
                    @php $checked = old('is_active', isset($product) ? ($product->is_active ? '1' : '') : '1') === '1'; @endphp
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative shrink-0">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ $checked ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 peer-checked:bg-[#1a4a6b] rounded-full transition-colors duration-200 peer-focus:ring-2 peer-focus:ring-[#1a4a6b]/30"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-sm text-gray-700">Activo</span>
                    </label>

                    {{-- Destacado → tag 'destacado' --}}
                    @php $checked = old('is_featured', isset($product) ? ($product->is_featured ? '1' : '') : '') === '1'; @endphp
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative shrink-0">
                            <input type="checkbox" name="is_featured" value="1"
                                   {{ $checked ? 'checked' : '' }}
                                   @change="syncFlagTag($event.target.checked, 'destacado')"
                                   class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 peer-checked:bg-[#1a4a6b] rounded-full transition-colors duration-200 peer-focus:ring-2 peer-focus:ring-[#1a4a6b]/30"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-sm text-gray-700">Destacado</span>
                    </label>

                    {{-- Nuevo → tag 'nuevo' --}}
                    @php $checked = old('is_new', isset($product) ? ($product->is_new ? '1' : '') : '') === '1'; @endphp
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative shrink-0">
                            <input type="checkbox" name="is_new" value="1"
                                   {{ $checked ? 'checked' : '' }}
                                   @change="syncFlagTag($event.target.checked, 'nuevo')"
                                   class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 peer-checked:bg-[#1a4a6b] rounded-full transition-colors duration-200 peer-focus:ring-2 peer-focus:ring-[#1a4a6b]/30"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-sm text-gray-700">Nuevo</span>
                    </label>

                    {{-- Oferta → tag 'ofertas' --}}
                    @php $checked = old('is_hot_deal', isset($product) ? ($product->is_hot_deal ? '1' : '') : '') === '1'; @endphp
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative shrink-0">
                            <input type="checkbox" name="is_hot_deal" value="1"
                                   {{ $checked ? 'checked' : '' }}
                                   @change="syncFlagTag($event.target.checked, 'ofertas')"
                                   class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 peer-checked:bg-[#1a4a6b] rounded-full transition-colors duration-200 peer-focus:ring-2 peer-focus:ring-[#1a4a6b]/30"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-sm text-gray-700">Oferta</span>
                    </label>

                </div>
            </div>

        </div>{{-- /col-span-2 --}}

        {{-- ── Sidebar ──────────────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Publicar --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-3">
                <h3 class="font-semibold text-gray-900 text-base">Publicar</h3>
                <button type="submit"
                        class="w-full py-2.5 rounded-lg text-sm font-semibold text-white transition-colors"
                        style="background:#1a4a6b">
                    {{ isset($product) ? 'Guardar cambios' : 'Crear producto' }}
                </button>
                <a href="{{ route('admin.productos.index') }}"
                   class="block w-full text-center py-2.5 rounded-lg text-sm font-medium border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                @if(isset($product))
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Creado: {{ $product->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Actualizado: {{ $product->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                @endif
            </div>

            {{-- Galería --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="font-semibold text-gray-900 text-base mb-1">Galería de medios</h3>
                <p class="text-xs text-gray-500 mb-4">Clic en una imagen para marcarla como principal. Los videos no pueden ser portada.</p>

                {{-- Hidden inputs --}}
                <template x-for="img in images" :key="img.url">
                    <input type="hidden" name="gallery_images[]" :value="img.url">
                </template>
                <input type="hidden" name="gallery_main" :value="mainUrl">

                {{-- Grid --}}
                <div class="grid grid-cols-3 gap-2 mb-3" x-show="images.length > 0">
                    <template x-for="(img, index) in images" :key="img.url">
                        <div class="relative group aspect-square border-2 rounded-lg overflow-hidden transition-colors"
                             :class="[img.url === mainUrl ? 'border-[#1a4a6b]' : 'border-gray-200', !isVideo(img.url) ? 'cursor-pointer hover:border-gray-400' : 'cursor-default opacity-70']"
                             @click="!isVideo(img.url) ? setMain(img.url) : null">
                            <img x-show="!isVideo(img.url)" :src="img.url" :alt="img.alt" class="w-full h-full object-cover">
                            <div x-show="isVideo(img.url)" class="w-full h-full flex flex-col items-center justify-center bg-gray-800 text-white gap-1">
                                <svg class="w-6 h-6 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                </svg>
                                <span class="text-[8px] font-bold uppercase opacity-60">VIDEO</span>
                            </div>
                            <div x-show="img.url === mainUrl"
                                 class="absolute top-1 left-1 text-white text-[8px] font-bold px-1.5 py-0.5 rounded leading-tight"
                                 style="background:#1a4a6b">PRINCIPAL</div>
                            <button type="button" @click.stop="removeImage(index)"
                                    class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-xs leading-none">
                                ×
                            </button>
                        </div>
                    </template>
                </div>

                <div x-show="images.length === 0"
                     class="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center text-gray-400 mb-3">
                    <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-xs">Sin archivos</p>
                </div>

                <button type="button" @click="openPicker()"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar archivo
                </button>
            </div>

        </div>{{-- /sidebar --}}

    </div>{{-- /grid --}}

    {{-- ── Modal picker ──────────────────────────────────────────────── --}}
    <div x-show="pickerOpen"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @click.self="pickerOpen = false">

        <div class="bg-white rounded-xl w-full max-w-4xl max-h-[80vh] flex flex-col shadow-2xl">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <h3 class="font-semibold text-gray-900">Agregar a la galería</h3>
                    <div class="flex border border-gray-200 rounded-lg overflow-hidden text-sm">
                        <button type="button" @click="pickerTab = 'library'" :class="pickerTab === 'library' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'" class="px-3 py-1.5 transition-colors">Biblioteca</button>
                        <button type="button" @click="pickerTab = 'upload'"  :class="pickerTab === 'upload'  ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'" class="px-3 py-1.5 transition-colors">Subir</button>
                        <button type="button" @click="pickerTab = 'url'"     :class="pickerTab === 'url'     ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'" class="px-3 py-1.5 transition-colors">URL</button>
                    </div>
                </div>
                <button type="button" @click="pickerOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Biblioteca --}}
            <div x-show="pickerTab === 'library'" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-3 border-b border-gray-100 flex-shrink-0">
                    <input type="text" x-model="pickerSearch" placeholder="Buscar..." class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <div x-show="pickerLoading" class="flex items-center justify-center h-32 text-gray-400 text-sm">Cargando...</div>
                    <div x-show="!pickerLoading && filteredPickerFiles.length === 0" class="text-center py-12 text-gray-400 text-sm">
                        No hay archivos.
                        <button type="button" @click="pickerTab = 'upload'" class="text-blue-600 block mx-auto mt-1 hover:underline">Subir un archivo</button>
                    </div>
                    <div x-show="!pickerLoading" class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                        <template x-for="file in filteredPickerFiles" :key="file.id">
                            <div class="relative aspect-square rounded-lg overflow-hidden cursor-pointer border-2 transition-all"
                                 :class="isPickerSelected(file.url) ? 'border-blue-500 ring-2 ring-blue-300' : 'border-transparent hover:border-gray-300'"
                                 @click="togglePickerSelect(file)">
                                <img x-show="file.is_image" :src="file.url" :alt="file.alt_text ?? ''" class="w-full h-full object-cover">
                                <div x-show="file.is_video" class="w-full h-full flex flex-col items-center justify-center bg-gray-800 text-white gap-1">
                                    <svg class="w-7 h-7 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                    </svg>
                                    <span class="text-[9px] font-bold uppercase" x-text="(file.file_name ?? '').split('.').pop()"></span>
                                </div>
                                <div x-show="isPickerSelected(file.url)" class="absolute inset-0 bg-blue-500/20 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600 drop-shadow bg-white rounded-full p-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-200 flex items-center justify-between flex-shrink-0">
                    <span class="text-sm text-gray-500"><span x-text="pickerSelected.length"></span> seleccionada(s)</span>
                    <div class="flex gap-2">
                        <button type="button" @click="pickerOpen = false" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</button>
                        <button type="button" @click="confirmPicker()" :disabled="pickerSelected.length === 0" :class="pickerSelected.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Agregar seleccionadas</button>
                    </div>
                </div>
            </div>

            {{-- Subir --}}
            <div x-show="pickerTab === 'upload'" class="flex flex-col flex-1 overflow-hidden">
                <div class="flex-1 flex flex-col items-center justify-center p-8"
                     @dragover.prevent="uploadDragging = true" @dragleave.prevent="uploadDragging = false" @drop.prevent="handleGalleryDrop($event)">
                    <label class="w-full cursor-pointer">
                        <div :class="uploadDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-gray-50'"
                             class="border-2 border-dashed rounded-2xl p-12 text-center transition-colors">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-gray-600 font-medium mb-1">Arrastrá archivos aquí</p>
                            <p class="text-sm text-gray-400">o hacé clic para seleccionar</p>
                            <p class="text-xs text-gray-400 mt-2">Máximo 20 MB · Imágenes y videos</p>
                        </div>
                        <input type="file" class="hidden" accept="image/*,video/*" multiple @change="handleGalleryUpload($event)">
                    </label>
                    <div x-show="galleryUploading" class="mt-4 w-full max-w-sm">
                        <div class="flex justify-between text-sm text-gray-600 mb-1"><span>Subiendo...</span><span x-text="galleryUploadProgress + '%'"></span></div>
                        <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-blue-600 h-2 rounded-full transition-all" :style="'width:' + galleryUploadProgress + '%'"></div></div>
                    </div>
                </div>
            </div>

            {{-- URL --}}
            <div x-show="pickerTab === 'url'" class="flex flex-col flex-1 overflow-hidden">
                <div class="flex-1 flex flex-col items-center justify-center p-8 gap-4">
                    <div class="w-full max-w-md">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pegá la URL del archivo</label>
                        <input type="url" x-model="manualUrl" placeholder="https://ejemplo.com/imagen.jpg"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div x-show="manualUrl" class="mt-3 rounded-lg overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center h-36">
                            <img x-show="!isVideo(manualUrl)" :src="manualUrl" alt="Preview" class="max-h-full max-w-full object-contain" x-on:error="$el.style.display='none'">
                            <div x-show="isVideo(manualUrl)" class="flex flex-col items-center text-gray-400 gap-2">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                                <span class="text-sm">Video detectado</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" @click="addManualUrl()" :disabled="!manualUrl"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 disabled:opacity-40 transition-colors">
                        Agregar URL
                    </button>
                </div>
            </div>
        </div>
    </div>

</form>

@push('scripts')
<script>
const VIDEO_EXT = /\.(mp4|webm|ogg|ogv|mov|avi|mkv|flv|wmv)(\?.*)?$/i;

function productForm(existingImages, existingMain, existingTags, catalogTags) {
    return {
        // Gallery
        images:  existingImages.map(img => ({ url: img.image_url, alt: img.alt_text ?? '', is_video: VIDEO_EXT.test(img.image_url) })),
        mainUrl: existingMain,

        // Tags
        tags:            existingTags || [],
        tagInput:        '',
        catalog:         catalogTags || [],   // [{name, slug}]
        showSuggestions: false,
        activeSuggestion: -1,

        // Picker
        pickerOpen: false, pickerTab: 'library', pickerLoading: false,
        pickerSearch: '', pickerFiles: [], pickerSelected: [],
        uploadDragging: false, galleryUploading: false, galleryUploadProgress: 0,
        manualUrl: '',

        // ── Init: sync both directions on page load + watch tags ──────────
        init() {
            const flagMap     = { is_featured: 'destacado', is_new: 'nuevo', is_hot_deal: 'ofertas' };
            const slugToField = { destacado: 'is_featured', nuevo: 'is_new', ofertas: 'is_hot_deal' };

            // Toggle ON → add tag (initial load)
            Object.entries(flagMap).forEach(([field, slug]) => {
                const checkbox = this.$el.querySelector(`input[name="${field}"]`);
                if (checkbox && checkbox.checked && !this.tags.includes(slug)) {
                    this.tags.push(slug);
                }
            });

            // Watch tags array → update checkboxes when tags are added/removed
            this.$watch('tags', (newTags) => {
                Object.entries(slugToField).forEach(([slug, field]) => {
                    const checkbox = this.$el.querySelector(`input[name="${field}"]`);
                    if (checkbox) checkbox.checked = newTags.includes(slug);
                });
            });
        },

        // ── Tags ──────────────────────────────────────────────────────────

        /** Display name for a slug: use catalog if available */
        tagLabel(slug) {
            const found = this.catalog.find(c => c.slug === slug);
            return found ? found.name : slug;
        },

        /** Slugify a raw input string */
        toSlug(raw) {
            return raw.trim().toLowerCase()
                .replace(/[,\s]+/g, '-')
                .replace(/[^a-z0-9\-áéíóúñü]/g, '');
        },

        /** Filtered suggestions: existing matches + optional "create new" */
        get filteredSuggestions() {
            const q = this.tagInput.trim().toLowerCase();
            if (!q) return this.catalog.filter(c => !this.tags.includes(c.slug)).slice(0, 8);
            const matches = this.catalog.filter(c =>
                (c.name.toLowerCase().includes(q) || c.slug.includes(q)) &&
                !this.tags.includes(c.slug)
            );
            const slug = this.toSlug(q);
            const exactExists = this.catalog.some(c => c.slug === slug);
            if (!exactExists && slug) {
                matches.push({ name: this.tagInput.trim(), slug, _new: true });
            }
            return matches.slice(0, 10);
        },

        /** Pick a suggestion from the dropdown */
        async pickSuggestion(s) {
            if (s._new) {
                await this.createAndAdd(s.name, s.slug);
            } else {
                if (!this.tags.includes(s.slug)) this.tags.push(s.slug);
            }
            this.tagInput = '';
            this.showSuggestions = false;
            this.activeSuggestion = -1;
            this.$nextTick(() => this.$refs.tagInput.focus());
        },

        /** Create tag via API then add to chips + catalog */
        async createAndAdd(name, slug) {
            if (this.tags.includes(slug)) return;
            // Optimistically add
            this.tags.push(slug);
            // Ensure in local catalog for label display
            if (!this.catalog.find(c => c.slug === slug)) {
                this.catalog.push({ name, slug });
            }
            // Persist to server
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('{{ route("admin.etiquetas.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ name }),
                });
                if (res.ok) {
                    const data = await res.json();
                    // Update slug in tags/catalog in case server normalised it differently
                    const idx = this.tags.indexOf(slug);
                    if (idx !== -1 && data.slug !== slug) {
                        this.tags.splice(idx, 1, data.slug);
                    }
                    const cIdx = this.catalog.findIndex(c => c.slug === slug);
                    if (cIdx !== -1) this.catalog[cIdx] = { name: data.name, slug: data.slug };
                }
            } catch (err) {
                console.warn('No se pudo crear la etiqueta en el servidor:', err);
            }
        },

        /** Add tag from raw input (Enter / comma / blur) */
        async addTag() {
            const raw  = this.tagInput.trim();
            if (!raw) return;
            const slug = this.toSlug(raw);
            if (!slug) { this.tagInput = ''; return; }

            const inCatalog = this.catalog.find(c => c.slug === slug);
            if (inCatalog) {
                if (!this.tags.includes(slug)) this.tags.push(slug);
                this.tagInput = '';
            } else {
                await this.createAndAdd(raw, slug);
                this.tagInput = '';
            }
            this.showSuggestions = false;
            this.activeSuggestion = -1;
        },

        removeTag(i) { this.tags.splice(i, 1); },

        /**
         * Called when is_featured / is_new / is_hot_deal toggle changes.
         * checked=true  → ensure the tag slug is in the list
         * checked=false → remove the tag slug from the list
         * Protected tags (destacado, nuevo, ofertas) cannot be manually removed
         * while the corresponding toggle is ON — but here we only react to the toggle.
         */
        syncFlagTag(checked, slug) {
            if (checked) {
                if (!this.tags.includes(slug)) this.tags.push(slug);
            } else {
                const i = this.tags.indexOf(slug);
                if (i !== -1) this.tags.splice(i, 1);
            }
        },

        handleTagKey(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                if (this.activeSuggestion >= 0 && this.filteredSuggestions[this.activeSuggestion]) {
                    this.pickSuggestion(this.filteredSuggestions[this.activeSuggestion]);
                } else {
                    this.addTag();
                }
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.activeSuggestion = Math.min(this.activeSuggestion + 1, this.filteredSuggestions.length - 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.activeSuggestion = Math.max(this.activeSuggestion - 1, -1);
            } else if (e.key === 'Escape') {
                this.showSuggestions = false;
                this.activeSuggestion = -1;
            } else if (e.key === 'Backspace' && !this.tagInput && this.tags.length) {
                this.tags.pop();
            }
        },

        // ── Gallery ───────────────────────────────────────────────────────
        isVideo(url) { return VIDEO_EXT.test(url ?? ''); },

        get filteredPickerFiles() {
            const q = this.pickerSearch.toLowerCase();
            return q ? this.pickerFiles.filter(f => (f.file_name ?? '').toLowerCase().includes(q) || (f.alt_text ?? '').toLowerCase().includes(q)) : this.pickerFiles;
        },

        async openPicker() {
            this.pickerOpen = true; this.pickerTab = 'library';
            this.pickerSelected = []; this.pickerSearch = ''; this.manualUrl = '';
            if (this.pickerFiles.length === 0) await this.loadPickerFiles();
        },

        async loadPickerFiles() {
            this.pickerLoading = true;
            try {
                const res  = await fetch('{{ route('admin.media.picker') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                const files = Array.isArray(data) ? data : (data.files ?? []);
                this.pickerFiles = files
                    .filter(f => (f.mime_type ?? '').startsWith('image/') || (f.mime_type ?? '').startsWith('video/'))
                    .map(f => ({ ...f, url: f.file_url, is_image: (f.mime_type ?? '').startsWith('image/'), is_video: (f.mime_type ?? '').startsWith('video/') }));
            } catch(e) { this.pickerFiles = []; }
            this.pickerLoading = false;
        },

        isPickerSelected(url) { return this.pickerSelected.includes(url); },
        togglePickerSelect(file) {
            const url = file.url;
            this.pickerSelected.includes(url) ? this.pickerSelected = this.pickerSelected.filter(u => u !== url) : this.pickerSelected.push(url);
        },

        confirmPicker() {
            this._addUrls(this.pickerSelected.map(url => {
                const f = this.pickerFiles.find(x => x.url === url);
                return { url, alt: f?.alt_text ?? f?.file_name ?? '', is_video: f?.is_video ?? false };
            }));
            this.pickerSelected = []; this.pickerOpen = false;
        },

        addManualUrl() {
            if (!this.manualUrl) return;
            this._addUrls([{ url: this.manualUrl, alt: '', is_video: this.isVideo(this.manualUrl) }]);
            this.manualUrl = ''; this.pickerOpen = false;
        },

        _addUrls(items) {
            const existing = this.images.map(i => i.url);
            items.forEach(item => { if (!existing.includes(item.url)) this.images.push(item); });
            if (!this.mainUrl || this.isVideo(this.mainUrl)) {
                const firstImg = this.images.find(i => !i.is_video);
                if (firstImg) this.mainUrl = firstImg.url;
            }
        },

        setMain(url) { if (!this.isVideo(url)) this.mainUrl = url; },

        removeImage(index) {
            const removed = this.images.splice(index, 1)[0];
            if (removed.url === this.mainUrl) {
                const firstImg = this.images.find(i => !i.is_video);
                this.mainUrl = firstImg?.url ?? '';
            }
        },

        handleGalleryDrop(e) {
            this.uploadDragging = false;
            const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/') || f.type.startsWith('video/'));
            if (files.length) this.uploadGalleryFiles(files);
        },

        handleGalleryUpload(e) {
            const files = Array.from(e.target.files);
            if (files.length) this.uploadGalleryFiles(files);
            e.target.value = '';
        },

        async uploadGalleryFiles(fileList) {
            this.galleryUploading = true; this.galleryUploadProgress = 0;
            const formData = new FormData();
            fileList.forEach(f => formData.append('files[]', f));
            formData.append('_token', '{{ csrf_token() }}');
            try {
                const xhr = new XMLHttpRequest();
                xhr.upload.onprogress = e => { if (e.lengthComputable) this.galleryUploadProgress = Math.round((e.loaded / e.total) * 100); };
                await new Promise((resolve, reject) => {
                    xhr.onload = () => {
                        if (xhr.status === 201) {
                            const data = JSON.parse(xhr.responseText);
                            const uploaded = (data.uploaded ?? []).map(f => ({ ...f, url: f.file_url, is_image: (f.mime_type ?? '').startsWith('image/'), is_video: (f.mime_type ?? '').startsWith('video/') }));
                            this.pickerFiles = [...uploaded, ...this.pickerFiles];
                            this._addUrls(uploaded.map(f => ({ url: f.url, alt: f.alt_text ?? f.file_name ?? '', is_video: f.is_video })));
                            this.pickerTab = 'library'; resolve();
                        } else reject();
                    };
                    xhr.onerror = reject;
                    xhr.open('POST', '{{ route('admin.media.upload') }}');
                    xhr.send(formData);
                });
            } catch { alert('Error al subir el archivo.'); }
            finally { this.galleryUploading = false; this.galleryUploadProgress = 0; }
        },
    };
}

</script>
@endpush

@endsection
