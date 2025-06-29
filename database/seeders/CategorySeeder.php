<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
       $categories = [
           ['name' => 'History'],
           ['name' => 'Adventure'],
           ['name' => 'Culture'],
           ['name' => 'Food'],
           ['name' => 'Shopping'],
           ['name' => 'Nature'],
           ['name' => 'Entertainment'],
           ['name' => 'Wellness'],
           ['name' => 'Events'],
           ['name' => 'Religion'],
       ];

       DB::table('categories')->insert($categories);
    }
}
