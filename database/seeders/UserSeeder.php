<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario administrador
        $admin = User::create([
            'name' => 'Abel Arana',
            'email' => 'abel.arana@hotmail.com',
            'phone' => '999999999',
            'password' => Hash::make('lobomalo123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Líderes de ventas
        $lideres = [
            [
                'name' => 'María González',
                'email' => 'maria.gonzalez@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($lideres as $lider) {
            $user = User::create($lider);
            $user->assignRole('lider');
        }

        // Vendedores
        $vendedores = [
            [
                'name' => 'Ana Martínez',
                'email' => 'ana.martinez@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Luis Pérez',
                'email' => 'luis.perez@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sofia López',
                'email' => 'sofia.lopez@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Roberto Silva',
                'email' => 'roberto.silva@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($vendedores as $vendedor) {
            $user = User::create($vendedor);
            $user->assignRole('vendedor');
        }

        // Clientes
        $clientes = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@cliente.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Carmen García',
                'email' => 'carmen.garcia@cliente.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Miguel Torres',
                'email' => 'miguel.torres@cliente.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($clientes as $cliente) {
            $user = User::create($cliente);
            $user->assignRole('cliente');
        }

        $this->command->info('Usuarios creados exitosamente con los roles: admin, lider, vendedor, cliente');
    }
}
