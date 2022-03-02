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
use App\Http\Livewire\ShoppingCart;
use App\Http\Livewire\UpdateCartItem;
use App\Http\Livewire\AddCartItemSize;
use App\Http\Livewire\AddCartItemColor;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShoppingCartViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_added_products_to_shopping_cart()
    {
        $product = $this->createProduct();
        $product1 = $this->createProduct();
        $product2 = $this->createProduct(true);
        $product3 = $this->createProduct(true, true);

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        Livewire::test(AddCartItemColor::class, ['product' => $product2])
            ->call('addItem', $product2);

        Livewire::test(AddCartItemSize::class, ['product' => $product3])
            ->call('addItem', $product);

        $response = $this->get('/shopping-cart');

        $response->assertStatus(200)
            ->assertDontSee($product->name)
            ->assertSee($product1->name)
            ->assertSee($product2->name)
            ->assertSee($product3->name);
    }


    /** @test */
    public function it_can_increment_and_decrement_product_without_color_or_size_quantity()
    {
        $product = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product])
            ->call('addItem', $product);

        $total = Cart::subtotal();

        Livewire::test(UpdateCartItem::class, ['rowId' => Cart::content()->first()->rowId])
            ->call('increment');
        $this->assertEquals($total * 2, Cart::subtotal());

        Livewire::test(UpdateCartItem::class, ['rowId' => Cart::content()->first()->rowId])
            ->call('decrement');
        $this->assertEquals($total, Cart::subtotal());
    }

    /** @test */
    public function it_deletes_a_product()
    {
        $product = $this->createProduct();
        $product2 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product])
            ->call('addItem', $product);
        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->call('addItem', $product2);

        Livewire::test(ShoppingCart::class)
            ->call('delete', Cart::content()->first()->rowId);

        $this->assertTrue(count(Cart::content()) == 1);
    }

    /** @test */
    public function it_deletes_the_shopping_cart()
    {
        $product = $this->createProduct();
        $product2 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product])
            ->call('addItem', $product);
        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->call('addItem', $product2);

        Livewire::test(ShoppingCart::class)
            ->call('destroy', Cart::content()->first()->rowId);

        $this->assertTrue(count(Cart::content()) == 0);
    }

    private function createProduct($color = false, $size = false)
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
            $productSize->colors()->attach($productColor->id, ['quantity' => 1]);
                } elseif ($color && !$size) {
            $product->quantity = null;
            $productColor = Color::factory()->create();
            $product->colors()->attach($productColor->id, ['quantity' => 1]);
        }

        return $product;
    }
}
