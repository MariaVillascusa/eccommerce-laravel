<?php

use App\Models\User;
use App\Models\Brand;
use Livewire\Livewire;
use App\Models\Category;
use App\Models\Subcategory;
use App\Http\Livewire\Admin\EditProduct;
use App\Models\Product;

it('edits a product', function () {
    enterAsAdmin();
    $product = createProduct();
    editProduct($product, [
        'name' => 'Macbook',
        'slug' => 'macbook',
        'description' => 'descripcion del producto',
        'price' => 50,
        'quantity' => 5,
    ]);
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
    $product = createProduct();
    editProduct($product, [
        'category_id' => '',
        'name' => 'noCategoryId'
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'noCategoryId'
    ]);
});

test('subcategory_id is required', function () {
    enterAsAdmin();
    $product = createProduct();
    editProduct($product, [
        'subcategory_id' => '',
        'name' => 'noSubcategoryId'
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'noSubcategoryId'
    ]);
});

test('name is required', function () {
    enterAsAdmin();
    $product = createProduct();
    editProduct($product, [
        'name' => '',
        'slug' => 'macbook'
    ]);
    $this->assertDatabaseMissing('products', [
        'slug' => 'macbook'
    ]);
});

test('slug is required', function () {
    enterAsAdmin();
    $product = createProduct();
    editProduct($product, [
        'slug' => ''
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'Macbook'
    ]);
});

test('slug is unique', function () {
    enterAsAdmin();
    $product1 = createProduct();
    $product2 = createProduct();
    editProduct($product2, [
        'name' => 'slugNoUnico',
        'slug' => $product1->slug
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'slugNoUnico'
    ]);
});

test('description is required', function () {
    enterAsAdmin();
    $product = createProduct();
    editProduct($product, [
        'description' => '',
        'name' => 'noDescription'
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'noDescription'
    ]);
});

test('price is required', function () {
    enterAsAdmin();
    $product = createProduct();
    editProduct($product, [
        'price' => '',
        'name' => 'noPrice'
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'noPrice'
    ]);
});

test('brand_id is required', function () {
    enterAsAdmin();
    $product = createProduct();
    editProduct($product, [
        'brand_id' => '',
        'name' => 'noBrand_id'
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'noBrand_id'
    ]);
});

test('quantity is numeric', function () {
    enterAsAdmin();
    createProductAsAdmin([
        'quantity' => 'noVálido',
        'name' => 'test'
    ]);
    $this->assertDatabaseMissing('products', [
        'name' => 'test'
    ]);
})->throws(Exception::class);


function editProduct($product, array $custom = [])
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

    Livewire::test(EditProduct::class, ['product' => $product])

        ->set('category_id', $values['category_id'])
        ->set('product.subcategory_id', $values['subcategory_id'])
        ->set('product.name', $values['name'])
        ->set('product.slug', $values['slug'])
        ->set('product.description', $values['description'])
        ->set('product.price', $values['price'])
        ->set('product.brand_id', $values['brand_id'])
        ->set('product.quantity', $values['quantity'])
        ->call('save');
}
