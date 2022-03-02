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

        $urlsProduct1 = collect($product1->images->pluck('url'));
        $urlsProduct2 = collect($product2->images->pluck('url'));

        $this->browse(function (Browser $browser) use ($product1, $urlsProduct1, $urlsProduct2) {
            $browser->visit('/products/' . $product1->slug)->elements('.slides img');
            $browser->pause(500);
            $srcs = [];
            for ($i = 0; $i < (count($urlsProduct1)); $i++) {
                array_push($srcs, substr($browser->attribute('@image-product-' . $i, 'src'), 9));
            }
            $diff1 = $urlsProduct1->diff($srcs);
            $diff2 = $urlsProduct2->diff($srcs);
            $this->assertEquals($diff1->all(), []);
            $this->assertNotEquals($diff2->all(), []);
        });
    }

    public function test_decrement_and_increment_buttons_are_visible()
    {
        $product = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->assertVisible('@decrement-button')
                ->assertVisible('@increment-button');
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
                ->pause(500)
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
        $product = $this->createProduct();
        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug);
            $browser->assertMissing('@color-select')
                ->assertMissing('@size-select');
        });
    }

    public function test_items_with_color_and_without_size_has_color_select_but_not_size_select()
    {
        $product = $this->createProduct(5, true);
        $color = Color::factory()->create();
        $product->colors()->attach($color->id, ['quantity' => 1]);

        $this->browse(function (Browser $browser) use ($product, $color) {
            $browser->visit('/products/' . $product->slug);
            $browser->pause(500)
                ->assertVisible('@color-select')
                ->click('@color-select')
                ->pause(500)
                ->assertSelectHasOption('@color-select', $color->id)
                ->assertMissing('@size-select');
        });
    }

    public function test_items_with_color_and_size_has_color_select_and_size_select()
    {
        $product = $this->createProduct(5, true, true);
        $color = Color::factory()->create();
        $size = Size::factory()->create([
            'product_id' => $product->id,
        ]);
        $size->colors()->attach($color->id, ['quantity' => 3]);


        $this->browse(function (Browser $browser) use ($product, $color, $size) {
            $browser->visit('/products/' . $product->slug);
            $browser->pause(500)
                ->assertVisible('@color-select')
                ->assertVisible('@size-select')
                ->assertSelectHasOption('@size-select', $size->id)
                ->select('size-select', $size->id)
                ->pause(500)
                ->assertSelectHasOption('@color-select', $color->id);
        });
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
