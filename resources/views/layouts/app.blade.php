<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $seo = \App\Models\SeoSetting::forPage($seoPage ?? 'global');
    @endphp

    <title>{{ $seo?->meta_title ?? $title ?? config('app.name') }}</title>

    @if($seo?->meta_description)
        <meta name="description" content="{{ $seo->meta_description }}">
    @endif
    @if($seo?->keywords)
        <meta name="keywords" content="{{ $seo->keywords }}">
    @endif
    @if($seo?->og_image)
        <meta property="og:image" content="{{ $seo->og_image }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {!! \App\Services\AnalyticsService::getScripts() !!}
</head>
<body class="font-sans antialiased bg-white text-gray-900">

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
