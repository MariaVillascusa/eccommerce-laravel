<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Size;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Image;
use Livewire\Livewire;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\AddCartItemSize;
use App\Http\Livewire\AddCartItemColor;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_changes_available_quantity_when_a_product_without_color_neither_size_is_added_to_shopping_cart()
    {
        $product = $this->createProduct(false, false, 10);

        Livewire::test(AddCartItem::class, ['product' => $product])
            ->call('addItem', $product);

        $this->assertEquals(qty_available($product->id), 9);
    }

    /** @test */
    public function it_changes_available_quantity_when_a_product_with_color_but_without_size_is_added_to_shopping_cart()
    {
        $product = $this->createProduct(true, false, 10);
        $color = $product->colors->first();

        Livewire::test(AddCartItemColor::class, ['product' => $product])
            ->set('options', ['color_id' => $color->id])
            ->call('addItem', $product);

        $this->assertEquals(qty_available($product->id, $color->id), 9);
    }

    /** @test */
    public function it_changes_available_quantity_when_a_product_with_color_and_size_is_added_to_shopping_cart()
    {
        $product = $this->createProduct(true, true, 10);
        $size = $product->sizes->first();
        $color = $product->sizes->first()->colors->first();

        Livewire::test(AddCartItemSize::class, ['product' => $product])
            ->set('options', ['size_id' => $size->id, 'color_id' => $color->id])
            ->call('addItem', $product);

        $this->assertEquals(qty_available($product->id, $color->id, $size->id), 9);
    }



    private function createProduct($color = false, $size = false, $quantity = 5)
    {
        $category = Category::factory()->create();

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => $color,
            'size' => $size
        ]);

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'quantity' => $quantity
        ]);

        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        if ($size && $color) {
            $product->quantity = null;
            $productColor = Color::factory()->create();
            $productSize = Size::factory()->create([
                'product_id' => $product->id
            ]);
            $productSize->colors()->attach($productColor->id, ['quantity' => $quantity]);

        } elseif ($color && !$size) {
            $product->quantity = null;
            $productColor = Color::factory()->create();
            $product->colors()->attach($productColor->id, ['quantity' => $quantity]);
        }

        return $product;
    }
}
