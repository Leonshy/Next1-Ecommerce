<?php

namespace App\Livewire;

use App\Models\GiftCard;
use App\Models\Order;
use App\Models\ShippingSetting;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CheckoutForm extends Component
{
    use WithFileUploads;
    // Step management
    public int $step = 1; // 1: address, 2: shipping, 3: payment

    // Customer data
    public string $customerName  = '';
    public string $customerEmail = '';
    public string $customerPhone = '';

    // Shipping
    public string $shippingAddress  = '';
    public string $shippingDepartment = '';
    public string $shippingCity      = '';
    public string $shippingMethod    = 'envio'; // envio | pickup
    public float  $shippingCost      = 0;
    public string $deliveryTime      = '';

    // Payment
    public string $paymentMethod    = 'bancard';
    public string $giftCardCode     = '';
    public float  $giftCardDiscount = 0;
    public string $notes            = '';
    public        $transferReceipt  = null; // archivo comprobante

    // Addresses
    public string $selectedAddressId = '';
    public array  $savedAddresses    = [];
    public string $addressMode       = 'saved'; // 'saved' | 'new'

    // New address form fields
    public string $newLabel           = 'Casa';
    public string $newRecipientName   = '';
    public string $newPhone           = '';
    public string $newNeighborhood    = '';
    public string $newHouseNumber     = '';
    public string $newCrossStreet     = '';
    public string $newReference       = '';
    public bool   $saveNewAddress     = true;

    // Edit modal
    public bool   $showEditModal      = false;
    public string $editingAddressId   = '';
    public string $editLabel          = '';
    public string $editRecipientName  = '';
    public string $editPhone          = '';
    public string $editDepartment     = '';
    public string $editCity           = '';
    public string $editStreetAddress  = '';
    public string $editNeighborhood   = '';
    public string $editHouseNumber    = '';
    public string $editCrossStreet    = '';
    public string $editReference      = '';
    public bool   $editIsDefault      = false;

    public function mount(): void
    {
        if (auth()->check()) {
            $user = auth()->user();
            $this->customerName  = $user->profile?->full_name ?? $user->name;
            $this->customerEmail = $user->email;
            $this->customerPhone = $user->profile?->phone ?? '';

            $this->savedAddresses = UserAddress::where('user_id', $user->id)->get()->toArray();

            $default = collect($this->savedAddresses)->firstWhere('is_default', true)
                    ?? collect($this->savedAddresses)->first();

            if ($default) {
                $this->selectAddress((string) $default['id']);
            } else {
                $this->addressMode = 'new';
            }
        } else {
            $this->addressMode = 'new';
        }

        $available = $this->availablePaymentMethods();
        if ($available && !array_key_exists($this->paymentMethod, $available)) {
            $this->paymentMethod = array_key_first($available);
        }
    }

    public function selectAddress(string $addressId): void
    {
        $this->selectedAddressId = $addressId;
        $this->addressMode       = 'saved';
        $address = collect($this->savedAddresses)->firstWhere('id', (int) $addressId)
                ?? collect($this->savedAddresses)->firstWhere('id', $addressId);

        if ($address) {
            $this->shippingAddress    = $address['street_address'];
            $this->shippingDepartment = $address['department'];
            $this->shippingCity       = $address['city'];
            $this->calculateShipping();
        }
    }

    public function switchToNewAddress(): void
    {
        $this->addressMode       = 'new';
        $this->selectedAddressId = '';
        $this->shippingAddress    = '';
        $this->shippingDepartment = '';
        $this->shippingCity       = '';
        $this->newRecipientName   = $this->customerName;
        $this->newPhone           = $this->customerPhone;
    }

    public function switchToSavedAddress(): void
    {
        $this->addressMode = 'saved';
        $default = collect($this->savedAddresses)->firstWhere('is_default', true)
                ?? collect($this->savedAddresses)->first();
        if ($default) {
            $this->selectAddress((string) $default['id']);
        }
    }

    public function openEditModal(string $addressId): void
    {
        $address = collect($this->savedAddresses)->firstWhere('id', (int) $addressId)
                ?? collect($this->savedAddresses)->firstWhere('id', $addressId);
        if (!$address) return;

        $this->editingAddressId  = $addressId;
        $this->editLabel         = $address['label']          ?? '';
        $this->editRecipientName = $address['recipient_name'] ?? '';
        $this->editPhone         = $address['phone']          ?? '';
        $this->editDepartment    = $address['department']     ?? '';
        $this->editCity          = $address['city']           ?? '';
        $this->editStreetAddress = $address['street_address'] ?? '';
        $this->editNeighborhood  = $address['neighborhood']   ?? '';
        $this->editHouseNumber   = $address['house_number']   ?? '';
        $this->editCrossStreet   = $address['cross_street_1'] ?? '';
        $this->editReference     = $address['reference']      ?? '';
        $this->editIsDefault     = (bool) ($address['is_default'] ?? false);
        $this->showEditModal     = true;
    }

    public function saveEditedAddress(): void
    {
        $this->validate([
            'editLabel'         => 'required|string|max:100',
            'editRecipientName' => 'required|string|max:255',
            'editPhone'         => 'required|string|max:30',
            'editDepartment'    => 'required|string',
            'editCity'          => 'required|string',
            'editStreetAddress' => 'required|string|max:255',
        ]);

        $address = UserAddress::find($this->editingAddressId);
        if (!$address || $address->user_id !== auth()->id()) return;

        if ($this->editIsDefault) {
            UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        $address->update([
            'label'          => $this->editLabel,
            'recipient_name' => $this->editRecipientName,
            'phone'          => $this->editPhone,
            'department'     => $this->editDepartment,
            'city'           => $this->editCity,
            'street_address' => $this->editStreetAddress,
            'neighborhood'   => $this->editNeighborhood,
            'house_number'   => $this->editHouseNumber,
            'cross_street_1' => $this->editCrossStreet,
            'reference'      => $this->editReference,
            'is_default'     => $this->editIsDefault,
        ]);

        $this->savedAddresses = UserAddress::where('user_id', auth()->id())->get()->toArray();

        if ((string) $this->selectedAddressId === (string) $this->editingAddressId) {
            $this->selectAddress($this->editingAddressId);
        }

        $this->showEditModal    = false;
        $this->editingAddressId = '';
    }

    public function cancelEditModal(): void
    {
        $this->showEditModal    = false;
        $this->editingAddressId = '';
    }

    public function updatedShippingDepartment(): void
    {
        $this->shippingCity = '';
        $this->calculateShipping();
    }

    public function updatedShippingCity(): void
    {
        $this->calculateShipping();
    }

    public function updatedEditDepartment(): void
    {
        $this->editCity = '';
    }

    public function calculateShipping(): void
    {
        if (!$this->shippingDepartment || !$this->shippingCity) return;

        $cart     = $this->getCart();
        $subtotal = array_reduce($cart, fn($c, $i) => $c + ($i['price'] * $i['quantity']), 0.0);

        $settings = ShippingSetting::getDefault();
        $result   = $settings->calculateShipping($this->shippingDepartment, $this->shippingCity, $subtotal);

        $this->shippingCost = $result['cost'];
        $this->deliveryTime = $result['delivery_time'];
    }

    private function getCart(): array
    {
        if (auth()->check()) {
            $dbCart = \App\Models\Cart::where('user_id', auth()->id())->first();
            return $dbCart?->items ?? [];
        }
        return session('cart', []);
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'customerName'  => 'required|string|max:255',
                'customerEmail' => 'required|email',
            ]);

            if ($this->addressMode === 'new') {
                $this->validate([
                    'shippingDepartment' => 'required|string',
                    'shippingCity'       => 'required|string',
                    'shippingAddress'    => 'required|string|max:255',
                ]);

                if ($this->saveNewAddress && auth()->check()) {
                    $saved = UserAddress::create([
                        'user_id'        => auth()->id(),
                        'label'          => $this->newLabel ?: 'Casa',
                        'recipient_name' => $this->newRecipientName ?: $this->customerName,
                        'phone'          => $this->newPhone ?: $this->customerPhone,
                        'department'     => $this->shippingDepartment,
                        'city'           => $this->shippingCity,
                        'street_address' => $this->shippingAddress,
                        'neighborhood'   => $this->newNeighborhood,
                        'house_number'   => $this->newHouseNumber,
                        'cross_street_1' => $this->newCrossStreet,
                        'reference'      => $this->newReference,
                        'is_default'     => false,
                    ]);
                    $this->savedAddresses    = UserAddress::where('user_id', auth()->id())->get()->toArray();
                    $this->selectedAddressId = (string) $saved->id;
                }
            } elseif ($this->addressMode === 'saved' && !$this->selectedAddressId) {
                $this->addError('selectedAddressId', 'Seleccioná una dirección de envío.');
                return;
            }
        }

        if ($this->step < 3) $this->step++;
    }

    public function prevStep(): void
    {
        if ($this->step > 1) $this->step--;
    }

    public function placeOrder(): void
    {
        $available = $this->availablePaymentMethods();
        $availableKeys = implode(',', array_keys($available));

        $rules = [
            'customerName'  => 'required|string|max:255',
            'customerEmail' => 'required|email',
            'paymentMethod' => "required|in:{$availableKeys}",
        ];

        if ($this->paymentMethod === 'transferencia') {
            $rules['transferReceipt'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:5120';
        }

        $this->validate($rules);

        try {
            DB::beginTransaction();

            $cart     = $this->getCart();
            $subtotal = array_reduce($cart, fn($c, $i) => $c + ($i['price'] * $i['quantity']), 0.0);

            if ($subtotal <= 0) {
                $this->addError('general', 'El carrito está vacío.');
                DB::rollBack();
                return;
            }

            $orderItems = [];
            foreach ($cart as $item) {
                $product = \App\Models\Product::find($item['id'] ?? $item['product_id'] ?? null);
                if (!$product) continue;
                $qty = (int) $item['quantity'];

                if ($product->stock !== null && $product->stock < $qty) {
                    $available = $product->stock;
                    $this->addError('general', "Stock insuficiente para \"{$product->name}\". Disponible: {$available}.");
                    DB::rollBack();
                    return;
                }

                $price = (float) $product->price;
                $orderItems[] = [
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_image' => $product->mainImage?->image_url,
                    'quantity'      => $qty,
                    'unit_price'    => $price,
                    'total_price'   => $price * $qty,
                ];
            }

            // Gift card
            $discount = 0;
            if ($this->giftCardCode) {
                $giftCard = GiftCard::where('code', $this->giftCardCode)->where('status', 'activa')->first();
                if ($giftCard && $giftCard->balance > 0) {
                    $discount = min($giftCard->balance, $subtotal);
                }
            }

            $shippingCost = $this->shippingMethod === 'pickup' ? 0 : $this->shippingCost;
            $total        = max(0, $subtotal - $discount + $shippingCost);

            // Estado según método de pago
            $status = $this->paymentMethod === 'transferencia' ? 'pendiente_transferencia' : 'pendiente';

            // Guardar comprobante si es transferencia
            $receiptPath = null;
            if ($this->paymentMethod === 'transferencia' && $this->transferReceipt) {
                $receiptPath = $this->transferReceipt->store('receipts', 'public');
            }

            $order = Order::create([
                'user_id'          => auth()->id(),
                'status'           => $status,
                'payment_method'   => $this->paymentMethod,
                'transfer_receipt' => $receiptPath,
                'customer_name'    => $this->customerName,
                'customer_email'   => $this->customerEmail,
                'customer_phone'   => $this->customerPhone,
                'shipping_address' => $this->shippingAddress,
                'shipping_city'    => $this->shippingCity,
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'shipping_cost'    => $shippingCost,
                'total'            => $total,
                'notes'            => $this->notes,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);

                if ($item['product_id']) {
                    \App\Models\Product::where('id', $item['product_id'])
                        ->whereNotNull('stock')
                        ->decrement('stock', $item['quantity']);
                }
            }

            DB::commit();

            // Email de confirmación (no bloquea el flujo si falla)
            try {
                (new \App\Services\SmtpEmailService())->sendOrderConfirmation($order);
            } catch (\Throwable) {}

            if ($this->paymentMethod === 'bancard') {
                $service   = new \App\Services\BancardService();
                $processId = time();
                $order->update(['bancard_process_id' => $processId]);
                $result = $service->createPayment(
                    $order,
                    route('checkout.confirmation', $order->id),
                    route('checkout.index')
                );
                if ($result['success']) {
                    $this->dispatch('bancard:init', url: $result['iframe_url']);
                    return;
                }
                $this->addError('general', $result['message'] ?? 'Error en el pago con Bancard.');
                return;
            }

            // Transferencia: redirigir a confirmación
            $this->redirect(route('checkout.confirmation', $order->id));

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CheckoutForm::placeOrder', ['error' => $e->getMessage()]);
            $this->addError('general', 'Ocurrió un error al procesar el pedido. Intentá de nuevo.');
        }
    }

    /** Retorna los métodos de pago disponibles: ['bancard' => 'Bancard', 'transferencia' => '...'] */
    public function availablePaymentMethods(): array
    {
        $methods = [];

        $bancard = \App\Models\PaymentSetting::getProvider('bancard');
        if ($bancard?->is_enabled) {
            $methods['bancard'] = 'Bancard (Tarjeta de crédito/débito)';
        }

        // Transferencia siempre disponible si tiene datos configurados
        $transferSettings = \App\Models\SiteContent::getByKey('transfer_settings')?->metadata ?? [];
        if (!empty($transferSettings['bank']) || !empty($transferSettings['account_number'])) {
            $methods['transferencia'] = 'Transferencia bancaria';
        }

        // Si no hay ninguno configurado, mostrar transferencia como fallback
        if (empty($methods)) {
            $methods['transferencia'] = 'Transferencia bancaria';
        }

        return $methods;
    }

    public function render()
    {
        $cart     = $this->getCart();
        $subtotal = array_reduce($cart, fn($c, $i) => $c + ($i['price'] * $i['quantity']), 0.0);
        $total    = max(0, $subtotal - $this->giftCardDiscount + $this->shippingCost);

        $shippingSettings    = ShippingSetting::getDefault();
        $availablePayments   = $this->availablePaymentMethods();
        $activeDepartmentIds = $shippingSettings->getActiveDepartmentIds();

        return view('livewire.checkout-form', compact(
            'cart', 'subtotal', 'total',
            'shippingSettings', 'availablePayments', 'activeDepartmentIds'
        ));
    }
}
