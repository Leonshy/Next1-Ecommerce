<x-app-layout>
@php
    $title       = $content?->title ?? 'Términos y Condiciones';
    $htmlContent = $content?->content ?? '';
    $lastUpdated = $content?->metadata['last_updated'] ?? null;
@endphp

{{-- Hero --}}
<section class="bg-primary text-white py-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-2">{{ $title }}</h1>
        @if($lastUpdated)
            <p class="text-white/70 text-sm">Última actualización: {{ \Carbon\Carbon::parse($lastUpdated)->format('d/m/Y') }}</p>
        @endif
    </div>
</section>

<div class="container mx-auto px-4 py-14 max-w-4xl">

    @if($htmlContent)
        <div class="bg-white border border-border rounded-xl p-8 md:p-10 prose prose-sm max-w-none
                    prose-headings:text-foreground prose-headings:font-bold prose-headings:border-b prose-headings:border-border prose-headings:pb-2 prose-headings:mb-4
                    prose-p:text-muted-foreground prose-p:leading-relaxed
                    prose-li:text-muted-foreground prose-li:leading-relaxed
                    prose-strong:text-foreground">
            {!! $htmlContent !!}
        </div>
    @else
        <div class="text-center py-16 text-muted-foreground">
            <p>Contenido no disponible.</p>
        </div>
    @endif

</div>

</x-app-layout>
