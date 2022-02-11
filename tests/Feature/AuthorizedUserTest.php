<?php

use App\Models\User;
use Livewire\Livewire;
use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\CreateOrder;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use Gloudemans\Shoppingcart\Facades\Cart;

it('redirects to login page if user is not logged')->get('/admin')
->assertStatus(302)->assertRedirect('/login');


it('can access to login page if user is logged', function () {
    Role::create(['name' => 'admin']);
    createProduct();

    actingAs($user = User::factory()->create()->assignRole('admin'))
    ->get('/admin/users')->assertStatus(200);
});

it('cannot access to an order with another user', function () {
    actingAs(User::factory()->create(['id' => 1]));
    $product = createProduct();

    livewire(AddCartItem::class, ['product' => $product])
        ->call('addItem', $product);

    livewire(CreateOrder::class)
        ->set('contact','contacto')
        ->set('phone', '611111111')
        ->call('create_order');

    actingAs(User::factory()->create(['id' => 2]))->get('/orders/1/payment')->assertStatus(403);
});



