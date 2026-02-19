<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_PE');
        $this->command->info('Generando usuarios de prueba (mínimo)...');

        // Usuario administrador (sin líder) — pin y password iguales, pin con Hash::make
        $adminPin = '123456';
        $admin = User::create([
            'name' => 'Abel Arana',
            'email' => 'abel.arana@hotmail.com',
            'dni' => '00000000',
            'pin' => Hash::make($adminPin),
            'phone' => '999999999',
            'ocupacion' => 'Administrador',
            'password' => Hash::make($adminPin),
            'email_verified_at' => now(),
            'is_active' => true,
            'lider_id' => null,
        ]);
        $admin->setRole('admin');

        $leadersCount = 2;
        $vendorsPerLeader = 1;
        $independentVendors = 0;
        $independentDateros = 0;

        // Líderes fijos para mantener compatibilidad con jerarquías
        $fixedLeaders = [
            ['name' => 'María González', 'email' => 'maria.gonzalez@crm.com'],
            ['name' => 'Carlos Rodríguez', 'email' => 'carlos.rodriguez@crm.com'],
        ];

        // PIN fijo para líderes de prueba (123456)
        $lideres = [];
        foreach ($fixedLeaders as $leaderData) {
            $lideres[] = $this->createUser($leaderData + [
                'dni' => $this->generateDni($faker),
                'pin' => '123456',
                'phone' => $faker->numerify('9########'),
                'ocupacion' => 'Líder de ventas',
                'lider_id' => $admin->id,
            ], 'lider');
        }

        for ($i = count($fixedLeaders); $i < $leadersCount; $i++) {
            $lideres[] = $this->createUser([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'dni' => $this->generateDni($faker),
                'pin' => $faker->numerify('######'),
                'phone' => $faker->numerify('9########'),
                'ocupacion' => 'Líder de ventas',
                'lider_id' => $admin->id,
            ], 'lider');
        }

        $vendedores = [];
        foreach ($lideres as $lider) {
            for ($i = 0; $i < $vendorsPerLeader; $i++) {
                $vendedores[] = $this->createUser([
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'dni' => $this->generateDni($faker),
                    'pin' => $faker->numerify('######'),
                    'phone' => $faker->numerify('9########'),
                    'ocupacion' => 'Vendedor',
                    'lider_id' => $lider->id,
                ], 'vendedor');
            }
        }

        // Vendedores independientes
        for ($i = 0; $i < $independentVendors; $i++) {
            $vendedores[] = $this->createUser([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'dni' => $this->generateDni($faker),
                'pin' => $faker->numerify('######'),
                'phone' => $faker->numerify('9########'),
                'ocupacion' => 'Vendedor independiente',
                'lider_id' => null,
            ], 'vendedor');
        }

        // Dateros (1 por vendedor)
        $dateros = [];
        foreach ($vendedores as $vendedor) {
            $dateros[] = $this->createUser([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'dni' => $this->generateDni($faker),
                'pin' => $faker->numerify('######'),
                'phone' => $faker->numerify('9########'),
                'ocupacion' => 'Datero',
                'lider_id' => $vendedor->id,
            ], 'datero');
        }

        // Dateros independientes
        for ($i = 0; $i < $independentDateros; $i++) {
            $dateros[] = $this->createUser([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'dni' => $this->generateDni($faker),
                'pin' => $faker->numerify('######'),
                'phone' => $faker->numerify('9########'),
                'ocupacion' => 'Datero independiente',
                'lider_id' => null,
            ], 'datero');
        }

        $this->command->info('Usuarios creados exitosamente (mínimo):');
        $this->command->info('Admin: Abel Arana');
        $this->command->info("Líderes: {$leadersCount} líderes creados");
        $this->command->info('Vendedores: ' . count($vendedores) . ' vendedores creados');
        $this->command->info('Dateros: ' . count($dateros) . ' dateros creados');
        $this->command->info('Total usuarios: ' . (1 + count($lideres) + count($vendedores) + count($dateros)) . ' usuarios');
    }

    private function createUser(array $data, string $role): User
    {
        // Pin y password siempre iguales; pin guardado con Hash::make
        $pin = $data['pin'] ?? '123456';
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'dni' => $data['dni'] ?? null,
            'pin' => Hash::make($pin),
            'phone' => $data['phone'] ?? null,
            'ocupacion' => $data['ocupacion'] ?? null,
            'password' => Hash::make($pin),
            'email_verified_at' => now(),
            'is_active' => true,
            'lider_id' => $data['lider_id'] ?? null,
        ]);

        $user->setRole($role);

        return $user;
    }

    private function generateDni($faker): string
    {
        return (string) $faker->unique()->numberBetween(10000000, 99999999);
    }
}
