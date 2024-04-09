<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // this could be placed in separate seeder UsersSeeder

        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => '1@cc.com',
            'password' => Hash::make('qweqwe')
        ]);

        User::factory()->create([
            'name' => 'Test User 2',
            'email' => '2@cc.com',
            'password' => Hash::make('qweqwe')
        ]);
    }
}
