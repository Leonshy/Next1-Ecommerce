@php $e = $editing ?? false; @endphp

{{-- ── Imagen con picker integrado ──────────────────────────────────────── --}}
<div x-data="{
        currentUrl: '',
        pickerOpen: false,
        pickerTab: 'library',
        pickerFiles: [],
        pickerLoading: false,
        pickerSearch: '',
        manualUrl: '',

        get filteredFiles() {
            const q = this.pickerSearch.toLowerCase();
            return q ? this.pickerFiles.filter(f => (f.file_name ?? '').toLowerCase().includes(q)) : this.pickerFiles;
        },

        async openPicker() {
            this.pickerOpen = true;
            this.pickerTab  = 'library';
            if (!this.pickerFiles.length) await this.loadFiles();
        },

        async loadFiles() {
            this.pickerLoading = true;
            try {
                const res  = await fetch('{{ route('admin.media.picker') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                const files = Array.isArray(data) ? data : (data.files ?? []);
                this.pickerFiles = files
                    .filter(f => (f.mime_type ?? '').startsWith('image/'))
                    .map(f => ({ ...f, url: f.file_url }));
            } catch(e) { this.pickerFiles = []; }
            finally { this.pickerLoading = false; }
        },

        select(file) { this.currentUrl = file.url; this.pickerOpen = false; },

        confirmUrl() {
            if (this.manualUrl) { this.currentUrl = this.manualUrl; this.manualUrl = ''; this.pickerOpen = false; }
        },
    }"
    @if($e) x-on:hero-slide-set-image.window="currentUrl = $event.detail.url" @endif>

    <label class="block text-sm font-medium text-gray-700 mb-2">Imagen del slide *</label>
    <input type="hidden" name="image_url" :value="currentUrl">

    {{-- Preview --}}
    <div x-show="currentUrl" class="relative mb-2 rounded-lg overflow-hidden border border-gray-200 bg-gray-100" style="aspect-ratio:16/6">
        <img :src="currentUrl" alt="" class="w-full h-full object-cover">
        <button type="button" @click="currentUrl = ''"
                class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-bold leading-none hover:bg-red-600">
            ×
        </button>
    </div>
    <div x-show="!currentUrl" class="mb-2 border-2 border-dashed border-gray-200 rounded-lg p-6 text-center text-gray-400">
        <svg class="w-8 h-8 mx-auto mb-1 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-xs">Sin imagen</p>
    </div>

    {{-- Botón abrir picker --}}
    <button type="button" @click="openPicker()"
            class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Agregar archivo
    </button>

    {{-- ── Modal picker ─────────────────────────────────────────────────── --}}
    <div x-show="pickerOpen" x-cloak
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         @keydown.escape.window="pickerOpen = false">
        <div class="absolute inset-0 bg-black/50" @click="pickerOpen = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col z-10">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <h3 class="font-semibold text-gray-900 text-sm">Seleccionar imagen</h3>
                    <div class="flex border border-gray-200 rounded-lg overflow-hidden text-xs">
                        <button type="button" @click="pickerTab = 'library'"
                                :class="pickerTab === 'library' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                class="px-3 py-1.5 transition-colors">Biblioteca</button>
                        <button type="button" @click="pickerTab = 'url'"
                                :class="pickerTab === 'url' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                class="px-3 py-1.5 transition-colors">URL</button>
                    </div>
                </div>
                <button type="button" @click="pickerOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Biblioteca --}}
            <div x-show="pickerTab === 'library'" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-3 border-b border-gray-100 flex-shrink-0">
                    <input type="text" x-model.debounce.300ms="pickerSearch" placeholder="Buscar..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                </div>
                <div class="flex-1 overflow-y-auto p-3">
                    <div x-show="pickerLoading" class="grid grid-cols-5 gap-2">
                        <template x-for="i in 15" :key="i">
                            <div class="aspect-square bg-gray-100 rounded-lg animate-pulse"></div>
                        </template>
                    </div>
                    <div x-show="!pickerLoading && filteredFiles.length === 0"
                         class="text-center py-12 text-gray-400 text-sm">
                        No hay imágenes en la biblioteca.
                    </div>
                    <div x-show="!pickerLoading" class="grid grid-cols-5 gap-2">
                        <template x-for="file in filteredFiles" :key="file.id">
                            <div class="aspect-square border-2 rounded-lg overflow-hidden cursor-pointer transition-all"
                                 :class="currentUrl === file.url ? 'border-[#1a4a6b] ring-2 ring-[#1a4a6b]/30' : 'border-gray-200 hover:border-gray-400'"
                                 @click="select(file)">
                                <img :src="file.url" :alt="file.alt_text ?? ''" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- URL --}}
            <div x-show="pickerTab === 'url'" class="flex-1 flex flex-col items-center justify-center p-8 gap-4">
                <div class="w-full max-w-sm">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pegá la URL de la imagen</label>
                    <input type="url" x-model="manualUrl" placeholder="https://ejemplo.com/imagen.jpg"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    <div x-show="manualUrl" class="mt-3 rounded-lg overflow-hidden border border-gray-200 h-28 bg-gray-50">
                        <img :src="manualUrl" alt="" class="w-full h-full object-cover" x-on:error="$el.style.opacity='0.3'">
                    </div>
                </div>
                <button type="button" @click="confirmUrl()" :disabled="!manualUrl"
                        class="px-5 py-2 bg-[#1a4a6b] text-white rounded-lg text-sm font-semibold hover:opacity-90 disabled:opacity-40">
                    Usar esta URL
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Título --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
    <input type="text" name="title"
           value="{{ old('title') }}"
           placeholder="Ej: Black Friday 2026"
           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
</div>

{{-- Subtítulo --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
    <input type="text" name="subtitle"
           value="{{ old('subtitle') }}"
           placeholder="Ej: Descuentos de hasta 50%"
           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
</div>

{{-- Botón --}}
<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Texto del botón</label>
        <input type="text" name="button_text"
               value="{{ old('button_text', 'Ver más') }}"
               placeholder="Ver más"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Enlace del botón</label>
        <input type="text" name="button_link"
               value="{{ old('button_link', '/productos') }}"
               placeholder="/productos"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
    </div>
</div>

{{-- Orden + toggles --}}
<div class="flex items-center gap-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
        <input type="number" name="display_order" min="0" value="{{ old('display_order', 0) }}"
               class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
    </div>
    <label class="flex items-center gap-2 cursor-pointer select-none mt-5">
        <input type="checkbox" name="is_active" value="1" checked
               class="w-4 h-4 rounded border-gray-300 text-[#1a4a6b] focus:ring-[#1a4a6b]/30">
        <span class="text-sm text-gray-700">Activo</span>
    </label>
</div>
