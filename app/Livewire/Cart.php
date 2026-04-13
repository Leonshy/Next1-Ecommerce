<?php

namespace App\Livewire;

use App\Models\Cart as CartModel;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Cart extends Component
{
    public array $items = [];

    public function mount(): void
    {
        $this->items = $this->loadItems();
    }

    public function addItem(string $productId, int $quantity = 1): void
    {
        $product = Product::active()->find($productId);
        if (!$product) return;

        if (isset($this->items[$productId])) {
            $this->items[$productId]['quantity'] += $quantity;
        } else {
            $this->items[$productId] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => (float) $product->price,
                'image'    => $product->mainImage?->image_url,
                'slug'     => $product->slug,
                'quantity' => $quantity,
            ];
        }

        $this->persist();
        $this->dispatch('cart:updated', count: $this->totalItems());
    }

    public function removeItem(string $productId): void
    {
        unset($this->items[$productId]);
        $this->persist();
        $this->dispatch('cart:updated', count: $this->totalItems());
    }

    public function updateQuantity(string $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($productId);
            return;
        }

        if (isset($this->items[$productId])) {
            $this->items[$productId]['quantity'] = $quantity;
            $this->persist();
        }
    }

    public function clearCart(): void
    {
        $this->items = [];
        $this->persist();
        $this->dispatch('cart:updated', count: 0);
    }

    public function totalItems(): int
    {
        return array_sum(array_column($this->items, 'quantity'));
    }

    public function subtotal(): float
    {
        return array_reduce($this->items, fn($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0.0);
    }

    // ─── Persistencia ────────────────────────────────────────────────────────────

    private function loadItems(): array
    {
        if (Auth::check()) {
            $dbCart = CartModel::where('user_id', Auth::id())->first();

            // Si hay items en sesión (compras como invitado), fusionar con el carrito de DB
            $sessionItems = session('cart', []);
            if (!empty($sessionItems)) {
                $dbItems = $dbCart?->items ?? [];
                $merged  = $this->mergeItems($dbItems, $sessionItems);
                session()->forget('cart');
                $this->saveToDb($merged);
                return $merged;
            }

            return $dbCart?->items ?? [];
        }

        return session('cart', []);
    }

    private function persist(): void
    {
        if (Auth::check()) {
            $this->saveToDb($this->items);
        } else {
            session(['cart' => $this->items]);
        }
    }

    private function saveToDb(array $items): void
    {
        CartModel::updateOrCreate(
            ['user_id' => Auth::id()],
            ['items'   => $items]
        );
    }

    /**
     * Fusiona dos carritos sumando cantidades si el producto ya existe.
     */
    private function mergeItems(array $base, array $incoming): array
    {
        foreach ($incoming as $productId => $item) {
            if (isset($base[$productId])) {
                $base[$productId]['quantity'] += $item['quantity'];
            } else {
                $base[$productId] = $item;
            }
        }
        return $base;
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
