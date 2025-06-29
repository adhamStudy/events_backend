<?php

namespace Database\Seeders;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'Riyadh',     'latitude' => 24.7136, 'longitude' => 46.6753],
            ['name' => 'Jeddah',     'latitude' => 21.4858, 'longitude' => 39.1925],
//            ['name' => 'Dammam',     'latitude' => 26.4207, 'longitude' => 50.0888],
//            ['name' => 'Mecca',      'latitude' => 21.3891, 'longitude' => 39.8579],
//            ['name' => 'Medina',     'latitude' => 24.5247, 'longitude' => 39.5692],
//            ['name' => 'Abha',       'latitude' => 18.2465, 'longitude' => 42.5117],
//            ['name' => 'Tabuk',      'latitude' => 28.3838, 'longitude' => 36.5550],
//            ['name' => 'Hail',       'latitude' => 27.5206, 'longitude' => 41.6906],
//            ['name' => 'Buraidah',   'latitude' => 26.3259, 'longitude' => 43.9750],
//            ['name' => 'Najran',     'latitude' => 17.4933, 'longitude' => 44.1277],
        ];

        DB::table('cities')->insert($cities);
        $user = User::create([
    'name' => 'Another Provider',
    'email' => 'another@provider.com',
    'password' => Hash::make('password'),
    'role' => 'provider',
]);

// إنشاء مزود الخدمة وربطه بالمستخدم
$provider = Provider::create([
    'user_id' => $user->id,
    'name' => 'Another Provider Name',
    'company_name' => 'Another Company',
    'description' => 'Sample provider description.',
]);
    }
}
