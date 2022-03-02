<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class OrdertTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_it_displays_orders_section_in_the_navbar_dropdown()
    {
        User::factory()->create();
        $categories = Category::factory(2)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/')
                ->click('.profile-dropdown button')
                ->assertSeeLink('Mis pedidos');
        });
    }
}
