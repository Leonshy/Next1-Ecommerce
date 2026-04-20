@extends('layouts.admin')
@section('title', 'Editar Plantilla: ' . $template->name)

@section('content')
<div x-data="{ resetConfirm: false }">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-5">
        <a href="{{ route('admin.settings.email-templates.index') }}" class="hover:text-gray-600 transition-colors">Plantillas de Email</a>
        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-600 font-medium truncate">{{ $template->name }}</span>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 items-start">

        {{-- ── Editor (col izquierda, más ancha) ─────────────────────────── --}}
        <div class="xl:col-span-3 space-y-4">
            <form method="POST" action="{{ route('admin.settings.email-templates.update', $template->template_key) }}">
                @csrf
                @method('PUT')

                {{-- Asunto --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Asunto del email</label>
                    <input type="text" name="subject" value="{{ old('subject', $template->subject) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                           placeholder="Ej: Tu pedido @{{order_number}} fue recibido">
                    @error('subject')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cuerpo HTML --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-semibold text-gray-700">Contenido HTML</label>
                        <a href="{{ route('admin.settings.email-templates.preview', $template->template_key) }}"
                           target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Vista previa
                        </a>
                    </div>
                    <p class="text-xs text-gray-400 mb-3">El encabezado y pie de página con el nombre de la tienda se agregan automáticamente.</p>
                    <textarea name="body_html" rows="26"
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition leading-relaxed"
                              style="resize:vertical">{{ old('body_html', $template->body_html) }}</textarea>
                    @error('body_html')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl transition-colors hover:opacity-90"
                            style="background:var(--brand-primary)">
                        Guardar cambios
                    </button>
                    <a href="{{ route('admin.settings.email-templates.index') }}"
                       class="px-4 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="button" @click="resetConfirm = true"
                            class="ml-auto px-4 py-2.5 text-sm font-medium text-red-500 border border-red-200 rounded-xl hover:bg-red-50 transition-colors">
                        Restaurar original
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Sidebar (col derecha) ───────────────────────────────────────── --}}
        <div class="xl:col-span-2 space-y-4 xl:sticky xl:top-6">

            {{-- Variables --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <p class="text-sm font-semibold text-gray-800 mb-0.5">Variables disponibles</p>
                <p class="text-xs text-gray-400 mb-3">Clic para copiar · se inserta donde estés escribiendo</p>

                <div class="space-y-px overflow-y-auto pr-1" style="max-height:340px">
                    @foreach($varDescriptions as $item)
                    @php $varTag = '{{' . $item['key'] . '}}'; @endphp
                    <button type="button"
                            onclick="copyVar('{{ $varTag }}', this)"
                            class="var-btn w-full text-left px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors group flex items-start gap-2.5">
                        <code class="text-xs font-mono text-blue-600 group-hover:text-blue-800 shrink-0 leading-5 mt-px">{{ $varTag }}</code>
                        <span class="text-xs text-gray-400 group-hover:text-gray-600 leading-5">{{ $item['desc'] }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Guía de sintaxis --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <p class="text-sm font-semibold text-gray-800 mb-3">Guía de sintaxis</p>
                <div class="space-y-3">
                    @php
                        $examples = [
                            ['label' => 'En texto', 'code' => 'Hola {{customer_name}},'],
                            ['label' => 'En asunto', 'code' => 'Pedido #{{order_number}} recibido'],
                            ['label' => 'En enlace', 'code' => '<a href="{{url}}">Confirmar</a>'],
                            ['label' => 'En estilo', 'code' => 'color: {{color}};'],
                        ];
                    @endphp
                    @foreach($examples as $ex)
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">{{ $ex['label'] }}</p>
                        <code class="block bg-gray-50 border border-gray-100 rounded-lg px-3 py-2 text-xs font-mono text-blue-700 break-all">{{ $ex['code'] }}</code>
                    </div>
                    @endforeach
                    <div class="bg-amber-50 border border-amber-100 rounded-xl px-3 py-2.5 text-xs text-amber-700">
                        <span class="font-semibold">Nota:</span> Las variables no usadas en esta plantilla simplemente no se reemplazan, no generan errores.
                    </div>
                </div>
            </div>

            {{-- Meta --}}
            <div class="bg-white rounded-2xl border border-gray-200 px-5 py-4 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400">Clave</p>
                    <code class="text-xs font-mono text-gray-500">{{ $template->template_key }}</code>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">Última edición</p>
                    <p class="text-xs text-gray-500">{{ $template->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal restaurar --}}
    <div x-show="resetConfirm" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         style="background:rgba(0,0,0,0.45)">
        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full mx-4">
            <p class="text-base font-bold text-gray-800 mb-2">¿Restaurar contenido original?</p>
            <p class="text-sm text-gray-500 mb-5">Se sobreescribirán el asunto y el cuerpo HTML con el contenido predeterminado. Esta acción no se puede deshacer.</p>
            <div class="flex gap-3">
                <form method="POST" action="{{ route('admin.settings.email-templates.reset', $template->template_key) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-xl transition-colors">
                        Sí, restaurar
                    </button>
                </form>
                <button type="button" @click="resetConfirm = false"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function copyVar(tag, btn) {
    navigator.clipboard.writeText(tag).then(() => {
        btn.classList.add('bg-green-50');
        btn.querySelector('code').classList.add('text-green-700');
        setTimeout(() => {
            btn.classList.remove('bg-green-50');
            btn.querySelector('code').classList.remove('text-green-700');
        }, 800);
    });
}
</script>
@endpush
@endsection
