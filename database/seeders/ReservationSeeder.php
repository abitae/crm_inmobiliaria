<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'abel.arana@hotmail.com')->first();
        $advisors = User::where('email', '!=', 'abel.arana@hotmail.com')->get();

        // Verificar que existan usuarios antes de continuar
        if (!$admin) {
            throw new \Exception('No se encontró el usuario administrador. Asegúrate de ejecutar UserSeeder primero.');
        }

        if ($advisors->isEmpty()) {
            throw new \Exception('No se encontraron asesores en la base de datos. Asegúrate de ejecutar UserSeeder primero.');
        }
        $clients = Client::all();
        $projects = Project::all();
        $units = Unit::where('status', 'disponible')->get();

        // Verificar que existan las entidades necesarias
        if ($projects->isEmpty()) {
            throw new \Exception('No se encontraron proyectos en la base de datos. Asegúrate de ejecutar ProjectSeeder primero.');
        }

        if ($units->isEmpty()) {
            throw new \Exception('No se encontraron unidades disponibles en la base de datos. Asegúrate de ejecutar UnitSeeder primero.');
        }

        $reservationTypes = ['pre_reserva', 'reserva_firmada', 'reserva_confirmada'];
        $statuses = ['activa', 'confirmada', 'cancelada', 'vencida', 'convertida_venta'];
        $paymentMethods = ['efectivo', 'transferencia', 'tarjeta', 'cheque'];
        $paymentStatuses = ['pendiente', 'pagado', 'parcial'];

        // Crear reservas realistas
        $reservations = [
            [
                'client_id' => $clients->where('name', 'Juan Carlos Vargas Mendoza')->first()->id,
                'project_id' => $projects->where('name', 'Lotes Miraflores Park')->first()->id,
                'unit_id' => $units->where('project_id', $projects->where('name', 'Lotes Miraflores Park')->first()->id)->first()->id,
                'advisor_id' => $advisors->random()->id,
                'reservation_number' => 'RES-2024-000001',
                'reservation_type' => 'reserva_firmada',
                'status' => 'activa',
                'reservation_date' => now()->subDays(10),
                'expiration_date' => now()->addDays(20),
                'reservation_amount' => 45000,
                'reservation_percentage' => 10,
                'payment_method' => 'transferencia',
                'payment_status' => 'pagado',
                'payment_reference' => 'TRF-001-2024',
                'notes' => 'Cliente pagó el 100% de la reserva de lote. Excelente perfil crediticio.',
                'terms_conditions' => 'Reserva válida por 30 días. Pago inicial del 10% del valor total.',
                'client_signature' => true,
                'advisor_signature' => true,
            ],
            [
                'client_id' => $clients->where('name', 'Carmen Flores Díaz')->first()->id,
                'project_id' => $projects->where('name', 'Lotes Surco Family')->first()->id,
                'unit_id' => $units->where('project_id', $projects->where('name', 'Lotes Surco Family')->first()->id)->first()->id,
                'advisor_id' => $advisors->random()->id,
                'reservation_number' => 'RES-2024-000002',
                'reservation_type' => 'pre_reserva',
                'status' => 'activa',
                'reservation_date' => now()->subDays(5),
                'expiration_date' => now()->addDays(25),
                'reservation_amount' => 65000,
                'reservation_percentage' => 10,
                'payment_method' => 'efectivo',
                'payment_status' => 'pagado',
                'payment_reference' => 'EFE-002-2024',
                'notes' => 'Cliente VIP, pago en efectivo por lote familiar. Pendiente firma de documentos.',
                'terms_conditions' => 'Reserva válida por 30 días. Pago inicial del 10% del valor total.',
                'client_signature' => false,
                'advisor_signature' => true,
            ],
            [
                'client_id' => $clients->where('name', 'María Elena Torres Ríos')->first()->id,
                'project_id' => $projects->where('name', 'Lotes San Isidro Business')->first()->id,
                'unit_id' => $units->where('project_id', $projects->where('name', 'Lotes San Isidro Business')->first()->id)->first()->id,
                'advisor_id' => $advisors->random()->id,
                'reservation_number' => 'RES-2024-000003',
                'reservation_type' => 'reserva_confirmada',
                'status' => 'confirmada',
                'reservation_date' => now()->subDays(15),
                'expiration_date' => now()->addDays(15),
                'reservation_amount' => 80000,
                'reservation_percentage' => 10,
                'payment_method' => 'tarjeta',
                'payment_status' => 'pagado',
                'payment_reference' => 'TAR-003-2024',
                'notes' => 'Reserva confirmada de lote comercial. Cliente procederá con la compra en los próximos días.',
                'terms_conditions' => 'Reserva válida por 30 días. Pago inicial del 10% del valor total.',
                'client_signature' => true,
                'advisor_signature' => true,
            ],
            [
                'client_id' => $clients->where('name', 'Roberto Silva Castro')->first()->id,
                'project_id' => $projects->where('name', 'Lotes San Borja Center')->first()->id,
                'unit_id' => $units->where('project_id', $projects->where('name', 'Lotes San Borja Center')->first()->id)->first()->id,
                'advisor_id' => $advisors->random()->id,
                'reservation_number' => 'RES-2024-000004',
                'reservation_type' => 'pre_reserva',
                'status' => 'activa',
                'reservation_date' => now()->subDays(3),
                'expiration_date' => now()->addDays(27),
                'reservation_amount' => 120000,
                'reservation_percentage' => 10,
                'payment_method' => 'transferencia',
                'payment_status' => 'parcial',
                'payment_reference' => 'TRF-004-2024',
                'notes' => 'Empresa pagó el 60% de la reserva de lote empresarial. Pendiente el 40% restante.',
                'terms_conditions' => 'Reserva válida por 30 días. Pago inicial del 10% del valor total.',
                'client_signature' => false,
                'advisor_signature' => true,
            ],
            [
                'client_id' => $clients->where('name', 'Fernando Mendoza Ruiz')->first()->id,
                'project_id' => $projects->where('name', 'Lotes Barranco Golf')->first()->id,
                'unit_id' => $units->where('project_id', $projects->where('name', 'Lotes Barranco Golf')->first()->id)->first()->id,
                'advisor_id' => $advisors->random()->id,
                'reservation_number' => 'RES-2024-000005',
                'reservation_type' => 'reserva_firmada',
                'status' => 'activa',
                'reservation_date' => now()->subDays(8),
                'expiration_date' => now()->addDays(22),
                'reservation_amount' => 30000,
                'reservation_percentage' => 10,
                'payment_method' => 'cheque',
                'payment_status' => 'pagado',
                'payment_reference' => 'CHQ-005-2024',
                'notes' => 'Constructor interesado en desarrollo de lotes. Reserva firmada y pagada.',
                'terms_conditions' => 'Reserva válida por 30 días. Pago inicial del 10% del valor total.',
                'client_signature' => true,
                'advisor_signature' => true,
            ],
        ];

        foreach ($reservations as $reservationData) {
            Reservation::create([
                ...$reservationData,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }

        // Crear reservas adicionales aleatorias
        $this->createRandomReservations($clients, $projects, $units, $advisors, $admin);

        $this->command->info('Reservas creadas exitosamente');
    }

    private function createRandomReservations($clients, $projects, $units, $advisors, $admin): void
    {
        $reservationTypes = ['pre_reserva', 'reserva_firmada', 'reserva_confirmada'];
        $statuses = ['activa', 'confirmada', 'cancelada', 'vencida', 'convertida_venta'];
        $paymentMethods = ['efectivo', 'transferencia', 'tarjeta', 'cheque'];
        $paymentStatuses = ['pendiente', 'pagado', 'parcial'];

        // Obtener el último número de reserva para evitar duplicados
        $lastReservation = Reservation::orderBy('id', 'desc')->first();
        $lastNumber = $lastReservation ? (int) substr($lastReservation->reservation_number, -6) : 5;

        for ($i = 0; $i < 5; $i++) {
            $client = $clients->random();
            $project = $projects->random();
            $unit = $units->where('project_id', $project->id)->first();
            $advisor = $advisors->random();
            $reservationType = $reservationTypes[array_rand($reservationTypes)];
            $status = $statuses[array_rand($statuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

            if (!$unit) continue;

            $reservationAmount = $unit->final_price * 0.1; // 10% del precio final
            $reservationDate = now()->subDays(rand(1, 30));
            $expirationDate = $reservationDate->copy()->addDays(30);

            // Generar número de reserva único
            $lastNumber++;
            $reservationNumber = 'RES-2024-' . str_pad($lastNumber, 6, '0', STR_PAD_LEFT);

            $reservationData = [
                'client_id' => $client->id,
                'project_id' => $project->id,
                'unit_id' => $unit->id,
                'advisor_id' => $advisor->id,
                'reservation_number' => $reservationNumber,
                'reservation_type' => $reservationType,
                'status' => $status,
                'reservation_date' => $reservationDate,
                'expiration_date' => $expirationDate,
                'reservation_amount' => $reservationAmount,
                'reservation_percentage' => 10,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'payment_reference' => strtoupper(substr($paymentMethod, 0, 3)) . '-' . rand(100, 999) . '-2024',
                'notes' => 'Reserva generada automáticamente para pruebas.',
                'terms_conditions' => 'Reserva válida por 30 días. Pago inicial del 10% del valor total.',
                'client_signature' => rand(0, 1),
                'advisor_signature' => rand(0, 1),
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ];

            Reservation::create($reservationData);
        }
    }
}
