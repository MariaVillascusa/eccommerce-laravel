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
        $category = Category::factory()->create();
        $subcategory1 = $this->createSubcategory($category);
        $subcategory2 = $this->createSubcategory($category);

        $brand = Brand::factory()->create();
        $category->brands()->attach([$brand->id]);

        $product1 = $this->createProduct($subcategory1, $brand);
        $product2 = $this->createProduct($subcategory2, $brand);

        $this->browse(function (Browser $browser) use ($category, $product1, $product2, $subcategory1, $subcategory2) {
            $browser->visit('/categories/' . $category->slug)
                ->pause(500)
                ->assertSeeLink($subcategory1->name)
                ->assertSeeLink($subcategory2->name)
                ->clickLink($subcategory1->name)
                ->pause(1000)
                ->assertSeeLink($product1->name)
                ->assertDontSeeLink($product2->name);
        });
    }

    public function test_it_filters_products_by_brand()
    {
        $category = Category::factory()->create();
        $subcategory = $this->createSubcategory($category);

        $brand1 = Brand::factory()->create();
        $brand2 = Brand::factory()->create();
        $category->brands()->attach([$brand1->id, $brand2->id]);

        $product1 = $this->createProduct($subcategory, $brand1);
        $product2 = $this->createProduct($subcategory, $brand2);

        $this->browse(function (Browser $browser) use ($category, $product1, $product2, $brand1, $brand2) {
            $browser->visit('/categories/' . $category->slug)
                ->pause(500)
                ->assertSeeLink($brand1->name)
                ->assertSeeLink($brand2->name)
                ->clickLink($brand1->name)
                ->pause(500)
                ->assertSeeLink($product1->name)
                ->assertDontSeeLink($product2->name);
        });
    }

    private function createProduct($subcategory, $brand)
    {
        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
        ]);
        Image::factory(2)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);
        return $product;
    }

    private function createSubcategory($category){
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
        ]);
        return $subcategory;
    }
}
