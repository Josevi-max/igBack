<?php

namespace Database\Seeders;

use App\Models\Commentary;
use App\Models\Publication;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(50)->create();

        User::factory()->create([
            'name' => 'Josevi',
            'username' => 'testUser',
            'email' => 'josevi@test.com',
            'password' => bcrypt('123456789')
        ]);

        Publication::factory()->count(30)->create();

        Commentary::factory()->count(80)->create();
    }
}
