<?php

namespace Tests\Browser;

use App\Models\Brand;
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

    public function test_decrement_quantity_button_is_visible_and_disabled()
    {
        $product = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->assertButtonDisabled('@decrement-button');
        });
    }

    public function test_increment_quantity_button_is_visible_and_enabled_if_there_is_not_color_to_choose()
    {
        $product = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug);

            if ($product->subcategory->color) {
                $browser->assertButtonDisabled('@increment-button');
            } else {
                $browser->assertButtonEnabled('@increment-button');
            }
        });
    }

    public function test_decrement_add_item_button_is_visible_and_enabled_if_there_is_not_color_to_choose()
    {
        $product = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug);

            if ($product->subcategory->color) {
                $browser->assertButtonDisabled('@addItem-button');
            } else {
                $browser->assertButtonEnabled('@addItem-button');
            }
        });
    }

    private function createProduct()
    {
        $categories = Category::factory(2)->create();

        $category = $categories[0];
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
        ]);

        $brand = Brand::factory()->create();
        $category->brands()->attach([$brand->id]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
        ]);
        Image::factory(4)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);
        return $product;
    }
}
