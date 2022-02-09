<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Size;

use App\Models\User;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Image;
use Livewire\Livewire;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\CreateOrder;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_not_authenticated_user_cannot_create_an_order()
    {
        $this->get('/orders/create')
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_create_an_order()
    {
        $product = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product])
            ->call('addItem', $product);
        $this->actingAs(User::factory()->create())->get('/orders/create')->assertStatus(200);

        Livewire::test(CreateOrder::class)->assertSee(Cart::content()->first()->name);
    }

    /** @test */
    public function shopping_cart_is_saved_in_database_when_a_use_logs_out()
    {
        $this->actingAs(User::factory()->create());

        $product = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product])
            ->call('addItem', $product);

        $data = Cart::content();

        $this->post('/logout');

        $this->assertDatabaseHas('shoppingcart', ['content' => serialize($data)]);
    }

    /** @test */
    public function it_deletes_the_shopping_cart_when_the_order_is_created()
    {
        $this->actingAs(User::factory()->create());

        $product = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product])
            ->call('addItem', $product);
        $this->assertTrue(count(Cart::content()) != 0);

        Livewire::test(CreateOrder::class)
        ->set('contact','contacto')
        ->set('phone', '611111111')
        ->call('create_order')
        ->assertRedirect('/orders/1/payment');

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
            $productColor->sizes()->attach($productSize->id, ['quantity' => 1]);
        } elseif ($color && !$size) {
            $product->quantity = null;
            $productColor = Color::factory()->create();
            $product->colors()->attach($productColor->id, ['quantity' => 1]);
        }
        return $product;
    }
}
