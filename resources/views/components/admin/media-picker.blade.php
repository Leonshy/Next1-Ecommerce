@props([
    'name'        => 'image_url',   // input name
    'value'       => '',            // current URL
    'label'       => 'Imagen',
    'accept'      => 'image/*',     // MIME types: 'image/*' | 'image/*,video/*' | etc.
    'listenEvent' => null,          // window event name to receive a new URL from outside
])

@php
    $pickerId   = 'mp_' . Str::random(8);
    $acceptJson = json_encode($accept);
@endphp

<div x-data="mediaPicker_{{ $pickerId }}()" x-init="init()"
     @if($listenEvent) @{{ $listenEvent }}.window="currentUrl = $event.detail.url" @endif>
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>

    {{-- Preview --}}
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
            <div x-show="currentUrl"
                 class="w-20 h-20 border border-gray-200 rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center relative">
                <img x-show="!isVideo(currentUrl)" :src="currentUrl" alt="" class="w-full h-full object-cover">
                <div x-show="isVideo(currentUrl)" class="flex flex-col items-center justify-center text-gray-400 text-xs gap-1">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                    </svg>
                    <span>VIDEO</span>
                </div>
            </div>
            <div x-show="!currentUrl"
                 class="w-20 h-20 border-2 border-dashed border-gray-200 rounded-lg flex items-center justify-center text-gray-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
        <div class="flex flex-col gap-2 flex-1 min-w-0">
            <input type="hidden" name="{{ $name }}" :value="currentUrl">
            <input type="text" x-model="currentUrl" placeholder="URL del archivo..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="flex gap-2 flex-wrap">
                <button type="button" @click="openModal()"
                        class="text-sm bg-gray-900 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 transition-colors">
                    Seleccionar / Subir
                </button>
                <button type="button" x-show="currentUrl" @click="currentUrl = ''"
                        class="text-sm text-red-500 hover:text-red-700 transition-colors">
                    Quitar
                </button>
            </div>
        </div>
    </div>

    {{-- ── Modal ── --}}
    <div x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="open = false">
        <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col z-10">

            {{-- Header --}}
            <div class="flex items-center justify-between p-5 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <h3 class="font-semibold text-gray-900">{{ $label }}</h3>
                    <div class="flex border border-gray-200 rounded-lg overflow-hidden text-sm">
                        <button type="button" @click="tab = 'library'"
                                :class="tab === 'library' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                class="px-3 py-1.5 transition-colors">Biblioteca</button>
                        <button type="button" @click="tab = 'upload'"
                                :class="tab === 'upload' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                class="px-3 py-1.5 transition-colors">Subir</button>
                        <button type="button" @click="tab = 'url'"
                                :class="tab === 'url' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                class="px-3 py-1.5 transition-colors">URL</button>
                    </div>
                </div>
                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- ── Tab: Biblioteca ── --}}
            <div x-show="tab === 'library'" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex-shrink-0">
                    <input type="text" x-model.debounce.300ms="pickerSearch" placeholder="Buscar..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <div x-show="pickerLoading" class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                        <template x-for="i in 15" :key="i">
                            <div class="aspect-square bg-gray-100 rounded-lg animate-pulse"></div>
                        </template>
                    </div>
                    <div x-show="!pickerLoading && filteredPickerFiles.length === 0"
                         class="text-center py-12 text-gray-400">
                        <p class="text-sm">No hay archivos en la biblioteca.</p>
                        <button type="button" @click="tab = 'upload'" class="text-blue-600 text-sm mt-1 hover:underline">Subir un archivo</button>
                    </div>
                    <div x-show="!pickerLoading" class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                        <template x-for="file in filteredPickerFiles" :key="file.id">
                            <div class="relative aspect-square bg-gray-50 border-2 rounded-lg overflow-hidden cursor-pointer transition-all"
                                 :class="selectedId === file.id ? 'border-blue-500 ring-2 ring-blue-300' : 'border-gray-200 hover:border-blue-300'"
                                 @click="selectFile(file)">
                                {{-- Imagen --}}
                                <img x-show="file.is_image" :src="file.file_url" :alt="file.alt_text ?? ''"
                                     class="w-full h-full object-cover">
                                {{-- Video --}}
                                <div x-show="file.is_video"
                                     class="w-full h-full flex flex-col items-center justify-center text-gray-400 gap-1">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                    </svg>
                                    <span class="text-[10px] font-bold uppercase" x-text="file.file_name.split('.').pop()"></span>
                                </div>
                                {{-- Otros --}}
                                <div x-show="!file.is_image && !file.is_video"
                                     class="w-full h-full flex items-center justify-center text-gray-400 text-xs p-2 text-center font-bold uppercase"
                                     x-text="file.file_name.split('.').pop()"></div>
                                {{-- Check --}}
                                <div x-show="selectedId === file.id"
                                     class="absolute inset-0 bg-blue-600/20 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-600 bg-white rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-100 flex justify-end gap-3 flex-shrink-0">
                    <button type="button" @click="open = false"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</button>
                    <button type="button" @click="confirmSelection()" :disabled="!selectedId"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 disabled:opacity-40 transition-colors">
                        Seleccionar
                    </button>
                </div>
            </div>

            {{-- ── Tab: Subir ── --}}
            <div x-show="tab === 'upload'" class="flex flex-col flex-1 overflow-hidden">
                <div class="flex-1 flex flex-col items-center justify-center p-8"
                     @dragover.prevent="uploadDragging = true"
                     @dragleave.prevent="uploadDragging = false"
                     @drop.prevent="handlePickerDrop($event)">
                    <label class="w-full cursor-pointer">
                        <div :class="uploadDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-gray-50'"
                             class="border-2 border-dashed rounded-2xl p-12 text-center transition-colors">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="text-gray-600 font-medium mb-1">Arrastrá archivos aquí</p>
                            <p class="text-sm text-gray-400">o hacé clic para seleccionar</p>
                            <p class="text-xs text-gray-400 mt-2">Máximo 20 MB · Formatos: <span class="font-medium">{{ str_replace(['image/*','video/*','application/*'], ['imágenes','videos','documentos'], $accept) }}</span></p>
                        </div>
                        <input type="file" class="hidden" accept="{{ $accept }}" @change="handlePickerUpload($event)">
                    </label>
                    <div x-show="pickerUploading" class="mt-4 w-full max-w-sm">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Subiendo...</span>
                            <span x-text="pickerUploadProgress + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all" :style="'width:' + pickerUploadProgress + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Tab: URL ── --}}
            <div x-show="tab === 'url'" class="flex flex-col flex-1 overflow-hidden">
                <div class="flex-1 flex flex-col items-center justify-center p-8 gap-4">
                    <div class="w-full max-w-md">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pegá la URL del archivo</label>
                        <input type="url" x-model="manualUrl"
                               placeholder="https://ejemplo.com/imagen.jpg"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{-- Preview --}}
                        <div x-show="manualUrl" class="mt-3 rounded-lg overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center h-32">
                            <img x-show="!isVideo(manualUrl)" :src="manualUrl" alt="Preview"
                                 class="max-h-full max-w-full object-contain"
                                 x-on:error="$el.style.display='none'">
                            <div x-show="isVideo(manualUrl)" class="flex flex-col items-center text-gray-400 gap-2">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                                <span class="text-sm">Video</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" @click="confirmUrl()" :disabled="!manualUrl"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 disabled:opacity-40 transition-colors">
                        Usar esta URL
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function mediaPicker_{{ $pickerId }}() {
    const ACCEPT = {{ $acceptJson }};

    function mimeAllowed(mime) {
        if (!mime) return true;
        return ACCEPT.split(',').some(pattern => {
            pattern = pattern.trim();
            if (pattern === '*' || pattern === '*/*') return true;
            if (pattern.endsWith('/*')) return mime.startsWith(pattern.slice(0, -1));
            return mime === pattern;
        });
    }

    function isVideoUrl(url) {
        if (!url) return false;
        return /\.(mp4|webm|ogg|ogv|mov|avi|mkv|flv|wmv)(\?.*)?$/i.test(url);
    }

    return {
        open: false,
        tab: 'library',
        currentUrl: '{{ addslashes($value) }}',
        manualUrl: '',
        pickerSearch: '',
        pickerFiles: [],
        pickerLoading: false,
        selectedId: null,
        selectedFile: null,
        uploadDragging: false,
        pickerUploading: false,
        pickerUploadProgress: 0,

        isVideo(url) { return isVideoUrl(url); },

        get filteredPickerFiles() {
            const s = this.pickerSearch.toLowerCase();
            return this.pickerFiles.filter(f =>
                (!s || f.file_name.toLowerCase().includes(s) || (f.alt_text || '').toLowerCase().includes(s))
            );
        },

        async init() {
            this.$watch('open', val => { if (val && !this.pickerFiles.length) this.loadPickerFiles(); });
        },

        openModal() {
            this.open      = true;
            this.tab       = 'library';
            this.manualUrl = '';
        },

        async loadPickerFiles() {
            this.pickerLoading = true;
            try {
                const res  = await fetch('{{ route("admin.media.picker") }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                const files = Array.isArray(data) ? data : (data.files ?? []);
                this.pickerFiles = files
                    .map(f => ({
                        ...f,
                        is_image: (f.mime_type ?? '').startsWith('image/'),
                        is_video: (f.mime_type ?? '').startsWith('video/'),
                    }))
                    .filter(f => mimeAllowed(f.mime_type));
            } finally {
                this.pickerLoading = false;
            }
        },

        selectFile(file) {
            this.selectedId   = file.id;
            this.selectedFile = file;
        },

        confirmSelection() {
            if (this.selectedFile) {
                this.currentUrl   = this.selectedFile.file_url;
                this.open         = false;
                this.selectedId   = null;
                this.selectedFile = null;
            }
        },

        confirmUrl() {
            if (this.manualUrl) {
                this.currentUrl = this.manualUrl;
                this.manualUrl  = '';
                this.open       = false;
            }
        },

        handlePickerDrop(e) {
            this.uploadDragging = false;
            const files = Array.from(e.dataTransfer.files);
            if (files.length) this.uploadPickerFiles(files);
        },

        handlePickerUpload(e) {
            const files = Array.from(e.target.files);
            if (files.length) this.uploadPickerFiles(files);
            e.target.value = '';
        },

        async uploadPickerFiles(fileList) {
            this.pickerUploading = true;
            this.pickerUploadProgress = 0;
            const formData = new FormData();
            fileList.forEach(f => formData.append('files[]', f));
            formData.append('_token', '{{ csrf_token() }}');
            try {
                const xhr = new XMLHttpRequest();
                xhr.upload.onprogress = e => {
                    if (e.lengthComputable)
                        this.pickerUploadProgress = Math.round((e.loaded / e.total) * 100);
                };
                await new Promise((resolve, reject) => {
                    xhr.onload = () => {
                        if (xhr.status === 201) {
                            const data = JSON.parse(xhr.responseText);
                            const uploaded = (data.uploaded ?? []).map(f => ({
                                ...f,
                                is_image: (f.mime_type ?? '').startsWith('image/'),
                                is_video: (f.mime_type ?? '').startsWith('video/'),
                            }));
                            this.pickerFiles = [...uploaded, ...this.pickerFiles];
                            if (uploaded.length) {
                                this.selectFile(uploaded[0]);
                                this.tab = 'library';
                            }
                            resolve();
                        } else reject(new Error('Upload failed'));
                    };
                    xhr.onerror = reject;
                    xhr.open('POST', '{{ route("admin.media.upload") }}');
                    xhr.send(formData);
                });
            } catch {
                alert('Error al subir el archivo. Verificá el formato e intentá de nuevo.');
            } finally {
                this.pickerUploading      = false;
                this.pickerUploadProgress = 0;
            }
        },
    };
}
</script>
