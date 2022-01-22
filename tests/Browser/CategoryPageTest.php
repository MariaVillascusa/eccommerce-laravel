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

class CategoryPageTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_it_displays_name_category()
    {
        $categories = Category::factory(2)->create();
        $category1 = $categories[0];
        $category2 = $categories[1];

        $this->browse(function (Browser $browser) use ($category1, $category2) {
            $browser->visit('/categories/' . $category1->slug)
                ->assertSee(strtoupper($category1->name))
                ->assertDontSee(strtoupper($category2->name));
        });
    }

    public function test_it_displays_aside_menu_with_its_subcategories_and_brands()
    {
        $categories = Category::factory(2)->create();
        $category1 = $categories[0];
        $category2 = $categories[1];

        $subcategory1 = $this->createSubcategory($category1);
        $subcategory2 = $this->createSubcategory($category2);

        $brands = Brand::factory(2)->create();
        $category1->brands()->attach($brands[0]->id);
        $category2->brands()->attach($brands[1]->id);

        $this->browse(function (Browser $browser) use ($category1, $subcategory1, $subcategory2, $brands) {
            $browser->visit('/categories/' . $category1->slug)
                ->assertSeeLink($subcategory1->name)
                ->assertDontSeeLink($subcategory2->name)
                ->assertSeeLink($brands[0]->name)
                ->assertDontSeeLink($brands[1]->name);
        });
    }

    public function test_it_displays_its_own_products()
    {
        $product1 = $this->createProduct();
        $product2 = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product1, $product2 ) {
            $browser->visit('/categories/' . $product1->subcategory->category->slug)
                ->pause(500)
                ->assertSeeLink($product1->name)
                ->assertDontSeeLink($product2->name);
        });
    }

    private function createProduct()
    {
        $categories = Category::factory(2)->create();
        $category = $categories[0];
        $subcategory = $this->createSubcategory($category);

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

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

    private function createSubcategory($category){
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
        ]);
        return $subcategory;
    }
}
