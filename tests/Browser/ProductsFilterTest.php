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

class ProductsFilterTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_it_filters_products_by_subcategory()
    {
        $categories = Category::factory(2)->create();
        $category = $categories[0];

        $subcategory1 = Subcategory::factory()->create([
            'category_id' => $category->id,
        ]);
        $subcategory2 = Subcategory::factory()->create([
            'category_id' => $category->id,
        ]);
        $brand = Brand::factory()->create();
        $category->brands()->attach([$brand->id]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory1->id,
            'brand_id' => $brand->id,
        ]);
        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory2->id,
            'brand_id' => $brand->id,
        ]);

        Image::factory(2)->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        Image::factory(2)->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($category, $product1, $product2, $subcategory1, $subcategory2) {
            $browser->visit('/categories/' . $category->slug)
                ->pause(500)
                ->assertSeeLink($subcategory1->name)
                ->assertSeeLink($subcategory2->name)
                ->clickLink($subcategory1->name)
                ->pause(500)
                ->assertSeeLink($product1->name)
                ->assertDontSeeLink($product2->name);
        });
    }

    public function test_it_filters_products_by_brand()
    {
        $categories = Category::factory(2)->create();
        $category = $categories[0];
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
        ]);

        $brand1 = Brand::factory()->create();
        $brand2 = Brand::factory()->create();
        $category->brands()->attach([$brand1->id, $brand2->id]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand1->id,
        ]);
        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand2->id,
        ]);

        Image::factory(2)->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        Image::factory(2)->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($category, $product1, $product2, $brand1, $brand2) {
            $browser->visit('/categories/' . $category->slug)
                ->pause(500)
                ->assertSeeLink($brand1->name)
                ->assertSeeLink($brand2->name)
                ->clickLink($brand1->name)
                ->pause(1000)
                ->assertSeeLink($product1->name)
                ->assertDontSeeLink($product2->name);
        });
    }
}
