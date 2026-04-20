<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $storeInfo  = \App\Models\SiteContent::getByKey('store_info')?->metadata ?? [];
        $storeName  = $storeInfo['name'] ?? config('app.name');
        $seoDb      = \App\Models\SeoSetting::forPage($seoPage ?? 'global');

        // $seoOverride (array) pasado desde el controlador tiene prioridad sobre la BD
        $o          = $seoOverride ?? [];
        $seoTitle       = $o['title']       ?? $seoDb?->meta_title       ?? $title ?? $storeName;
        $seoDescription = $o['description'] ?? $seoDb?->meta_description ?? '';
        $seoKeywords    = $o['keywords']    ?? $seoDb?->keywords          ?? '';
        $seoCanonical   = $o['canonical']   ?? $seoDb?->canonical_url     ?? request()->url();
        $seoOgImage     = $o['og_image']    ?? $seoDb?->og_image          ?? '';
        $seoOgType      = $o['og_type']     ?? (isset($seoPage) && $seoPage === 'products' ? 'product' : 'website');
    @endphp

    <title>{{ $seoTitle }}</title>

    @if(!empty($storeInfo['faviconUrl']))
        <link rel="icon" href="{{ $storeInfo['faviconUrl'] }}">
    @endif

    {{-- Meta básicos --}}
    <meta name="description" content="{{ $seoDescription }}">
    @if($seoKeywords)
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    <link rel="canonical" href="{{ $seoCanonical }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="{{ $seoOgType }}">
    <meta property="og:title"       content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url"         content="{{ $seoCanonical }}">
    <meta property="og:site_name"   content="{{ $storeName }}">
    @if($seoOgImage)
        <meta property="og:image"   content="{{ $seoOgImage }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    @if($seoOgImage)
        <meta name="twitter:image"   content="{{ $seoOgImage }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {!! \App\Services\AnalyticsService::getScripts() !!}
</head>
<body class="font-sans antialiased bg-background text-foreground">

    @include('partials.header')

    @livewire('cart')

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <main>
        {{ $slot }}
    </main>

    @include('partials.footer')
    @include('partials.whatsapp-button')

    @livewireScripts
</body>
</html>
