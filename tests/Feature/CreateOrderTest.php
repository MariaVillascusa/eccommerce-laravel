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
use App\Http\Livewire\AddCartItemColor;
use App\Http\Livewire\AddCartItemSize;
use App\Listeners\MergeTheCart;
use App\Models\Order;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Auth\Events\Login;
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

    // Este test también forma parte de la pregunta 2
    /** @test */
    public function shopping_cart_is_saved_in_database_when_a_user_logs_out()
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
    public function shopping_cart_returns_when_a_user_logs_in()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product1 = $this->createProduct();
        $product2 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);
        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->call('addItem', $product2);

        $data = Cart::content();
        $products = collect([]);
        foreach (Cart::content() as $item) {
            $products->push($item);
        }
        $this->post('/logout');

        $listener = new MergeTheCart();
        $event = new Login('web', $user, true);
        $this->actingAs($user);

        $listener->handle($event);

        $this->assertEquals($data, Cart::content());

        $actualProducts = collect([]);
        foreach (Cart::content() as $item) {
            $actualProducts->push($item);
        }
        $this->assertEquals($actualProducts[0]->name,$products[0]->name);
        $this->assertEquals($actualProducts[0]->qty,$products[0]->qty);
        $this->assertEquals($actualProducts[0]->price,$products[0]->price);
        $this->assertEquals($actualProducts[1]->name,$products[1]->name);
        $this->assertEquals($actualProducts[1]->qty,$products[1]->qty);
        $this->assertEquals($actualProducts[1]->price,$products[1]->price);

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
            ->set('contact', 'contacto')
            ->set('phone', '611111111')
            ->call('create_order')
            ->assertRedirect('/orders/1/payment');

        $this->assertTrue(count(Cart::content()) == 0);
    }
    /** @test */
    public function it_changes_stock_when_a_product_without_color_neither_size_order_is_created()
    {
        $this->actingAs(User::factory()->create());

        $product = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product])
            ->call('addItem', $product);

        Livewire::test(CreateOrder::class)
            ->set('contact', 'contacto')
            ->set('phone', '611111111')
            ->call('create_order');

        $this->assertDatabaseHas('products', [
            'quantity' => 14
        ]);
    }
    /** @test */
    public function it_changes_stock_when_a_product_with_color_and_without_size_order_is_created()
    {
        $this->actingAs(User::factory()->create());

        $product = $this->createProduct(true, false);
        $color = $product->colors->first();

        Livewire::test(AddCartItemColor::class, ['product' => $product])
            ->set('options', ['color_id' => $color->id])
            ->call('addItem', $product);

        Livewire::test(CreateOrder::class)
            ->set('contact', 'contacto')
            ->set('phone', '611111111')
            ->call('create_order');

        $this->assertEquals($product->stock, 0);
        $this->assertDatabaseHas('color_product', [
            'quantity' => 0
        ]);
    }

    /** @test */
    public function it_changes_stock_when_a_product_with_color_and_size_order_is_created()
    {
        $this->actingAs(User::factory()->create());

        $product = $this->createProduct(true, true);
        $size = $product->sizes->first();
        $color = $product->sizes->first()->colors->first();

        Livewire::test(AddCartItemSize::class, ['product' => $product])
            ->set('options', ['size_id' => $size->id, 'color_id' => $color->id])
            ->call('addItem', $product);

        Livewire::test(CreateOrder::class)
            ->set('contact', 'contacto')
            ->set('phone', '611111111')
            ->call('create_order');

        $this->assertEquals($product->stock, 0);
        $this->assertDatabaseHas('color_size', [
            'quantity' => 0
        ]);
    }

    /** @test */
    public function it_cancels_orders_over_10_mins()
    {
        $this->actingAs(User::factory()->create());

        $product = $this->createProduct();
        $product2 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product])
            ->call('addItem', $product);

        Livewire::test(CreateOrder::class)
            ->set('contact', 'contacto')
            ->set('phone', '611111111')
            ->call('create_order');

        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->call('addItem', $product2);

        Livewire::test(CreateOrder::class)
            ->set('contact', 'contacto')
            ->set('phone', '611111111')
            ->call('create_order');

        $order1 = Order::first();

        $order1->created_at = now()->subMinutes(11);
        $order1->save();

        $this->artisan('schedule:run');
        $order1Before = Order::where('created_at', '<', now()->subMinutes(10))->get()->first();
        $order2Before = Order::where('created_at', '>=', now()->subMinutes(10))->get()->first();

        $this->assertEquals($order1Before->status, 5);
        $this->assertEquals($order2Before->status, 1);
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
