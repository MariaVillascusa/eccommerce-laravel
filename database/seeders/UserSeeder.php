<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'admin']);

        User::factory()->create([
            'name' => 'Maria',
            'email' => 'maria@mail.com',
            'password' => bcrypt('12345678')
        ])->assignRole('admin');

        User::factory(50)->create([
            'password' => bcrypt('12345678')
        ]);
    }
}
