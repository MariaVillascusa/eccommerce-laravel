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
        $product = $this->createProduct($categories);

        $this->get('/products/' . $product->slug)
            ->assertStatus(200);
    }

    /** @test */
    public function it_displays_the_product_details()
    {
        $categories = Category::factory(2)->create();
        $product1 = $this->createProduct($categories, 10, 15.99);
        $product2 = $this->createProduct($categories);

        $this->get('/products/' . $product1->slug)
            ->assertStatus(200)
            ->assertSee($product1->name)
            ->assertSee($product1->price)
            ->assertSeeText('Stock disponible: '. $product1->quantity)
            ->assertDontSee($product2->name)
            ->assertDontSee($product2->price)
            ->assertDontSeeText('Stock disponible: ' . $product2->quantity);
    }



    private function createProduct($categories, $quantity=5, $price=10.99)
    {
        $category = $categories[0];
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => false,
            'size' => false
        ]);

        $brand = Brand::factory()->create();
        $category->brands()->attach([$brand->id]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'quantity' => $quantity,
            'price' => $price
        ]);
        Image::factory(2)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);
        return $product;
    }
}
