<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    public bool $show = false;

    public int $selectedIndex = 0;

    public string $selectedCategory = '';

    protected $listeners = ['focus-search' => 'focusSearch'];

    public function focusSearch(): void
    {
        $this->show = true;
    }

    public function toggle(): void
    {
        $this->show = !$this->show;
        if (!$this->show) {
            $this->reset('query', 'selectedIndex', 'selectedCategory');
        }
    }

    public function selectResult(string $type, int $id): void
    {
        $this->show = false;
        $this->reset('query', 'selectedIndex', 'selectedCategory');

        $route = match ($type) {
            'product' => route('products.show', $id),
            'client' => route('clients.show', $id),
            'sale' => route('sales.show', $id),
            'supplier' => route('suppliers.show', $id),
            default => '#',
        };

        $this->redirect($route);
    }

    public function render()
    {
        $results = ['products' => [], 'clients' => [], 'sales' => [], 'suppliers' => []];
        $totalCount = 0;
        $categories = [];

        if (strlen($this->query) >= 2) {
            $query = '%' . $this->query . '%';

            $productsQuery = Product::where(function ($q) use ($query) {
                $q->where('name', 'like', $query)
                  ->orWhere('sku', 'like', $query)
                  ->orWhere('barcode', 'like', $query);
            })->with('category');

            $results['products'] = (clone $productsQuery)->limit(5)->get();
            $totalCount += (clone $productsQuery)->count();

            $results['clients'] = Client::where(function ($q) use ($query) {
                $q->where('name', 'like', $query)
                  ->orWhere('nit', 'like', $query)
                  ->orWhere('phone', 'like', $query);
            })->limit(5)->get();
            $totalCount += Client::where(function ($q) use ($query) {
                $q->where('name', 'like', $query)
                  ->orWhere('nit', 'like', $query)
                  ->orWhere('phone', 'like', $query);
            })->count();

            $results['sales'] = Sale::where('invoice_number', 'like', $query)
                ->orWhereHas('client', fn($q) => $q->where('name', 'like', $query))
                ->limit(5)->get();

            $results['suppliers'] = Supplier::where(function ($q) use ($query) {
                $q->where('name', 'like', $query)
                  ->orWhere('contact_name', 'like', $query)
                  ->orWhere('phone', 'like', $query);
            })->limit(5)->get();

            $categories = Category::where('name', 'like', $query)->limit(3)->get();
        }

        return view('livewire.global-search', [
            'results' => $results,
            'totalCount' => $totalCount,
            'categories' => $categories,
        ]);
    }
}
