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
        $categories = Category::factory(2)->create();
        $category = $categories[0];
        $product = $this->createProduct($category);
        $deletedProduct = $this->createProduct($category,Product::BORRADOR);

        $this->browse(function (Browser $browser) use ($product, $deletedProduct) {
            $browser->visit('/')
                ->pause(3000)
                ->assertSee($product->name)
                ->assertDontSee($deletedProduct->name);
        });
    }

    private function createProduct($category, $status = Product::PUBLICADO)
    {
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
        ]);

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'status' => $status
        ]);
        Image::factory(2)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);
        return $product;
    }
}
