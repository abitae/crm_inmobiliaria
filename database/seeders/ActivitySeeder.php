<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Client;
use App\Models\Opportunity;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'abel.arana@hotmail.com')->first();
        $advisors = User::where('email', '!=', 'abel.arana@hotmail.com')->take(5)->get();

        // Verificar que existan usuarios antes de continuar
        if (!$admin) {
            throw new \Exception('No se encontró el usuario administrador. Asegúrate de ejecutar UserSeeder primero.');
        }

        if ($advisors->isEmpty()) {
            throw new \Exception('No se encontraron asesores en la base de datos. Asegúrate de ejecutar UserSeeder primero.');
        }
        $clients = Client::all();
        $projects = Project::all();
        $units = Unit::all();
        $opportunities = Opportunity::all();

        $activityTypes = ['llamada', 'reunion', 'visita', 'seguimiento', 'tarea'];
        $statuses = ['programada', 'en_progreso', 'completada', 'cancelada'];
        $priorities = ['baja', 'media', 'alta', 'urgente'];

        // Crear actividades realistas
        $activities = [
            [
                'title' => 'Visita a departamento A-101',
                'description' => 'Mostrar departamento A-101 del proyecto Residencial Miraflores Park al cliente Juan Vargas.',
                'activity_type' => 'visita',
                'status' => 'programada',
                'priority' => 'alta',
                'start_date' => now()->addDays(2)->setTime(15, 0),
                'end_date' => now()->addDays(2)->setTime(16, 30),
                'duration' => 90,
                'location' => 'Residencial Miraflores Park - Av. Arequipa 1234',
                'client_id' => $clients->where('name', 'Juan Carlos Vargas Mendoza')->first()->id,
                'project_id' => $projects->where('name', 'Residencial Miraflores Park')->first()->id,
                'unit_id' => $units->where('project_id', $projects->where('name', 'Residencial Miraflores Park')->first()->id)->first()->id,
                'opportunity_id' => $opportunities->where('client_id', $clients->where('name', 'Juan Carlos Vargas Mendoza')->first()->id)->first()->id,
                'advisor_id' => $advisors->random()->id,
                'assigned_to' => $advisors->random()->id,
                'reminder_before' => 30,
                'reminder_sent' => false,
                'notes' => 'Cliente muy interesado. Llevar catálogo de acabados y opciones de financiamiento.',
            ],
            [
                'title' => 'Llamada de seguimiento',
                'description' => 'Llamada de seguimiento con María Torres sobre su interés en oficinas corporativas.',
                'activity_type' => 'llamada',
                'status' => 'completada',
                'priority' => 'media',
                'start_date' => now()->subDays(1)->setTime(10, 0),
                'end_date' => now()->subDays(1)->setTime(10, 30),
                'duration' => 30,
                'location' => 'Oficina',
                'client_id' => $clients->where('name', 'María Elena Torres Ríos')->first()->id,
                'project_id' => $projects->where('name', 'Torres San Isidro Business')->first()->id,
                'unit_id' => null,
                'opportunity_id' => $opportunities->where('client_id', $clients->where('name', 'María Elena Torres Ríos')->first()->id)->first()->id,
                'advisor_id' => $advisors->random()->id,
                'assigned_to' => $advisors->random()->id,
                'reminder_before' => 15,
                'reminder_sent' => true,
                'notes' => 'Cliente confirmó interés. Enviar propuesta comercial por email.',
                'result' => 'Cliente confirmó interés. Pendiente envío de propuesta.',
            ],
            [
                'title' => 'Reunión de presentación',
                'description' => 'Presentación del proyecto Casas Surco Family a Carmen Flores.',
                'activity_type' => 'reunion',
                'status' => 'programada',
                'priority' => 'alta',
                'start_date' => now()->addDays(5)->setTime(14, 0),
                'end_date' => now()->addDays(5)->setTime(15, 30),
                'duration' => 90,
                'location' => 'Showroom Surco',
                'client_id' => $clients->where('name', 'Carmen Flores Díaz')->first()->id,
                'project_id' => $projects->where('name', 'Casas Surco Family')->first()->id,
                'unit_id' => null,
                'opportunity_id' => $opportunities->where('client_id', $clients->where('name', 'Carmen Flores Díaz')->first()->id)->first()->id,
                'advisor_id' => $advisors->random()->id,
                'assigned_to' => $advisors->random()->id,
                'reminder_before' => 60,
                'reminder_sent' => false,
                'notes' => 'Cliente VIP. Preparar presentación completa con maquetas y videos.',
            ],
            [
                'title' => 'Seguimiento de oportunidad',
                'description' => 'Seguimiento semanal de la oportunidad de Roberto Silva.',
                'activity_type' => 'seguimiento',
                'status' => 'en_progreso',
                'priority' => 'media',
                'start_date' => now()->setTime(11, 0),
                'end_date' => now()->setTime(11, 30),
                'duration' => 30,
                'location' => 'Oficina',
                'client_id' => $clients->where('name', 'Roberto Silva Castro')->first()->id,
                'project_id' => $projects->where('name', 'Oficinas San Borja Center')->first()->id,
                'unit_id' => null,
                'opportunity_id' => $opportunities->where('client_id', $clients->where('name', 'Roberto Silva Castro')->first()->id)->first()->id,
                'advisor_id' => $advisors->random()->id,
                'assigned_to' => $advisors->random()->id,
                'reminder_before' => 10,
                'reminder_sent' => true,
                'notes' => 'Evaluar necesidades específicas de la empresa y presentar opciones.',
            ],
            [
                'title' => 'Tarea administrativa',
                'description' => 'Actualizar base de datos de clientes y preparar reporte semanal.',
                'activity_type' => 'tarea',
                'status' => 'programada',
                'priority' => 'baja',
                'start_date' => now()->addDays(1)->setTime(9, 0),
                'end_date' => now()->addDays(1)->setTime(10, 0),
                'duration' => 60,
                'location' => 'Oficina',
                'client_id' => null,
                'project_id' => null,
                'unit_id' => null,
                'opportunity_id' => null,
                'advisor_id' => null,
                'assigned_to' => $advisors->random()->id,
                'reminder_before' => 0,
                'reminder_sent' => false,
                'notes' => 'Tarea rutinaria de mantenimiento de datos.',
            ],
        ];

        foreach ($activities as $activityData) {
            Activity::create([
                ...$activityData,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }

        // Crear actividades adicionales aleatorias
        $this->createRandomActivities($clients, $projects, $units, $opportunities, $advisors, $admin);

        $this->command->info('Actividades creadas exitosamente');
    }

    private function createRandomActivities($clients, $projects, $units, $opportunities, $advisors, $admin): void
    {
        $activityTypes = ['llamada', 'reunion', 'visita', 'seguimiento', 'tarea'];
        $statuses = ['programada', 'en_progreso', 'completada', 'cancelada'];
        $priorities = ['baja', 'media', 'alta', 'urgente'];

        for ($i = 0; $i < 40; $i++) {
            $activityType = $activityTypes[array_rand($activityTypes)];
            $status = $statuses[array_rand($statuses)];
            $priority = $priorities[array_rand($priorities)];
            $advisor = $advisors->random();
            $assignedTo = $advisors->random();

            $startDate = now()->addDays(rand(-30, 30))->setTime(rand(8, 18), rand(0, 59));
            $duration = rand(15, 180);
            $endDate = $startDate->copy()->addMinutes($duration);

            $activityData = [
                'title' => $this->generateActivityTitle($activityType),
                'description' => $this->generateActivityDescription($activityType),
                'activity_type' => $activityType,
                'status' => $status,
                'priority' => $priority,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration' => $duration,
                'location' => $this->generateLocation($activityType),
                'client_id' => rand(0, 1) ? $clients->random()->id : null,
                'project_id' => rand(0, 1) ? $projects->random()->id : null,
                'unit_id' => rand(0, 1) ? $units->random()->id : null,
                'opportunity_id' => rand(0, 1) ? $opportunities->random()->id : null,
                'advisor_id' => rand(0, 1) ? $advisor->id : null,
                'assigned_to' => $assignedTo->id,
                'reminder_before' => rand(0, 1) ? rand(15, 120) : null,
                'reminder_sent' => rand(0, 1),
                'notes' => 'Actividad generada automáticamente para pruebas.',
                'result' => $status === 'completada' ? 'Actividad completada exitosamente.' : null,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ];

            Activity::create($activityData);
        }
    }

    private function generateActivityTitle(string $type): string
    {
        $titles = [
            'llamada' => ['Llamada de seguimiento', 'Llamada de prospección', 'Llamada de confirmación'],
            'reunion' => ['Reunión de presentación', 'Reunión de negociación', 'Reunión de seguimiento'],
            'visita' => ['Visita a propiedad', 'Visita al showroom', 'Visita técnica'],
            'seguimiento' => ['Seguimiento de oportunidad', 'Seguimiento de cliente', 'Seguimiento de proyecto'],
            'tarea' => ['Tarea administrativa', 'Tarea de seguimiento', 'Tarea de documentación'],
        ];

        return $titles[$type][array_rand($titles[$type])];
    }

    private function generateActivityDescription(string $type): string
    {
        $descriptions = [
            'llamada' => 'Llamada telefónica para seguimiento y atención al cliente.',
            'reunion' => 'Reunión presencial para presentación de propuestas y negociación.',
            'visita' => 'Visita a la propiedad para mostrar características y ventajas.',
            'seguimiento' => 'Seguimiento del estado de la oportunidad y próximos pasos.',
            'tarea' => 'Tarea administrativa para mantenimiento de datos y reportes.',
        ];

        return $descriptions[$type];
    }

    private function generateLocation(string $type): string
    {
        $locations = [
            'llamada' => 'Oficina',
            'reunion' => ['Showroom', 'Oficina', 'Sala de reuniones'],
            'visita' => ['Propiedad', 'Showroom', 'Oficina'],
            'seguimiento' => 'Oficina',
            'tarea' => 'Oficina',
        ];

        if (is_array($locations[$type])) {
            return $locations[$type][array_rand($locations[$type])];
        }

        return $locations[$type];
    }
}
