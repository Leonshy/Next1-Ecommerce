<x-guest-layout>
@php $hcaptcha = \App\Models\HcaptchaSetting::first(); $captchaActive = $hcaptcha?->is_enabled && $hcaptcha->protect_login && $hcaptcha->site_key; @endphp
<div class="w-full max-w-md" x-data="{ showPassword: false, resetOpen: false, resetEmail: '', captchaDone: {{ $captchaActive ? 'false' : 'true' }} }"
     @captcha-verified.document="captchaDone = true"
     @captcha-expired.document="captchaDone = false">

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

        {{-- Header --}}
        @php $storeInfo = \App\Models\SiteContent::getByKey('store_info')?->metadata ?? []; @endphp
        <div class="px-8 pt-8 pb-4 text-center">
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
            <h1 class="text-2xl font-bold text-gray-900">Bienvenido</h1>
            <p class="text-sm text-gray-500 mt-1">Inicia sesión o crea una cuenta para continuar</p>
        </div>

        {{-- Tabs --}}
        <div class="grid grid-cols-2 mx-8 mb-6 bg-gray-100 rounded-xl p-1">
            <span class="text-center py-2 px-4 rounded-lg bg-white text-primary font-semibold text-sm shadow-sm">
                Iniciar Sesión
            </span>
            <a href="{{ route('register') }}"
               class="text-center py-2 px-4 rounded-lg text-gray-500 font-medium text-sm hover:text-gray-700 transition-colors">
                Registrarse
            </a>
        </div>

        {{-- Form --}}
        <div class="px-8 pb-8">

            {{-- Session status --}}
            @if (session('status'))
                <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}"
                  class="space-y-4 @error('email') ring-2 ring-red-400 rounded-xl p-4 -mx-4 @enderror">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input id="email" name="email" type="email"
                               value="{{ old('email') }}"
                               required autofocus autocomplete="username"
                               placeholder="tu@email.com"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input id="password" name="password"
                               :type="showPassword ? 'text' : 'password'"
                               required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors">
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember_me"
                               class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/30">
                        <span class="text-sm text-gray-600">Recordarme</span>
                    </label>
                    <button type="button" @click="resetOpen = true; resetEmail = document.getElementById('email').value"
                            class="text-sm text-gray-500 hover:text-primary transition-colors">
                        ¿Olvidaste tu contraseña?
                    </button>
                </div>

                {{-- hCaptcha --}}
                @if($captchaActive)
                    <div class="flex justify-center">
                        <div class="h-captcha"
                             data-sitekey="{{ $hcaptcha->site_key }}"
                             data-callback="onHcaptchaVerified"
                             data-expired-callback="onHcaptchaExpired"></div>
                    </div>
                @endif

                {{-- Submit --}}
                <button type="submit"
                        class="w-full bg-primary text-white py-2.5 rounded-lg font-semibold text-sm hover:bg-primary-light transition-colors mt-2">
                    Iniciar Sesión
                </button>

                {{-- Error de autenticación --}}
                @error('email')
                    <div class="flex items-start gap-2 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                        <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    </div>
                @enderror
            </form>

            {{-- Separador Google --}}
            <div class="flex items-center gap-3 mt-6">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-xs text-gray-400 font-medium">O continúa con</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            <a href="{{ route('auth.google') }}"
               :class="!captchaDone ? 'pointer-events-none opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
               class="mt-4 flex items-center justify-center gap-3 w-full border border-gray-200 rounded-lg py-2.5 text-sm font-medium text-gray-700 transition-colors"
               :title="!captchaDone ? 'Completá el captcha primero' : ''">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Acceder con Google
            </a>

            {{-- Divider --}}
            <p class="text-center text-sm text-gray-500 mt-5">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Regístrate</a>
            </p>
        </div>
    </div>

    {{-- Forgot Password Modal --}}
    <div x-show="resetOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         style="display:none"
         @click.self="resetOpen = false">

        <div x-show="resetOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">

            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Recuperar contraseña</h2>
                <button type="button" @click="resetOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-500 mb-4">
                Ingresa tu email y te enviaremos un enlace para restablecer tu contraseña.
            </p>

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <input name="email" type="email"
                           x-model="resetEmail"
                           placeholder="tu@email.com"
                           required
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors">
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="resetOpen = false"
                            class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 bg-primary text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-primary-light transition-colors">
                        Enviar enlace
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@if($captchaActive)
    <script>
        function onHcaptchaVerified() { document.dispatchEvent(new CustomEvent('captcha-verified')); }
        function onHcaptchaExpired()  { document.dispatchEvent(new CustomEvent('captcha-expired')); }
    </script>
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
@endif
</x-guest-layout>
