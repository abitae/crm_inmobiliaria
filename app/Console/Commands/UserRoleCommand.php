<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class UserRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:role {action} {--user=} {--role=} {--list} {--activate} {--deactivate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gestionar roles de usuarios (un solo rol por usuario)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                $this->listUsers();
                break;
            case 'assign':
                $this->assignRole();
                break;
            case 'remove':
                $this->removeRole();
                break;
            case 'change':
                $this->changeRole();
                break;
            case 'activate':
                $this->activateUser();
                break;
            case 'deactivate':
                $this->deactivateUser();
                break;
            default:
                $this->error('Acción no válida. Use: list, assign, remove, change, activate, deactivate');
                return 1;
        }

        return 0;
    }

    /**
     * Listar usuarios con sus roles
     */
    private function listUsers()
    {
        $this->info('Usuarios y sus roles:');
        $this->newLine();

        $users = User::withRole()->get();
        
        $headers = ['ID', 'Nombre', 'Email', 'Rol', 'Estado'];
        $rows = [];

        foreach ($users as $user) {
            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->getRoleName() ?? 'Sin rol',
                $user->isActive() ? 'Activo' : 'Inactivo'
            ];
        }

        $this->table($headers, $rows);
    }

    /**
     * Asignar rol a usuario
     */
    private function assignRole()
    {
        $userId = $this->option('user');
        $roleName = $this->option('role');

        if (!$userId || !$roleName) {
            $this->error('Debe especificar --user=ID y --role=NOMBRE');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario con ID $userId no encontrado");
            return;
        }

        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Rol '$roleName' no encontrado");
            return;
        }

        $user->setRole($roleName);
        $this->info("Rol '$roleName' asignado al usuario {$user->name}");
    }

    /**
     * Remover rol de usuario
     */
    private function removeRole()
    {
        $userId = $this->option('user');

        if (!$userId) {
            $this->error('Debe especificar --user=ID');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario con ID $userId no encontrado");
            return;
        }

        $user->syncRoles([]);
        $this->info("Rol removido del usuario {$user->name}");
    }

    /**
     * Cambiar rol de usuario
     */
    private function changeRole()
    {
        $userId = $this->option('user');
        $roleName = $this->option('role');

        if (!$userId || !$roleName) {
            $this->error('Debe especificar --user=ID y --role=NOMBRE');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario con ID $userId no encontrado");
            return;
        }

        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Rol '$roleName' no encontrado");
            return;
        }

        $oldRole = $user->getRoleName();
        $user->setRole($roleName);
        
        $this->info("Rol cambiado de '$oldRole' a '$roleName' para el usuario {$user->name}");
    }

    /**
     * Activar usuario
     */
    private function activateUser()
    {
        $userId = $this->option('user');

        if (!$userId) {
            $this->error('Debe especificar --user=ID');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario con ID $userId no encontrado");
            return;
        }

        $user->activate();
        $this->info("Usuario {$user->name} activado exitosamente");
    }

    /**
     * Desactivar usuario
     */
    private function deactivateUser()
    {
        $userId = $this->option('user');

        if (!$userId) {
            $this->error('Debe especificar --user=ID');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario con ID $userId no encontrado");
            return;
        }

        $user->deactivate();
        $this->info("Usuario {$user->name} desactivado exitosamente");
    }
}
