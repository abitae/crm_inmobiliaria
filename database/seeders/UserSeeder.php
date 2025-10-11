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
        // Usuario administrador (sin líder)
        $admin = User::create([
            'name' => 'Abel Arana',
            'email' => 'abel.arana@hotmail.com',
            'phone' => '999999999',
            'password' => Hash::make('lobomalo123'),
            'email_verified_at' => now(),
            'is_active' => true,
            'lider_id' => null, // Admin no tiene líder
        ]);
        $admin->setRole('admin');

        // Crear 15 líderes de ventas (reportan al admin)
        $lideres = [];
        $nombresLideres = [
            'María González', 'Carlos Rodríguez', 'Ana Patricia López', 'Roberto Silva',
            'Carmen García', 'Diego Morales', 'Laura Jiménez', 'Miguel Torres',
            'Sofia Ramírez', 'Pedro Martínez', 'Elena Vargas', 'Fernando Castro',
            'Isabel Moreno', 'Antonio Ruiz', 'Valentina Herrera'
        ];

        for ($i = 0; $i < 15; $i++) {
            $lider = User::create([
                'name' => $nombresLideres[$i],
                'email' => strtolower(str_replace(' ', '.', $nombresLideres[$i])) . '@crm.com',
                'phone' => '999' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'lider_id' => $admin->id, // Reportan al admin
            ]);
            $lider->setRole('lider');
            $lideres[] = $lider;
        }

        // Crear 60 vendedores (4 por cada líder)
        $vendedores = [];
        $nombresVendedores = [
            'Ana Martínez', 'Luis Pérez', 'Sofia López', 'Roberto Silva', 'Miguel Torres',
            'Elena Vargas', 'Carlos Mendoza', 'Patricia Ruiz', 'Fernando Castro', 'Isabel Moreno',
            'Antonio Herrera', 'Valentina Ramírez', 'Diego Morales', 'Carmen García', 'Pedro Martínez',
            'Laura Jiménez', 'Roberto Silva', 'Sofia Ramírez', 'Miguel Torres', 'Elena Vargas',
            'Fernando Castro', 'Isabel Moreno', 'Antonio Ruiz', 'Valentina Herrera', 'Diego Morales',
            'Carmen García', 'Pedro Martínez', 'Laura Jiménez', 'Roberto Silva', 'Sofia Ramírez',
            'Miguel Torres', 'Elena Vargas', 'Fernando Castro', 'Isabel Moreno', 'Antonio Ruiz',
            'Valentina Herrera', 'Diego Morales', 'Carmen García', 'Pedro Martínez', 'Laura Jiménez',
            'Roberto Silva', 'Sofia Ramírez', 'Miguel Torres', 'Elena Vargas', 'Fernando Castro',
            'Isabel Moreno', 'Antonio Ruiz', 'Valentina Herrera', 'Diego Morales', 'Carmen García',
            'Pedro Martínez', 'Laura Jiménez', 'Roberto Silva', 'Sofia Ramírez', 'Miguel Torres',
            'Elena Vargas', 'Fernando Castro', 'Isabel Moreno', 'Antonio Ruiz', 'Valentina Herrera'
        ];

        for ($i = 0; $i < 60; $i++) {
            $liderAsignado = $lideres[$i % 15]; // Distribuir entre los 15 líderes
            $vendedor = User::create([
                'name' => $nombresVendedores[$i],
                'email' => strtolower(str_replace(' ', '.', $nombresVendedores[$i])) . ($i + 1) . '@crm.com',
                'phone' => '998' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'lider_id' => $liderAsignado->id,
            ]);
            $vendedor->setRole('vendedor');
            $vendedores[] = $vendedor;
        }

        // Crear 120 dateros (2 por cada vendedor)
        $dateros = [];
        $nombresDateros = [
            'Pedro Ramírez', 'Laura Jiménez', 'Diego Morales', 'Carmen García', 'Juan Pérez',
            'María López', 'Carlos Silva', 'Ana Martínez', 'Roberto Torres', 'Sofia Vargas',
            'Miguel Castro', 'Elena Moreno', 'Fernando Ruiz', 'Isabel Herrera', 'Antonio Ramírez',
            'Valentina García', 'Diego Morales', 'Carmen Jiménez', 'Pedro López', 'Laura Silva',
            'Roberto Torres', 'Sofia Vargas', 'Miguel Castro', 'Elena Moreno', 'Fernando Ruiz',
            'Isabel Herrera', 'Antonio Ramírez', 'Valentina García', 'Diego Morales', 'Carmen Jiménez',
            'Pedro López', 'Laura Silva', 'Roberto Torres', 'Sofia Vargas', 'Miguel Castro',
            'Elena Moreno', 'Fernando Ruiz', 'Isabel Herrera', 'Antonio Ramírez', 'Valentina García',
            'Diego Morales', 'Carmen Jiménez', 'Pedro López', 'Laura Silva', 'Roberto Torres',
            'Sofia Vargas', 'Miguel Castro', 'Elena Moreno', 'Fernando Ruiz', 'Isabel Herrera',
            'Antonio Ramírez', 'Valentina García', 'Diego Morales', 'Carmen Jiménez', 'Pedro López',
            'Laura Silva', 'Roberto Torres', 'Sofia Vargas', 'Miguel Castro', 'Elena Moreno',
            'Fernando Ruiz', 'Isabel Herrera', 'Antonio Ramírez', 'Valentina García', 'Diego Morales',
            'Carmen Jiménez', 'Pedro López', 'Laura Silva', 'Roberto Torres', 'Sofia Vargas',
            'Miguel Castro', 'Elena Moreno', 'Fernando Ruiz', 'Isabel Herrera', 'Antonio Ramírez',
            'Valentina García', 'Diego Morales', 'Carmen Jiménez', 'Pedro López', 'Laura Silva',
            'Roberto Torres', 'Sofia Vargas', 'Miguel Castro', 'Elena Moreno', 'Fernando Ruiz',
            'Isabel Herrera', 'Antonio Ramírez', 'Valentina García', 'Diego Morales', 'Carmen Jiménez',
            'Pedro López', 'Laura Silva', 'Roberto Torres', 'Sofia Vargas', 'Miguel Castro',
            'Elena Moreno', 'Fernando Ruiz', 'Isabel Herrera', 'Antonio Ramírez', 'Valentina García',
            'Diego Morales', 'Carmen Jiménez', 'Pedro López', 'Laura Silva', 'Roberto Torres',
            'Sofia Vargas', 'Miguel Castro', 'Elena Moreno', 'Fernando Ruiz', 'Isabel Herrera',
            'Antonio Ramírez', 'Valentina García', 'Diego Morales', 'Carmen Jiménez', 'Pedro López',
            'Laura Silva', 'Roberto Torres', 'Sofia Vargas', 'Miguel Castro', 'Elena Moreno',
            'Fernando Ruiz', 'Isabel Herrera', 'Antonio Ramírez', 'Valentina García', 'Diego Morales',
            'Carmen Jiménez', 'Pedro López', 'Laura Silva', 'Roberto Torres', 'Sofia Vargas',
            'Miguel Castro', 'Elena Moreno', 'Fernando Ruiz', 'Isabel Herrera', 'Antonio Ramírez',
            'Valentina García', 'Diego Morales', 'Carmen Jiménez', 'Pedro López', 'Laura Silva',
            'Roberto Torres', 'Sofia Vargas', 'Miguel Castro', 'Elena Moreno', 'Fernando Ruiz',
            'Isabel Herrera', 'Antonio Ramírez', 'Valentina García', 'Diego Morales', 'Carmen Jiménez'
        ];

        for ($i = 0; $i < 120; $i++) {
            $vendedorAsignado = $vendedores[$i % 60]; // Distribuir entre los 60 vendedores
            $datero = User::create([
                'name' => $nombresDateros[$i],
                'email' => strtolower(str_replace(' ', '.', $nombresDateros[$i])) . ($i + 1) . '@crm.com',
                'phone' => '997' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'lider_id' => $vendedorAsignado->id,
            ]);
            $datero->setRole('datero');
            $dateros[] = $datero;
        }

        $this->command->info('Usuarios creados exitosamente con jerarquías:');
        $this->command->info('Admin: Abel Arana');
        $this->command->info('Líderes: 15 líderes creados');
        $this->command->info('Vendedores: 60 vendedores creados (4 por cada líder)');
        $this->command->info('Dateros: 120 dateros creados (2 por cada vendedor)');
        $this->command->info('Total usuarios: ' . (1 + 15 + 60 + 120) . ' usuarios');
    }
}
