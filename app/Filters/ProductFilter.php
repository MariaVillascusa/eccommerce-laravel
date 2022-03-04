<?php

namespace App\Filters;


use App\Models\Size;
use App\Models\Product;
use App\Filters\QueryFilter;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProductFilter extends QueryFilter
{

    public function rules(): array
    {
        return [
            'search' => 'filled',
            'price' => 'array',
            'price.0' => 'lte:' . Product::max('price'),
            'price.1' => 'gte:' . Product::min('price'),
            'category' => 'filled|exists:categories,id',
            'subcategory' => 'filled|exists:subcategories,id',
            'brand' => 'filled|exists:brands,id',
            'stock' => ['filled', Rule::in([0, 1, 2])],
            'status' => [
                'filled',
                Rule::in([Product::BORRADOR, Product::PUBLICADO])
            ],
            'from' => 'filled|date_format:Y-m-d',
            'to' => 'filled|date_format:Y-m-d',
            'size' => 'filled|in:'. Size::all()->pluck('name'),
            'color' => 'filled',
            'sort' => 'filled|array',
            'sort.0'=>[
                'filled',
                'in:products.name, price, categories.name, brands.name, products.quantity, products.created_at, status'
            ],
            'sort.1' => 'in:true,false'
        ];
    }

    public function sort($query, $data)
    {
        $query->join('brands', 'brands.id', 'brand_id')
            ->join('subcategories', 'subcategories.id', 'subcategory_id')
            ->join('categories', 'categories.id', 'category_id')
            ->select('products.*')
            ->orderBy($data['field'], $data['asc'] ? 'asc' : 'desc');
    }

    public function search($query, $search)
    {
        return $query->where('products.name', 'LIKE', "%{$search}%");
    }

    public function price($query, $prices)
    {
        return $query->whereBetween('price', [$prices[0], $prices[1]]);
    }

    public function category($query, $category)
    {
        return $query->whereHas('subcategory.category', function ($query) use ($category) {
            $query->where('id', $category);
        });
    }

    public function subcategory($query, $subcategory)
    {
        return $query->where('subcategory_id', $subcategory);
    }

    public function brand($query, $brand)
    {
        return $query->where('brand_id', $brand);
    }

    public function from($query, $date)
    {
        $date = Carbon::createFromFormat('Y-m-d', $date);
        $query->whereDate('products.created_at', '>=', $date);
    }

    public function to($query, $date)
    {
        $date = Carbon::createFromFormat('Y-m-d', $date);

        $query->whereDate('products.created_at', '<=', $date);
    }

    public function size($query, $name)
    {
        return $query->whereHas('sizes', function ($query) use ($name) {
            $query->where('sizes.name', $name);
        });
    }

    public function color($query, $id)
    {
        return $query->whereHas('colors', function ($query) use ($id) {
            $query->where('colors.id', $id);
        })->orWhereHas('sizes', function ($query) use ($id) {
            $query->where(function ($query) use ($id) {
                $query->whereHas('colors', function ($query) use ($id) {
                    $query->where('color_id', $id);
                });
            });
        });
    }

    public function stock($query, $stock)
    {
        $range = config('stock')['stock'][$stock];

        return $query->whereBetween('quantity', [$range[0], $range[1]])
            ->whereHas('subcategory', function ($query) {
                $query->where('color', false);
            })

            ->orWhere(function ($query) use ($range) {
                $query->orWhereHas('colors', function ($query) use ($range) {
                    $query->groupBy('product_id')->havingRaw('sum(quantity) >= ? and sum(quantity) < ?', [$range[0], $range[1]]);
                })
                    ->whereHas('subcategory', function ($query) {
                        $query->where('color', true)->where('size', false);
                    });
            })
            ->orWhereHas('sizes.colors', function ($query) use ($range) {
                $query->groupBy('size_id')->havingRaw('sum(quantity) >= ? and sum(quantity) < ?', [$range[0], $range[1]]);
            });

        //     // $sizes = $query->where(function ($query) use ($range) {
        //     //     $query->join('sizes', 'product_id', 'products.id')
        //     //         ->join('color_size', 'size_id', 'sizes.id')
        //     //         ->select('products.*')
        //     //         ->groupBy('products.id', 'products.name', 'products.slug', 'products.description', 'products.price', 'products.quantity', 'products.subcategory_id', 'products.brand_id', 'products.status', 'products.created_at', 'products.updated_at')
        //     //         ->havingRaw('sum(color_size.quantity) >= ? and sum(color_size.quantity) < ?', [$range[0], $range[1]])
        //     //         // ->whereHas('subcategory', function ($query) {
        //     //         //     $query->where('color', true)->where('size', true);
        //     //         // })
        //     //     ;
        //     // });
    }
}
