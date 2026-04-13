<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class PredictiveSearch extends Component
{
    public string $query = '';
    public array $results = [];
    public bool $isOpen = false;

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->isOpen  = false;
            return;
        }

        $this->results = Product::active()
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->query}%")
                  ->orWhere('sku', 'like', "%{$this->query}%");
            })
            ->with('mainImage')
            ->limit(8)
            ->get()
            ->map(fn($p) => [
                'id'    => $p->id,
                'name'  => $p->name,
                'slug'  => $p->slug,
                'price' => $p->formatted_price,
                'image' => $p->mainImage?->image_url,
                'url'   => route('products.show', $p->slug),
            ])
            ->toArray();

        $this->isOpen = count($this->results) > 0;
    }

    public function clearSearch(): void
    {
        $this->query   = '';
        $this->results = [];
        $this->isOpen  = false;
    }

    public function render()
    {
        return view('livewire.predictive-search');
    }
}
