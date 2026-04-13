<div class="relative w-full">

    <form action="{{ route('products.index') }}" method="GET" class="flex h-10">
        <input type="text"
               name="q"
               wire:model.live.debounce.300ms="query"
               value="{{ request('q') }}"
               placeholder="Buscar productos..."
               autocomplete="off"
               class="flex-1 min-w-0 border border-r-0 border-border bg-white px-4 py-2 text-sm text-foreground placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors">

        @if($query)
            <button type="button" wire:click="clearSearch"
                    class="border border-r-0 border-border bg-white px-2 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif

        <button type="submit"
                class="flex items-center justify-center bg-accent hover:bg-accent-hover text-white px-5 rounded-r-full transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </button>
    </form>

    {{-- Dropdown de sugerencias --}}
    @if($isOpen && count($results) > 0)
        <div class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-lg border border-border z-50 overflow-hidden">
            @foreach($results as $result)
                <a href="{{ $result['url'] }}" wire:click="clearSearch"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-muted/50 transition-colors">
                    @if($result['image'])
                        <img src="{{ $result['image'] }}" alt="{{ $result['name'] }}"
                             class="w-10 h-10 object-cover rounded-lg bg-gray-100 flex-shrink-0">
                    @else
                        <div class="w-10 h-10 bg-muted rounded-lg flex-shrink-0 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-foreground truncate">{{ $result['name'] }}</p>
                        <p class="text-xs text-accent font-semibold">Gs. {{ $result['price'] }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @endforeach
            <a href="{{ route('products.index', ['q' => $query]) }}"
               class="flex items-center justify-center gap-2 px-4 py-3 text-sm text-primary font-semibold hover:bg-muted/50 border-t border-border transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Ver todos los resultados para "{{ $query }}"
            </a>
        </div>
    @endif

</div>
