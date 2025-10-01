<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LogPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear el permiso para ver logs
        $permission = Permission::firstOrCreate([
            'name' => 'view_logs',
            'guard_name' => 'web'
        ]);

        // Asignar el permiso a los roles apropiados
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }

        $liderRole = Role::where('name', 'lider')->first();
        if ($liderRole) {
            $liderRole->givePermissionTo($permission);
        }

        // Los asesores y dateros no necesitan ver logs por defecto
        // pero se puede agregar si es necesario
    }
}
