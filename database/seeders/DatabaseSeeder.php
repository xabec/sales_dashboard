<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'level' => 'admin',
            'name' => 'Darius Silkaitis',
            'email' => 'xdvxas@gmail.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
