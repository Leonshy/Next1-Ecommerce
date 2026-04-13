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
    <div class="col-span-2">
        <x-admin.color-picker
            name="background_gradient"
            :value="old('background_gradient', '#1a4a6b')"
            label="Color de fondo"
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
