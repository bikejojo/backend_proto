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
            ['name' => 'administrativo', 'guard_name' => 'web'],
            ['name' => 'administrativo']
        );

        $supportRole = Role::updateOrCreate(
            ['name' => 'soporte', 'guard_name' => 'web'],
            ['name' => 'soporte']
        );

        $commercialRole = Role::updateOrCreate(
            ['name' => 'comercial', 'guard_name' => 'web'],
            ['name' => 'comercial']
        );

        // Crear o actualizar permisos
        Permission::updateOrCreate(['name' => 'manage-users', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage-roles', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view-reports', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'access-dashboard', 'guard_name' => 'web']);

        Permission::updateOrCreate(['name' => 'view-client-data', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'log-incidents', 'guard_name' => 'web']);

        Permission::updateOrCreate(['name' => 'view-publicity', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'view-client-history', 'guard_name' => 'web']);

        // Asignar permisos a roles
        $adminRole->syncPermissions(['manage-users', 'manage-roles', 'view-reports', 'access-dashboard']);
        $supportRole->syncPermissions(['view-client-data', 'log-incidents']);
        $commercialRole->syncPermissions(['view-publicity', 'view-client-history']);
    }
}
