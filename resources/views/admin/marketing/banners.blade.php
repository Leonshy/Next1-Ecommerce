@extends('layouts.admin')
@section('title', 'Espacios Publicitarios')

@section('content')

@if(session('success'))
    <div class="mb-5 rounded-xl border bg-green-50 border-green-200 p-4">
        <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
    </div>
@endif
@if($errors->any())
    <div class="mb-5 rounded-xl border bg-red-50 border-red-200 p-4">
        <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="mb-5">
    <h2 class="text-xl font-bold text-gray-900">Espacios Publicitarios</h2>
    <p class="text-sm text-gray-500 mt-0.5">Gestión del slide principal y los banners de anuncios</p>
</div>

{{-- Tabs --}}
<div x-data="{
        tab: sessionStorage.getItem('admin_tab_banners') || 'slides',
        setTab(t) { this.tab = t; sessionStorage.setItem('admin_tab_banners', t); }
     }">

    <div class="flex gap-1 mb-6 bg-white border border-gray-200 rounded-xl p-1 w-fit">
        <button @click="setTab('slides')"
                :class="tab === 'slides' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100'"
                class="px-5 py-1.5 rounded-lg text-sm font-medium transition-colors">
            Slide Principal
        </button>
        <button @click="setTab('banners')"
                :class="tab === 'banners' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100'"
                class="px-5 py-1.5 rounded-lg text-sm font-medium transition-colors">
            Anuncios
        </button>
    </div>

    {{-- ══════════ TAB: HERO SLIDES ══════════ --}}
    <div x-show="tab === 'slides'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500">Slides del carrusel principal de la página de inicio.</p>
            <button onclick="document.getElementById('modal-hero-create').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90"
                    style="background:#1a4a6b">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo slide
            </button>
        </div>

        <div class="space-y-3">
            @forelse($heroSlides as $slide)
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm flex gap-0">
                    {{-- Preview imagen --}}
                    <div class="w-48 h-28 flex-shrink-0 bg-gray-100 relative overflow-hidden">
                        <img src="{{ $slide->image_url }}"
                             alt="{{ $slide->title }}"
                             class="w-full h-full object-cover"
                             onerror="this.src='https://placehold.co/192x112/e5e7eb/9ca3af?text=Sin+imagen'">
                        @if(!$slide->is_active)
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <span class="text-white text-xs font-semibold">Inactivo</span>
                            </div>
                        @endif
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 p-4 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs text-gray-400">Orden: {{ $slide->display_order }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $slide->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $slide->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            @if($slide->title)
                                <p class="font-semibold text-gray-900 text-sm truncate">{{ $slide->title }}</p>
                            @endif
                            @if($slide->subtitle)
                                <p class="text-xs text-gray-400 truncate">{{ $slide->subtitle }}</p>
                            @endif
                            @if($slide->button_text)
                                <p class="text-xs text-gray-400 mt-1">Botón: <span class="text-gray-600">{{ $slide->button_text }}</span> → {{ $slide->button_link }}</p>
                            @endif
                        </div>
                        <div class="flex gap-2 flex-shrink-0">
                            <button onclick="openEditSlide('{{ $slide->id }}', {{ json_encode($slide->toArray()) }})"
                                    class="text-xs font-semibold px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition-colors"
                                    style="color:#1a4a6b; border-color:#1a4a6b">
                                Editar
                            </button>
                            <form method="POST" action="{{ route('admin.hero-slides.destroy', $slide->id) }}"
                                  onsubmit="return confirm('¿Eliminar este slide?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-colors">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center text-gray-400 text-sm bg-white border border-gray-200 rounded-xl">
                    No hay slides creados aún.
                </div>
            @endforelse
        </div>
    </div>

    {{-- ══════════ TAB: PROMO BANNERS ══════════ --}}
    <div x-show="tab === 'banners'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display:none">

        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500">Banners de la fila de anuncios (4 columnas en el inicio).</p>
            <button onclick="document.getElementById('modal-banner-create').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90"
                    style="background:#1a4a6b">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo anuncio
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($banners as $banner)
                @php $bg = $banner->background_gradient ?? 'linear-gradient(135deg,#1a4a6b,#0f2035)'; @endphp
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    <div class="relative h-24 overflow-hidden" style="background:{{ $bg }}">
                        <div class="absolute inset-0 p-4 flex flex-col justify-center"
                             style="color:{{ $banner->text_color ?? 'white' }}">
                            <p class="font-bold text-sm uppercase leading-tight">{{ $banner->title }}</p>
                            @if($banner->subtitle)
                                <p class="text-xs opacity-80 mt-0.5 uppercase">{{ $banner->subtitle }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $banner->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $banner->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                            <span class="text-xs text-gray-400">Orden: {{ $banner->display_order }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="openEditBanner('{{ $banner->id }}', {{ json_encode($banner->toArray()) }})"
                                    class="flex-1 text-xs font-semibold py-1.5 rounded-lg border hover:bg-gray-50 transition-colors"
                                    style="color:#1a4a6b; border-color:#1a4a6b">
                                Editar
                            </button>
                            <form method="POST" action="{{ route('admin.banners.destroy', $banner->id) }}"
                                  onsubmit="return confirm('¿Eliminar este banner?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-colors">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-16 text-center text-gray-400 text-sm bg-white border border-gray-200 rounded-xl">
                    No hay banners creados aún.
                </div>
            @endforelse
        </div>
    </div>

</div>{{-- fin x-data tabs --}}


{{-- ══════════ MODALES HERO SLIDES ══════════ --}}

{{-- Crear slide --}}
<div id="modal-hero-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Nuevo slide</h3>
            <button onclick="document.getElementById('modal-hero-create').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.hero-slides.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            @include('admin.marketing._hero-slide-fields')
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-hero-create').classList.add('hidden')"
                        class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="flex-1 text-white py-2 rounded-lg text-sm font-semibold hover:opacity-90" style="background:#1a4a6b">Crear slide</button>
            </div>
        </form>
    </div>
</div>

{{-- Editar slide --}}
<div id="modal-hero-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Editar slide</h3>
            <button onclick="document.getElementById('modal-hero-edit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="edit-form-slide" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PATCH')
            @include('admin.marketing._hero-slide-fields', ['editing' => true])
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-hero-edit').classList.add('hidden')"
                        class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="flex-1 text-white py-2 rounded-lg text-sm font-semibold hover:opacity-90" style="background:#1a4a6b">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════ MODALES PROMO BANNERS ══════════ --}}

