<?php

namespace Tests\Feature;

use App\Http\Livewire\Navigation;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class ListCategoriesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    /** @test */
    public function displays_the_navigation_component()
    {
        $categories = $this->getCategories();

        $response = $this->get('/',);

        $response->assertStatus(200)
            ->assertSeeLivewire('navigation');
    }

    /** @test */
    public function it_shows_categories_list()
    {


        $categories = $this->getCategories();

        $this->NavigationComponent()
            ->assertStatus(200)
            ->assertSee('Moda')
            ->assertSee('Informática');
    }


    public function NavigationComponent()
    {
        return Livewire::test(Navigation::class);
    }

    public function getCategories()
    {
        return [Category::factory()->create([
            'name' => 'Moda',
        ]), Category::factory()->create([
            'name' => 'Informática',
        ])];
    }
}
