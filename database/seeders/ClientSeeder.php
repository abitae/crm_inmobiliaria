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
        
        // Generar fechas de nacimiento aleatorias entre 18 y 65 años
        $birthDates = [
            '1985-03-15',
            '1978-07-22',
            '1990-11-08',
            '1982-05-30',
            '1975-12-14',
            '1988-09-03',
            '1980-01-25',
            '1992-06-18'
        ];

        $clients = [
            [
                'name' => 'Juan Carlos Vargas Mendoza',
                'phone' => '+51 999 123 456',
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
                'phone' => '+51 999 234 567',
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
                'phone' => '+51 999 345 678',
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
                'phone' => '+51 999 456 789',
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
                'phone' => '+51 999 567 890',
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
            [
                'name' => 'Patricia Ríos Morales',
                'phone' => '+51 999 678 901',
                'document_type' => 'DNI',
                'document_number' => '45678901',
                'address' => 'Av. Primavera 5678',
                'birth_date' => '1988-09-03',
                'client_type' => 'inversor',
                'source' => 'formulario_web',
                'status' => 'contacto_inicial',
                'score' => 65,
                'notes' => 'Inversora en propiedades de playa',
            ],
            [
                'name' => 'Alberto García Paredes',
                'phone' => '+51 999 789 012',
                'document_type' => 'DNI',
                'document_number' => '56789012',
                'address' => 'Av. Angamos 6789',
                'birth_date' => '1980-01-25',
                'client_type' => 'comprador',
                'source' => 'referidos',
                'status' => 'cierre',
                'score' => 95,
                'notes' => 'Cliente que ya compró, mantener relación',
            ],
            [
                'name' => 'Lucía Herrera Vega',
                'phone' => '+51 999 890 123',
                'document_type' => 'DNI',
                'document_number' => '67890123',
                'address' => 'Av. Arequipa 7890',
                'birth_date' => '1992-06-18',
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