{{-- Crear banner --}}
<div id="modal-banner-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Nuevo anuncio</h3>
            <button onclick="document.getElementById('modal-banner-create').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.banners.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            @include('admin.marketing._banner-fields')
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-banner-create').classList.add('hidden')"
                        class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="flex-1 text-white py-2 rounded-lg text-sm font-semibold hover:opacity-90" style="background:#1a4a6b">Crear anuncio</button>
            </div>
        </form>
    </div>
</div>

{{-- Editar banner --}}
<div id="modal-banner-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Editar anuncio</h3>
            <button onclick="document.getElementById('modal-banner-edit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="edit-form-banner" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PATCH')
            @include('admin.marketing._banner-fields', ['editing' => true])
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-banner-edit').classList.add('hidden')"
                        class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="flex-1 text-white py-2 rounded-lg text-sm font-semibold hover:opacity-90" style="background:#1a4a6b">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditSlide(id, data) {
    const form = document.getElementById('edit-form-slide');
    form.action = `/admin/marketing/hero-slides/${id}`;

    // Poblar campos de texto
    const set = (name, value) => {
        const el = form.querySelector(`[name="${name}"]`);
        if (!el) return;
        if (el.type === 'checkbox') el.checked = !!value;
        else el.value = value ?? '';
    };
    ['title','subtitle','button_text','button_link','display_order','is_active']
        .forEach(k => set(k, data[k]));

    // Enviar imagen al picker via evento de ventana
    window.dispatchEvent(new CustomEvent('hero-slide-set-image', {
        detail: { url: data.image_url ?? '' }
    }));

    document.getElementById('modal-hero-edit').classList.remove('hidden');
}

function openEditBanner(id, data) {
    const form = document.getElementById('edit-form-banner');
    form.action = `/admin/marketing/banners/${id}`;

    const set = (name, value) => {
        const el = form.querySelector(`[name="${name}"]`);
        if (!el) return;
        if (el.type === 'checkbox') el.checked = !!value;
        else el.value = value ?? '';
    };
    ['title','subtitle','description','button_text','button_link','watermark_text','display_order','is_active']
        .forEach(k => set(k, data[k]));

    // Campos de color: notificar a cada color-picker vía evento de ventana
    window.dispatchEvent(new CustomEvent('banner-set-bg',         { detail: { value: data.background_gradient ?? '#1a4a6b' } }));
    window.dispatchEvent(new CustomEvent('banner-set-text-color', { detail: { value: data.text_color         ?? 'white'   } }));
    window.dispatchEvent(new CustomEvent('banner-set-btn-color',  { detail: { value: data.button_text_color  ?? 'white'   } }));

    document.getElementById('modal-banner-edit').classList.remove('hidden');
}
</script>

@endsection
