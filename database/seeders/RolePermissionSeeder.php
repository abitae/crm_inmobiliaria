<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Dashboard
            'view_dashboard',

            // Clientes
            'view_clients',
            'create_clients',
            'edit_clients',
            'delete_clients',

            // Proyectos
            'view_projects',
            'create_projects',
            'edit_projects',
            'delete_projects',

            // Unidades
            'view_units',
            'create_units',
            'edit_units',
            'delete_units',

            // Oportunidades
            'view_opportunities',
            'create_opportunities',
            'edit_opportunities',
            'delete_opportunities',

            // Reservas
            'view_reservations',
            'create_reservations',
            'edit_reservations',
            'delete_reservations',

            // Comisiones
            'view_commissions',
            'create_commissions',
            'edit_commissions',
            'delete_commissions',

            // Tareas
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'delete_tasks',

            // Actividades
            'view_activities',
            'create_activities',
            'edit_activities',
            'delete_activities',


            // Documentos
            'view_documents',
            'create_documents',
            'edit_documents',
            'delete_documents',

            // Reportes
            'view_reports',
            'export_reports',

            // Usuarios y roles
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_roles',
            'manage_users',
            'manage_permissions',

            // Configuración
            'view_settings',
            'edit_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $liderRole = Role::firstOrCreate(['name' => 'lider']);
        $vendedorRole = Role::firstOrCreate(['name' => 'vendedor']);
        $dateroRole = Role::firstOrCreate(['name' => 'datero']);
        $clienteRole = Role::firstOrCreate(['name' => 'cliente']);

        // Asignar todos los permisos al admin
        $adminRole->givePermissionTo(Permission::all());

        // Asignar permisos al lider (supervisor de vendedores)
        $liderRole->givePermissionTo([
            'view_dashboard',
            'view_clients',
            'create_clients',
            'edit_clients',
            'view_projects',
            'view_units',
            'view_opportunities',
            'create_opportunities',
            'edit_opportunities',
            'view_reservations',
            'create_reservations',
            'edit_reservations',
            'view_commissions',
            'create_commissions',
            'edit_commissions',
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'view_activities',
            'create_activities',
            'edit_activities',
            'view_documents',
            'create_documents',
            'edit_documents',
            'view_reports',
            'export_reports',
            'view_users',
            'create_users', // Para crear nuevos vendedores
            'edit_users',   // Para gestionar vendedores
            'view_settings',
            'manage_roles',
            'manage_users',
            'manage_permissions',
        ]);

        // Asignar permisos al vendedor
        $vendedorRole->givePermissionTo([
            'view_dashboard',
            'view_clients',
            'create_clients',
            'edit_clients',
            'view_projects',
            'view_units',
            'view_opportunities',
            'create_opportunities',
            'edit_opportunities',
            'view_reservations',
            'create_reservations',
            'edit_reservations',
            'view_commissions',
            'create_commissions',
            'edit_commissions',
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'view_activities',
            'create_activities',
            'edit_activities',
            'view_documents',
            'create_documents',
            'edit_documents',
            'view_reports',
            'manage_roles',
            'manage_users',
            'manage_permissions',
        ]);

        // Asignar permisos al datero (captador de datos)
        $dateroRole->givePermissionTo([
            'view_dashboard',
            'view_clients',
            'create_clients',
            'edit_clients',
            'view_projects',
            'view_units',
            'view_opportunities',
            'create_opportunities',
            'view_activities',
            'create_activities',
            'view_documents',
            'create_documents',
            'view_tasks',
            'create_tasks',
            'view_reports',
        ]);

        // Asignar permisos básicos al cliente
        $clienteRole->givePermissionTo([
            'view_dashboard',
            'view_projects',
            'view_units',
            'view_opportunities',
            'view_reservations',
            'view_commissions',
            'view_tasks',
            'view_activities',
            'view_documents',
            'manage_roles',
            'manage_users',
            'manage_permissions',
        ]);
    }
}
