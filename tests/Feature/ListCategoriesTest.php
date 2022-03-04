<?php

namespace Tests\Feature;

use App\Http\Livewire\Navigation;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\CreateData;
use Tests\TestCase;

class ListCategoriesTest extends TestCase
{
    use RefreshDatabase, CreateData;

    /** @test */
    public function displays_the_navigation_component()
    {
        $categories = [$this->createCategory()];

        $response = $this->get('/',);

        $response->assertStatus(200)
            ->assertSeeLivewire('navigation');
    }

    /** @test */
    public function it_shows_categories_list()
    {
        $category1=$this->createCategory();
        $category2=$this->createCategory();
        $categories = [$category1, $category2];

        $this->NavigationComponent()
            ->assertStatus(200)
            ->assertSee($category1->name)
            ->assertSee($category2->name);
    }

    public function NavigationComponent()
    {
        return Livewire::test(Navigation::class);
    }
}
