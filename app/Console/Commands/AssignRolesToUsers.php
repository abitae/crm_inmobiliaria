<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignRolesToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna roles a usuarios existentes basándose en el campo role anterior';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Asignando roles a usuarios existentes...');

        // Verificar si existen roles
        if (!Role::count()) {
            $this->error('No existen roles. Ejecuta primero: php artisan db:seed --class=RolePermissionSeeder');
            return 1;
        }

        $users = User::all();
        $assigned = 0;

        foreach ($users as $user) {
            // Verificar si el usuario ya tiene roles
            if ($user->roles()->count() > 0) {
                $this->line("Usuario {$user->email} ya tiene roles asignados, saltando...");
                continue;
            }

            // Asignar rol por defecto (vendedor)
            $user->assignRole('vendedor');
            $assigned++;
            $this->line("Rol 'vendedor' asignado a {$user->email}");
        }

        $this->info("Se asignaron roles a {$assigned} usuarios.");
        $this->info('¡Comando completado exitosamente!');

        return 0;
    }
}
