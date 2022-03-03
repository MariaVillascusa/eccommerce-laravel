<?php

use App\Models\User;
use Livewire\Livewire;
use App\Http\Livewire\Admin\ShowProducts;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\actingAs;


it('searchs products from admin page', function () {
    Role::create(['name' => 'admin']);

    $product1 = createProduct();
    $product2 = createProduct();
    $product1->name = 'Teléfono';
    $product2->name = 'Teclado';
    $product1->save();
    $product2->save();

    actingAs(User::factory()->create()->assignRole('admin'))
        ->get('/admin')->assertStatus(200);
    Livewire::test(ShowProducts::class)
        ->set('search', 'Tecl')
        ->assertSee('Teclado')
        ->assertDontSee('Teléfono');
});
