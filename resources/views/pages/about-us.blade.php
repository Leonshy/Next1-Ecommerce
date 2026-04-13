<x-app-layout>
@php
    $meta     = $content?->metadata ?? [];
    $title    = $meta['title']          ?? $content?->title ?? 'Sobre Nosotros';
    $subtitle = $meta['subtitle']       ?? '';
    $mission  = $meta['mission']        ?? '';
    $vision   = $meta['vision']         ?? '';
    $values   = $meta['values']         ?? [];
    $ctaTitle = $meta['ctaTitle']       ?? '';
    $ctaDesc  = $meta['ctaDescription'] ?? '';

    $iconPaths = [
        'Heart'  => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
        'Users'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        'Award'  => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
        'Truck'  => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
        'Target' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'Eye'    => 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        'Star'   => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        'Shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    ];
@endphp

{{-- Hero --}}
<section class="bg-primary text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-lg text-white/80 max-w-2xl mx-auto leading-relaxed">{{ $subtitle }}</p>
        @endif
    </div>
</section>

{{-- Misión y Visión --}}
@if($mission || $vision)
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            @if($mission)
            <div class="bg-muted rounded-xl p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 bg-primary rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-foreground">Nuestra Misión</h2>
                </div>
                <p class="text-muted-foreground leading-relaxed">{{ $mission }}</p>
            </div>
            @endif
            @if($vision)
            <div class="bg-muted rounded-xl p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 bg-accent rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPaths['Eye'] }}"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-foreground">Nuestra Visión</h2>
                </div>
                <p class="text-muted-foreground leading-relaxed">{{ $vision }}</p>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- Valores --}}
@if(count($values))
<section class="py-16 bg-background border-t border-border">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-foreground">Nuestros Valores</h2>
            <div class="w-12 h-1 bg-accent mx-auto mt-3"></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 max-w-5xl mx-auto">
            @foreach($values as $val)
            @php $iconKey = $val['icon'] ?? 'Star'; $path = $iconPaths[$iconKey] ?? $iconPaths['Star']; @endphp
            <div class="bg-white border border-border rounded-xl p-6 text-center hover:shadow-soft transition-shadow">
                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
                    </svg>
                </div>
                <h3 class="font-bold text-foreground mb-2">{{ $val['title'] ?? '' }}</h3>
                <p class="text-sm text-muted-foreground leading-relaxed">{{ $val['description'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
@if($ctaTitle)
<section class="py-16 bg-primary text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-2xl font-bold mb-3">{{ $ctaTitle }}</h2>
        @if($ctaDesc)
            <p class="text-white/80 mb-7 max-w-xl mx-auto">{{ $ctaDesc }}</p>
        @endif
        <a href="{{ route('products.index') }}"
           class="inline-block bg-accent text-white font-semibold px-8 py-3 hover:opacity-90 transition-opacity">
            Ver todos los productos
        </a>
    </div>
</section>
@endif

</x-app-layout>
