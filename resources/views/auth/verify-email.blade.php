<x-guest-layout>
@php $storeInfo = \App\Models\SiteContent::getByKey('store_info')?->metadata ?? []; @endphp

<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

        {{-- Header --}}
        <div class="px-8 pt-8 pb-6 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 mb-5">
                @if(!empty($storeInfo['logoUrl']))
                    <img src="{{ $storeInfo['logoUrl'] }}" alt="{{ $storeInfo['storeName'] ?? config('app.name') }}"
                         class="h-10 w-auto object-contain">
                @else
                    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                        <span class="text-white font-black text-sm">N1</span>
                    </div>
                    <span class="text-primary font-black text-2xl tracking-tight">{{ $storeInfo['storeName'] ?? config('app.name') }}</span>
                @endif
            </a>

            {{-- Ícono de sobre --}}
            <div class="mx-auto mb-4 w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900">Verificá tu email</h1>
            <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                Te enviamos un enlace de verificación. Revisá tu bandeja de entrada y hacé clic en el enlace para activar tu cuenta.
            </p>
        </div>

        <div class="px-8 pb-8 space-y-4">

            {{-- Alerta de éxito --}}
            @if (session('status') == 'verification-link-sent')
                <div class="flex items-start gap-3 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-green-700">Se envió un nuevo enlace de verificación a tu dirección de email.</p>
                </div>
            @endif

            {{-- Botón reenviar --}}
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                        class="w-full bg-primary text-white py-2.5 rounded-lg font-semibold text-sm hover:bg-primary-light transition-colors">
                    Reenviar email de verificación
                </button>
            </form>

            {{-- Separador + logout --}}
            <div class="flex items-center gap-3">
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-xs text-gray-400">o</span>
                <div class="flex-1 h-px bg-gray-100"></div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full border border-gray-200 text-gray-600 py-2.5 rounded-lg font-medium text-sm hover:bg-gray-50 transition-colors">
                    Cerrar sesión
                </button>
            </form>

            <p class="text-center text-xs text-gray-400">
                ¿No encontrás el email? Revisá la carpeta de spam.
            </p>
        </div>

    </div>
</div>
</x-guest-layout>
