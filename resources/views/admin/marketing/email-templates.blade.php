@extends('layouts.admin')
@section('title', 'Plantillas de Email')

@section('content')
<div class="max-w-4xl">

    <div class="mb-6">
        <p class="text-sm text-gray-500">Editá el asunto y contenido HTML de cada email que envía la tienda automáticamente.</p>
    </div>

    <div class="space-y-3">
        @foreach($templates as $template)
        @php
            $icons = [
                'newsletter_verification' => '📧',
                'order_confirmation'      => '🛒',
                'order_status_update'     => '📦',
                'account_locked'          => '🔒',
                'new_login_alert'         => '🛡️',
                'two_factor_code'         => '🔐',
            ];
            $icon = $icons[$template->template_key] ?? '✉️';
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-4 flex items-center gap-4">
            <span class="text-2xl shrink-0">{{ $icon }}</span>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm">{{ $template->name }}</p>
                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $template->description }}</p>
                <p class="text-xs text-gray-300 mt-1 font-mono">{{ $template->template_key }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.settings.email-templates.preview', $template->template_key) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9.293 0a9 9 0 1115.586 0A9 9 0 015.707 12z"/>
                    </svg>
                    Vista previa
                </a>
                <a href="{{ route('admin.settings.email-templates.edit', $template->template_key) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg text-white transition-colors"
                   style="background:var(--brand-primary)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-100 rounded-xl px-5 py-4 text-xs text-blue-700">
        <p class="font-semibold mb-1">¿Cómo usar las variables?</p>
        <p>En el asunto y cuerpo del email podés usar variables como <code class="bg-blue-100 px-1 rounded font-mono">@{{ nombre_variable }}</code> que serán reemplazadas automáticamente con los datos reales al enviarse.</p>
        <p class="mt-1">Cada plantilla tiene su propio conjunto de variables disponibles, que se muestran al editar.</p>
    </div>

</div>
@endsection
