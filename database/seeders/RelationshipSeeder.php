<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Models\Commission;
use App\Models\Activity;
use App\Models\Task;
use App\Models\Interaction;
use App\Models\Document;
use App\Models\Reservation;
use App\Models\Opportunity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RelationshipSeeder extends Seeder
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
        $clients = Client::all();
        $projects = Project::all();
        $units = Unit::all();

        // Crear relaciones many-to-many
        $this->createClientProjectInterests($clients, $projects, $admin);
        $this->createClientUnitInterests($clients, $units, $admin);
        $this->createAdvisorProjectAssignments($projects, $admin);



        $this->command->info('Relaciones creadas exitosamente');
    }

    private function createClientProjectInterests($clients, $projects, $admin): void
    {
        $interestLevels = ['bajo', 'medio', 'alto', 'muy_alto'];
        $notes = [
            'Cliente muy interesado en el proyecto',
            'Interés moderado, requiere más información',
            'Alto interés, potencial comprador',
            'Interés inicial, en proceso de evaluación'
        ];

        foreach ($clients as $client) {
            // Cada cliente puede estar interesado en 1-3 proyectos
            $interestedProjects = $projects->random(rand(1, 3));

            foreach ($interestedProjects as $project) {
                $interestLevel = $interestLevels[array_rand($interestLevels)];
                $note = $notes[array_rand($notes)];

                $client->projects()->attach($project->id, [
                    'interest_level' => $interestLevel,
                    'notes' => $note,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createClientUnitInterests($clients, $units, $admin): void
    {
        $interestLevels = ['bajo', 'medio', 'alto', 'muy_alto'];
        $notes = [
            'Unidad de interés para el cliente',
            'Cliente evaluando esta unidad',
            'Alto interés en esta unidad específica',
            'Unidad recomendada por el asesor'
        ];

        foreach ($clients as $client) {
            // Cada cliente puede estar interesado en 1-5 unidades
            $interestedUnits = $units->random(rand(1, 5));

            foreach ($interestedUnits as $unit) {
                $interestLevel = $interestLevels[array_rand($interestLevels)];
                $note = $notes[array_rand($notes)];

                $client->units()->attach($unit->id, [
                    'interest_level' => $interestLevel,
                    'notes' => $note,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createAdvisorProjectAssignments($projects, $admin): void
    {
        $advisors = User::where('email', '!=', 'abel.arana@hotmail.com')->take(5)->get();

        // Verificar que existan asesores antes de continuar
        if ($advisors->isEmpty()) {
            throw new \Exception('No se encontraron asesores en la base de datos. Asegúrate de ejecutar UserSeeder primero.');
        }

        foreach ($projects as $project) {
            // Cada proyecto puede tener 1-3 asesores asignados
            $assignedAdvisors = $advisors->random(rand(1, 3));
            $primaryAdvisor = $assignedAdvisors->first();

            foreach ($assignedAdvisors as $advisor) {
                $isPrimary = $advisor->id === $primaryAdvisor->id;
                $notes = $isPrimary ? 'Asesor principal del proyecto' : 'Asesor de apoyo';

                $project->advisors()->attach($advisor->id, [
                    'assigned_at' => now()->subDays(rand(1, 90)),
                    'is_primary' => $isPrimary,
                    'notes' => $notes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }



    private function getBasePriceByProjectType(string $projectType): float
    {
        return match ($projectType) {
            'lotes' => rand(800, 1500),
            'casas' => rand(1200, 2000),
            'departamentos' => rand(1500, 3000),
            'oficinas' => rand(2000, 4000),
            'mixto' => rand(1500, 3500),
            default => 2000
        };
    }
}
