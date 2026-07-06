<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WorkReport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => 'password',
        ]);

        $user = User::factory()->create([
            'name' => 'Pengguna Demo',
            'email' => 'user@example.com',
            'role' => 'user',
            'password' => 'password',
        ]);

        WorkReport::factory(8)->for($user)->create();
        WorkReport::factory(3)->for($admin)->create();
    }
}
