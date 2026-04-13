<div>
    @if($submitted)
        <p class="text-green-400 text-sm font-medium">{{ $message }}</p>
    @else
        <form wire:submit="submit" class="flex space-x-2">
            <input type="email"
                   wire:model="email"
                   placeholder="Tu email"
                   class="flex-1 px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors font-medium">
                Suscribirse
            </button>
        </form>
        @error('email')
            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
        @enderror
    @endif
</div>
