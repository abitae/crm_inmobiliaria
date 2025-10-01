<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateLogPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:create-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the view_logs permission and assign it to appropriate roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating view_logs permission...');

        // Crear el permiso para ver logs
        $permission = Permission::firstOrCreate([
            'name' => 'view_logs',
            'guard_name' => 'web'
        ]);

        $this->info('Permission created: ' . $permission->name);

        // Asignar el permiso a los roles apropiados
        $roles = ['admin', 'lider'];
        
        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                    $this->info("Permission assigned to role: {$roleName}");
                } else {
                    $this->info("Role {$roleName} already has this permission");
                }
            } else {
                $this->warn("Role {$roleName} not found");
            }
        }

        $this->info('Log permission setup completed!');
    }
}
