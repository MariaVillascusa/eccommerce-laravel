<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use App\Models\Category;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfileLinksTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_user_not_logged_can_see_login_link()
    {
        $categories = Category::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('.profile-dropdown')
                ->pause(500)
                ->assertSeeLink('Iniciar sesión');
        });
    }

    public function test_user_not_logged_can_see_register_link()
    {
        $categories = Category::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('.profile-dropdown')
                ->pause(500)
                ->assertSeeLink('Registrarse');
        });
    }

    public function test_user_logged_can_see_profile_link()
    {
        $categories = Category::factory()->create();
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)->visit('/')
                ->click('.profile-dropdown')
                ->pause(500)
                ->assertSeeLink('Perfil');
        });
    }

    public function test_user_logged_can_see_logout_link()
    {
        $categories = Category::factory()->create();
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)->visit('/')
                ->click('.profile-dropdown')
                ->pause(500)
                ->assertSeeLink('Finalizar sesión');
        });
    }
}
