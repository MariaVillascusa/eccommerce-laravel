<?php

namespace App\Http\Livewire\Admin;

use App\Models\Size;
use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use App\Models\Color;
use App\Models\Subcategory;
use Livewire\WithPagination;
use App\Filters\ProductFilter;
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
    public $status;
    public $from = '';
    public $to = '';
    public $size;
    public $color;

    public $sortField = 'name';
    public $sortAsc = true;

    public $subcategories = [];
    public $brands = [];
    public $stockList;
    public $columns = ['Imagen', 'Nombre', 'Precio', 'Categoría', 'Marca', 'Stock', 'Colores', 'Tallas', 'Fecha', 'Estado'];
    public $selectedColumns = [];
    public $sizes = [];
    public $colors = [];


    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'subcategory' => ['except' => ''],
        'brand' => ['except' => ''],
        'stock' => ['except' => ''],
        'status' => ['except' => ''],
        'from' => ['except' => ''],
        'to' => ['except' => ''],
        'size' => ['except' => ''],
        'color' => ['except' => ''],
    ];

    public function mount()
    {
        $this->selectedColumns = ['Imagen', 'Nombre', 'Precio', 'Categoría', 'Marca', 'Stock', 'Colores', 'Tallas', 'Fecha', 'Estado'];
        $this->min_price = Product::min('price');
        $this->max_price = Product::max('price');
        $this->getSubcategories();
        $this->getBrands();
        $this->stockList = config('stock.stock');
        $this->statusList = [
            'borrador' => Product::BORRADOR,
            'publicado' => Product::PUBLICADO
        ];
        $this->sizes = array_unique(Size::all()->pluck('name')->all());
        $this->colors = Color::all();
    }

    public function showColumn($column)
    {
        return in_array($column, $this->selectedColumns);
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category', 'subcategory', 'brand', 'stock', 'status', 'from', 'to', 'size', 'color']);
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

    public function sortBy($field)
    {
        if($this->sortField === $field)
        {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
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
                'status' => $this->status,
                'stock' => $this->stock,
                'from' => $this->from,
                'to' => $this->to,
                'size' => $this->size,
                'color' => $this->color,
                'sort' => ['field' => $this->sortField, 'asc' => $this->sortAsc]
            ])
            ->paginate($this->productsPerPage);

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
