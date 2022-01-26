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
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShoppingCartTest extends TestCase
{
    /** @test */
    public function a_product_without_color_can_be_added_to_shopping_cart()
    {
        $product1 = $this->createProduct();
        $product2 = $this->createProduct();

        $this->get('products/' . $product2->slug)
            ->assertStatus(200);

        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->call('addItem', $product2)
            ->assertStatus(200);
        $this->assertEquals($product2->id, Cart::content()->first()->id);
        $this->assertNotEquals($product1->id, Cart::content()->first()->id);
        $this->assertTrue(Cart::content()->first()->color_id == null);
        $this->assertTrue(Cart::content()->first()->size_id == null);
    }

    /** @test */
    public function a_product_with_color_and_without_size_can_be_added_to_shopping_cart()
    {
        $product1 = $this->createProduct(true);
        $product2 = $this->createProduct(true);
        $color = $product2->colors->first();

        $this->get('products/' . $product2->slug)
            ->assertStatus(200);

        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->set('options', ['color' => $color->name])
            ->call('addItem', $product2)
            ->assertStatus(200);
        $this->assertEquals($product2->id, Cart::content()->first()->id);
        $this->assertNotEquals($product1->id, Cart::content()->first()->id);

        $this->assertTrue(Cart::content()->first()->options['color'] == $product2->colors->first()->name);
        $this->assertTrue(Cart::content()->first()->size_id == null);
    }

    /** @test */
    public function a_product_with_color_and_size_can_be_added_to_shopping_cart()
    {
        $product1 = $this->createProduct(true,true);
        $product2 = $this->createProduct(true,true);
        $color = $product2->colors->first();
        $size = $color->sizes->first();

        $this->get('products/' . $product2->slug)
            ->assertStatus(200);

        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->set('options', ['size' => $size->name, 'color' => $color->name])
            ->call('addItem', $product2)
            ->assertStatus(200);
        $this->assertEquals($product2->id, Cart::content()->first()->id);
        $this->assertNotEquals($product1->id, Cart::content()->first()->id);

        $this->assertTrue(Cart::content()->first()->options['color'] == $product2->colors->first()->name);
        $this->assertTrue(Cart::content()->first()->options['size'] == $product2->colors->first()->sizes->first()->name);
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
        if ($color) {
            $productColor = Color::factory()->create();
            $product->colors()->attach($productColor->id, ['quantity' => 1]);

            if ($size) {
                $productSize = Size::factory()->create([
                    'product_id' => $product->id
                ]);
                $productColor->sizes()->attach($productSize->id, ['quantity' => 1]);
            }
        }
        return $product;
    }
}
