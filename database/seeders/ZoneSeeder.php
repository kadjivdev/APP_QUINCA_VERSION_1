<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('zones')->insert([
            ['libelle' => 'ZONE SUD', 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'ZONE NORD', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
