<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Opportunity;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class OpportunitySeeder extends Seeder
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
        $units = Unit::where('status', 'disponible')->get();

        $stages = ['calificado', 'visita', 'cierre'];
        $statuses = ['registrado', 'reservado', 'cuotas', 'pagado', 'transferido', 'cancelado'];
        $sources = ['redes_sociales', 'ferias', 'referidos', 'formulario_web', 'publicidad'];
        $campaigns = ['Campaña Q1 2024', 'Campaña Q2 2024', 'Campaña Q3 2024', 'Campaña Q4 2024'];

        // Crear oportunidades realistas
        $opportunities = [
            [
                'client_id' => $clients->where('name', 'Juan Carlos Vargas Mendoza')->first()->id,
                'project_id' => $projects->where('name', 'Lotes Miraflores Park')->first()->id,
                'unit_id' => $units->where('project_id', $projects->where('name', 'Lotes Miraflores Park')->first()->id)->first()->id,
                'advisor_id' => $advisors->random()->id,
                'stage' => 'visita',
                'status' => 'registrado',
                'probability' => 85,
                'expected_value' => 450000,
                'expected_close_date' => now()->addDays(30),
                'source' => 'formulario_web',
                'campaign' => 'Campaña Q1 2024',
                'notes' => 'Cliente muy interesado en lotes residenciales, ya visitó la propiedad 3 veces. Negociando precio final.',
            ],
            [
                'client_id' => $clients->where('name', 'María Elena Torres Ríos')->first()->id,
                'project_id' => $projects->where('name', 'Lotes San Isidro Business')->first()->id,
                'unit_id' => null,
                'advisor_id' => $advisors->random()->id,
                'stage' => 'calificado',
                'status' => 'registrado',
                'probability' => 70,
                'expected_value' => 800000,
                'expected_close_date' => now()->addDays(45),
                'source' => 'referidos',
                'campaign' => 'Campaña Q1 2024',
                'notes' => 'Inversora buscando lotes comerciales para desarrollo corporativo. Evaluando diferentes opciones.',
            ],
            [
                'client_id' => $clients->where('name', 'Carmen Flores Díaz')->first()->id,
                'project_id' => $projects->where('name', 'Lotes Surco Family')->first()->id,
                'unit_id' => $units->where('project_id', $projects->where('name', 'Lotes Surco Family')->first()->id)->first()->id,
                'advisor_id' => $advisors->random()->id,
                'stage' => 'visita',
                'status' => 'reservado',
                'probability' => 60,
                'expected_value' => 650000,
                'expected_close_date' => now()->addDays(60),
                'source' => 'redes_sociales',
                'campaign' => 'Campaña Q2 2024',
                'notes' => 'Cliente VIP, busca lotes familiares para construir casa. Programada visita para la próxima semana.',
            ],
            [
                'client_id' => $clients->where('name', 'Roberto Silva Castro')->first()->id,
                'project_id' => $projects->where('name', 'Lotes San Borja Center')->first()->id,
                'unit_id' => null,
                'advisor_id' => $advisors->random()->id,
                'stage' => 'calificado',
                'status' => 'registrado',
                'probability' => 40,
                'expected_value' => 1200000,
                'expected_close_date' => now()->addDays(90),
                'source' => 'ferias',
                'campaign' => 'Campaña Q1 2024',
                'notes' => 'Empresa buscando lotes comerciales para desarrollo empresarial. En proceso de evaluación de necesidades.',
            ],
            [
                'client_id' => $clients->where('name', 'Fernando Mendoza Ruiz')->first()->id,
                'project_id' => $projects->where('name', 'Lotes Barranco Golf')->first()->id,
                'unit_id' => null,
                'advisor_id' => $advisors->random()->id,
                'stage' => 'calificado',
                'status' => 'registrado',
                'probability' => 50,
                'expected_value' => 300000,
                'expected_close_date' => now()->addDays(120),
                'source' => 'publicidad',
                'campaign' => 'Campaña Q2 2024',
                'notes' => 'Constructor interesado en lotes residenciales para desarrollo. Evaluando viabilidad del proyecto.',
            ],
            [
                'client_id' => $clients->where('name', 'Alberto García Paredes')->first()->id,
                'project_id' => $projects->where('name', 'Lotes Miraflores Park')->first()->id,
                'unit_id' => $units->where('project_id', $projects->where('name', 'Lotes Miraflores Park')->first()->id)->first()->id,
                'advisor_id' => $advisors->random()->id,
                'stage' => 'cierre',
                'status' => 'pagado',
                'probability' => 100,
                'expected_value' => 480000,
                'close_value' => 475000,
                'actual_close_date' => now()->subDays(15),
                'close_reason' => 'Venta exitosa de lote con descuento del 5%',
                'source' => 'referidos',
                'campaign' => 'Campaña Q4 2023',
                'notes' => 'Cliente satisfecho con la compra del lote. Excelente experiencia de venta.',
            ],
            [
                'client_id' => $clients->where('name', 'Patricia Ríos Morales')->first()->id,
                'project_id' => $projects->where('name', 'Lotes Chorrillos Plaza')->first()->id,
                'unit_id' => null,
                'advisor_id' => $advisors->random()->id,
                'stage' => 'calificado',
                'status' => 'registrado',
                'probability' => 30,
                'expected_value' => 350000,
                'expected_close_date' => now()->addDays(180),
                'source' => 'formulario_web',
                'campaign' => 'Campaña Q3 2024',
                'notes' => 'Inversora en lotes de playa. Primer contacto realizado.',
            ],
        ];

        foreach ($opportunities as $opportunityData) {
            Opportunity::create([
                ...$opportunityData,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }

        // Crear oportunidades adicionales aleatorias
        $this->createRandomOpportunities($clients, $projects, $units, $advisors, $admin);

        $this->command->info('Oportunidades creadas exitosamente');
    }

    private function createRandomOpportunities($clients, $projects, $units, $advisors, $admin): void
    {
        $stages = ['calificado', 'visita', 'cierre'];
        $statuses = ['registrado', 'reservado', 'cuotas', 'pagado', 'transferido', 'cancelado'];
        $sources = ['redes_sociales', 'ferias', 'referidos', 'formulario_web', 'publicidad'];

        for ($i = 0; $i < 25; $i++) {
            $client = $clients->random();
            $project = $projects->random();
            $unit = $units->where('project_id', $project->id)->first();
            $advisor = $advisors->random();
            $stage = $stages[array_rand($stages)];
            $status = $statuses[array_rand($statuses)];

            $expectedValue = rand(200000, 1500000);
            $probability = rand(20, 95);

            $opportunityData = [
                'client_id' => $client->id,
                'project_id' => $project->id,
                'unit_id' => $unit ? $unit->id : null,
                'advisor_id' => $advisor->id,
                'stage' => $stage,
                'status' => $status,
                'probability' => $probability,
                'expected_value' => $expectedValue,
                'expected_close_date' => now()->addDays(rand(30, 180)),
                'source' => $sources[array_rand($sources)],
                'campaign' => 'Campaña Q' . rand(1, 4) . ' 2024',
                'notes' => 'Oportunidad generada automáticamente para pruebas.',
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ];

            // Si la oportunidad está pagada, agregar datos de cierre
            if ($status === 'pagado') {
                $opportunityData['close_value'] = $expectedValue * (rand(90, 110) / 100);
                $opportunityData['actual_close_date'] = now()->subDays(rand(1, 60));
                $opportunityData['close_reason'] = 'Venta exitosa';
                $opportunityData['probability'] = 100;
            }

            // Si la oportunidad está cancelada, agregar razón
            if ($status === 'cancelado') {
                $opportunityData['lost_reason'] = 'Cliente no interesado';
                $opportunityData['actual_close_date'] = now()->subDays(rand(1, 30));
                $opportunityData['probability'] = 0;
            }

            Opportunity::create($opportunityData);
        }
    }
}
