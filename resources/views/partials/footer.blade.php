@php
    $storeInfo = \App\Models\SiteContent::getByKey('store_info');
    $info = $storeInfo?->metadata ?? [];
@endphp

<footer class="bg-foreground text-white mt-12">

    {{-- Newsletter Bar --}}
    <div class="bg-muted border-t-[3px] border-secondary py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="bg-secondary/10 p-3 rounded-full">
                        <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-secondary uppercase text-lg">Suscríbete a nuestro boletín</h4>
                        <p class="text-muted-foreground text-sm">Recibe ofertas exclusivas en tu correo</p>
                    </div>
                </div>
                <div class="w-full md:w-auto">
                    @livewire('newsletter-form')
                </div>
            </div>
        </div>
    </div>

    {{-- Main Footer --}}
    <div class="py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                {{-- Información --}}
                <div>
                    <h5 class="font-bold uppercase mb-4 text-lg text-white">Información</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('about') }}" class="text-white/70 hover:text-white transition-colors">Sobre nosotros</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-white/70 hover:text-white transition-colors">Políticas de privacidad</a></li>
                        <li><a href="{{ route('terms') }}" class="text-white/70 hover:text-white transition-colors">Términos y condiciones</a></li>
                        <li><a href="{{ route('faq') }}" class="text-white/70 hover:text-white transition-colors">Preguntas frecuentes</a></li>
                    </ul>
                </div>

                {{-- Servicio al Cliente --}}
                <div>
                    <h5 class="font-bold uppercase mb-4 text-lg text-white">Servicio al Cliente</h5>
                    <ul class="space-y-2 text-sm">
                        @auth
                            @if(auth()->user()->isAdmin())
                                <li><a href="{{ route('admin.home') }}" class="text-white/70 hover:text-white transition-colors">Ir al Panel</a></li>
                            @else
                                <li><a href="{{ route('account.index') }}" class="text-white/70 hover:text-white transition-colors">Mi cuenta</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}" class="text-white/70 hover:text-white transition-colors">Iniciar Sesión</a></li>
                        @endauth
                        <li><a href="#" onclick="$dispatch('cart:toggle')" class="text-white/70 hover:text-white transition-colors">Mi carrito</a></li>
                        @auth
                        <li><a href="{{ route('account.wishlist') }}" class="text-white/70 hover:text-white transition-colors">Lista de deseos</a></li>
                        @endauth
                        <li><a href="{{ route('gift-cards') }}" class="text-white/70 hover:text-white transition-colors">Gift Cards</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-white/70 hover:text-white transition-colors">Ver productos</a></li>
                    </ul>
                </div>

                {{-- Horarios --}}
                <div>
                    <h5 class="font-bold uppercase mb-4 text-lg text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-light" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Horarios
                    </h5>
                    @if(!empty($info['schedule']))
                        @php
                            $days = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
                            $schedule = is_array($info['schedule']) ? $info['schedule'] : [];
                        @endphp
                        <ul class="space-y-1 text-sm">
                            @foreach($schedule as $day => $hours)
                                @if(!empty($hours['start']) && !empty($hours['end']) && empty($hours['closed']))
                                    <li class="flex justify-between text-white/70">
                                        <span class="font-medium capitalize">{{ $day }}</span>
                                        <span>{{ $hours['start'] }} - {{ $hours['end'] }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <ul class="space-y-1 text-sm text-white/70">
                            <li class="flex justify-between"><span class="font-medium">Lun - Vie</span><span>08:00 - 18:00</span></li>
                            <li class="flex justify-between"><span class="font-medium">Sábado</span><span>08:00 - 13:00</span></li>
                            <li class="flex justify-between"><span class="font-medium">Domingo</span><span>Cerrado</span></li>
                        </ul>
                    @endif
                </div>

                {{-- Contacto --}}
                <div>
                    <h5 class="font-bold uppercase mb-4 text-lg text-white">Contacto</h5>
                    <ul class="space-y-3 text-sm mb-4">
                        @if(!empty($info['address']))
                            <li class="flex items-start gap-3 text-white/70">
                                <svg class="w-5 h-5 text-primary-light flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $info['address'] }}</span>
                            </li>
                        @else
                            <li class="flex items-start gap-3 text-white/70">
                                <svg class="w-5 h-5 text-primary-light flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Asunción, Paraguay</span>
                            </li>
                        @endif
                        @if(!empty($info['phone']))
                            <li class="flex items-center gap-3 text-white/70">
                                <svg class="w-5 h-5 text-primary-light flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <a href="tel:{{ preg_replace('/\s+/', '', $info['phone']) }}" class="hover:text-white transition-colors">{{ $info['phone'] }}</a>
                            </li>
                        @else
                            <li class="flex items-center gap-3 text-white/70">
                                <svg class="w-5 h-5 text-primary-light flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>+595 971 000 000</span>
                            </li>
                        @endif
                        @if(!empty($info['email']))
                            <li class="flex items-center gap-3 text-white/70">
                                <svg class="w-5 h-5 text-primary-light flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <a href="mailto:{{ $info['email'] }}" class="hover:text-white transition-colors">{{ $info['email'] }}</a>
                            </li>
                        @else
                            <li class="flex items-center gap-3 text-white/70">
                                <svg class="w-5 h-5 text-primary-light flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>contacto@next1.com.py</span>
                            </li>
                        @endif
                    </ul>

                    {{-- Social Icons --}}
                    <div class="flex gap-2">
                        @if(!empty($info['facebook']))
                            <a href="{{ $info['facebook'] }}" target="_blank" rel="noopener noreferrer"
                               class="w-8 h-8 bg-white/10 hover:bg-primary flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                            </a>
                        @endif
                        @if(!empty($info['instagram']))
                            <a href="{{ $info['instagram'] }}" target="_blank" rel="noopener noreferrer"
                               class="w-8 h-8 bg-white/10 hover:bg-primary flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" fill="hsl(207,60%,28%)"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="hsl(207,60%,28%)" stroke-width="1.5" stroke-linecap="round"/></svg>
                            </a>
                        @endif
                        {{-- Default social icons if none configured --}}
                        @if(empty($info['facebook']) && empty($info['instagram']))
                            <a href="#" class="w-8 h-8 bg-white/10 hover:bg-primary flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                            </a>
                            <a href="#" class="w-8 h-8 bg-white/10 hover:bg-primary flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/></svg>
                            </a>
                            <a href="#" class="w-8 h-8 bg-white/10 hover:bg-primary flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" fill="hsl(207,60%,28%)"/></svg>
                            </a>
                            <a href="#" class="w-8 h-8 bg-white/10 hover:bg-primary flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-1.96C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.4 19.54C5.12 20 12 20 12 20s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="hsl(207,60%,28%)"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Copyright --}}
    <div class="border-t border-white/10 py-4">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-white/50">
                <p>© {{ now()->year }} {{ $info['store_name'] ?? 'NEXT1' }}. Todos los derechos reservados.</p>
                <div class="flex items-center gap-4">
                    <span>Pagos seguros</span>
                    <div class="flex gap-2">
                        <div class="w-10 h-6 bg-white/20 rounded"></div>
                        <div class="w-10 h-6 bg-white/20 rounded"></div>
                        <div class="w-10 h-6 bg-white/20 rounded"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</footer>
