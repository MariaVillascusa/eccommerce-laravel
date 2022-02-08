<?php

namespace Tests\Feature;

use App\Http\Livewire\Search;
use Tests\TestCase;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_filters_by_name()
    {
        $this->createProduct('Teclado');
        $this->createProduct('Teléfono');

        Livewire::test(Search::class)
            ->set('search', 'Tecl')
            ->assertSee('Teclado')
            ->assertDontSee('Teléfono');
    }

    /** @test */
    public function it_doesnt_show_any_product_if_search_input_is_empty(){
        $this->createProduct('Teclado');
        $this->get('/');

        Livewire::test(Search::class)
            ->set('search', '')
            ->assertDontSee('Teclado');

    }

    private function createProduct($name)
    {
        $category = Category::factory()->create();
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => false,
            'size' => false,
        ]);

        $brand = Brand::factory()->create();
        $category->brands()->attach([$brand->id]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'name' => $name,
            'brand_id' => $brand->id,
            'quantity' => 2
        ]);
        Image::factory(2)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);
        return $product;
    }
}
