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
use App\Http\Livewire\DropdownCart;
use App\Http\Livewire\AddCartItemSize;
use App\Http\Livewire\AddCartItemColor;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShoppingCartTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function a_product_without_color_can_be_added_to_shopping_cart()
    {
        $product1 = $this->createProduct();
        $product2 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->call('addItem', $product2)
            ->assertStatus(200);
        $this->assertEquals($product2->id, Cart::content()->first()->id);
        $this->assertNotEquals($product1->id, Cart::content()->first()->id);
        $this->assertTrue(Cart::content()->first()->color_id == null);
        $this->assertTrue(Cart::content()->first()->size_id == null);
    }

    /** @test */
    public function a_product_with_color_neither_size_can_be_added_to_shopping_cart()
    {
        $product1 = $this->createProduct(true);
        $product2 = $this->createProduct(true);
        $color = $product2->colors->first();

        Livewire::test(AddCartItemColor::class, ['product' => $product2])
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
        $product1 = $this->createProduct(true, true);
        $product2 = $this->createProduct(true, true);
        $size = $product2->sizes->first();
        $color = $product2->sizes->first()->colors->first();

        Livewire::test(AddCartItemSize::class, ['product' => $product2])
            ->set('options', ['size' => $size->name, 'color' => $color->name])
            ->call('addItem', $product2)
            ->assertStatus(200);
        $this->assertEquals($product2->id, Cart::content()->first()->id);
        $this->assertNotEquals($product1->id, Cart::content()->first()->id);

        $this->assertTrue(Cart::content()->first()->options['color'] == $product2->sizes->first()->colors->first()->name);
        $this->assertTrue(Cart::content()->first()->options['size'] == $product2->sizes->first()->name);
    }

    public function test_it_shows_items_when_clicking_on_shopping_cart()
    {
        $product1 = $this->createProduct();
        $product2 = $this->createProduct();
        $product3 = $this->createProduct(true);
        $product4 = $this->createProduct(true, true);

        $color3 = $product3->colors->first();
        $color4 = $product4->sizes->first()->colors->first();

        $size4 = $product4->sizes->first();

        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->call('addItem', $product2);

        Livewire::test(AddCartItemColor::class, ['product' => $product3])
            ->set('options', ['color' => $color3->name])
            ->call('addItem', $product3);

        Livewire::test(AddCartItemSize::class, ['product' => $product4])
            ->set('options', ['size' => $size4->name, 'color' => $color4->name])
            ->call('addItem', $product4);

        Livewire::test(DropdownCart::class)->assertStatus(200)
            ->assertSee($product2->name)
            ->assertSee($product3->name)
            ->assertSee($product4->name)
            ->assertDontSee($product1->name);
    }

    /** @test */
    public function red_circle_number_increments_when_a_product_is_added_to_cart()
    {
        $product = $this->createProduct();
        $product2 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product])
            ->call('addItem', $product);

        Livewire::test(DropdownCart::class)->assertSee(1);

        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->call('addItem', $product2);

        Livewire::test(DropdownCart::class)->assertSee(2);
    }

    /** @test */
    public function it_is_not_possible_to_add_to_shopping_cart_a_higher_product_quantity_than_the_product_has()
    {
        $quantity=2;
        $product = $this->createProduct(false, false, $quantity);
        $this->get('products/' . $product->slug);

        for ($i = 0; $i < 4; $i++) {
            Livewire::test(AddCartItem::class, ['product' => $product])
                ->call('addItem', $product);
            $product->quantity = qty_available($product->id); // Si no establecemos la cantidad así no se sincroniza
        }

        $this->assertEquals($quantity, Cart::content()->first()->qty);
    }

    /** @test */
    public function it_shows_quantity_product()
    {
        $product = $this->createProduct();
        $product2 = $this->createProduct(false, false, 23);

        $this->get('products/' . $product->slug)
            ->assertStatus(200)
            ->assertDontSeeText('Stock disponible: ' . $product2->quantity)
            ->assertSeeText('Stock disponible: ' . $product->quantity);
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
            $productColor->sizes()->attach($productSize->id, ['quantity' => 1]);
        } elseif ($color && !$size) {
            $product->quantity = null;
            $productColor = Color::factory()->create();
            $product->colors()->attach($productColor->id, ['quantity' => 1]);
        }

        return $product;
    }
}
