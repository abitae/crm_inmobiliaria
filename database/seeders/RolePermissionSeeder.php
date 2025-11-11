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

            // Unidades (Solo Lotes)
            // Nota: Las unidades en este sistema son exclusivamente lotes
            'view_units',      // Ver lotes disponibles
            'create_units',    // Crear nuevos lotes (solo admin)
            'edit_units',      // Editar lotes (solo admin)
            'delete_units',    // Eliminar lotes (solo admin)

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

            // Logs
            'view_logs',

            // Permisos jerárquicos específicos
            'view_team_metrics',
            'manage_team_members',
            'view_subordinates',
            'assign_tasks_team',
            'view_team_reports',
            'approve_team_actions',

            // Gestión de Dateros
            'view_dateros',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $liderRole = Role::firstOrCreate(['name' => 'lider']);
        $vendedorRole = Role::firstOrCreate(['name' => 'vendedor']);
        $dateroRole = Role::firstOrCreate(['name' => 'datero']);

        // Asignar todos los permisos al admin
        $adminRole->givePermissionTo(Permission::all());

        // Asignar permisos al lider (supervisor de vendedores y dateros)
        $liderRole->givePermissionTo([
            'view_dashboard',
            'view_clients',
            'create_clients',
            'edit_clients',
            'view_projects',
            'view_units',      // Ver lotes disponibles
            'view_opportunities',
            'create_opportunities',
            'edit_opportunities',
            'view_reservations',
            'create_reservations',
            'edit_reservations',
            'delete_reservations', // Puede eliminar reservas
            'view_commissions',
            'create_commissions',
            'edit_commissions',
            'delete_commissions', // Puede eliminar comisiones
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
            'create_users', // Para crear vendedores y dateros de su equipo
            'edit_users',   // Para gestionar su equipo
            'view_settings',
            'view_logs',    // Para ver logs del sistema
            // Permisos específicos de liderazgo
            'view_team_metrics',
            'manage_team_members',
            'view_subordinates',
            'assign_tasks_team',
            'view_team_reports',
            'approve_team_actions',
            'view_dateros', // Para ver lista de dateros
        ]);

        // Asignar permisos al vendedor (supervisor de dateros)
        $vendedorRole->givePermissionTo([
            'view_dashboard',
            'view_clients',
            'create_clients',
            'edit_clients',
            'view_projects',
            'view_units',      // Ver lotes disponibles
            'view_opportunities',
            'create_opportunities',
            'edit_opportunities',
            'view_reservations',
            'create_reservations',
            'edit_reservations',
            // Nota: vendedor no puede eliminar reservas, solo cancelarlas
            'view_commissions',
            'create_commissions',
            'edit_commissions',
            // Nota: vendedor no puede eliminar comisiones
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
            'view_users',
            'create_users', // Para crear dateros de su equipo
            'edit_users',   // Para gestionar sus dateros
            'view_settings',
            // Permisos específicos de vendedor
            'view_team_metrics',
            'manage_team_members',
            'view_subordinates',
            'assign_tasks_team',
            'view_team_reports',
            'view_dateros', // Para ver lista de dateros
        ]);

        // Asignar permisos al datero (captador de datos - nivel más bajo)
        $dateroRole->givePermissionTo([
            'view_dashboard',
            'view_clients',
            'create_clients',
            'edit_clients',
            'view_projects',
            'view_units',      // Ver lotes disponibles (solo lectura)
            'view_opportunities',
            'create_opportunities',
            // Nota: datero no puede editar oportunidades
            'view_commissions', // Puede ver sus propias comisiones (solo lectura)
            // Nota: datero no puede crear/editar/eliminar comisiones
            // Nota: datero no tiene acceso a reservas
            'view_activities',
            'create_activities',
            'view_documents',
            'create_documents',
            // Nota: edit_documents removido - dateros solo crean documentos
            'view_tasks',
            'create_tasks',
            'view_reports',
            // Sin permisos de gestión de usuarios ni equipos
        ]);
    }
}
