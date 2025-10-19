<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PeminjamanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage peminjaman',
            'delete peminjaman',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'admin')->first();
        $petugasRole = Role::where('name', 'petugas')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        if ($petugasRole) {
            $petugasRole->givePermissionTo('manage peminjaman');
        }

        $this->command->info('✅ Peminjaman permissions created and assigned successfully!');
    }
}