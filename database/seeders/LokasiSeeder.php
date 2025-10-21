<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lokasis')->insert([
            ['nama_lokasi' => 'LAB PPLG-1', 'created_at' => now(), 'updated_at' => now()],
            ['nama_lokasi' => 'LAB PPLG-2', 'created_at' => now(), 'updated_at' => now()],
            ['nama_lokasi' => 'LAB PPLG-3', 'created_at' => now(), 'updated_at' => now()],
            ['nama_lokasi' => 'Ruang UKS', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('✅ 10 Lokasi Sekolah berhasil di-seed!');
    }
}
