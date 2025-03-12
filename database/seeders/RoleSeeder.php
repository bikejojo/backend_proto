<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o actualizar roles
        $adminRole = Role::updateOrCreate(
            ['name' => 'Soporte', 'guard_name' => 'web'],
            ['name' => 'Soporte']
        );

        $supportRole = Role::updateOrCreate(
            ['name' => 'Comercial', 'guard_name' => 'web'],
            ['name' => 'Comercial']
        );

        $commercialRole = Role::updateOrCreate(
            ['name' => 'Administrativo', 'guard_name' => 'web'],
            ['name' => 'Administrativo']
        );

        // Crear o actualizar permisos
        Permission::updateOrCreate(['name' => 'access-dashboard', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage-technician', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage-clients', 'guard_name' => 'web']);

        Permission::updateOrCreate(['name' => 'view-publicity', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage-users-roles', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage-subcription', 'guard_name' => 'web']);

        Permission::updateOrCreate(['name' => 'view-promotion', 'guard_name' => 'web']);

        // Asignar permisos a roles
        $adminRole->syncPermissions(['view-publicity', 'manage-clients', 'manage-technician', 'access-dashboard','manage-users-roles','manage-subcription','view-promotion']);
        $supportRole->syncPermissions(['manage-technician', 'manage-clients']);
        $commercialRole->syncPermissions(['view-publicity', 'manage-subcription','view-promotion']);
    }
}
