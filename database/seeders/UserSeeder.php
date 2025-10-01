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
            'is_active' => true,
        ]);
        $admin->setRole('admin');

       /*  // Líderes de ventas
        $lideres = [
            [
                'name' => 'María González',
                'email' => 'maria.gonzalez@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        ];

        foreach ($lideres as $lider) {
            $user = User::create($lider);
            $user->setRole('lider');
        }

        // Vendedores
        $vendedores = [
            [
                'name' => 'Ana Martínez',
                'email' => 'ana.martinez@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Luis Pérez',
                'email' => 'luis.perez@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Sofia López',
                'email' => 'sofia.lopez@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Roberto Silva',
                'email' => 'roberto.silva@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        ];

        foreach ($vendedores as $vendedor) {
            $user = User::create($vendedor);
            $user->setRole('vendedor');
        }

        // Dateros (captadores de datos)
        $dateros = [
            [
                'name' => 'Pedro Ramírez',
                'email' => 'pedro.ramirez@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Laura Jiménez',
                'email' => 'laura.jimenez@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Diego Morales',
                'email' => 'diego.morales@crm.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        ];

        foreach ($dateros as $datero) {
            $user = User::create($datero);
            $user->setRole('datero');
        }

        // Clientes
        $clientes = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@cliente.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Carmen García',
                'email' => 'carmen.garcia@cliente.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Miguel Torres',
                'email' => 'miguel.torres@cliente.com',
                'phone' => '999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        ];

        foreach ($clientes as $cliente) {
            $user = User::create($cliente);
            $user->setRole('cliente');
        } */

        $this->command->info('Usuarios creados exitosamente con los roles: admin, lider, vendedor, datero, cliente');
    }
}
