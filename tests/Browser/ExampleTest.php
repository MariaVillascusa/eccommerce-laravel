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
        $categories = [Category::factory()->create()];
        $name= $categories[0]->name;


        $this->browse(function (Browser $browser) use ($name) {
            $browser->visit('/')
                    ->assertSee(strtoupper($name));
        });
    }
}
