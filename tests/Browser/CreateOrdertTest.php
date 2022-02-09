<?php

namespace Tests\Browser;

use App\Models\Size;
use App\Models\User;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Image;
use Livewire\Livewire;
use App\Models\Product;
use Tests\DuskTestCase;
use App\Models\Category;
use Laravel\Dusk\Browser;
use App\Models\Subcategory;
use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\CreateOrder;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateOrdertTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_it_shows_shipping_form_when_shipping_option_is_selected()
    {
        $this->browse(function (Browser $browser) {
            $product = $this->createProduct();
            Livewire::test(AddCartItem::class, ['product' => $product])
                ->call('addItem', $product);

            $browser->loginAs(User::factory()->create());
            $browser->visit('/orders/create')->check('@home');

            $browser->assertVisible('@shipping-form');
        });
    }

    public function test_it_doesnt_show_shipping_form_when_shipping_option_is_not_selected()
    {
        $this->browse(function (Browser $browser) {
            $product = $this->createProduct();
            Livewire::test(AddCartItem::class, ['product' => $product])
                ->call('addItem', $product);

            $browser->loginAs(User::factory()->create());
            $browser->visit('/orders/create')->check('@store');

            $browser->assertMissing('@shipping-form');
        });
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
