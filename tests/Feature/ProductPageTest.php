<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreateData;

class ProductPageTest extends TestCase
{
    use RefreshDatabase, CreateData;

    /** @test */
    public function a_product_page_is_accessible()
    {
        $categories = [$this->createCategory()];
        $product = $this->createProduct(false, false, 5, 20, $categories[0]->id);

        $this->get('/products/' . $product->slug)
            ->assertStatus(200);
    }

    /** @test */
    public function it_displays_the_product_details()
    {
        $categories = [$this->createCategory()];

        $product1 = $this->createProduct(false, false, 5, 20, $categories[0]->id);
        $product2 = $this->createProduct(false, false, 96, 587135);

        $this->get('/products/' . $product1->slug)
            ->assertStatus(200)
            ->assertSee($product1->name)
            ->assertSee($product1->price)
            ->assertSeeText('Stock disponible: ' . $product1->quantity)
            ->assertDontSee($product2->name)
            ->assertDontSee($product2->price)
            ->assertDontSeeText('Stock disponible: ' . $product2->quantity);
    }
}
