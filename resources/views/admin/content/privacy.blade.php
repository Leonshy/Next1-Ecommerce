@extends('layouts.admin')
@section('title', 'Políticas de Privacidad')

@section('content')
<form method="POST" action="{{ route('admin.content.privacy.update') }}">
    @csrf

    <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Políticas de Privacidad</h2>
                @if($record?->metadata['last_updated'] ?? null)
                    <p class="text-xs text-gray-400 mt-0.5">Última actualización: {{ $record->metadata['last_updated'] }}</p>
                @endif
            </div>
            <a href="{{ route('privacy') }}" target="_blank"
               class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                Ver página pública
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título de la página *</label>
            <input type="text" name="title" value="{{ $record?->title ?? 'Políticas de Privacidad' }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Contenido</label>
            <p class="text-xs text-gray-400 mb-2">Podés usar HTML básico: &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;, &lt;em&gt;</p>
            <textarea name="content" rows="20"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $record?->content ?? '' }}</textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-gray-700 transition-colors">
                Guardar cambios
            </button>
        </div>
    </div>
</form>
@endsection
