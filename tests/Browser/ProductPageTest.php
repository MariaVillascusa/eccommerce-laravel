<?php

namespace Tests\Browser;

use App\Models\Size;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use Tests\DuskTestCase;
use App\Models\Category;
use Laravel\Dusk\Browser;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProductPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_the_product_image_is_visible()
    {
        $product1 = $this->createProduct();
        $product2 = $this->createProduct();
        $urlImageProduct1 = $product1->images()->first()->url;
        $urlImageProduct2 = $product2->images()->first()->url;

        $this->browse(function (Browser $browser) use ($product1, $urlImageProduct1, $urlImageProduct2) {
            $url = $browser->visit('/products/' . $product1->slug)
                ->attribute('.flex-active-slide img', 'src');
            $this->assertEquals($url, '/storage/' . $urlImageProduct1);
            $this->assertNotEquals($url, '/storage/' . $urlImageProduct2);
        });
    }

    public function test_decrement_button_is_visible_and_disabled()
    {
        $product = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->assertButtonDisabled('@decrement-button');
        });
    }

    public function test_product_without_colors_has_increment_button_enabled()
    {
        $product = $this->createProduct();
        $this->browse(function (Browser $browser) use ($product) {

            $browser->visit('/products/' . $product->slug)
                ->assertButtonEnabled('@increment-button');
        });
    }

    public function test_product_with_colors_has_increment_button_disabled()
    {
        $product = $this->createProduct(5, true, false);

        $this->browse(function (Browser $browser) use ($product) {

            $browser->visit('/products/' . $product->slug)
                ->assertButtonDisabled('@increment-button');
        });
    }

    public function test_product_with_colors_and_size_has_increment_button_disabled()
    {
        $product = $this->createProduct(5, true, true);

        $this->browse(function (Browser $browser) use ($product) {

            $browser->visit('/products/' . $product->slug)
                ->assertButtonDisabled('@increment-button');
        });
    }

    public function test_product_without_colors_has_add_item_button_visible_and_enable()
    {
        $product = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product) {

            $browser->visit('/products/' . $product->slug)
                ->assertButtonEnabled('@addItem-button');
        });
    }

    public function test_product_with_colors_has_add_item_button_disabled()
    {
        $product = $this->createProduct(5, true);

        $this->browse(function (Browser $browser) use ($product) {

            $browser->visit('/products/' . $product->slug)
                ->assertButtonDisabled('@addItem-button');
        });
    }

    public function test_product_with_colors_and_size_has_add_item_button_disabled()
    {
        $product = $this->createProduct(5, true, true);

        $this->browse(function (Browser $browser) use ($product) {

            $browser->visit('/products/' . $product->slug)
                ->assertButtonDisabled('@addItem-button');
        });
    }

    public function test_increment_button_limit_is_product_quantity()
    {
        $product = $this->createProduct(2);
        $quantity = $product->quantity;
        $this->browse(function (Browser $browser) use ($product, $quantity) {
            $browser->visit('/products/' . $product->slug)
                ->assertButtonEnabled('@increment-button');
            $browser->press('@increment-button');

            $browser->press('@increment-button')
                ->pause(3000)
                ->assertButtonDisabled('@increment-button');
        });
    }

    public function test_decrement_button_limit_is_zero()
    {
        $product = $this->createProduct(3);
        $quantity = $product->quantity;
        $this->browse(function (Browser $browser) use ($product, $quantity) {
            $browser->visit('/products/' . $product->slug);

            $browser->assertButtonDisabled('@decrement-button')
                ->press('@increment-button')
                ->pause(500)
                ->assertButtonEnabled('@decrement-button');
        });
    }

    public function test_items_without_color_has_not_color_select_neither_size_select()
    {
    }

    private function createProduct($quantity = 15, $color = false, $size = false)
    {
        $categories = Category::factory(2)->create();

        $category = $categories[0];
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
        Image::factory(4)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);
        return $product;
    }
}
