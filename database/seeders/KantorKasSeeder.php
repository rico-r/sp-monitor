<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KantorKasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        //
        DB::table('kantorkas')->insert([
            ['nama_kantorkas' => 'Dolopo'],
            ['nama_kantorkas' => 'Jiwan'],
            ['nama_kantorkas' => 'Sumotoro'],
            ['nama_kantorkas' => 'Rejoso'],
        ]);
    }
}
