<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin - Tidak terikat lokasi tertentu
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@mail.com',
            'password' => bcrypt('password'),
            'lokasi_id' => null,
        ]);
        $admin->assignRole('admin');

        // Petugas Guru PPLG
        $petugas1 = User::create([
            'name' => 'Guru PPLG 1',
            'email' => 'guru.pplg1@mail.com',
            'password' => bcrypt('password'),
            'lokasi_id' => 1, // LAB PPLG-1
        ]);
        $petugas1->assignRole('petugas');

        $petugas2 = User::create([
            'name' => 'Guru PPLG 2',
            'email' => 'guru.pplg2@mail.com',
            'password' => bcrypt('password'),
            'lokasi_id' => 2, // LAB PPLG-2
        ]);
        $petugas2->assignRole('petugas');

        $petugas3 = User::create([
            'name' => 'Guru PPLG 3',
            'email' => 'guru.pplg3@mail.com',
            'password' => bcrypt('password'),
            'lokasi_id' => 3, // LAB PPLG-3
        ]);
        $petugas3->assignRole('petugas');

        // Pembina PMR
        $pembinaPMR = User::create([
            'name' => 'Pembina PMR',
            'email' => 'pembina.pmr@mail.com',
            'password' => bcrypt('password'),
            'lokasi_id' => 4, // Ruang UKS
        ]);
        $pembinaPMR->assignRole('petugas');

        $this->command->info('✅ ' . User::count() . ' User berhasil di-seed dengan lokasi penugasan!');
        $this->command->newLine();
        $this->command->info('📋 Daftar User:');
        $this->command->info('├── Admin (Tanpa lokasi spesifik): admin@mail.com');
        $this->command->info('├── Guru PPLG 1: guru.pplg1@mail.com (LAB PPLG-1)');
        $this->command->info('├── Guru PPLG 2: guru.pplg2@mail.com (LAB PPLG-2)');
        $this->command->info('├── Guru PPLG 3: guru.pplg3@mail.com (LAB PPLG-3)');
        $this->command->info('└── Pembina PMR: pembina.pmr@mail.com (Ruang UKS)');
        $this->command->info('🔑 Password untuk semua user: password');
    }
}