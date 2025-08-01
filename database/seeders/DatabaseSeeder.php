<?php

namespace Database\Seeders;

use App\Models\Provider;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // إنشاء Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // إنشاء Provider
        $providerUser = User::create([
            'name' => 'Beta',
            'email' => 'beta@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'provider',
        ]);

        // ربطه بجدول providers
        Provider::create([
            'user_id' => $providerUser->id,
            'name' => 'Beta Company',
            'company_name' => 'Provider Company',
            'description' => 'This is a test provider.',
        ]);
    }
}
