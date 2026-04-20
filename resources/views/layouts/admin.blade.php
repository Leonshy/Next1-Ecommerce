<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Next1</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --brand-primary: #1a4a6b;
            --brand-accent:  #e07b1d;
        }
        .sidebar-link-active {
            background-color: hsl(207 60% 28% / 0.08);
            color: var(--brand-primary);
            font-weight: 600;
        }
        .sidebar-link-active .nav-icon { color: var(--brand-primary); }
        .sidebar-link:hover { background-color: hsl(207 60% 28% / 0.05); }
        .sidebar-child-active {
            background-color: hsl(207 60% 28% / 0.1);
            color: var(--brand-primary);
            font-weight: 600;
        }
    </style>
</head>
<body class="font-sans antialiased" style="background:#F1F5F9">

<div class="flex min-h-screen">

    {{-- ── Sidebar ────────────────────────────────────────────────────────── --}}
    <aside class="w-72 bg-white border-r border-gray-200 flex flex-col flex-shrink-0 fixed inset-y-0 z-30">

        {{-- Logo --}}
        <div class="px-5 py-4 border-b border-gray-100">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/next1-logo.png') }}" alt="Next1" class="h-10 object-contain">
            </a>
            <p class="text-xs text-gray-400 mt-1 font-medium tracking-wide uppercase">Panel de Administración</p>
        </div>

        {{-- User info --}}
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0"
                     style="background:var(--brand-primary)">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                              style="background:hsl(207 60% 28% / 0.1); color:var(--brand-primary)">
                            {{ ucfirst(auth()->user()->getHighestRole()) }}
                        </span>
                        @if(auth()->user()->two_factor_enabled)
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-green-100 text-green-700" title="Verificación en dos pasos activa">
                                2FA ✓
                            </span>
                        @else
                            <a href="{{ route('account.profile.edit') }}"
                               class="text-xs px-2 py-0.5 rounded-full font-medium bg-amber-100 text-amber-700 hover:bg-amber-200 transition-colors" title="Activar verificación en dos pasos">
                                2FA off
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto text-sm">

            {{-- Dashboard --}}
            @php $isDash = request()->routeIs('admin.home'); @endphp
            <a href="{{ route('admin.home') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ $isDash ? 'sidebar-link-active' : 'text-gray-600' }}">
                <svg class="nav-icon w-4 h-4 shrink-0 {{ $isDash ? '' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10-2a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/>
                </svg>
                Dashboard
                @if($isDash)
                    <svg class="w-3.5 h-3.5 ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif
            </a>

            {{-- Tienda --}}
            @php $tiendaOpen = request()->routeIs('admin.productos.*') || request()->routeIs('admin.pedidos.*'); @endphp
            <div x-data="{ open: {{ $tiendaOpen ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ $tiendaOpen ? 'text-[#1a4a6b] font-semibold' : 'text-gray-600' }}">
                    <svg class="nav-icon w-4 h-4 shrink-0 {{ $tiendaOpen ? 'text-[#1a4a6b]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span class="flex-1 text-left">Tienda</span>
                    <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 text-gray-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                     class="ml-4 mt-0.5 space-y-0.5 border-l-2 pl-3" style="border-color:hsl(207 60% 28% / 0.15)">
                    @php $isProd = request()->routeIs('admin.productos.*'); @endphp
                    <a href="{{ route('admin.productos.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors {{ $isProd ? 'sidebar-child-active' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                        Productos
                    </a>
                    @php $isPed = request()->routeIs('admin.pedidos.*'); @endphp
                    <a href="{{ route('admin.pedidos.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors {{ $isPed ? 'sidebar-child-active' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Pedidos
                    </a>
                </div>
            </div>

            @can('admin')

            {{-- Biblioteca de Medios --}}
            @php $isMedia = request()->routeIs('admin.media.*'); @endphp
            <a href="{{ route('admin.media.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ $isMedia ? 'sidebar-link-active' : 'text-gray-600' }}">
                <svg class="nav-icon w-4 h-4 shrink-0 {{ $isMedia ? '' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Biblioteca de Medios
                @if($isMedia)
                    <svg class="w-3.5 h-3.5 ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif
            </a>

            {{-- Contenido --}}
            @php $contenidoOpen = request()->routeIs('admin.content.*'); @endphp
            <div x-data="{ open: {{ $contenidoOpen ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ $contenidoOpen ? 'text-[#1a4a6b] font-semibold' : 'text-gray-600' }}">
                    <svg class="nav-icon w-4 h-4 shrink-0 {{ $contenidoOpen ? 'text-[#1a4a6b]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="flex-1 text-left">Contenido</span>
                    <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 text-gray-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                     class="ml-4 mt-0.5 space-y-0.5 border-l-2 pl-3" style="border-color:hsl(207 60% 28% / 0.15)">
                    @foreach([
                        ['route' => 'admin.content.store-info',  'match' => 'admin.content.store-info*',  'label' => 'Info de la Tienda'],
                        ['route' => 'admin.content.about-us',    'match' => 'admin.content.about-us*',    'label' => 'Quiénes Somos'],
                        ['route' => 'admin.content.faq',         'match' => 'admin.content.faq*',         'label' => 'Preguntas Frecuentes'],
                        ['route' => 'admin.content.terms',       'match' => 'admin.content.terms*',       'label' => 'Términos y Condiciones'],
                        ['route' => 'admin.content.privacy',     'match' => 'admin.content.privacy*',     'label' => 'Pol. de Privacidad'],
                    ] as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs($item['match']) ? 'sidebar-child-active' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Marketing --}}
            @php $marketingOpen = request()->routeIs('admin.campanas.*','admin.banners.*','admin.newsletter.*','admin.gift-cards.*'); @endphp
            <div x-data="{ open: {{ $marketingOpen ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ $marketingOpen ? 'text-[#1a4a6b] font-semibold' : 'text-gray-600' }}">
                    <svg class="nav-icon w-4 h-4 shrink-0 {{ $marketingOpen ? 'text-[#1a4a6b]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    <span class="flex-1 text-left">Marketing</span>
                    <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 text-gray-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                     class="ml-4 mt-0.5 space-y-0.5 border-l-2 pl-3" style="border-color:hsl(207 60% 28% / 0.15)">
                    @foreach([
                        ['route' => 'admin.campanas.index',   'match' => 'admin.campanas.*',   'label' => 'Campañas'],
                        ['route' => 'admin.banners.index',    'match' => 'admin.banners.*',    'label' => 'Espacios Publicitarios'],
                        ['route' => 'admin.newsletter.index', 'match' => 'admin.newsletter.*', 'label' => 'Newsletter'],
                        ['route' => 'admin.gift-cards.index', 'match' => 'admin.gift-cards.*', 'label' => 'Gift Cards'],
                    ] as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs($item['match']) ? 'sidebar-child-active' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Usuarios --}}
            @php $isUsers = request()->routeIs('admin.usuarios.*'); @endphp
            <a href="{{ route('admin.usuarios.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ $isUsers ? 'sidebar-link-active' : 'text-gray-600' }}">
                <svg class="nav-icon w-4 h-4 shrink-0 {{ $isUsers ? '' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Usuarios
                @if($isUsers)
                    <svg class="w-3.5 h-3.5 ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif
            </a>

            {{-- Configuración --}}
            @php $configOpen = request()->routeIs('admin.settings.*'); @endphp
            <div x-data="{ open: {{ $configOpen ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ $configOpen ? 'text-[#1a4a6b] font-semibold' : 'text-gray-600' }}">
                    <svg class="nav-icon w-4 h-4 shrink-0 {{ $configOpen ? 'text-[#1a4a6b]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="flex-1 text-left">Configuración</span>
                    <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 text-gray-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                     class="ml-4 mt-0.5 space-y-0.5 border-l-2 pl-3" style="border-color:hsl(207 60% 28% / 0.15)">
                    @foreach([
                        ['route' => 'admin.settings.shipping',  'match' => 'admin.settings.shipping',  'label' => 'Envíos'],
                        ['route' => 'admin.settings.payments',  'match' => 'admin.settings.payments',  'label' => 'Pagos'],
                        ['route' => 'admin.settings.seo',       'match' => 'admin.settings.seo*',      'label' => 'SEO'],
                        ['route' => 'admin.settings.analytics', 'match' => 'admin.settings.analytics', 'label' => 'Analytics'],
                        ['route' => 'admin.settings.email',     'match' => 'admin.settings.email',     'label' => 'Email SMTP'],
                        ['route' => 'admin.settings.hcaptcha',    'match' => 'admin.settings.hcaptcha',     'label' => 'hCaptcha'],
                        ['route' => 'admin.settings.maintenance', 'match' => 'admin.settings.maintenance*', 'label' => 'Mantenimiento'],
                    ] as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs($item['match']) ? 'sidebar-child-active' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            @endcan
        </nav>

        {{-- Footer --}}
        <div class="border-t border-gray-100 p-3 space-y-0.5">
            <a href="{{ route('account.profile.edit') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-gray-600">
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Mi perfil y seguridad
            </a>
            <a href="{{ route('home') }}" target="_blank"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-gray-600">
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Ver Tienda
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-red-500 hover:text-red-600 hover:bg-red-50">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main ───────────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-h-screen" style="margin-left:18rem">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-20 px-6 py-4">
            <h1 class="text-xl font-bold" style="color:var(--brand-primary)">@yield('title', 'Dashboard')</h1>
        </header>

        <main class="flex-1 p-6">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="mb-5 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                    <span class="flex-1">{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                    <span class="flex-1">{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
