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
        ]), Category::factory()->create([
            'name' => 'Informática',
        ])];

        Subcategory::factory()->create([
            'category_id' => $categories[1]->id,
            'name' => 'Portátiles',
        ]);

        Subcategory::factory()->create([
            'category_id' => $categories[0]->id,
            'name' => 'Gafas',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('.categories-menu')
                ->mouseover('.category-Moda')
                ->assertSee('Gafas')
                ->assertDontSee('Portátiles');

                $browser->mouseover('.category-Informática')
                ->assertSee('Portátiles');
        });
    }
}
