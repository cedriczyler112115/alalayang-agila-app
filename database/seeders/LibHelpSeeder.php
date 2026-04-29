<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LibHelpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\LibHelp::create(['name' => 'Rescue']);
        \App\Models\LibHelp::create(['name' => 'Medical Emergency']);
    }
}
