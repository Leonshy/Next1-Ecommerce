@extends('layouts.admin')
@section('title', 'Biblioteca de Medios')

@section('content')
<div
    x-data="mediaLibrary()"
    x-init="init()"
    @dragover.prevent="dragging = true"
    @dragleave.prevent="dragging = false"
    @drop.prevent="handleDrop($event)"
>
    {{-- Top bar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
        <div class="flex items-center gap-3">
            {{-- Search --}}
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model.debounce.300ms="search" placeholder="Buscar archivos..."
                       class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-56">
            </div>
            {{-- Type filter --}}
            <select x-model="typeFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los tipos</option>
                <option value="image">Imágenes</option>
                <option value="video">Videos</option>
                <option value="document">Documentos</option>
            </select>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-400" x-text="filteredFiles.length + ' archivo(s)'"></span>
            {{-- Upload button --}}
            <label class="cursor-pointer bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Subir archivos
                <input type="file" multiple class="hidden" accept="image/*,video/*,audio/*,.pdf,.doc,.docx"
                       @change="handleFileInput($event)">
            </label>
        </div>
    </div>

    {{-- Upload progress --}}
    <div x-show="uploading" class="mb-4 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-blue-700">Subiendo archivos...</span>
            <span class="text-sm text-blue-600" x-text="uploadProgress + '%'"></span>
        </div>
        <div class="w-full bg-blue-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="'width:' + uploadProgress + '%'"></div>
        </div>
    </div>

    {{-- Drag overlay --}}
    <div x-show="dragging" x-cloak
         class="fixed inset-0 z-50 bg-blue-600/20 backdrop-blur-sm flex items-center justify-center pointer-events-none">
        <div class="bg-white rounded-2xl shadow-2xl border-4 border-dashed border-blue-500 p-16 text-center">
            <svg class="w-16 h-16 text-blue-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            <p class="text-xl font-bold text-blue-700">Soltá los archivos aquí</p>
        </div>
    </div>

    {{-- Empty state --}}
    <div x-show="!loading && filteredFiles.length === 0"
         class="border-2 border-dashed border-gray-200 rounded-2xl py-20 text-center text-gray-400">
        <svg class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <p class="font-semibold text-lg" x-text="search ? 'Sin resultados para \''+search+'\'' : 'No hay archivos en la biblioteca'"></p>
        <p class="text-sm mt-1">Arrastrá archivos aquí o hacé clic en "Subir archivos"</p>
    </div>

    {{-- Loading --}}
    <div x-show="loading" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
        <template x-for="i in 12" :key="i">
            <div class="aspect-square bg-gray-100 rounded-xl animate-pulse"></div>
        </template>
    </div>

    {{-- Grid --}}
    <div x-show="!loading && filteredFiles.length > 0"
         class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
        <template x-for="file in filteredFiles" :key="file.id">
            <div class="group relative bg-white border border-gray-200 rounded-xl overflow-hidden cursor-pointer hover:border-blue-400 hover:shadow-md transition-all"
                 @click="openDetail(file)">

                {{-- Thumbnail --}}
                <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                    <img x-show="file.is_image" :src="file.file_url" :alt="file.alt_text"
                         class="w-full h-full object-cover">
                    <div x-show="!file.is_image" class="flex flex-col items-center justify-center text-gray-300 p-4">
                        <svg x-show="file.mime_type && file.mime_type.startsWith('video')" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <svg x-show="!file.mime_type || (!file.mime_type.startsWith('video') && !file.mime_type.startsWith('audio'))" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="text-xs mt-2 font-mono uppercase" x-text="file.file_name.split('.').pop()"></span>
                    </div>
                </div>

                {{-- Hover overlay --}}
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-end justify-center pb-3 gap-2 opacity-0 group-hover:opacity-100">
                    {{-- Copy URL --}}
                    <button @click.stop="copyUrl(file)"
                            class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow hover:bg-blue-50 transition-colors"
                            title="Copiar URL">
                        <svg x-show="copiedId !== file.id" class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <svg x-show="copiedId === file.id" class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    {{-- Delete --}}
                    <button @click.stop="confirmDelete(file)"
                            class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow hover:bg-red-50 transition-colors"
                            title="Eliminar">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>

                {{-- Usage badge --}}
                <div x-show="file.usages_count > 0"
                     class="absolute top-2 right-2 bg-blue-600 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center"
                     :title="'Usado en ' + file.usages_count + ' lugar(es)'"
                     x-text="file.usages_count"></div>

                {{-- Filename --}}
                <div class="p-2">
                    <p class="text-xs text-gray-600 truncate" x-text="file.file_name"></p>
                    <p class="text-[10px] text-gray-400" x-text="file.size_label"></p>
                </div>
            </div>
        </template>
    </div>

    {{-- ───── DETAIL MODAL ───── --}}
    <div x-show="detailOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="detailOpen = false">
        <div class="absolute inset-0 bg-black/50" @click="detailOpen = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto z-10" x-show="selectedFile">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900 truncate pr-4" x-text="selectedFile?.file_name"></h3>
                <button @click="detailOpen = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 space-y-5">
                {{-- Preview --}}
                <div class="bg-gray-50 rounded-xl overflow-hidden flex items-center justify-center min-h-48">
                    <img x-show="selectedFile?.is_image" :src="selectedFile?.file_url" :alt="selectedFile?.alt_text"
                         class="max-h-72 object-contain">
                    <div x-show="!selectedFile?.is_image" class="text-center text-gray-400 py-8">
                        <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm font-medium" x-text="selectedFile?.mime_type"></p>
                    </div>
                </div>

                {{-- Metadata --}}
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Tamaño</p>
                        <p class="font-medium text-gray-700" x-text="selectedFile?.size_label"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Tipo</p>
                        <p class="font-medium text-gray-700 truncate" x-text="selectedFile?.mime_type"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Subido</p>
                        <p class="font-medium text-gray-700" x-text="selectedFile?.created_at"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-0.5">Usos</p>
                        <p class="font-medium text-gray-700" x-text="(selectedFile?.usages_count || 0) + ' lugar(es)'"></p>
                    </div>
                </div>

                {{-- Alt text --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Texto alternativo (alt)</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="editingAlt"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Descripción de la imagen para accesibilidad y SEO">
                        <button @click="saveAlt()" :disabled="savingAlt"
                                class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 disabled:opacity-50 transition-colors">
                            <span x-text="savingAlt ? 'Guardando...' : 'Guardar'"></span>
                        </button>
                    </div>
                </div>

                {{-- URL --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL pública</label>
                    <div class="flex gap-2">
                        <input type="text" :value="selectedFile?.file_url" readonly
                               class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-600 font-mono">
                        <button @click="copyUrl(selectedFile)"
                                class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition-colors flex items-center gap-1.5">
                            <svg x-show="copiedId !== selectedFile?.id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <svg x-show="copiedId === selectedFile?.id" class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Copiar
                        </button>
                    </div>
                </div>

                {{-- Usages --}}
                <div x-show="selectedFile?.usages_count > 0">
                    <p class="text-sm font-medium text-gray-700 mb-2">Usado en:</p>
                    <div class="space-y-1.5">
                        <template x-for="u in selectedFile?.usages" :key="u.entity_id + u.field_name">
                            <div class="flex items-center gap-2 text-xs bg-gray-50 rounded-lg px-3 py-2">
                                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-medium" x-text="u.entity_type"></span>
                                <span class="text-gray-500" x-text="u.field_name"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Delete --}}
                <div class="flex justify-end pt-2 border-t border-gray-100">
                    <button @click="confirmDelete(selectedFile)"
                            class="flex items-center gap-2 text-red-500 hover:text-red-700 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Eliminar archivo
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ───── DELETE CONFIRM MODAL ───── --}}
    <div x-show="deleteTarget" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="deleteTarget = null"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 z-10 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">¿Eliminar archivo?</h3>
            <p class="text-sm text-gray-500 mb-1 truncate" x-text="deleteTarget?.file_name"></p>
            <p x-show="deleteTarget?.usages_count > 0"
               class="text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-2 mb-4"
               x-text="'Este archivo se usa en ' + deleteTarget?.usages_count + ' lugar(es). Eliminarlo puede causar imágenes rotas.'"></p>
            <p x-show="!deleteTarget?.usages_count" class="text-sm text-gray-400 mb-4">Esta acción no se puede deshacer.</p>
            <div class="flex gap-3">
                <button @click="deleteTarget = null"
                        class="flex-1 border border-gray-300 text-gray-700 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button @click="deleteFile()"
                        class="flex-1 bg-red-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function mediaLibrary() {
    return {
        files: [],
        loading: true,
        search: '',
        typeFilter: '',
        uploading: false,
        uploadProgress: 0,
        dragging: false,
        detailOpen: false,
        selectedFile: null,
        editingAlt: '',
        savingAlt: false,
        copiedId: null,
        deleteTarget: null,

        get filteredFiles() {
            const s = this.search.toLowerCase();
            return this.files.filter(f => {
                const matchSearch = !s || f.file_name.toLowerCase().includes(s) || (f.alt_text || '').toLowerCase().includes(s);
                const matchType = !this.typeFilter ||
                    (this.typeFilter === 'image' && f.is_image) ||
                    (this.typeFilter === 'video' && (f.mime_type || '').startsWith('video/')) ||
                    (this.typeFilter === 'document' && (f.mime_type || '').startsWith('application/'));
                return matchSearch && matchType;
            });
        },

        async init() {
            await this.loadFiles();
        },

        async loadFiles() {
            this.loading = true;
            try {
                const res = await fetch('{{ route("admin.media.picker") }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.files = await res.json();
            } finally {
                this.loading = false;
            }
        },

        handleDrop(e) {
            this.dragging = false;
            const files = Array.from(e.dataTransfer.files);
            if (files.length) this.uploadFiles(files);
        },

        handleFileInput(e) {
            const files = Array.from(e.target.files);
            if (files.length) this.uploadFiles(files);
            e.target.value = '';
        },

        async uploadFiles(fileList) {
            this.uploading = true;
            this.uploadProgress = 0;
            const formData = new FormData();
            fileList.forEach(f => formData.append('files[]', f));
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const xhr = new XMLHttpRequest();
                xhr.upload.onprogress = (e) => {
                    if (e.lengthComputable)
                        this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                };

                await new Promise((resolve, reject) => {
                    xhr.onload = () => {
                        if (xhr.status === 201) {
                            const data = JSON.parse(xhr.responseText);
                            this.files = [...data.uploaded, ...this.files];
                            resolve();
                        } else {
                            reject(new Error('Upload failed'));
                        }
                    };
                    xhr.onerror = reject;
                    xhr.open('POST', '{{ route("admin.media.upload") }}');
                    xhr.send(formData);
                });
            } catch (err) {
                alert('Error al subir los archivos. Verificá el tamaño máximo (20 MB).');
            } finally {
                this.uploading = false;
                this.uploadProgress = 0;
            }
        },

        openDetail(file) {
            this.selectedFile = file;
            this.editingAlt = file.alt_text || '';
            this.detailOpen = true;
        },

        async saveAlt() {
            if (!this.selectedFile) return;
            this.savingAlt = true;
            try {
                await fetch(`/admin/multimedia/${this.selectedFile.id}/alt`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ alt_text: this.editingAlt }),
                });
                const f = this.files.find(x => x.id === this.selectedFile.id);
                if (f) f.alt_text = this.editingAlt;
                this.selectedFile.alt_text = this.editingAlt;
            } finally {
                this.savingAlt = false;
            }
        },

        copyUrl(file) {
            if (!file) return;
            navigator.clipboard.writeText(file.file_url);
            this.copiedId = file.id;
            setTimeout(() => this.copiedId = null, 2000);
        },

        confirmDelete(file) {
            this.deleteTarget = file;
        },

        async deleteFile() {
            if (!this.deleteTarget) return;
            try {
                await fetch(`/admin/multimedia/${this.deleteTarget.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                this.files = this.files.filter(f => f.id !== this.deleteTarget.id);
                if (this.selectedFile?.id === this.deleteTarget.id) this.detailOpen = false;
                this.deleteTarget = null;
            } catch {
                alert('Error al eliminar el archivo.');
            }
        },
    };
}
</script>
@endpush
@endsection
