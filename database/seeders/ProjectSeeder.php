<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'abel.arana@hotmail.com')->first();

        // Verificar que exista el admin antes de continuar
        if (!$admin) {
            throw new \Exception('No se encontró el usuario administrador. Asegúrate de ejecutar UserSeeder primero.');
        }

        $projects = [
            [
                'name' => 'Residencial Miraflores Park',
                'description' => 'Exclusivo proyecto residencial en el corazón de Miraflores, con acabados de lujo y amenidades premium.',
                'project_type' => 'departamentos',
                'stage' => 'venta_activa',
                'legal_status' => 'habilitado',
                'address' => 'Av. Arequipa 1234',
                'district' => 'Miraflores',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'latitude' => -12.1194,
                'longitude' => -77.0333,
                'total_units' => 120,
                'available_units' => 45,
                'reserved_units' => 25,
                'sold_units' => 50,
                'blocked_units' => 0,
                'start_date' => '2023-01-15',
                'end_date' => '2025-06-30',
                'delivery_date' => '2025-12-31',
                'status' => 'activo',
            ],
            [
                'name' => 'Torres San Isidro Business',
                'description' => 'Complejo de oficinas corporativas de alta gama en San Isidro, ideal para empresas multinacionales.',
                'project_type' => 'oficinas',
                'stage' => 'lanzamiento',
                'legal_status' => 'habilitado',
                'address' => 'Av. Javier Prado 2345',
                'district' => 'San Isidro',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'latitude' => -12.0972,
                'longitude' => -77.0267,
                'total_units' => 80,
                'available_units' => 60,
                'reserved_units' => 15,
                'sold_units' => 5,
                'blocked_units' => 0,
                'start_date' => '2024-03-01',
                'end_date' => '2026-08-31',
                'delivery_date' => '2026-12-31',
                'status' => 'activo',
            ],
            [
                'name' => 'Lotes Barranco Golf',
                'description' => 'Exclusivos lotes residenciales con vista al mar en Barranco, perfectos para construir casas de lujo.',
                'project_type' => 'lotes',
                'stage' => 'preventa',
                'legal_status' => 'con_titulo',
                'address' => 'Av. Costanera 3456',
                'district' => 'Barranco',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'latitude' => -12.1419,
                'longitude' => -77.0217,
                'total_units' => 50,
                'available_units' => 40,
                'reserved_units' => 8,
                'sold_units' => 2,
                'blocked_units' => 0,
                'start_date' => '2024-06-01',
                'end_date' => '2027-12-31',
                'delivery_date' => '2027-12-31',
                'status' => 'activo',
            ],
            [
                'name' => 'Casas Surco Family',
                'description' => 'Proyecto de casas familiares en Surco, con amplios jardines y excelente conectividad.',
                'project_type' => 'casas',
                'stage' => 'venta_activa',
                'legal_status' => 'habilitado',
                'address' => 'Av. Benavides 4567',
                'district' => 'Surco',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'latitude' => -12.1583,
                'longitude' => -76.9933,
                'total_units' => 60,
                'available_units' => 20,
                'reserved_units' => 15,
                'sold_units' => 25,
                'blocked_units' => 0,
                'start_date' => '2022-09-01',
                'end_date' => '2024-12-31',
                'delivery_date' => '2025-03-31',
                'status' => 'activo',
            ],
            [
                'name' => 'Mixto Chorrillos Plaza',
                'description' => 'Proyecto mixto con departamentos, oficinas y locales comerciales en Chorrillos.',
                'project_type' => 'mixto',
                'stage' => 'lanzamiento',
                'legal_status' => 'en_tramite',
                'address' => 'Av. Primavera 5678',
                'district' => 'Chorrillos',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'latitude' => -12.1750,
                'longitude' => -76.9917,
                'total_units' => 200,
                'available_units' => 180,
                'reserved_units' => 15,
                'sold_units' => 5,
                'blocked_units' => 0,
                'start_date' => '2024-01-01',
                'end_date' => '2027-06-30',
                'delivery_date' => '2027-12-31',
                'status' => 'activo',
            ],
            [
                'name' => 'Oficinas San Borja Center',
                'description' => 'Centro empresarial moderno en San Borja con oficinas flexibles y espacios de coworking.',
                'project_type' => 'oficinas',
                'stage' => 'venta_activa',
                'legal_status' => 'habilitado',
                'address' => 'Av. Aviación 6789',
                'district' => 'San Borja',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'latitude' => -12.1083,
                'longitude' => -76.9917,
                'total_units' => 100,
                'available_units' => 35,
                'reserved_units' => 20,
                'sold_units' => 45,
                'blocked_units' => 0,
                'start_date' => '2023-06-01',
                'end_date' => '2025-12-31',
                'delivery_date' => '2026-03-31',
                'status' => 'activo',
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create([
                ...$projectData,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }

        // Crear proyectos adicionales usando factory (comentado porque no existe ProjectFactory)
        // Project::factory(8)->create([
        //     'created_by' => $admin->id,
        //     'updated_by' => $admin->id,
        // ]);

        $this->command->info('Proyectos creados exitosamente');
    }
}
