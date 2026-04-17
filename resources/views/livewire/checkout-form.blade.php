<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Formulario --}}
    <div class="lg:col-span-2">

        {{-- Step indicator --}}
        <div class="flex items-center space-x-4 mb-6">
            @foreach(['Dirección' => 1, 'Envío' => 2, 'Pago' => 3] as $label => $s)
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold {{ $step >= $s ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                        {{ $s }}
                    </div>
                    <span class="text-sm {{ $step >= $s ? 'text-gray-900 font-medium' : 'text-gray-400' }}">{{ $label }}</span>
                </div>
                @if($s < 3)
                    <div class="flex-1 h-px {{ $step > $s ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                @endif
            @endforeach
        </div>

        {{-- Step 1: Dirección --}}
        @if($step === 1)
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900">Datos de contacto y envío</h2>

            @if(count($savedAddresses) > 0)
                <div class="space-y-2 mb-4">
                    <p class="text-sm font-medium text-gray-700">Mis direcciones guardadas</p>
                    @foreach($savedAddresses as $addr)
                        <label class="flex items-start space-x-3 p-3 border rounded-lg cursor-pointer {{ $selectedAddressId === $addr['id'] ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model="selectedAddressId" value="{{ $addr['id'] }}"
                                   wire:change="selectAddress('{{ $addr['id'] }}')" class="mt-0.5">
                            <div>
                                <p class="text-sm font-medium">{{ $addr['label'] }} — {{ $addr['recipient_name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $addr['street_address'] }}, {{ $addr['city'] }}, {{ $addr['department'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo *</label>
                    <input type="text" wire:model="customerName" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" wire:model="customerEmail" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" wire:model="customerPhone" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departamento *</label>
                    <input type="text" wire:model.live="shippingDepartment" placeholder="ej: Central"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad *</label>
                    <input type="text" wire:model.live="shippingCity" placeholder="ej: Luque"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección *</label>
                    <input type="text" wire:model="shippingAddress"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex justify-end">
                <button wire:click="nextStep" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700">
                    Continuar →
                </button>
            </div>
        </div>
        @endif

        {{-- Step 2: Envío --}}
        @if($step === 2)
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900">Método de envío</h2>

            <label class="flex items-center justify-between p-4 border rounded-xl cursor-pointer {{ $shippingMethod === 'envio' ? 'border-blue-600 bg-blue-50' : 'border-gray-200' }}">
                <div class="flex items-center space-x-3">
                    <input type="radio" wire:model="shippingMethod" value="envio" class="text-blue-600">
                    <div>
                        <p class="font-medium text-sm">Envío a domicilio</p>
                        @if($deliveryTime)<p class="text-xs text-gray-500">{{ $deliveryTime }}</p>@endif
                    </div>
                </div>
                <span class="font-semibold text-sm">{{ $shippingCost > 0 ? 'Gs. ' . number_format($shippingCost, 0, ',', '.') : 'Gratis' }}</span>
            </label>

            @if($shippingSettings->store_pickup_enabled)
            <label class="flex items-center justify-between p-4 border rounded-xl cursor-pointer {{ $shippingMethod === 'pickup' ? 'border-blue-600 bg-blue-50' : 'border-gray-200' }}">
                <div class="flex items-center space-x-3">
                    <input type="radio" wire:model="shippingMethod" value="pickup" class="text-blue-600">
                    <div>
                        <p class="font-medium text-sm">Retiro en tienda</p>
                        <p class="text-xs text-gray-500">Gratis</p>
                    </div>
                </div>
                <span class="font-semibold text-sm text-green-600">Gratis</span>
            </label>
            @endif

            <div class="flex justify-between">
                <button wire:click="prevStep" class="text-gray-500 hover:text-gray-700 text-sm">← Volver</button>
                <button wire:click="nextStep" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700">
                    Continuar →
                </button>
            </div>
        </div>
        @endif

        {{-- Step 3: Pago --}}
        @if($step === 3)
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900">Método de pago</h2>

            @error('general')
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">{{ $message }}</div>
            @enderror

            {{-- Métodos de pago disponibles (dinámico según config admin) --}}
            @forelse($availablePayments as $key => $label)
                @php
                    $icons = ['bancard' => '💳', 'transferencia' => '🏦'];
                    $subs  = ['bancard' => 'Visa, Mastercard', 'transferencia' => 'Subí tu comprobante al finalizar'];
                @endphp
                <label class="flex items-center space-x-3 p-4 border rounded-xl cursor-pointer {{ $paymentMethod === $key ? 'border-blue-600 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" wire:model.live="paymentMethod" value="{{ $key }}" class="text-blue-600">
                    <div>
                        <p class="font-medium text-sm">{{ $icons[$key] ?? '💰' }} {{ $label }}</p>
                        <p class="text-xs text-gray-500">{{ $subs[$key] ?? '' }}</p>
                    </div>
                </label>
            @empty
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-sm text-yellow-700">
                    No hay métodos de pago disponibles. Contactá al administrador.
                </div>
            @endforelse

            {{-- Datos bancarios + comprobante (solo si elige transferencia) --}}
            @if($paymentMethod === 'transferencia')
            @php $transferSettings = \App\Models\SiteContent::getByKey('transfer_settings')?->metadata ?? []; @endphp
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 space-y-3">
                <p class="text-sm font-semibold text-blue-800">Datos para la transferencia:</p>
                @if(!empty($transferSettings))
                    <div class="text-sm text-blue-700 space-y-1">
                        @if(!empty($transferSettings['bank']))         <p><span class="font-medium">Banco:</span> {{ $transferSettings['bank'] }}</p>@endif
                        @if(!empty($transferSettings['account_name'])) <p><span class="font-medium">Titular:</span> {{ $transferSettings['account_name'] }}</p>@endif
                        @if(!empty($transferSettings['account_number']))<p><span class="font-medium">Cuenta:</span> {{ $transferSettings['account_number'] }}</p>@endif
                        @if(!empty($transferSettings['ruc']))          <p><span class="font-medium">RUC:</span> {{ $transferSettings['ruc'] }}</p>@endif
                        @if(!empty($transferSettings['extra']))        <p class="text-xs text-blue-600 mt-1">{{ $transferSettings['extra'] }}</p>@endif
                    </div>
                @else
                    <p class="text-xs text-blue-600">Configurá los datos bancarios en Panel Admin → Configuración → Pagos.</p>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Comprobante de transferencia <span class="text-red-500">*</span>
                    </label>
                    <input type="file" wire:model="transferReceipt"
                           accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-blue-700">
                    <p class="text-xs text-gray-400 mt-1">Formatos aceptados: JPG, PNG, PDF. Máximo 5MB.</p>
                    @error('transferReceipt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    <div wire:loading wire:target="transferReceipt" class="text-xs text-gray-500 mt-1">Subiendo archivo...</div>
                </div>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                <textarea wire:model="notes" rows="2"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Instrucciones especiales para la entrega..."></textarea>
            </div>

            <div class="flex justify-between">
                <button wire:click="prevStep" class="text-gray-500 hover:text-gray-700 text-sm">← Volver</button>
                <button wire:click="placeOrder" wire:loading.attr="disabled" wire:target="placeOrder,transferReceipt"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-60 flex items-center gap-2">
                    <span wire:loading.remove wire:target="placeOrder">Confirmar Pedido</span>
                    <span wire:loading wire:target="placeOrder" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        Procesando...
                    </span>
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- Resumen --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-gray-200 p-5 sticky top-20">
            <h2 class="font-semibold text-gray-900 mb-4">Resumen del pedido</h2>

            <div class="space-y-3 mb-4">
                @foreach($cart as $item)
                    <div class="flex items-center space-x-3">
                        @if($item['image'])
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-10 h-10 object-cover rounded-lg bg-gray-50">
                        @else
                            <div class="w-10 h-10 bg-gray-100 rounded-lg"></div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-900 truncate">{{ $item['name'] }}</p>
                            <p class="text-xs text-gray-400">x{{ $item['quantity'] }}</p>
                        </div>
                        <p class="text-xs font-semibold">Gs. {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>

            <div class="space-y-2 text-sm border-t pt-3">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>Gs. {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Envío</span><span>{{ $shippingCost > 0 ? 'Gs. ' . number_format($shippingCost, 0, ',', '.') : 'Gratis' }}</span></div>
                @if($giftCardDiscount > 0)
                    <div class="flex justify-between text-green-600"><span>Gift Card</span><span>-Gs. {{ number_format($giftCardDiscount, 0, ',', '.') }}</span></div>
                @endif
                <div class="flex justify-between font-bold text-base border-t pt-2">
                    <span>Total</span>
                    <span>Gs. {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Listener para el pago Bancard (lanzado desde Livewire placeOrder)
window.addEventListener('bancard:init', (e) => {
    const url = e.detail.url;
    const overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.8);z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px;';
    overlay.innerHTML = `<iframe src="${url}" style="width:min(460px,100%);height:min(560px,85vh);border:none;border-radius:12px;display:block;"></iframe>`;
    document.body.appendChild(overlay);
});
</script>
