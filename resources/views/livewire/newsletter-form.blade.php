<div>
    @if($submitted)
        <div class="flex items-start gap-3 bg-white/80 border border-green-200 rounded-xl px-5 py-4 max-w-md">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-gray-800">¡Listo! Revisá tu email</p>
                <p class="text-xs text-gray-500 mt-0.5">Te enviamos un enlace de confirmación. Revisá también la carpeta de spam.</p>
            </div>
        </div>
    @else
        <form wire:submit="submit" class="w-full max-w-md">
            <div class="flex gap-2">
                <div class="flex-1 relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <input type="email"
                           wire:model="email"
                           placeholder="tu@email.com"
                           autocomplete="email"
                           class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-secondary/40 focus:border-secondary transition-colors">
                </div>
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="flex items-center gap-1.5 px-5 py-2.5 bg-secondary text-white text-sm font-semibold rounded-xl hover:bg-secondary/90 active:scale-95 transition-all disabled:opacity-60 whitespace-nowrap flex-shrink-0">
                    <span wire:loading.remove>Suscribirme</span>
                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </button>
            </div>

            @error('email')
                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </p>
            @enderror
            @error('hcaptchaToken')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror

            <p class="text-xs text-muted-foreground mt-2 opacity-70">Sin spam. Podés darte de baja cuando quieras.</p>
        </form>
    @endif
</div>
