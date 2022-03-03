<?php

use App\Models\User;
use App\Models\Brand;
use Livewire\Livewire;
use App\Models\Category;
use App\Models\Subcategory;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\actingAs;
use App\Http\Livewire\Admin\CreateProduct;

it('creates a product', function () {
    enterAsAdmin();
    createProductAsAdmin();

    $this->assertDatabaseHas('products', [
        'name' => 'Macbook',
        'slug' => 'macbook',
        'description' => 'descripcion del producto',
        'price' => 50,
        'quantity' => 5,
    ]);
});

test('category_id is required', function () {
    enterAsAdmin();
    createProductAsAdmin([
        'category_id' => '',
        'name' => 'noCategoryId'
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'noCategoryId'
    ]);
});

test('subcategory_id is required', function () {
    enterAsAdmin();
    createProductAsAdmin([
        'subcategory_id' => '',
        'name' => 'noSubcategoryId'
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'noSubcategoryId'
    ]);
});

test('name is required', function () {
    enterAsAdmin();
    createProductAsAdmin([
        'name' => '',
        'slug' => 'macbook'
    ]);
    $this->assertDatabaseMissing('products', [
        'slug' => 'macbook'
    ]);
});

test('slug is required', function () {
    enterAsAdmin();
    createProductAsAdmin([
        'slug' => ''
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'Macbook'
    ]);
});

test('slug is unique', function () {
    enterAsAdmin();
    createProductAsAdmin([
        'slug'=>'slug-no-unico'
    ]);
    createProductAsAdmin([
        'name'=>'slugNoUnico',
        'slug' => 'slug-no-unico'
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'slugNoUnico'
    ]);
});

test('description is required', function () {
    enterAsAdmin();
    createProductAsAdmin([
        'description' => '',
        'name' => 'noDescription'
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'noDescription'
    ]);
});

test('price is required', function () {
    enterAsAdmin();
    createProductAsAdmin([
        'price' => '',
        'name' => 'noPrice'
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'noPrice'
    ]);
});

test('brand_id is required', function () {
    enterAsAdmin();
    createProductAsAdmin([
        'brand_id' => '',
        'name' => 'noBrand_id'
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'noBrand_id'
    ]);
});


function enterAsAdmin()
{
    Role::create(['name' => 'admin']);
    actingAs(User::factory()->create()->assignRole('admin'));
}

function createProductAsAdmin(array $custom = [])
{
    $category = Category::factory()->create();
    $subcategory = Subcategory::factory()->create([
        'category_id' => $category->id,
        'color' => false,
        'size' => false
    ]);
    $brand = Brand::factory()->create();
    $category->brands()->attach($brand->id);
    $values = array_merge([
        'name' => 'Macbook',
        'slug' => 'macbook',
        'category_id' => $category->id,
        'subcategory_id' => $subcategory->id,
        'description' => 'descripcion del producto',
        'price' => 50,
        'brand_id' => $brand->id,
        'quantity' => 5,
    ], $custom);

    Livewire::test(CreateProduct::class)
        ->set('category_id', $values['category_id'])
        ->set('subcategory_id', $values['subcategory_id'])
        ->set('name', $values['name'])
        ->set('slug', $values['slug'])
        ->set('description', $values['description'])
        ->set('price', $values['price'])
        ->set('brand_id', $values['brand_id'])
        ->set('quantity', $values['quantity'])
        ->call('save');
}
