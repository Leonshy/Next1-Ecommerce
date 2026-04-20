@php $e = $editing ?? false; @endphp

<div class="grid grid-cols-2 gap-3">
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
        <input type="text" name="title" required value="{{ old('title') }}" placeholder="Ej: VENTA LAPTOP"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
    </div>
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
        <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="Ej: LA MEJOR TECNOLOGÍA"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
    </div>

    {{-- Imagen de fondo con picker --}}
    <div class="col-span-2" x-data="{
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
                this.pickerTab = 'library';
                if (!this.pickerFiles.length) await this.loadFiles();
            },
            async loadFiles() {
                this.pickerLoading = true;
                try {
                    const res = await fetch('{{ route('admin.media.picker') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();
                    const files = Array.isArray(data) ? data : (data.files ?? []);
                    this.pickerFiles = files.filter(f => (f.mime_type ?? '').startsWith('image/')).map(f => ({ ...f, url: f.file_url }));
                } catch(e) { this.pickerFiles = []; }
                finally { this.pickerLoading = false; }
            },
            select(file) { this.currentUrl = file.url; this.pickerOpen = false; },
            confirmUrl() {
                if (this.manualUrl) { this.currentUrl = this.manualUrl; this.manualUrl = ''; this.pickerOpen = false; }
            },
        }"
        @if($e) x-on:banner-set-image.window="currentUrl = $event.detail.url" @endif>

        <label class="block text-sm font-medium text-gray-700 mb-1">Imagen de fondo <span class="text-gray-400 font-normal">(opcional)</span></label>
        <input type="hidden" name="image_url" :value="currentUrl">

        <div x-show="currentUrl" class="relative mb-2 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 h-24">
            <img :src="currentUrl" alt="" class="w-full h-full object-cover">
            <button type="button" @click="currentUrl = ''"
                    class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-bold leading-none hover:bg-red-600">×</button>
        </div>
        <div x-show="!currentUrl" class="mb-2 border-2 border-dashed border-gray-200 rounded-lg p-4 text-center text-gray-400">
            <svg class="w-6 h-6 mx-auto mb-1 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-xs">Sin imagen</p>
        </div>

        <button type="button" @click="openPicker()"
                class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Seleccionar imagen
        </button>

        {{-- Modal picker --}}
        <div x-show="pickerOpen" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center p-4" @keydown.escape.window="pickerOpen = false">
            <div class="absolute inset-0 bg-black/50" @click="pickerOpen = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col z-10">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <h3 class="font-semibold text-gray-900 text-sm">Seleccionar imagen</h3>
                        <div class="flex border border-gray-200 rounded-lg overflow-hidden text-xs">
                            <button type="button" @click="pickerTab = 'library'" :class="pickerTab === 'library' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'" class="px-3 py-1.5 transition-colors">Biblioteca</button>
                            <button type="button" @click="pickerTab = 'url'" :class="pickerTab === 'url' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'" class="px-3 py-1.5 transition-colors">URL</button>
                        </div>
                    </div>
                    <button type="button" @click="pickerOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div x-show="pickerTab === 'library'" class="flex flex-col flex-1 overflow-hidden">
                    <div class="p-3 border-b border-gray-100 flex-shrink-0">
                        <input type="text" x-model.debounce.300ms="pickerSearch" placeholder="Buscar..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    </div>
                    <div class="flex-1 overflow-y-auto p-3">
                        <div x-show="pickerLoading" class="grid grid-cols-5 gap-2">
                            <template x-for="i in 15" :key="i"><div class="aspect-square bg-gray-100 rounded-lg animate-pulse"></div></template>
                        </div>
                        <div x-show="!pickerLoading && filteredFiles.length === 0" class="text-center py-12 text-gray-400 text-sm">No hay imágenes en la biblioteca.</div>
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

    {{-- Overlay color + opacidad (solo si hay imagen) --}}
    <div class="col-span-2" x-data="{ opacity: {{ old('overlay_opacity', 0.40) }} }"
         x-init="$el.querySelector('input[name=overlay_opacity]').value = opacity"
         @if($e) x-on:banner-set-overlay.window="opacity = $event.detail.opacity" @endif>
        <label class="block text-sm font-medium text-gray-700 mb-2">Overlay de color sobre la imagen</label>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 flex-1">
                <x-admin.color-picker
                    name="overlay_color"
                    :value="old('overlay_color', '#000000')"
                    label=""
                    :listen-event="$e ? 'banner-set-overlay-color' : null" />
                <span class="text-xs text-gray-500">Color</span>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-500">Opacidad</span>
                    <span class="text-xs font-medium text-gray-700" x-text="Math.round(opacity * 100) + '%'"></span>
                </div>
                <input type="range" name="overlay_opacity" min="0" max="1" step="0.05"
                       x-model="opacity"
                       class="w-full h-1.5 rounded-full appearance-none bg-gray-200 accent-[#1a4a6b] cursor-pointer">
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-1">Si no hay imagen, el overlay se ignora. Con opacidad 0 la imagen se ve sin filtro.</p>
    </div>

    <div class="col-span-2">
        <x-admin.color-picker
            name="background_gradient"
            :value="old('background_gradient', '#1a4a6b')"
            label="Color de fondo (cuando no hay imagen)"
            :listen-event="$e ? 'banner-set-bg' : null" />
        <p class="text-xs text-gray-400 mt-1">También podés escribir un gradiente CSS: <code class="text-gray-500">linear-gradient(135deg,#1a4a6b,#e07b1d)</code></p>
    </div>
    <div>
        <x-admin.color-picker
            name="text_color"
            :value="old('text_color', 'white')"
            label="Color de texto"
            :listen-event="$e ? 'banner-set-text-color' : null" />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
        <input type="number" name="display_order" min="0" value="{{ old('display_order', 0) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Texto del botón</label>
        <input type="text" name="button_text" value="{{ old('button_text', 'Ver más') }}" placeholder="Comprar"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
    </div>
    <div>
        <x-admin.color-picker
            name="button_text_color"
            :value="old('button_text_color', 'white')"
            label="Color texto botón"
            :listen-event="$e ? 'banner-set-btn-color' : null" />
    </div>
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Enlace del botón</label>
        <input type="text" name="button_link" value="{{ old('button_link', '/productos') }}" placeholder="/productos"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
    </div>
    <div class="col-span-2 pt-1">
        <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" name="is_active" value="1" checked
                   class="w-4 h-4 rounded border-gray-300 text-[#1a4a6b] focus:ring-[#1a4a6b]/30">
            <span class="text-sm text-gray-700">Activo</span>
        </label>
    </div>
</div>
