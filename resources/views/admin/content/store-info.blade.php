@extends('layouts.admin')
@section('title', 'Info de la Tienda')

@section('content')
<form method="POST" action="{{ route('admin.content.store-info.update') }}" x-data="{ activeTab: 'general' }">
    @csrf

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 bg-white border border-gray-200 rounded-xl p-1 w-fit">
        @foreach([
            ['general',  'Datos Generales'],
            ['contacto', 'Contacto'],
            ['horarios', 'Horarios'],
            ['sociales',  'Redes Sociales'],
        ] as [$tab, $label])
        <button type="button"
                @click="activeTab = '{{ $tab }}'"
                :class="activeTab === '{{ $tab }}' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100'"
                class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- Tab: Datos Generales --}}
    <div x-show="activeTab === 'general'" class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Datos Generales</h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la tienda *</label>
            <input type="text" name="storeName" value="{{ $data['storeName'] ?? '' }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Slogan</label>
            <input type="text" name="slogan" value="{{ $data['slogan'] ?? '' }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción corta</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $data['description'] ?? '' }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
            <input type="text" name="address" value="{{ $data['address'] ?? '' }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">URL de Google Maps (embed)</label>
            <input type="url" name="mapUrl" value="{{ $data['mapUrl'] ?? '' }}"
                   placeholder="https://www.google.com/maps/embed?..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-400 mt-1">Pegá el enlace de "Compartir &gt; Incorporar un mapa" de Google Maps.</p>
        </div>
    </div>

    {{-- Tab: Contacto --}}
    <div x-show="activeTab === 'contacto'" class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Información de Contacto</h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email de contacto</label>
            <input type="email" name="email" value="{{ $data['email'] ?? '' }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono 1</label>
                <input type="text" name="phone1" value="{{ $data['phone1'] ?? '' }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono 2</label>
                <input type="text" name="phone2" value="{{ $data['phone2'] ?? '' }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
            <input type="text" name="whatsapp" value="{{ $data['whatsapp'] ?? '' }}"
                   placeholder="595981000000"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-400 mt-1">Número completo con código de país, sin + (ej: 595981000000)</p>
        </div>
    </div>

    {{-- Tab: Horarios --}}
    <div x-show="activeTab === 'horarios'" class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-5">Horarios de Atención</h2>
        <div class="space-y-3">
            @php
                $days = [
                    'lunes'     => 'Lunes',
                    'martes'    => 'Martes',
                    'miercoles' => 'Miércoles',
                    'jueves'    => 'Jueves',
                    'viernes'   => 'Viernes',
                    'sabado'    => 'Sábado',
                    'domingo'   => 'Domingo',
                ];
            @endphp
            @foreach($days as $key => $label)
                @php
                    $day = $data['schedule'][$key] ?? ['start' => '08:00', 'end' => '18:00', 'closed' => false];
                @endphp
                <div x-data="{ closed: {{ $day['closed'] ? 'true' : 'false' }} }"
                     class="flex items-center gap-4 py-2 border-b border-gray-50 last:border-0">
                    <div class="w-28 text-sm font-medium text-gray-700">{{ $label }}</div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="schedule_{{ $key }}_closed" value="1"
                               @change="closed = $event.target.checked"
                               {{ $day['closed'] ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        <span class="text-sm text-gray-500">Cerrado</span>
                    </label>
                    <div x-show="!closed" class="flex items-center gap-2">
                        <input type="time" name="schedule_{{ $key }}_start" value="{{ $day['start'] }}"
                               class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <span class="text-gray-400 text-sm">a</span>
                        <input type="time" name="schedule_{{ $key }}_end" value="{{ $day['end'] }}"
                               class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div x-show="closed" class="text-sm text-red-400 italic">No abre</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Tab: Redes Sociales --}}
    <div x-show="activeTab === 'sociales'" class="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Redes Sociales</h2>
        @php
            $nets = [
                'facebook'  => ['label' => 'Facebook', 'placeholder' => 'https://facebook.com/tutienda'],
                'instagram' => ['label' => 'Instagram', 'placeholder' => 'https://instagram.com/tutienda'],
                'twitter'   => ['label' => 'X / Twitter', 'placeholder' => 'https://twitter.com/tutienda'],
                'youtube'   => ['label' => 'YouTube', 'placeholder' => 'https://youtube.com/@tucanal'],
                'tiktok'    => ['label' => 'TikTok', 'placeholder' => 'https://tiktok.com/@tutienda'],
            ];
        @endphp
        @foreach($nets as $net => $info)
            @php $sn = $data['socialNetworks'][$net] ?? ['url' => '', 'enabled' => false]; @endphp
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer w-8">
                    <input type="checkbox" name="social_{{ $net }}_enabled" value="1"
                           {{ $sn['enabled'] ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600">
                </label>
                <span class="w-28 text-sm font-medium text-gray-700">{{ $info['label'] }}</span>
                <input type="url" name="social_{{ $net }}_url" value="{{ $sn['url'] }}"
                       placeholder="{{ $info['placeholder'] }}"
                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        @endforeach
    </div>

    <div class="mt-6 flex justify-end">
        <button type="submit"
                class="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-gray-700 transition-colors">
            Guardar cambios
        </button>
    </div>
</form>
@endsection
