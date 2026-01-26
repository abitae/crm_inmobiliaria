<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
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
        $units = Unit::all();
        $opportunities = Opportunity::all();

        // Verificar que existan las entidades necesarias
        if ($projects->isEmpty()) {
            throw new \Exception('No se encontraron proyectos en la base de datos. Asegúrate de ejecutar ProjectSeeder primero.');
        }

        if ($units->isEmpty()) {
            throw new \Exception('No se encontraron unidades en la base de datos. Asegúrate de ejecutar UnitSeeder primero.');
        }

        if ($opportunities->isEmpty()) {
            throw new \Exception('No se encontraron oportunidades en la base de datos. Asegúrate de ejecutar OpportunitySeeder primero.');
        }

        $taskTypes = ['seguimiento', 'visita', 'llamada', 'documento', 'otros'];
        $statuses = ['pendiente', 'en_progreso', 'completada', 'cancelada'];
        $priorities = ['baja', 'media', 'alta', 'urgente'];

        // Crear tareas realistas
        $project1 = $projects->first();
        $project2 = $projects->skip(1)->first();
        $project3 = $projects->skip(2)->first();
        
        $unit1 = $units->where('project_id', $project1->id)->first();
        
        $client1 = $clients->where('name', 'Carmen Flores Díaz')->first();
        $client2 = $clients->where('name', 'Juan Carlos Vargas Mendoza')->first();
        $client3 = $clients->where('name', 'Roberto Silva Castro')->first();
        
        $opportunity1 = $opportunities->where('client_id', $client1 ? $client1->id : null)->first();
        $opportunity2 = $opportunities->where('client_id', $client2 ? $client2->id : null)->first();
        $opportunity3 = $opportunities->where('client_id', $client3 ? $client3->id : null)->first();

        $tasks = [
            [
                'title' => 'Llamada de seguimiento a cliente VIP',
                'description' => 'Realizar llamada de seguimiento a Carmen Flores sobre su interés en lotes familiares.',
                'task_type' => 'llamada',
                'status' => 'pendiente',
                'priority' => 'alta',
                'due_date' => now()->addDays(2),
                'estimated_hours' => 0.5,
                'client_id' => $client1 ? $client1->id : null,
                'project_id' => $project1->id,
                'unit_id' => null,
                'opportunity_id' => $opportunity1 ? $opportunity1->id : null,
                'assigned_to' => $advisors->random()->id,
                'notes' => 'Cliente VIP muy interesada. Preparar propuesta especial.',
            ],
            [
                'title' => 'Visita técnica a lote A-101',
                'description' => 'Realizar visita técnica al lote A-101 para verificar condiciones.',
                'task_type' => 'visita',
                'status' => 'pendiente',
                'priority' => 'media',
                'due_date' => now()->addDays(3),
                'estimated_hours' => 1.5,
                'client_id' => $client2 ? $client2->id : null,
                'project_id' => $project1->id,
                'unit_id' => $unit1 ? $unit1->id : null,
                'opportunity_id' => $opportunity2 ? $opportunity2->id : null,
                'assigned_to' => $advisors->random()->id,
                'notes' => 'Verificar condiciones del lote antes de la presentación final.',
            ],
            [
                'title' => 'Preparar propuesta comercial',
                'description' => 'Elaborar propuesta comercial para Roberto Silva sobre lotes empresariales.',
                'task_type' => 'documento',
                'status' => 'en_progreso',
                'priority' => 'alta',
                'due_date' => now()->addDays(1),
                'estimated_hours' => 2.0,
                'client_id' => $client3 ? $client3->id : null,
                'project_id' => $project3->id,
                'unit_id' => null,
                'opportunity_id' => $opportunity3 ? $opportunity3->id : null,
                'assigned_to' => $advisors->random()->id,
                'notes' => 'Incluir opciones de financiamiento y descuentos corporativos.',
            ],
            [
                'title' => 'Actualizar base de datos de clientes',
                'description' => 'Actualizar información de contactos y seguimientos en la base de datos.',
                'task_type' => 'otros',
                'status' => 'pendiente',
                'priority' => 'baja',
                'due_date' => now()->addDays(5),
                'estimated_hours' => 1.0,
                'client_id' => null,
                'project_id' => null,
                'unit_id' => null,
                'opportunity_id' => null,
                'assigned_to' => $advisors->random()->id,
                'notes' => 'Tarea rutinaria de mantenimiento de datos.',
            ],
            [
                'title' => 'Reunión de seguimiento semanal',
                'description' => 'Reunión semanal con el equipo para revisar avances y objetivos.',
                'task_type' => 'seguimiento',
                'status' => 'pendiente',
                'priority' => 'media',
                'due_date' => now()->addDays(1),
                'estimated_hours' => 1.0,
                'client_id' => null,
                'project_id' => null,
                'unit_id' => null,
                'opportunity_id' => null,
                'assigned_to' => $advisors->random()->id,
                'notes' => 'Revisar métricas del equipo y planificar actividades.',
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create([
                ...$taskData,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }

        // Crear tareas adicionales para cada asesor según jerarquía
        $this->createTasksForHierarchy($advisors, $clients, $projects, $units, $opportunities, $admin);

        $this->command->info('Tareas creadas exitosamente');
    }

    private function createTasksForHierarchy($advisors, $clients, $projects, $units, $opportunities, $admin): void
    {
        $taskTypes = ['seguimiento', 'visita', 'llamada', 'documento', 'otros'];
        $statuses = ['pendiente', 'en_progreso', 'completada', 'cancelada'];
        $priorities = ['baja', 'media', 'alta', 'urgente'];

        foreach ($advisors as $advisor) {
            // Determinar cuántas tareas crear según el rol
            $taskCount = match($advisor->getRoleName()) {
                'admin' => 50,
                'lider' => 30,
                'vendedor' => 15,
                'datero' => 8,
                default => 5
            };

            for ($i = 0; $i < $taskCount; $i++) {
                $taskType = $taskTypes[array_rand($taskTypes)];
                $status = $statuses[array_rand($statuses)];
                $priority = $priorities[array_rand($priorities)];

                $dueDate = now()->addDays(rand(1, 14));
                $estimatedHours = rand(1, 8) / 2; // Convertir a horas (0.5, 1.0, 1.5, etc.)

                $taskData = [
                    'title' => $this->generateTaskTitle($taskType, $advisor->getRoleName()),
                    'description' => $this->generateTaskDescription($taskType, $advisor->getRoleName()),
                    'task_type' => $taskType,
                    'status' => $status,
                    'priority' => $priority,
                    'due_date' => $dueDate,
                    'estimated_hours' => $estimatedHours,
                    'client_id' => rand(0, 1) ? $clients->random()->id : null,
                    'project_id' => rand(0, 1) ? $projects->random()->id : null,
                    'unit_id' => rand(0, 1) ? $units->random()->id : null,
                    'opportunity_id' => rand(0, 1) ? $opportunities->random()->id : null,
                    'assigned_to' => $advisor->id,
                    'notes' => "Tarea generada para {$advisor->name} - {$advisor->getRoleName()}",
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ];

                Task::create($taskData);
            }
        }
    }

    private function generateTaskTitle(string $type, string $role): string
    {
        $titles = [
            'llamada' => [
                'admin' => 'Llamada de seguimiento estratégico',
                'lider' => 'Llamada de coordinación de equipo',
                'vendedor' => 'Llamada de seguimiento de ventas',
                'datero' => 'Llamada de captación de datos',
            ],
            'reunion' => [
                'admin' => 'Reunión de planificación estratégica',
                'lider' => 'Reunión de equipo',
                'vendedor' => 'Reunión de presentación',
                'datero' => 'Reunión de capacitación',
            ],
            'visita' => [
                'admin' => 'Visita de supervisión',
                'lider' => 'Visita de acompañamiento',
                'vendedor' => 'Visita de ventas',
                'datero' => 'Visita de recolección de datos',
            ],
            'seguimiento' => [
                'admin' => 'Seguimiento de gestión',
                'lider' => 'Seguimiento de equipo',
                'vendedor' => 'Seguimiento de oportunidades',
                'datero' => 'Seguimiento de captación',
            ],
            'documento' => [
                'admin' => 'Documentación estratégica',
                'lider' => 'Documentación de equipo',
                'vendedor' => 'Documentación de ventas',
                'datero' => 'Documentación de datos',
            ],
            'otros' => [
                'admin' => 'Tarea administrativa ejecutiva',
                'lider' => 'Tarea administrativa de equipo',
                'vendedor' => 'Tarea administrativa de ventas',
                'datero' => 'Tarea administrativa de datos',
            ],
        ];

        return $titles[$type][$role] ?? $titles[$type]['vendedor'];
    }

    private function generateTaskDescription(string $type, string $role): string
    {
        $descriptions = [
            'llamada' => 'Llamada telefónica para seguimiento y coordinación.',
            'seguimiento' => 'Seguimiento del progreso y próximos pasos.',
            'visita' => 'Visita de campo para verificación y seguimiento.',
            'seguimiento' => 'Seguimiento del progreso y próximos pasos.',
            'documento' => 'Elaboración y actualización de documentación.',
            'otros' => 'Tarea administrativa de mantenimiento y gestión.',
        ];

        return $descriptions[$type];
    }
}
