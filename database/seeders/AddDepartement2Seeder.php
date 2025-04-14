<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddDepartement2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        DB::table('departements')->insert([
            ['libelle' => 'Département NORD', 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Département SUD', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
