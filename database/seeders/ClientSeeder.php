<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    private const CLIENTS_PER_ADVISOR_MIN = 15;
    private const CLIENTS_PER_ADVISOR_MAX = 30;

    /**
     * Run the database seeds.
     * Cada usuario solo ve clientes donde assigned_advisor_id = su id.
     * Al crear: assigned_advisor_id, created_by y updated_by = mismo usuario.
     */
    public function run(): void
    {
        $admin = User::where('email', 'abel.arana@hotmail.com')->first();
        if (!$admin) {
            throw new \Exception('No se encontró el usuario administrador. Ejecuta UserSeeder primero.');
        }

        // Líderes y vendedores (cada uno tendrá 15-30 clientes asignados)
        $leadersAndVendors = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['lider', 'vendedor']);
        })->get();

        $faker = Faker::create('es_PE');
        $cityId = City::first()?->id;
        $clientTypes = ['inversor', 'comprador', 'empresa', 'constructor'];
        $sources = ['redes_sociales', 'ferias', 'referidos', 'formulario_web', 'publicidad'];
        $statuses = ['nuevo', 'contacto_inicial', 'en_seguimiento', 'cierre', 'perdido'];

        $fixedCount = 0;
        // Clientes fijos de ejemplo (asignados al primer líder o vendedor disponible)
        $firstAdvisor = $leadersAndVendors->first();
        if ($firstAdvisor) {
            $fixedClients = [
                [
                    'name' => 'Juan Carlos Vargas Mendoza',
                    'phone' => '999123456',
                    'document_type' => 'DNI',
                    'document_number' => '12345678',
                    'address' => 'Av. Arequipa 1234',
                    'birth_date' => '1985-03-15',
                    'client_type' => 'comprador',
                    'source' => 'formulario_web',
                    'status' => 'en_seguimiento',
                    'score' => 85,
                    'notes' => 'Cliente interesado en departamentos de 2-3 dormitorios en Miraflores',
                ],
                [
                    'name' => 'María Elena Torres Ríos',
                    'phone' => '999234567',
                    'document_type' => 'DNI',
                    'document_number' => '23456789',
                    'address' => 'Jr. de la Unión 567',
                    'birth_date' => '1978-07-22',
                    'client_type' => 'inversor',
                    'source' => 'referidos',
                    'status' => 'contacto_inicial',
                    'score' => 70,
                    'notes' => 'Inversora buscando propiedades para alquiler',
                ],
                [
                    'name' => 'Roberto Silva Castro',
                    'phone' => '999345678',
                    'document_type' => 'RUC',
                    'document_number' => '20123456789',
                    'address' => 'Av. Javier Prado 2345',
                    'birth_date' => '1990-11-08',
                    'client_type' => 'empresa',
                    'source' => 'ferias',
                    'status' => 'nuevo',
                    'score' => 60,
                    'notes' => 'Empresa buscando oficinas corporativas',
                ],
                [
                    'name' => 'Carmen Flores Díaz',
                    'phone' => '999456789',
                    'document_type' => 'DNI',
                    'document_number' => '34567890',
                    'address' => 'Av. Benavides 3456',
                    'birth_date' => '1982-05-30',
                    'client_type' => 'comprador',
                    'source' => 'redes_sociales',
                    'status' => 'en_seguimiento',
                    'score' => 90,
                    'notes' => 'Cliente VIP, busca casa familiar en Surco',
                ],
                [
                    'name' => 'Fernando Mendoza Ruiz',
                    'phone' => '999567890',
                    'document_type' => 'RUC',
                    'document_number' => '20134567890',
                    'address' => 'Av. La Marina 4567',
                    'birth_date' => '1975-12-14',
                    'client_type' => 'constructor',
                    'source' => 'publicidad',
                    'status' => 'nuevo',
                    'score' => 75,
                    'notes' => 'Constructor interesado en lotes para desarrollo',
                ],
            ];
            foreach ($fixedClients as $data) {
                $this->createClient($data, $firstAdvisor->id, $cityId, $faker, $clientTypes, $sources, $statuses);
                $fixedCount++;
            }
        }

        // 15 a 30 clientes por cada líder y por cada vendedor
        $totalCreated = 0;
        foreach ($leadersAndVendors as $advisor) {
            $count = random_int(self::CLIENTS_PER_ADVISOR_MIN, self::CLIENTS_PER_ADVISOR_MAX);
            $roleName = $advisor->getRoleName();
            for ($i = 0; $i < $count; $i++) {
                $this->createClient([
                    'name' => $faker->name(),
                    'phone' => $faker->unique()->numerify('9########'),
                    'document_type' => $faker->randomElement(['DNI', 'RUC']),
                    'document_number' => null, // se genera en createClient
                    'address' => $faker->streetAddress(),
                    'birth_date' => $faker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d'),
                    'client_type' => $clientTypes[array_rand($clientTypes)],
                    'source' => $sources[array_rand($sources)],
                    'status' => $statuses[array_rand($statuses)],
                    'score' => rand(40, 100),
                    'notes' => "Cliente asignado a {$advisor->name} ({$roleName}).",
                ], $advisor->id, $cityId, $faker, $clientTypes, $sources, $statuses);
                $totalCreated++;
            }
            $this->command->info("  → {$advisor->name} ({$roleName}): {$count} clientes.");
        }

        // Clientes asignados al admin (para que pueda ver algunos en el listado)
        $adminCount = random_int(10, 20);
        for ($i = 0; $i < $adminCount; $i++) {
            $this->createClient([
                'name' => $faker->name(),
                'phone' => $faker->unique()->numerify('9########'),
                'document_type' => $faker->randomElement(['DNI', 'RUC']),
                'document_number' => null,
                'address' => $faker->streetAddress(),
                'birth_date' => $faker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d'),
                'client_type' => $clientTypes[array_rand($clientTypes)],
                'source' => $sources[array_rand($sources)],
                'status' => $statuses[array_rand($statuses)],
                'score' => rand(40, 100),
                'notes' => 'Cliente asignado al administrador.',
            ], $admin->id, $cityId, $faker, $clientTypes, $sources, $statuses);
            $totalCreated++;
        }
        $this->command->info("  → Admin: {$adminCount} clientes.");

        $total = $totalCreated + $fixedCount;
        $this->command->info("Clientes creados: {$total} (assigned_advisor_id = created_by = updated_by por usuario).");
    }

    /**
     * Crea un cliente con assigned_advisor_id, created_by y updated_by = mismo usuario.
     */
    private function createClient(
        array $data,
        int $advisorId,
        ?int $cityId,
        $faker,
        array $clientTypes,
        array $sources,
        array $statuses
    ): Client {
        $documentType = $data['document_type'] ?? $faker->randomElement(['DNI', 'RUC']);
        $documentNumber = $data['document_number'] ?? ($documentType === 'RUC'
            ? $faker->unique()->numerify('20#########')
            : $faker->unique()->numerify('########'));

        return Client::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'document_type' => $documentType,
            'document_number' => $documentNumber,
            'address' => $data['address'] ?? null,
            'birth_date' => $data['birth_date'],
            'client_type' => $data['client_type'] ?? $clientTypes[array_rand($clientTypes)],
            'source' => $data['source'] ?? $sources[array_rand($sources)],
            'status' => $data['status'] ?? $statuses[array_rand($statuses)],
            'score' => $data['score'] ?? rand(40, 100),
            'notes' => $data['notes'] ?? null,
            'create_mode' => 'dni',
            'city_id' => $cityId,
            'assigned_advisor_id' => $advisorId,
            'created_by' => $advisorId,
            'updated_by' => $advisorId,
            'create_type' => 'propio',
        ]);
    }
}
