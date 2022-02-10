<?php

namespace Tests\Browser;

use App\Models\Size;
use App\Models\User;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Image;
use Livewire\Livewire;
use App\Models\Product;
use Tests\DuskTestCase;
use App\Models\Category;
use Laravel\Dusk\Browser;
use App\Models\Subcategory;
use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\CreateOrder;
use App\Models\City;
use App\Models\Department;
use App\Models\District;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateOrdertTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_it_shows_shipping_form_when_shipping_option_is_selected()
    {
        $this->browse(function (Browser $browser) {
            $product = $this->createProduct();
            Livewire::test(AddCartItem::class, ['product' => $product])
                ->call('addItem', $product);

            $browser->loginAs(User::factory()->create());
            $browser->visit('/orders/create')->check('@home');

            $browser->assertVisible('@shipping-form');
        });
    }

    public function test_it_doesnt_show_shipping_form_when_shipping_option_is_not_selected()
    {
        $this->browse(function (Browser $browser) {
            $product = $this->createProduct();
            Livewire::test(AddCartItem::class, ['product' => $product])
                ->call('addItem', $product);

            $browser->loginAs(User::factory()->create());
            $browser->visit('/orders/create')->check('@store');

            $browser->assertMissing('@shipping-form');
        });
    }

    public function test_departments_select_contains_all_departments()
    {
        $this->browse(function (Browser $browser) {
            $product = $this->createProduct();
            Livewire::test(AddCartItem::class, ['product' => $product])
                ->call('addItem', $product);

            $browser->loginAs(User::factory()->create());

            $departments = Department::factory(2)->create()->pluck('id')->all();

            $browser->visit('/orders/create')->assertSelectHasOptions('departments', $departments);
        });
    }

    public function test_cities_select_contains_correct_cities()
    {
        $this->browse(function (Browser $browser) {
            $product = $this->createProduct();
            Livewire::test(AddCartItem::class, ['product' => $product])
                ->call('addItem', $product);

            $browser->loginAs(User::factory()->create());

            $departments = Department::factory(2)->create();
            $cities1= City::factory(2)->create([
                'department_id'=> $departments[0]->id
            ]);
            $cities2= City::factory(2)->create([
                'department_id'=> $departments[1]->id
            ]);
            $idCities1 = $cities1->pluck('id')->all();
            $idCities2 = $cities2->pluck('id')->all();

            $browser->visit('/orders/create')
            ->check('@home')
            ->select('departments', 2)
            ->pause(1000)
            ->assertSelectHasOptions('cities', $idCities2)
            ->assertSelectMissingOptions('cities', $idCities1);
        });
    }

    public function test_districts_select_contains_correct_districts()
    {
        $this->browse(function (Browser $browser) {
            $product = $this->createProduct();
            Livewire::test(AddCartItem::class, ['product' => $product])
                ->call('addItem', $product);

            $browser->loginAs(User::factory()->create());

            $departments = Department::factory(2)->create();
            $cities= City::factory(2)->create([
                'department_id'=> $departments[0]->id
            ]);
            $districts1 = District::factory(2)->create([
                'city_id'=>$cities[0]->id
            ]);
            $districts2 = District::factory(2)->create([
                'city_id'=>$cities[1]->id
            ]);

            $idDistricts1 = $districts1->pluck('id')->all();
            $idDistricts2 = $districts2->pluck('id')->all();

            $browser->visit('/orders/create')
            ->check('@home')
            ->select('departments', 1)
            ->pause(1000)
            ->select('cities', 2)
            ->pause(1000)
            ->assertSelectHasOptions('districts', $idDistricts2)
            ->assertSelectMissingOptions('districts', $idDistricts1);
        });
    }


    private function createProduct($color = false, $size = false)
    {
        $category = Category::factory()->create();

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => $color,
            'size' => $size
        ]);

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
        ]);

        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        if ($size && $color) {
            $product->quantity = null;
            $productColor = Color::factory()->create();
            $productSize = Size::factory()->create([
                'product_id' => $product->id
            ]);
            $productColor->sizes()->attach($productSize->id, ['quantity' => 1]);
        } elseif ($color && !$size) {
            $product->quantity = null;
            $productColor = Color::factory()->create();
            $product->colors()->attach($productColor->id, ['quantity' => 1]);
        }
        return $product;
    }
}
