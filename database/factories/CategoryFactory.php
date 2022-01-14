<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->sentence;

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
            'image' => 'categories/' . $this->faker->image(storage_path('app/public/categories'), 640, 480, null, false)
        ];
    }
}
