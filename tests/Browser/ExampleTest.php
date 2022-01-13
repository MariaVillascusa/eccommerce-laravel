<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\Category;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $categories = $this->getCategories();

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Categorías');
        });
    }

    public function getCategories()
    {
        return [Category::factory()->create([
            'name' => 'Moda',
            'slug' => 'moda-slug',
            'icon' => 'icon'
        ]), Category::factory()->create([
            'name' => 'Informática',
            'slug' => 'info-slug',
            'icon' => 'icon'
        ])];
    }
}
