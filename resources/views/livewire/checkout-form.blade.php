<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Formulario --}}
    <div class="lg:col-span-2">

        {{-- Step indicator --}}
        <div class="flex items-center space-x-2 sm:space-x-4 mb-6">
            @foreach(['Dirección' => 1, 'Envío' => 2, 'Pago' => 3] as $label => $s)
                <div class="flex items-center space-x-1.5 sm:space-x-2 flex-shrink-0">
                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold {{ $step >= $s ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                        {{ $s }}
                    </div>
                    <span class="text-xs sm:text-sm {{ $step >= $s ? 'text-gray-900 font-medium' : 'text-gray-400' }}">{{ $label }}</span>
                </div>
                @if($s < 3)
                    <div class="flex-1 h-px {{ $step > $s ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                @endif
            @endforeach
        </div>

        {{-- Step 1: Dirección --}}
        @if($step === 1)
        @php $pyLocations = \App\Data\ParaguayLocations::departments(); @endphp
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">

            {{-- Datos de contacto --}}
            <div>
                <h2 class="font-semibold text-gray-900 mb-4">Datos de contacto</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo *</label>
                        <input type="text" wire:model="customerName"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('customerName')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" wire:model="customerEmail"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('customerEmail')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" wire:model="customerPhone"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Método de entrega (si pickup está habilitado) --}}
            @if($shippingSettings->store_pickup_enabled)
            <div>
                <h2 class="font-semibold text-gray-900 mb-3">¿Cómo querés recibir tu pedido?</h2>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer {{ $shippingMethod === 'envio' ? 'border-blue-600 bg-blue-50' : 'border-gray-200' }}">
                        <input type="radio" wire:model.live="shippingMethod" value="envio" class="text-blue-600">
                        <div>
                            <p class="text-sm font-medium text-gray-900">🚚 Envío a domicilio</p>
                            <p class="text-xs text-gray-500">Te lo llevamos</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer {{ $shippingMethod === 'pickup' ? 'border-blue-600 bg-blue-50' : 'border-gray-200' }}">
                        <input type="radio" wire:model.live="shippingMethod" value="pickup" class="text-blue-600">
                        <div>
                            <p class="text-sm font-medium text-gray-900">🏪 Retiro en tienda</p>
                            <p class="text-xs text-green-600 font-medium">Gratis</p>
                        </div>
                    </label>
                </div>
            </div>

            <hr class="border-gray-100">
            @endif

            {{-- Dirección de envío (solo si no es pickup) --}}
            @if($shippingMethod !== 'pickup')
            <div>
                <h2 class="font-semibold text-gray-900 mb-4">Dirección de envío</h2>

                {{-- Modo: direcciones guardadas --}}
                @if($addressMode === 'saved' && count($savedAddresses) > 0)
                    <div class="space-y-2">
                        @foreach($savedAddresses as $addr)
                            @php $isSelected = (string)$selectedAddressId === (string)$addr['id']; @endphp
                            <div class="flex items-start gap-3 p-3 border rounded-xl transition-colors cursor-pointer
                                        {{ $isSelected ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-gray-300 bg-white' }}"
                                 wire:click="selectAddress('{{ $addr['id'] }}')">
                                <div class="pt-0.5">
                                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center mt-0.5
                                                {{ $isSelected ? 'border-blue-600' : 'border-gray-300' }}">
                                        @if($isSelected)
                                            <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">{{ $addr['label'] }}
                                        @if($addr['is_default'])
                                            <span class="ml-1 text-xs font-normal text-blue-600 bg-blue-100 px-1.5 py-0.5 rounded-full">Principal</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-700">{{ $addr['recipient_name'] }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $addr['street_address'] }}@if($addr['house_number']) {{ $addr['house_number'] }}@endif,
                                        {{ $addr['city'] }}, {{ $addr['department'] }}
                                    </p>
                                </div>
                                <button type="button"
                                        wire:click.stop="openEditModal('{{ $addr['id'] }}')"
                                        class="shrink-0 text-xs text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                    Editar
                                </button>
                            </div>
                        @endforeach

                        @error('selectedAddressId')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        {{-- Opción: usar otra dirección --}}
                        <button type="button" wire:click="switchToNewAddress"
                                class="w-full mt-1 flex items-center gap-2 p-3 border border-dashed border-gray-300 rounded-xl text-sm text-gray-500 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Usar una dirección diferente
                        </button>
                    </div>

                {{-- Modo: nueva dirección --}}
                @else
                    @if(count($savedAddresses) > 0)
                        <button type="button" wire:click="switchToSavedAddress"
                                class="flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-800 mb-4 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Volver a mis direcciones guardadas
                        </button>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4"
                         x-data="{
                            departments:    {{ json_encode($pyLocations) }},
                            activeDeptIds:  {{ json_encode($activeDepartmentIds) }},
                            isDeptActive(id) { return this.activeDeptIds.length === 0 || this.activeDeptIds.includes(id); },
                            get districts() {
                                const dept = this.departments.find(d => d.id === $wire.shippingDepartment);
                                return dept ? dept.districts : [];
                            }
                         }">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departamento *</label>
                            <select wire:model.live="shippingDepartment"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">— Seleccioná un departamento —</option>
                                @foreach($pyLocations as $dept)
                                    <option value="{{ $dept['id'] }}"
                                            {{ $shippingDepartment === $dept['id'] ? 'selected' : '' }}
                                            :disabled="!isDeptActive('{{ $dept['id'] }}')"
                                            :class="isDeptActive('{{ $dept['id'] }}') ? '' : 'text-gray-400'">
                                        {{ $dept['name'] }}{{ '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shippingDepartment')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad / Distrito *</label>
                            <select wire:model.live="shippingCity"
                                    :disabled="districts.length === 0"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value="">— Seleccioná una ciudad —</option>
                                <template x-for="district in districts" :key="district">
                                    <option :value="district"
                                            :selected="district === '{{ $shippingCity }}'"
                                            x-text="district"></option>
                                </template>
                            </select>
                            @error('shippingCity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección (calle) *</label>
                            <input type="text" wire:model="shippingAddress"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('shippingAddress')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número / Depto</label>
                            <input type="text" wire:model="newHouseNumber"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Barrio</label>
                            <input type="text" wire:model="newNeighborhood"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Referencias</label>
                            <input type="text" wire:model="newReference"
                                   placeholder="ej: Casa color verde, portón negro"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        @auth
                        <div class="sm:col-span-2 bg-gray-50 rounded-lg p-3 space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="saveNewAddress"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Guardar como nueva dirección</span>
                            </label>
                            @if($saveNewAddress)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Etiqueta</label>
                                    <input type="text" wire:model="newLabel" placeholder="Casa, Oficina..."
                                           class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nombre del destinatario</label>
                                    <input type="text" wire:model="newRecipientName"
                                           class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            @endif
                        </div>
                        @endauth
                    </div>
                @endif
            </div>
            @endif {{-- fin @if shippingMethod !== pickup --}}

            <div class="flex justify-end pt-2">
                <button wire:click="nextStep"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700">
                    Continuar →
                </button>
            </div>
        </div>

        {{-- Modal: editar dirección --}}
        @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-init="document.body.style.overflow='hidden'"
             x-destroy="document.body.style.overflow=''">
            <div class="absolute inset-0 bg-black/50" wire:click="cancelEditModal"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 space-y-4"
                 x-data="{
                    departments: {{ json_encode($pyLocations) }},
                    get districts() {
                        const dept = this.departments.find(d => d.id === $wire.editDepartment);
                        return dept ? dept.districts : [];
                    }
                 }">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 text-lg">Editar dirección</h3>
                    <button wire:click="cancelEditModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Etiqueta *</label>
                        <input type="text" wire:model="editLabel" placeholder="Casa, Oficina..."
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('editLabel')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del destinatario *</label>
                        <input type="text" wire:model="editRecipientName"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('editRecipientName')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                        <input type="text" wire:model="editPhone"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('editPhone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departamento *</label>
                        <select wire:model.live="editDepartment"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">— Seleccioná —</option>
                            @foreach($pyLocations as $dept)
                                <option value="{{ $dept['id'] }}" {{ $editDepartment === $dept['id'] ? 'selected' : '' }}>{{ $dept['name'] }}</option>
                            @endforeach
                        </select>
                        @error('editDepartment')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad / Distrito *</label>
                        <select wire:model.live="editCity"
                                :disabled="districts.length === 0"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                            <option value="">— Seleccioná —</option>
                            <template x-for="district in districts" :key="district">
                                <option :value="district"
                                        :selected="district === '{{ $editCity }}'"
                                        x-text="district"></option>
                            </template>
                        </select>
                        @error('editCity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección (calle) *</label>
                        <input type="text" wire:model="editStreetAddress"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('editStreetAddress')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número / Depto</label>
                        <input type="text" wire:model="editHouseNumber"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Barrio</label>
                        <input type="text" wire:model="editNeighborhood"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Referencias</label>
                        <input type="text" wire:model="editReference"
                               placeholder="ej: Casa color verde, portón negro"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-2 flex items-center gap-2">
                        <input type="checkbox" wire:model="editIsDefault" id="editIsDefault"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="editIsDefault" class="text-sm text-gray-700">Establecer como dirección principal</label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" wire:click="cancelEditModal"
                            class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="button" wire:click="saveEditedAddress"
                            wire:loading.attr="disabled" wire:target="saveEditedAddress"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveEditedAddress">Guardar cambios</span>
                        <span wire:loading wire:target="saveEditedAddress">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>
        @endif
        @endif

        {{-- Step 2: Envío --}}
        @if($step === 2)
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900">Resumen de envío</h2>

            @if($shippingMethod === 'pickup')
                <div class="flex items-center justify-between p-4 border border-green-300 bg-green-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🏪</span>
                        <div>
                            <p class="font-medium text-sm text-gray-900">Retiro en tienda</p>
                            <p class="text-xs text-gray-500">Pasá a buscar tu pedido cuando esté listo</p>
                        </div>
                    </div>
                    <span class="font-semibold text-sm text-green-600">Gratis</span>
                </div>
            @else
                <div class="flex items-center justify-between p-4 border border-blue-200 bg-blue-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🚚</span>
                        <div>
                            <p class="font-medium text-sm text-gray-900">Envío a domicilio</p>
                            @if($shippingAddress)<p class="text-xs text-gray-500">{{ $shippingAddress }}, {{ $shippingCity }}</p>@endif
                            @if($deliveryTime)<p class="text-xs text-gray-500">{{ $deliveryTime }}</p>@endif
                        </div>
                    </div>
                    <span class="font-semibold text-sm">{{ $shippingCost > 0 ? 'Gs. ' . number_format($shippingCost, 0, ',', '.') : 'Gratis' }}</span>
                </div>
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
                    $icons        = ['bancard' => '💳', 'transferencia' => '🏦', 'pagopar' => '🔵'];
                    $subs         = ['bancard' => 'Visa, Mastercard', 'transferencia' => 'Subí tu comprobante al finalizar', 'pagopar' => 'Tarjeta, Tigo Money, QR'];
                    $pmSetting    = \App\Models\PaymentSetting::where('provider', $key)->first();
                    $pmDiscount   = $pmSetting ? (float) $pmSetting->discount_percentage : 0;
                @endphp
                <label class="flex items-center space-x-3 p-4 border rounded-xl cursor-pointer {{ $paymentMethod === $key ? 'border-blue-600 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" wire:model.live="paymentMethod" value="{{ $key }}" class="text-blue-600">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-medium text-sm">{{ $icons[$key] ?? '💰' }} {{ $label }}</p>
                            @if($pmDiscount > 0)
                                <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">
                                    {{ number_format($pmDiscount, 0) }}% OFF
                                </span>
                            @endif
                        </div>
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

            {{-- Banner de envío gratis --}}
            @if($shippingSettings->free_shipping_enabled && $shippingMethod !== 'pickup')
                @php
                    $freeMin      = (float) $shippingSettings->free_shipping_min_amount;
                    $freeRemaining = max(0, $freeMin - $subtotal);
                    $freeProgress  = $freeMin > 0 ? min(100, ($subtotal / $freeMin) * 100) : 100;
                @endphp
                @if($freeRemaining > 0)
                <div class="mb-4 bg-amber-50 border border-amber-200 rounded-xl p-3">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="text-base">🚚</span>
                        <p class="text-xs font-semibold text-amber-800">
                            ¡Faltan <span class="text-amber-600">Gs. {{ number_format($freeRemaining, 0, ',', '.') }}</span> para envío gratis!
                        </p>
                    </div>
                    <div class="w-full bg-amber-200 rounded-full h-1.5">
                        <div class="bg-amber-500 h-1.5 rounded-full transition-all duration-300"
                             style="width: {{ number_format($freeProgress, 1, '.', '') }}%"></div>
                    </div>
                </div>
                @else
                <div class="mb-4 bg-green-50 border border-green-200 rounded-xl p-3 flex items-center gap-2">
                    <span class="text-base">🎉</span>
                    <p class="text-xs font-semibold text-green-700">¡Tenés envío gratis en este pedido!</p>
                </div>
                @endif
            @endif

            <div class="space-y-2 text-sm border-t pt-3">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>Gs. {{ number_format($subtotal, 0, ',', '.') }}</span></div>

                @if($giftCardDiscount > 0)
                    <div class="flex justify-between text-green-600">
                        <span class="text-gray-500">Descuento Gift Card</span>
                        <span>-Gs. {{ number_format($giftCardDiscount, 0, ',', '.') }}</span>
                    </div>
                @endif

                @if($paymentDiscount > 0)
                    <div class="flex justify-between text-green-600">
                        <span class="text-gray-500">Descuento por pago ({{ number_format($paymentDiscountPct, 0) }}%)</span>
                        <span>-Gs. {{ number_format($paymentDiscount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="flex justify-between">
                    <span class="text-gray-500">Envío</span>
                    <span>{{ $shippingCost > 0 ? 'Gs. ' . number_format($shippingCost, 0, ',', '.') : 'Gratis' }}</span>
                </div>

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
