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
            'password' => Hash::make('lobomalo123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Asesores de ventas
        $advisors = [
            [
                'name' => 'María González',
                'email' => 'maria.gonzalez@crm.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@crm.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ana Martínez',
                'email' => 'ana.martinez@crm.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Luis Pérez',
                'email' => 'luis.perez@crm.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sofia López',
                'email' => 'sofia.lopez@crm.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($advisors as $advisor) {
            $user = User::create($advisor);
            $user->assignRole('advisor');
        }

        // Usuarios adicionales para diferentes roles
        $additionalUsers = [
            [
                'name' => 'Gerente Ventas',
                'email' => 'gerente@crm.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Supervisor Comercial',
                'email' => 'supervisor@crm.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($additionalUsers as $userData) {
            $user = User::create($userData);
            $user->assignRole('user');
        }

        $this->command->info('Usuarios creados exitosamente');
    }
}
