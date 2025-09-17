<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lokasis')->insert([
            [
                'nama_lokasi' => 'Ruang Rapat Utama',
                'created_at' => now(),
                'update_at' => now()
            ],
        ]); 
    }
}
