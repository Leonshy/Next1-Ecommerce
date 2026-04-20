<x-guest-layout>
<div class="w-full max-w-md" x-data="{ resending: false }">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

        {{-- Header --}}
        @php $storeInfo = \App\Models\SiteContent::getByKey('store_info')?->metadata ?? []; @endphp
        <div class="px-8 pt-8 pb-6 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 mb-4">
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

            <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900">Verificación en dos pasos</h1>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                Enviamos un código de 6 dígitos a tu correo.<br>
                Ingresalo para continuar.
            </p>
        </div>

        <div class="px-8 pb-8">

            {{-- Status --}}
            @if(session('status'))
                <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Código --}}
            <form method="POST" action="{{ route('2fa.verify') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 text-center">Código de verificación</label>
                    <input type="text" name="code"
                           inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
                           autofocus autocomplete="one-time-code"
                           placeholder="• • • • • •"
                           class="w-full text-center text-3xl font-mono font-bold tracking-[0.5em] py-4 border-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors @error('code') border-red-400 @else border-gray-200 @enderror"
                           oninput="this.value=this.value.replace(/\D/g,'')">
                    @error('code')
                        <p class="mt-2 text-sm text-red-500 text-center">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-400 text-center">El código expira en 10 minutos.</p>
                </div>

                <button type="submit"
                        class="w-full bg-primary text-white py-3 rounded-xl font-semibold text-sm hover:bg-primary-light transition-colors">
                    Verificar acceso
                </button>
            </form>

            {{-- Reenviar --}}
            <div class="mt-5 text-center">
                <p class="text-sm text-gray-500 mb-2">¿No recibiste el código?</p>
                <form method="POST" action="{{ route('2fa.resend') }}" @submit="resending = true">
                    @csrf
                    <button type="submit"
                            :disabled="resending"
                            class="text-sm font-semibold text-primary hover:underline disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
                        <span x-show="!resending">Reenviar código</span>
                        <span x-show="resending" style="display:none">Enviando...</span>
                    </button>
                </form>
            </div>

            {{-- Volver --}}
            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                    ← Volver al inicio de sesión
                </a>
            </div>

        </div>
    </div>
</div>
</x-guest-layout>
