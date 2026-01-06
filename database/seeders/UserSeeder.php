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

        // Crear 5 líderes de ventas (reportan al admin)
        $lideres = [];
        $nombresLideres = [
            'María González', 'Carlos Rodríguez', 'Ana Patricia López', 'Roberto Silva',
            'Carmen García'
        ];

        for ($i = 0; $i < 5; $i++) {
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

        // Crear 100 vendedores (20 por cada líder)
        $vendedores = [];
        $nombresVendedores = [
            'Ana', 'Luis', 'Sofia', 'Roberto', 'Miguel', 'Elena', 'Carlos', 'Patricia', 'Fernando', 'Isabel',
            'Antonio', 'Valentina', 'Diego', 'Carmen', 'Pedro', 'Laura', 'Juan', 'María', 'José', 'Andrea',
            'Ricardo', 'Gabriela', 'Daniel', 'Monica', 'Alejandro', 'Paola', 'Andrés', 'Natalia', 'Sergio', 'Claudia',
            'Francisco', 'Diana', 'Javier', 'Raúl', 'Verónica', 'Manuel', 'Lucía', 'Rodrigo', 'Cecilia', 'Esteban',
            'Mariana', 'Felipe', 'Adriana', 'Gustavo', 'Carolina', 'Héctor', 'Óscar', 'Rosa', 'Emilio', 'Teresa',
            'Víctor', 'Lorena', 'Mauricio', 'Gloria', 'Pablo', 'Silvia', 'Alberto', 'Martha', 'Enrique', 'Beatriz',
            'Rafael', 'Alicia', 'Gerardo', 'Eugenia', 'Arturo', 'Dolores', 'Ignacio', 'Ramón', 'Eduardo', 'Jorge',
            'Mercedes', 'Salvador', 'Josefina', 'Tomás', 'Francisca', 'Agustín', 'Manuela', 'Benito', 'Rosario', 'César',
            'Amparo', 'Concepción', 'Ángel', 'Brenda', 'Cristina', 'David', 'Edgar', 'Fabiola', 'Gonzalo', 'Hilda',
            'Iván', 'Julia', 'Kevin', 'Liliana', 'Mario', 'Nora', 'Octavio', 'Patricia', 'Quetzal', 'Renata'
        ];
        $apellidosVendedores = [
            'Martínez', 'Pérez', 'López', 'García', 'González', 'Rodríguez', 'Hernández', 'Sánchez', 'Ramírez', 'Torres',
            'Flores', 'Rivera', 'Gómez', 'Díaz', 'Cruz', 'Morales', 'Ortiz', 'Gutiérrez', 'Chávez', 'Ramos',
            'Reyes', 'Mendoza', 'Moreno', 'Jiménez', 'Alvarez', 'Ruiz', 'Vargas', 'Castro', 'Romero', 'Soto',
            'Contreras', 'Guerrero', 'Ortega', 'Delgado', 'Salazar', 'Vega', 'Medina', 'Herrera', 'Aguilar', 'Sandoval',
            'Silva', 'Méndez', 'Rojas', 'Cortés', 'Núñez', 'Peña', 'Luna', 'Campos', 'Vásquez', 'Fuentes',
            'Carrillo', 'Paredes', 'Navarro', 'Valdez', 'Espinoza', 'Mejía', 'Acosta', 'Miranda', 'Ponce', 'Sosa',
            'Villanueva', 'Cárdenas', 'Benítez', 'Zúñiga', 'Aguirre', 'Montes', 'Serrano', 'León', 'Calderón', 'Ríos',
            'Molina', 'Franco', 'Barrera', 'Escobar', 'Pacheco', 'Cervantes', 'Galván', 'Velázquez', 'Castañeda', 'Juárez',
            'Tapia', 'Ibarra', 'Macías', 'Solis', 'Maldonado', 'Rangel', 'Zamora', 'Bautista', 'Robles', 'Quiroz',
            'Yáñez', 'Ximénez', 'Wong', 'Villalobos', 'Uribe', 'Trejo', 'Suárez', 'Roldán', 'Pineda', 'Ochoa'
        ];

        for ($i = 0; $i < 100; $i++) {
            $nombre = $nombresVendedores[$i % count($nombresVendedores)];
            $apellido = $apellidosVendedores[intval($i / count($nombresVendedores)) % count($apellidosVendedores)];
            $nombreCompleto = $nombre . ' ' . $apellido;
            
            $liderAsignado = $lideres[$i % 5]; // Distribuir entre los 5 líderes (20 por cada uno)
            $emailBase = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['', 'a', 'e', 'i', 'o', 'u', 'n'], $nombre . '.' . $apellido));
            $email = $emailBase . ($i + 1) . '@crm.com'; // Siempre incluir número único
            
            $vendedor = User::create([
                'name' => $nombreCompleto,
                'email' => $email,
                'phone' => '998' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'lider_id' => $liderAsignado->id,
            ]);
            $vendedor->setRole('vendedor');
            $vendedores[] = $vendedor;
        }

        // Crear 100 dateros (1 por cada vendedor)
        $dateros = [];
        $nombresDateros = [
            'Pedro', 'Laura', 'Diego', 'Carmen', 'Juan', 'María', 'Carlos', 'Ana', 'Roberto', 'Sofia',
            'Miguel', 'Elena', 'Fernando', 'Isabel', 'Antonio', 'Valentina', 'Ricardo', 'Gabriela', 'Daniel', 'Monica',
            'Alejandro', 'Paola', 'Andrés', 'Natalia', 'Sergio', 'Claudia', 'Francisco', 'Diana', 'Javier', 'Raúl',
            'Verónica', 'Manuel', 'Lucía', 'Rodrigo', 'Cecilia', 'Esteban', 'Mariana', 'Felipe', 'Adriana', 'Gustavo',
            'Carolina', 'Héctor', 'Óscar', 'Rosa', 'Emilio', 'Teresa', 'Víctor', 'Lorena', 'Mauricio', 'Gloria',
            'Pablo', 'Silvia', 'Alberto', 'Martha', 'Enrique', 'Beatriz', 'Rafael', 'Alicia', 'Gerardo', 'Eugenia',
            'Arturo', 'Dolores', 'Ignacio', 'Ramón', 'Eduardo', 'Jorge', 'Mercedes', 'Salvador', 'Josefina', 'Tomás',
            'Francisca', 'Agustín', 'Manuela', 'Benito', 'Rosario', 'César', 'Amparo', 'Concepción', 'Ángel', 'Brenda',
            'Cristina', 'David', 'Edgar', 'Fabiola', 'Gonzalo', 'Hilda', 'Iván', 'Julia', 'Kevin', 'Liliana',
            'Mario', 'Nora', 'Octavio', 'Quetzal', 'Renata', 'Samuel', 'Tania', 'Ulises', 'Vanesa', 'Wilfredo'
        ];
        $apellidosDateros = [
            'Ramírez', 'Jiménez', 'Morales', 'García', 'Pérez', 'López', 'Silva', 'Martínez', 'Torres', 'Vargas',
            'Castro', 'Moreno', 'Ruiz', 'Herrera', 'Flores', 'Rivera', 'Gómez', 'Díaz', 'Cruz', 'Ortiz',
            'Gutiérrez', 'Chávez', 'Ramos', 'Reyes', 'Mendoza', 'Alvarez', 'Romero', 'Soto', 'Contreras', 'Guerrero',
            'Ortega', 'Delgado', 'Salazar', 'Vega', 'Medina', 'Aguilar', 'Sandoval', 'Méndez', 'Rojas', 'Cortés',
            'Núñez', 'Peña', 'Luna', 'Campos', 'Vásquez', 'Fuentes', 'Carrillo', 'Paredes', 'Navarro', 'Valdez',
            'Espinoza', 'Mejía', 'Acosta', 'Miranda', 'Ponce', 'Sosa', 'Villanueva', 'Cárdenas', 'Benítez', 'Zúñiga',
            'Aguirre', 'Montes', 'Serrano', 'León', 'Calderón', 'Ríos', 'Molina', 'Franco', 'Barrera', 'Escobar',
            'Pacheco', 'Cervantes', 'Galván', 'Velázquez', 'Castañeda', 'Juárez', 'Tapia', 'Ibarra', 'Macías', 'Solis',
            'Maldonado', 'Rangel', 'Zamora', 'Bautista', 'Robles', 'Quiroz', 'Yáñez', 'Ximénez', 'Wong', 'Villalobos',
            'Uribe', 'Trejo', 'Suárez', 'Roldán', 'Pineda', 'Ochoa', 'Nieto', 'Márquez', 'Lozano', 'Kaufman'
        ];

        for ($i = 0; $i < 100; $i++) {
            $nombre = $nombresDateros[$i % count($nombresDateros)];
            $apellido = $apellidosDateros[intval($i / count($nombresDateros)) % count($apellidosDateros)];
            $nombreCompleto = $nombre . ' ' . $apellido;
            
            $vendedorAsignado = $vendedores[$i]; // 1 datero por cada vendedor
            $emailBase = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['', 'a', 'e', 'i', 'o', 'u', 'n'], $nombre . '.' . $apellido));
            $email = $emailBase . ($i + 1) . '@crm.com'; // Siempre incluir número único
            
            $datero = User::create([
                'name' => $nombreCompleto,
                'email' => $email,
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
        $this->command->info('Líderes: 5 líderes creados');
        $this->command->info('Vendedores: 100 vendedores creados (20 por cada líder)');
        $this->command->info('Dateros: 100 dateros creados (1 por cada vendedor)');
        $this->command->info('Total usuarios: ' . (1 + 5 + 100 + 100) . ' usuarios');
    }
}
