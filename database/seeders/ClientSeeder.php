<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $advisors = User::where('email', '!=', 'abel.arana@hotmail.com')->take(5)->get();
        $admin = User::where('email', 'abel.arana@hotmail.com')->first();

        // Verificar que existan usuarios antes de continuar
        if ($advisors->isEmpty()) {
            throw new \Exception('No se encontraron asesores en la base de datos. Asegúrate de ejecutar UserSeeder primero.');
        }

        if (!$admin) {
            throw new \Exception('No se encontró el usuario administrador. Asegúrate de ejecutar UserSeeder primero.');
        }

        $clientTypes = ['inversor', 'comprador', 'empresa', 'constructor'];
        $sources = ['redes_sociales', 'ferias', 'referidos', 'formulario_web', 'publicidad'];
        $statuses = ['nuevo', 'contacto_inicial', 'en_seguimiento', 'cierre', 'perdido'];
        $districts = ['Miraflores', 'San Isidro', 'Barranco', 'Surco', 'Chorrillos', 'San Borja'];
        $provinces = ['Lima'];
        $regions = ['Lima Metropolitana'];

        $clients = [
            [
                'first_name' => 'Juan Carlos',
                'last_name' => 'Vargas Mendoza',
                'email' => 'juan.vargas@email.com',
                'phone' => '+51 999 123 456',
                'document_type' => 'DNI',
                'document_number' => '12345678',
                'address' => 'Av. Arequipa 1234',
                'district' => 'Miraflores',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'client_type' => 'comprador',
                'source' => 'formulario_web',
                'status' => 'en_seguimiento',
                'score' => 85,
                'notes' => 'Cliente interesado en departamentos de 2-3 dormitorios en Miraflores',
            ],
            [
                'first_name' => 'María Elena',
                'last_name' => 'Torres Ríos',
                'email' => 'maria.torres@email.com',
                'phone' => '+51 999 234 567',
                'document_type' => 'DNI',
                'document_number' => '23456789',
                'address' => 'Jr. de la Unión 567',
                'district' => 'San Isidro',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'client_type' => 'inversor',
                'source' => 'referidos',
                'status' => 'contacto_inicial',
                'score' => 70,
                'notes' => 'Inversora buscando propiedades para alquiler',
            ],
            [
                'first_name' => 'Roberto',
                'last_name' => 'Silva Castro',
                'email' => 'roberto.silva@empresa.com',
                'phone' => '+51 999 345 678',
                'document_type' => 'RUC',
                'document_number' => '20123456789',
                'address' => 'Av. Javier Prado 2345',
                'district' => 'San Borja',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'client_type' => 'empresa',
                'source' => 'ferias',
                'status' => 'nuevo',
                'score' => 60,
                'notes' => 'Empresa buscando oficinas corporativas',
            ],
            [
                'first_name' => 'Carmen',
                'last_name' => 'Flores Díaz',
                'email' => 'carmen.flores@email.com',
                'phone' => '+51 999 456 789',
                'document_type' => 'DNI',
                'document_number' => '34567890',
                'address' => 'Av. Benavides 3456',
                'district' => 'Surco',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'client_type' => 'comprador',
                'source' => 'redes_sociales',
                'status' => 'en_seguimiento',
                'score' => 90,
                'notes' => 'Cliente VIP, busca casa familiar en Surco',
            ],
            [
                'first_name' => 'Fernando',
                'last_name' => 'Mendoza Ruiz',
                'email' => 'fernando.mendoza@constructor.com',
                'phone' => '+51 999 567 890',
                'document_type' => 'RUC',
                'document_number' => '20134567890',
                'address' => 'Av. La Marina 4567',
                'district' => 'San Miguel',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'client_type' => 'constructor',
                'source' => 'publicidad',
                'status' => 'nuevo',
                'score' => 75,
                'notes' => 'Constructor interesado en lotes para desarrollo',
            ],
            [
                'first_name' => 'Patricia',
                'last_name' => 'Ríos Morales',
                'email' => 'patricia.rios@email.com',
                'phone' => '+51 999 678 901',
                'document_type' => 'DNI',
                'document_number' => '45678901',
                'address' => 'Av. Primavera 5678',
                'district' => 'Chorrillos',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'client_type' => 'inversor',
                'source' => 'formulario_web',
                'status' => 'contacto_inicial',
                'score' => 65,
                'notes' => 'Inversora en propiedades de playa',
            ],
            [
                'first_name' => 'Alberto',
                'last_name' => 'García Paredes',
                'email' => 'alberto.garcia@email.com',
                'phone' => '+51 999 789 012',
                'document_type' => 'DNI',
                'document_number' => '56789012',
                'address' => 'Av. Angamos 6789',
                'district' => 'Miraflores',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'client_type' => 'comprador',
                'source' => 'referidos',
                'status' => 'cierre',
                'score' => 95,
                'notes' => 'Cliente que ya compró, mantener relación',
            ],
            [
                'first_name' => 'Lucía',
                'last_name' => 'Herrera Vega',
                'email' => 'lucia.herrera@email.com',
                'phone' => '+51 999 890 123',
                'document_type' => 'DNI',
                'document_number' => '67890123',
                'address' => 'Av. Arequipa 7890',
                'district' => 'Barranco',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'client_type' => 'comprador',
                'source' => 'redes_sociales',
                'status' => 'en_seguimiento',
                'score' => 80,
                'notes' => 'Interesada en propiedades con vista al mar',
            ],
        ];

        foreach ($clients as $index => $clientData) {
            $advisor = $advisors->random();

            Client::create([
                ...$clientData,
                'assigned_advisor_id' => $advisor->id,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }

        // Crear clientes adicionales usando factory (comentado porque no existe ClientFactory)
        // Client::factory(20)->create([
        //     'created_by' => $admin->id,
        //     'updated_by' => $admin->id,
        // ]);

        $this->command->info('Clientes creados exitosamente');
    }
}
