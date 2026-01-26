<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
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
        $projects = Project::all();

        foreach ($projects as $project) {
            $this->createUnitsForProject($project, $admin);
        }

        $this->command->info('Unidades creadas exitosamente');
    }

    private function createUnitsForProject(Project $project, User $admin): void
    {
        $unitTypes = ['lote', 'casa', 'departamento', 'oficina', 'local'];
        $statuses = ['disponible', 'reservado', 'vendido', 'transferido', 'cuotas'];

        // Determinar el tipo de unidad basado en el tipo de proyecto
        $unitType = match ($project->project_type) {
            'lotes' => 'lote',
            'casas' => 'casa',
            'departamentos' => 'departamento',
            'oficinas' => 'oficina',
            'mixto' => $unitTypes[array_rand($unitTypes)],
            default => 'departamento'
        };

        $totalUnits = $project->total_units;
        $soldUnits = $project->sold_units;
        $reservedUnits = $project->reserved_units;
        $blockedUnits = $project->blocked_units;
        $availableUnits = $project->available_units;

        // Crear unidades vendidas
        for ($i = 1; $i <= $soldUnits; $i++) {
            $this->createUnit($project, $unitType, 'vendido', $i, $admin);
        }

        // Crear unidades reservadas
        for ($i = $soldUnits + 1; $i <= $soldUnits + $reservedUnits; $i++) {
            $this->createUnit($project, $unitType, 'reservado', $i, $admin);
        }

        // Crear unidades transferidas (antes bloqueadas)
        for ($i = $soldUnits + $reservedUnits + 1; $i <= $soldUnits + $reservedUnits + $blockedUnits; $i++) {
            $this->createUnit($project, $unitType, 'transferido', $i, $admin);
        }

        // Crear unidades disponibles
        for ($i = $soldUnits + $reservedUnits + $blockedUnits + 1; $i <= $totalUnits; $i++) {
            $this->createUnit($project, $unitType, 'disponible', $i, $admin);
        }
    }

    private function createUnit(Project $project, string $unitType, string $status, int $unitNumber, User $admin): void
    {
        $basePrice = $this->getBasePriceByType($unitType);
        $area = $this->getAreaByType($unitType);
        $totalPrice = $basePrice * $area;
        $finalPrice = $totalPrice;
        $discountPercentage = 0;
        $discountAmount = 0;

        // Aplicar descuento aleatorio (0-15%)
        if (rand(1, 100) <= 30) { // 30% de probabilidad de descuento
            $discountPercentage = rand(5, 15);
            $discountAmount = $totalPrice * ($discountPercentage / 100);
            $finalPrice = $totalPrice - $discountAmount;
        }

        $unitData = [
            'project_id' => $project->id,
            'unit_manzana' => 'Manzana ' . rand(1, 10),
            'unit_number' => $unitNumber,
            'unit_type' => $unitType,
            'status' => $status,
            'area' => $area,
            'base_price' => $basePrice,
            'total_price' => $totalPrice,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ];

        // Agregar campos específicos según el tipo de unidad
        if ($unitType === 'departamento' || $unitType === 'casa') {
            $unitData = array_merge($unitData, [
                'floor' => $unitType === 'departamento' ? rand(1, 20) : 1,
                'tower' => $unitType === 'departamento' ? 'A' : null,
                'bedrooms' => rand(1, 4),
                'bathrooms' => rand(1, 3),
                'parking_spaces' => rand(0, 2),
                'storage_rooms' => rand(0, 1),
                'balcony_area' => rand(5, 20),
                'terrace_area' => $unitType === 'casa' ? rand(10, 50) : 0,
                'garden_area' => $unitType === 'casa' ? rand(20, 100) : 0,
            ]);
        } elseif ($unitType === 'oficina') {
            $unitData = array_merge($unitData, [
                'floor' => rand(1, 15),
                'tower' => 'B',
                'parking_spaces' => rand(1, 3),
            ]);
        } elseif ($unitType === 'lote') {
            $unitData = array_merge($unitData, [
                'garden_area' => rand(100, 500),
            ]);
        }

        // Agregar comisión
        $unitData['commission_percentage'] = rand(3, 8);
        $unitData['commission_amount'] = ($finalPrice * $unitData['commission_percentage']) / 100;

        Unit::create($unitData);
    }

    private function getBasePriceByType(string $unitType): float
    {
        return match ($unitType) {
            'lote' => rand(800, 1500), // Precio por m² para lotes
            'casa' => rand(1200, 2000), // Precio por m² para casas
            'departamento' => rand(1500, 3000), // Precio por m² para departamentos
            'oficina' => rand(2000, 4000), // Precio por m² para oficinas
            'local' => rand(2500, 5000), // Precio por m² para locales
            default => 2000
        };
    }

    private function getAreaByType(string $unitType): float
    {
        return match ($unitType) {
            'lote' => rand(150, 500), // Área en m² para lotes
            'casa' => rand(120, 300), // Área en m² para casas
            'departamento' => rand(60, 150), // Área en m² para departamentos
            'oficina' => rand(80, 200), // Área en m² para oficinas
            'local' => rand(50, 150), // Área en m² para locales
            default => 100
        };
    }
}
