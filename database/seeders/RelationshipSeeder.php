<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectPrice;
use App\Models\Unit;
use App\Models\UnitPrice;
use App\Models\User;
use Illuminate\Database\Seeder;

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

        // Crear tablas de precios
        $this->createProjectPrices($projects, $admin);
        $this->createUnitPrices($units, $admin);

        $this->command->info('Relaciones y precios creados exitosamente');
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

    private function createProjectPrices($projects, $admin): void
    {
        foreach ($projects as $project) {
            // Crear historial de precios para cada proyecto
            $basePrice = $this->getBasePriceByProjectType($project->project_type);
            $currentDate = now()->subMonths(6);

            for ($i = 0; $i < 3; $i++) {
                $priceIncrease = rand(0, 15); // 0-15% de incremento
                $pricePerSqm = $basePrice * (1 + $priceIncrease / 100);
                $totalUnits = $project->total_units;
                $baseAmount = $pricePerSqm * $totalUnits * 100; // Área promedio por unidad
                $discountPercentage = rand(0, 10);
                $finalPrice = $baseAmount * (1 - $discountPercentage / 100);

                $validFrom = $currentDate->copy();
                $validUntil = $i < 2 ? $currentDate->copy()->addMonths(2) : null;
                $isActive = $i === 2; // Solo el último precio está activo

                ProjectPrice::create([
                    'project_id' => $project->id,
                    'price_per_sqm' => $pricePerSqm,
                    'base_price' => $baseAmount,
                    'discount_percentage' => $discountPercentage,
                    'final_price' => $finalPrice,
                    'valid_from' => $validFrom,
                    'valid_until' => $validUntil,
                    'is_active' => $isActive,
                    'notes' => "Precio histórico #" . ($i + 1) . " del proyecto",
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ]);

                $currentDate->addMonths(2);
            }
        }
    }

    private function createUnitPrices($units, $admin): void
    {
        foreach ($units as $unit) {
            // Crear historial de precios para cada unidad
            $basePrice = $unit->base_price;
            $currentDate = now()->subMonths(3);

            for ($i = 0; $i < 2; $i++) {
                $priceIncrease = rand(-5, 10); // -5% a +10% de variación
                $pricePerSqm = $basePrice * (1 + $priceIncrease / 100);
                $totalPrice = $pricePerSqm * $unit->area;
                $discountPercentage = rand(0, 15);
                $discountAmount = $totalPrice * ($discountPercentage / 100);
                $finalPrice = $totalPrice - $discountAmount;

                $validFrom = $currentDate->copy();
                $validUntil = $i < 1 ? $currentDate->copy()->addMonths(1) : null;
                $isActive = $i === 1; // Solo el último precio está activo

                UnitPrice::create([
                    'unit_id' => $unit->id,
                    'price_per_sqm' => $pricePerSqm,
                    'base_price' => $totalPrice,
                    'discount_percentage' => $discountPercentage,
                    'discount_amount' => $discountAmount,
                    'final_price' => $finalPrice,
                    'valid_from' => $validFrom,
                    'valid_until' => $validUntil,
                    'is_active' => $isActive,
                    'notes' => "Precio histórico #" . ($i + 1) . " de la unidad",
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ]);

                $currentDate->addMonths(1);
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
