<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddDepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        DB::table('departements')->insert(
            ['libelle' => 'Département BTP', 'created_at' => now(), 'updated_at' => now()],
        );
    }
}
