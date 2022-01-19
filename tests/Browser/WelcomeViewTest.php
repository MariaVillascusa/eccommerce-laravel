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

class WelcomeViewTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_it_shows_public_products()
    {
        $categories = [Category::factory()->create()];
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
        Image::factory(2)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $deletedProduct = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'status' => Product::BORRADOR
        ]);
        Image::factory(2)->create([
            'imageable_id' => $deletedProduct->id,
            'imageable_type' => Product::class
        ]);

        $categories[0]->products = [$product];

        $this->browse(function (Browser $browser) use ($product, $deletedProduct) {
            $browser->visit('/')
                ->assertSee($product->name)
                ->assertDontSee($deletedProduct->name);
        });
    }
}
