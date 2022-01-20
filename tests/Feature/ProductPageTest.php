<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Brand;
use App\Models\Image;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_product_page_is_accessible()
    {
        $categories = Category::factory(2)->create();
        $product1 = $this->createProduct($categories);
        $product2 = $this->createProduct($categories);

        $this->get('/products/' . $product1->slug)
            ->assertStatus(200);
    }

    /** @test */
    public function it_displays_the_product_details()
    {
        $categories = Category::factory(2)->create();
        $product1 = $this->createProduct($categories);
        $product2 = $this->createProduct($categories);

        $this->get('/products/' . $product1->slug)
            ->assertStatus(200)
            ->assertSee($product1->name)
            ->assertSee($product1->price)
            ->assertSee($product1->quantity)
            ->assertDontSee($product2->name)
            ->assertDontSee($product2->price)
            ->assertDontSee($product2->quantity);
    }



    private function createProduct($categories)
    {
        $category = $categories[0];
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
        ]);

        $brand = Brand::factory()->create();
        $category->brands()->attach([$brand->id]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'quantity' => rand(1,100000)
        ]);
        Image::factory(2)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);
        return $product;
    }
}
