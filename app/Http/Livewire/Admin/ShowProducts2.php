<?php

namespace App\Http\Livewire\Admin;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ShowProducts2 extends Component
{
    use WithPagination;

    public $search;
    public $numPages = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::where('name', 'LIKE', "%{$this->search}%")->paginate($this->numPages);
        return view('livewire.admin.show-products2', compact('products'))->layout('layouts.admin');
    }
}
