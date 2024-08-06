<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 10; $i++) {
            DB::table('keys')->insert([
                'key' => random_int(100000, 999999), // Menghasilkan angka acak 6 digit
                'jabatan' => ($i % 5) + 1, // Menetapkan jabatan antara 1 dan 5
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
