<?php

namespace App\Livewire;

use App\Models\Wishlist;
use Livewire\Component;

class WishlistButton extends Component
{
    public string $productId;
    public bool $inWishlist = false;

    public function mount(string $productId): void
    {
        $this->productId = $productId;
        $this->inWishlist = auth()->check()
            ? Wishlist::where('user_id', auth()->id())->where('product_id', $productId)->exists()
            : false;
    }

    public function toggle(): void
    {
        if (!auth()->check()) {
            $this->dispatch('notify', type: 'info', message: 'Iniciá sesión para guardar favoritos.');
            return;
        }

        $exists = Wishlist::where('user_id', auth()->id())->where('product_id', $this->productId)->first();

        if ($exists) {
            $exists->delete();
            $this->inWishlist = false;
            $this->dispatch('notify', type: 'info', message: 'Eliminado de favoritos.');
        } else {
            Wishlist::create(['user_id' => auth()->id(), 'product_id' => $this->productId]);
            $this->inWishlist = true;
            $this->dispatch('notify', type: 'success', message: 'Agregado a favoritos.');
        }
    }

    public function render()
    {
        return view('livewire.wishlist-button');
    }
}
