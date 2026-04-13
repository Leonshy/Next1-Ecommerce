<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Lista de Deseos</h1>

        @if($wishlist->count())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach($wishlist as $item)
                    @include('partials.product-card', ['product' => $item->product])
                @endforeach
            </div>
        @else
            <div class="text-center py-16 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <p class="text-lg font-medium">Tu lista de deseos está vacía</p>
                <a href="{{ route('products.index') }}" class="mt-3 inline-block text-blue-600 font-medium hover:underline">Explorar productos</a>
            </div>
        @endif
    </div>
</x-app-layout>
