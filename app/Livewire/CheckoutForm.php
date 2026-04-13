<?php

namespace App\Livewire;

use App\Models\ShippingSetting;
use App\Models\UserAddress;
use Livewire\Component;

class CheckoutForm extends Component
{
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
    public string $paymentMethod  = 'bancard';
    public string $giftCardCode   = '';
    public float  $giftCardDiscount = 0;
    public string $notes          = '';

    // Addresses
    public string $selectedAddressId = '';
    public array $savedAddresses = [];

    public function mount(): void
    {
        if (auth()->check()) {
            $user = auth()->user();
            $this->customerName  = $user->profile?->full_name ?? $user->name;
            $this->customerEmail = $user->email;
            $this->customerPhone = $user->profile?->phone ?? '';

            $this->savedAddresses = UserAddress::where('user_id', $user->id)->get()->toArray();

            $default = collect($this->savedAddresses)->firstWhere('is_default', true);
            if ($default) {
                $this->selectAddress($default['id']);
            }
        }
    }

    public function selectAddress(string $addressId): void
    {
        $this->selectedAddressId = $addressId;
        $address = collect($this->savedAddresses)->firstWhere('id', $addressId);

        if ($address) {
            $this->shippingAddress    = $address['street_address'];
            $this->shippingDepartment = $address['department'];
            $this->shippingCity       = $address['city'];
            $this->calculateShipping();
        }
    }

    public function updatedShippingDepartment(): void
    {
        $this->calculateShipping();
    }

    public function updatedShippingCity(): void
    {
        $this->calculateShipping();
    }

    public function calculateShipping(): void
    {
        if (!$this->shippingDepartment || !$this->shippingCity) return;

        $cart     = session('cart', []);
        $subtotal = array_reduce($cart, fn($c, $i) => $c + ($i['price'] * $i['quantity']), 0.0);

        $settings = ShippingSetting::getDefault();
        $result   = $settings->calculateShipping($this->shippingDepartment, $this->shippingCity, $subtotal);

        $this->shippingCost = $result['cost'];
        $this->deliveryTime = $result['delivery_time'];
    }

    public function nextStep(): void
    {
        if ($this->step < 3) $this->step++;
    }

    public function prevStep(): void
    {
        if ($this->step > 1) $this->step--;
    }

    public function render()
    {
        $cart     = session('cart', []);
        $subtotal = array_reduce($cart, fn($c, $i) => $c + ($i['price'] * $i['quantity']), 0.0);
        $total    = max(0, $subtotal - $this->giftCardDiscount + $this->shippingCost);

        $shippingSettings = ShippingSetting::getDefault();

        return view('livewire.checkout-form', compact('cart', 'subtotal', 'total', 'shippingSettings'));
    }
}
