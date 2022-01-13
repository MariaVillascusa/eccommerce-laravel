<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\Category;
use Laravel\Dusk\Browser;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubcategoriesListMenuTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_shows_subcategories_list()
    {
        $categories = [Category::factory()->create([
            'name' => 'Moda',
            'slug' => 'moda-slug',
            'icon' => 'icon'
        ]), Category::factory()->create([
            'name' => 'Informática',
            'slug' => 'info-slug',
            'icon' => 'icon'
        ])];

        $subcategory1 = Subcategory::factory()->create([
            'category_id' => $categories[1]->id,
            'name' => 'Portátiles',
            'slug' => 'portatiles-slug'
        ]);

        $subcategory2 = Subcategory::factory()->create([
            'category_id' => $categories[0]->id,
            'name' => 'Gafas',
            'slug' => 'gafas-slug'
        ]);

        $categories[0]->subcategories = [$subcategory2];
        $categories[1]->subcategories = [$subcategory1];


        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('.categories-menu')
                ->mouseover('.category-Moda')
                ->assertSee('Gafas')
                ->assertDontSee('Portátiles');

                // $browser->mouseover('.category-Informática')
                // ->assertSee('Portátiles')
                // ->assertDontsee('Gafas');
            ;
        });
    }
}
