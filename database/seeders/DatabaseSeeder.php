<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Role & Permission dulu
        $this->call([
            RolePermissionSeeder::class,
            PeminjamanPermissionSeeder::class,
            PerbaikanPemeliharaanPermissionSeeder::class,
        ]);

        // 2. Buat Master Data DULU (Lokasi & Kategori harus ada sebelum User & Barang)
        $this->call([
            LokasiSeeder::class,
            KategoriSeeder::class,
        ]);

        // 3. Buat User dengan lokasi penugasan (sekarang lokasi sudah ada)
        $this->call(UserSeeder::class);

        // 4. Buat Barang (membutuhkan Kategori & Lokasi)
        $this->call(BarangSeeder::class);

        // 5. Buat Transaksi (membutuhkan User, Lokasi, dan Barang)
        $this->call(PeminjamanSeeder::class);

        $this->command->info('');
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('📊 Total: 4 Lokasi, 9 Kategori, 92 Barang, 5 User');
        $this->command->info('');
        $this->command->info('👤 Admin Account:');
        $this->command->info('   Email: admin@mail.com | Password: password');
        $this->command->info('');
        $this->command->info('👥 Petugas Accounts:');
        $this->command->info('   Guru PPLG 1: guru.pplg1@mail.com (LAB PPLG-1)');
        $this->command->info('   Guru PPLG 2: guru.pplg2@mail.com (LAB PPLG-2)');
        $this->command->info('   Guru PPLG 3: guru.pplg3@mail.com (LAB PPLG-3)');
        $this->command->info('   Pembina PMR: pembina.pmr@mail.com (Ruang UKS)');
        $this->command->info('');
        $this->command->info('🔑 Password untuk semua user: password');
        $this->command->info('');
    }
}