<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\City;
use App\Models\Category;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $cityIds = City::pluck('id')->toArray();
        $categoryIds = Category::pluck('id')->toArray();

        if (empty($cityIds) || empty($categoryIds)) {
            $this->command->error('Please seed cities and categories first.');
            return;
        }

        $sampleEvents = [
//            ['Riyadh Tech Expo', 24.7136, 46.6753],
//            ['Jeddah Art Fair', 21.4858, 39.1925],
//            ['Mecca Health Conference', 21.3891, 39.8579],
//            ['Medina Innovation Day', 24.5247, 39.5692],
//            ['Dammam AI Summit', 26.3927, 49.9777],
//            ['Abha Green Future', 18.2162, 42.5053],
//            ['Taif Heritage Week', 21.2854, 40.4261],
//            ['Tabuk Climate Forum', 28.3838, 36.5552],
//            ['Hail Robotics Expo', 27.5114, 41.7208],
//            ['Khobar Startup Mixer', 26.2794, 50.2083],
        ];

//        foreach ($sampleEvents as [$name, $lat, $lng]) {
//            Event::create([
//                'name' => $name,
//                'description' => fake()->paragraph(),
//                'start_time' => Carbon::now()->addDays(rand(1, 30)),
//                'end_time' => Carbon::now()->addDays(rand(31, 60)),
//                'city_id' => fake()->randomElement($cityIds),
//                'category_id' => fake()->randomElement($categoryIds),
//                'image' => null,
//                'latitude' => $lat,
//                'longitude' => $lng,
//                'is_active' => true,
//            ]);
//        }
    }
}
