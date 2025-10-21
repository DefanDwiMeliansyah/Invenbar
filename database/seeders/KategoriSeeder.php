<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategoris')->insert([
            ['nama_kategori' => 'Komputer & Laptop', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Peralatan Jaringan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Perangkat Input/Output', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Software & Lisensi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Peralatan Multimedia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Storage & Backup', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Peralatan Medis', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Obat-obatan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Furnitur Medis', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
