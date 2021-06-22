<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'User Test',
            'email' => 'user@email.com',
            'password' => bcrypt('password')
        ]);

        User::factory(rand(3, 5))->create();
    }
}
