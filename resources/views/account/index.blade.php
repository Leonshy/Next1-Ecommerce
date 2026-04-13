<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Mi Cuenta</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Sidebar nav --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 h-fit">
                <nav class="space-y-0.5">
                    <a href="{{ route('account.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg bg-blue-50 text-blue-700 font-semibold text-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Resumen
                    </a>
                    <a href="{{ route('account.orders') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 font-semibold text-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Pedidos
                    </a>
                    <a href="{{ route('account.addresses') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 font-semibold text-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Direcciones
                    </a>
                    <a href="{{ route('account.wishlist') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 font-semibold text-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Favoritos
                    </a>
                    <a href="{{ route('account.profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 font-semibold text-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Configuración
                    </a>
                </nav>
            </div>

            {{-- Contenido --}}
            <div class="md:col-span-2 space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h2 class="font-semibold mb-3">Hola, {{ $user->profile?->full_name ?? $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="p-4 border-b border-gray-100 flex justify-between">
                        <h3 class="font-semibold text-sm">Últimos pedidos</h3>
                        <a href="{{ route('account.orders') }}" class="text-xs text-blue-600 hover:underline">Ver todos</a>
                    </div>
                    @forelse($orders as $order)
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50 last:border-0">
                            <div>
                                <p class="text-sm font-medium">{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y') }}</p>
                            </div>
                            <span class="text-sm font-semibold">Gs. {{ $order->formatted_total }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                                {{ $order->status_label }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 p-4 text-center">No tenés pedidos aún</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
