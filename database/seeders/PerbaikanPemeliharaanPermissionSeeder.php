<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PerbaikanPemeliharaanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions untuk Perbaikan & Pemeliharaan
        $permissions = [
            'manage perbaikan-pemeliharaan',
            'delete perbaikan-pemeliharaan',
            'approve perbaikan-pemeliharaan',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign ke role Admin (semua permission)
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // Assign ke role Petugas (hanya manage)
        $petugasRole = Role::where('name', 'petugas')->first();
        if ($petugasRole) {
            $petugasRole->givePermissionTo([
                'manage perbaikan-pemeliharaan',
            ]);
        }

        $this->command->info('✅ Perbaikan & Pemeliharaan permissions created and assigned successfully!');
    }
}