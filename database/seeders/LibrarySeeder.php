<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $r1 = \App\Models\LibRegion::create(['name' => 'Region I']);
        $r2 = \App\Models\LibRegion::create(['name' => 'Region II']);
        $r3 = \App\Models\LibRegion::create(['name' => 'Region III']);

        \App\Models\LibClubName::create(['name' => 'Eagle Club A', 'lib_region_id' => $r1->id]);
        \App\Models\LibClubName::create(['name' => 'Eagle Club B', 'lib_region_id' => $r2->id]);
        \App\Models\LibClubName::create(['name' => 'Eagle Club C', 'lib_region_id' => $r3->id]);
    }
}
