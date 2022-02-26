<?php

namespace App\Http\Livewire\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductFilter;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Builder;

class ShowProducts2 extends Component
{
    use WithPagination;

    public $productsPerPage = 10;

    public $min_price;
    public $max_price;
    public $search;
    public $category;
    public $subcategory;
    public $brand;
    public $stock;

    public $subcategories = [];
    public $brands = [];
    public $stockList;
    public $columns = ['Imagen', 'Nombre', 'Precio', 'Categoría', 'Marca', 'Stock', 'Colores', 'Tallas', 'Fecha', 'Estado'];
    public $selectedColumns = [];


    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'subcategory' => ['except' => ''],
        'brand' => ['except' => ''],
        'stock' => ['except' => '']
    ];

    public function mount()
    {
        $this->selectedColumns = ['Imagen', 'Nombre', 'Precio', 'Categoría', 'Marca', 'Stock', 'Colores', 'Tallas'];
        $this->min_price = Product::min('price');
        $this->max_price = Product::max('price');
        $this->getSubcategories();
        $this->getBrands();
        $this->stockList = config('stock.stock');
    }

    public function showColumn($column)
    {
        return in_array($column, $this->selectedColumns);
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category', 'subcategory', 'brand', 'stock']);
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->reset(['subcategory', 'brand']);
        $this->getSubcategories();
        $this->getBrands();
    }

    public function updating()
    {
        $this->resetPage();
    }

    public function getSubcategories()
    {
        $this->subcategories = Subcategory::where('category_id', $this->category)->get();
    }

    public function getBrands()
    {
        $this->brands = Brand::whereHas('categories', function (Builder $query) {
            return $query->where('category_id', $this->category);
        })->get();
    }

    public function getProducts(ProductFilter $productFilter)
    {
        $products = Product::query()
            ->with('subcategory.category', 'subcategory', 'brand', 'images', 'colors', 'sizes')
            ->filterBy($productFilter, [
                'search' => $this->search,
                'price' => [$this->min_price, $this->max_price],
                'category' => $this->category,
                'subcategory' => $this->subcategory,
                'brand' => $this->brand,
                'stock' => $this->stock,
            ])
            ->paginate($this->productsPerPage);

        // $products = $products->filter(function($product){
        //     return $product->stock > 30;
        // });

        $products->appends($productFilter->valid());

        return $products;
    }

    public function render(ProductFilter $productFilter)
    {
        $products = $this->getProducts($productFilter);
        return view('livewire.admin.show-products2', [
            'products' => $products,
            'categories' => Category::get(),
        ])->layout('layouts.admin');
    }
}
