<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Hero --}}
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-3">Gift Cards</h1>
            <p class="text-gray-500 max-w-xl mx-auto">
                El regalo perfecto para cualquier ocasión. Comprá una Gift Card de Next1 y dejá que quien la recibe elija lo que más le gusta.
            </p>
        </div>

        {{-- Opciones de monto --}}
        <div x-data="giftCardForm()" class="space-y-8">

            <div>
                <h2 class="text-base font-semibold text-gray-900 mb-4">Elegí el monto</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach([50000, 100000, 200000, 500000] as $amount)
                    <button type="button"
                            @click="selectAmount({{ $amount }})"
                            :class="selected === {{ $amount }} ? 'border-blue-600 bg-blue-50 text-blue-700 ring-2 ring-blue-500' : 'border-gray-200 hover:border-blue-300 text-gray-700'"
                            class="border-2 rounded-xl py-4 text-center font-semibold transition-all">
                        <span class="block text-lg">Gs. {{ number_format($amount, 0, ',', '.') }}</span>
                    </button>
                    @endforeach
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <label class="text-sm text-gray-600 whitespace-nowrap">Otro monto:</label>
                    <input type="number" step="10000" min="10000" placeholder="Gs."
                           x-model="customAmount"
                           @input="selected = null"
                           class="w-44 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Datos del comprador y destinatario --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-5">Detalles del regalo</h2>

                <form method="POST" action="{{ route('gift-cards.purchase') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <input type="hidden" name="amount" :value="finalAmount">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Para (nombre del destinatario)</label>
                        <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('recipient_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email del destinatario</label>
                        <input type="email" name="recipient_email" value="{{ old('recipient_email') }}" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('recipient_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje personalizado (opcional)</label>
                        <textarea name="message" rows="3" maxlength="300"
                                  placeholder="Ej: ¡Feliz cumpleaños! Espero que encuentres algo que te encante."
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('message') }}</textarea>
                    </div>

                    <div class="sm:col-span-2 bg-gray-50 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Monto seleccionado</p>
                            <p class="text-2xl font-bold text-gray-900" x-text="finalAmount ? 'Gs. ' + finalAmount.toLocaleString('es-PY') : '—'"></p>
                        </div>
                        <button type="submit"
                                :disabled="!finalAmount"
                                :class="finalAmount ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                                class="px-8 py-3 text-white rounded-xl font-semibold text-sm transition-colors">
                            Comprar Gift Card
                        </button>
                    </div>
                </form>
            </div>

            {{-- Cómo funciona --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4">
                @foreach([
                    ['icon' => '🎁', 'title' => 'Comprás la Gift Card',    'desc' => 'Elegís el monto y los datos del destinatario'],
                    ['icon' => '📧', 'title' => 'Lo recibe por email',     'desc' => 'Le enviamos el código al destinatario automáticamente'],
                    ['icon' => '🛍️', 'title' => 'Lo usa en cualquier compra', 'desc' => 'Válido para todos los productos de la tienda'],
                ] as $step)
                <div class="text-center">
                    <div class="text-3xl mb-2">{{ $step['icon'] }}</div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $step['title'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- ¿Tenés un código? --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                <h3 class="font-semibold text-blue-900 mb-2">¿Tenés un código de Gift Card?</h3>
                <p class="text-sm text-blue-700 mb-3">Aplicalo durante el checkout y el monto se descontará de tu compra.</p>
                <a href="{{ route('products.index') }}"
                   class="inline-block text-sm font-medium text-blue-700 border border-blue-300 rounded-lg px-4 py-2 hover:bg-blue-100 transition-colors">
                    Ir a la tienda
                </a>
            </div>
        </div>
    </div>

    <script>
    function giftCardForm() {
        return {
            selected: null,
            customAmount: '',
            get finalAmount() {
                return this.selected || (this.customAmount ? parseInt(this.customAmount) : null);
            },
            selectAmount(val) {
                this.selected = val;
                this.customAmount = '';
            },
        };
    }
    </script>
</x-app-layout>
