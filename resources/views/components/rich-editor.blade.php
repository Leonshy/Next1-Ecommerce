@props(['name', 'value' => '', 'rows' => 14])

@php
    $editorId = 're-' . md5($name . uniqid());
@endphp

<div x-data="richEditor('{{ $editorId }}')"
     x-init="init()"
     class="border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">

    {{-- ── Toolbar ── --}}
    <div class="flex flex-wrap items-center gap-0.5 px-2 py-1.5 bg-gray-50 border-b border-gray-200 select-none">

        {{-- Bloques --}}
        <button type="button" @mousedown.prevent="block('h2')"
                class="px-2.5 py-1 text-xs font-bold rounded hover:bg-gray-200 transition-colors text-gray-700"
                title="Título (H2)">Título</button>
        <button type="button" @mousedown.prevent="block('h3')"
                class="px-2.5 py-1 text-xs font-semibold rounded hover:bg-gray-200 transition-colors text-gray-700"
                title="Subtítulo (H3)">Subtítulo</button>
        <button type="button" @mousedown.prevent="block('p')"
                class="px-2.5 py-1 text-xs rounded hover:bg-gray-200 transition-colors text-gray-700"
                title="Párrafo">Párrafo</button>
        <button type="button" @mousedown.prevent="block('ul')"
                class="px-2.5 py-1 text-xs rounded hover:bg-gray-200 transition-colors text-gray-700"
                title="Lista">Lista</button>

        <span class="w-px h-4 bg-gray-300 mx-1"></span>

        {{-- Inline --}}
        <button type="button" @mousedown.prevent="exec('bold')"
                class="w-7 h-7 flex items-center justify-center rounded hover:bg-gray-200 transition-colors font-bold text-sm text-gray-700"
                title="Negrita"><b>N</b></button>
        <button type="button" @mousedown.prevent="exec('italic')"
                class="w-7 h-7 flex items-center justify-center rounded hover:bg-gray-200 transition-colors italic text-sm text-gray-700"
                title="Cursiva"><i>C</i></button>
        <button type="button" @mousedown.prevent="exec('underline')"
                class="w-7 h-7 flex items-center justify-center rounded hover:bg-gray-200 transition-colors underline text-sm text-gray-700"
                title="Subrayado">S</button>
        <button type="button" @mousedown.prevent="exec('strikeThrough')"
                class="w-7 h-7 flex items-center justify-center rounded hover:bg-gray-200 transition-colors line-through text-sm text-gray-700"
                title="Tachado">T</button>

        <span class="w-px h-4 bg-gray-300 mx-1"></span>

        {{-- Link --}}
        <button type="button" @mousedown.prevent="insertLink()"
                class="w-7 h-7 flex items-center justify-center rounded hover:bg-gray-200 transition-colors text-gray-700"
                title="Hipervínculo">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
        </button>

        {{-- Unlink --}}
        <button type="button" @mousedown.prevent="exec('unlink')"
                class="w-7 h-7 flex items-center justify-center rounded hover:bg-gray-200 transition-colors text-gray-500"
                title="Quitar enlace">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636a9 9 0 010 12.728M5.636 18.364a9 9 0 010-12.728M9 9l6 6"/>
            </svg>
        </button>

        <span class="w-px h-4 bg-gray-300 mx-1"></span>

        {{-- Limpiar --}}
        <button type="button" @mousedown.prevent="exec('removeFormat')"
                class="px-2.5 py-1 text-xs rounded hover:bg-gray-200 transition-colors text-gray-500"
                title="Limpiar formato">✕ Limpiar</button>
    </div>

    {{-- ── Área editable ── --}}
    <div id="{{ $editorId }}"
         contenteditable="true"
         @input="sync()"
         @paste="onPaste($event)"
         class="min-h-[{{ $rows * 24 }}px] px-4 py-3 text-sm text-gray-800 outline-none overflow-auto prose prose-sm max-w-none
                [&_h2]:text-xl [&_h2]:font-bold [&_h2]:mt-3 [&_h2]:mb-1
                [&_h3]:text-base [&_h3]:font-semibold [&_h3]:mt-2 [&_h3]:mb-1
                [&_p]:my-1 [&_ul]:list-disc [&_ul]:pl-5 [&_a]:text-blue-600 [&_a]:underline">
    </div>

    {{-- Hidden textarea sincronizada con el editor --}}
    <textarea id="{{ $editorId }}-input" name="{{ $name }}" class="hidden">{{ $value }}</textarea>
</div>

@pushOnce('scripts')
<script>
function richEditor(id) {
    return {
        init() {
            const editor = document.getElementById(id);
            const input  = document.getElementById(id + '-input');
            if (editor && input) {
                editor.innerHTML = input.value || '';
            }
        },
        sync() {
            const editor = document.getElementById(id);
            const input  = document.getElementById(id + '-input');
            if (editor && input) input.value = editor.innerHTML;
        },
        block(tag) {
            const editor = document.getElementById(id);
            editor.focus();
            if (tag === 'ul') {
                document.execCommand('insertUnorderedList', false, null);
            } else {
                document.execCommand('formatBlock', false, tag);
            }
            this.sync();
        },
        exec(cmd, val) {
            const editor = document.getElementById(id);
            editor.focus();
            document.execCommand(cmd, false, val || null);
            this.sync();
        },
        insertLink() {
            const editor = document.getElementById(id);
            editor.focus();
            const url = prompt('URL del enlace (ej: https://ejemplo.com):');
            if (url && url.trim()) {
                document.execCommand('createLink', false, url.trim());
                // Hacer que el link abra en nueva pestaña
                setTimeout(() => {
                    editor.querySelectorAll('a[href="' + url.trim() + '"]').forEach(a => {
                        a.target = '_blank';
                        a.rel = 'noopener noreferrer';
                    });
                    this.sync();
                }, 10);
            }
        },
        onPaste(e) {
            e.preventDefault();
            // Pegar solo texto plano para evitar HTML externo
            const text = (e.clipboardData || window.clipboardData).getData('text/plain');
            document.execCommand('insertText', false, text);
            this.sync();
        }
    }
}
</script>
@endPushOnce
