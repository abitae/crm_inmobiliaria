<?php

namespace Database\Seeders;

use App\Models\Commission;
use App\Models\Opportunity;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommissionSeeder extends Seeder
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
        $projects = Project::all();
        $units = Unit::all();
        $opportunities = Opportunity::all();

        $commissionTypes = ['venta', 'reserva', 'seguimiento', 'bono'];
        $statuses = ['pendiente', 'aprobada', 'pagada', 'cancelada'];
        $paymentMethods = ['transferencia', 'efectivo', 'cheque', 'depósito'];

        // Crear comisiones realistas
        $commissions = [
            [
                'advisor_id' => $advisors->random()->id,
                'project_id' => $projects->where('name', 'Lotes Miraflores Park')->first()->id,
                'unit_id' => $units->where('project_id', $projects->where('name', 'Lotes Miraflores Park')->first()->id)->first()->id,
                'opportunity_id' => $opportunities->where('client_id', 1)->first()->id,
                'commission_type' => 'venta',
                'base_amount' => 475000,
                'commission_percentage' => 5,
                'commission_amount' => 23750,
                'bonus_amount' => 5000,
                'total_commission' => 28750,
                'status' => 'pagada',
                'payment_date' => now()->subDays(10),
                'payment_method' => 'transferencia',
                'payment_reference' => 'COM-001-2024',
                'notes' => 'Comisión por venta exitosa del lote A-101. Bono por excelente servicio.',
                'approved_by' => $admin->id,
                'approved_at' => now()->subDays(15),
                'paid_by' => $admin->id,
                'paid_at' => now()->subDays(10),
            ],
            [
                'advisor_id' => $advisors->random()->id,
                'project_id' => $projects->where('name', 'Lotes Surco Family')->first()->id,
                'unit_id' => $units->where('project_id', $projects->where('name', 'Lotes Surco Family')->first()->id)->first()->id,
                'opportunity_id' => null,
                'commission_type' => 'reserva',
                'base_amount' => 65000,
                'commission_percentage' => 3,
                'commission_amount' => 1950,
                'bonus_amount' => 0,
                'total_commission' => 1950,
                'status' => 'aprobada',
                'payment_date' => null,
                'payment_method' => null,
                'payment_reference' => null,
                'notes' => 'Comisión por reserva de lote familiar. Pendiente de pago.',
                'approved_by' => $admin->id,
                'approved_at' => now()->subDays(5),
                'paid_by' => null,
                'paid_at' => null,
            ],
            [
                'advisor_id' => $advisors->random()->id,
                'project_id' => $projects->where('name', 'Lotes San Isidro Business')->first()->id,
                'unit_id' => null,
                'opportunity_id' => $opportunities->where('client_id', 2)->first()->id,
                'commission_type' => 'seguimiento',
                'base_amount' => 800000,
                'commission_percentage' => 2,
                'commission_amount' => 16000,
                'bonus_amount' => 2000,
                'total_commission' => 18000,
                'status' => 'pendiente',
                'payment_date' => null,
                'payment_method' => null,
                'payment_reference' => null,
                'notes' => 'Comisión por seguimiento de oportunidad de lotes comerciales.',
                'approved_by' => null,
                'approved_at' => null,
                'paid_by' => null,
                'paid_at' => null,
            ],
            [
                'advisor_id' => $advisors->random()->id,
                'project_id' => $projects->where('name', 'Lotes San Borja Center')->first()->id,
                'unit_id' => null,
                'opportunity_id' => $opportunities->where('client_id', 3)->first()->id,
                'commission_type' => 'bono',
                'base_amount' => 1200000,
                'commission_percentage' => 0,
                'commission_amount' => 0,
                'bonus_amount' => 10000,
                'total_commission' => 10000,
                'status' => 'pendiente',
                'payment_date' => null,
                'payment_method' => null,
                'payment_reference' => null,
                'notes' => 'Bono por excelente trabajo con cliente empresarial de lotes.',
                'approved_by' => null,
                'approved_at' => null,
                'paid_by' => null,
                'paid_at' => null,
            ],
            [
                'advisor_id' => $advisors->random()->id,
                'project_id' => $projects->where('name', 'Lotes Barranco Golf')->first()->id,
                'unit_id' => null,
                'opportunity_id' => $opportunities->where('client_id', 4)->first()->id,
                'commission_type' => 'seguimiento',
                'base_amount' => 300000,
                'commission_percentage' => 2,
                'commission_amount' => 6000,
                'bonus_amount' => 0,
                'total_commission' => 6000,
                'status' => 'pendiente',
                'payment_date' => null,
                'payment_method' => null,
                'payment_reference' => null,
                'notes' => 'Comisión por seguimiento de oportunidad de lotes residenciales.',
                'approved_by' => null,
                'approved_at' => null,
                'paid_by' => null,
                'paid_at' => null,
            ],
        ];

        foreach ($commissions as $commissionData) {
            Commission::create([
                ...$commissionData,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }

        // Crear comisiones adicionales aleatorias
        $this->createRandomCommissions($advisors, $projects, $units, $opportunities, $admin);

        $this->command->info('Comisiones creadas exitosamente');
    }

    private function createRandomCommissions($advisors, $projects, $units, $opportunities, $admin): void
    {
        $commissionTypes = ['venta', 'reserva', 'seguimiento', 'bono'];
        $statuses = ['pendiente', 'aprobada', 'pagada', 'cancelada'];
        $paymentMethods = ['transferencia', 'efectivo', 'cheque', 'depósito'];

        for ($i = 0; $i < 30; $i++) {
            $advisor = $advisors->random();
            $project = $projects->random();
            $unit = $units->where('project_id', $project->id)->first();
            $opportunity = $opportunities->random();
            $commissionType = $commissionTypes[array_rand($commissionTypes)];
            $status = $statuses[array_rand($statuses)];

            $baseAmount = rand(50000, 2000000);
            $commissionPercentage = $commissionType === 'bono' ? 0 : rand(2, 8);
            $commissionAmount = ($baseAmount * $commissionPercentage) / 100;
            $bonusAmount = $commissionType === 'bono' ? rand(1000, 15000) : rand(0, 5000);
            $totalCommission = $commissionAmount + $bonusAmount;

            $commissionData = [
                'advisor_id' => $advisor->id,
                'project_id' => $project->id,
                'unit_id' => $unit ? $unit->id : null,
                'opportunity_id' => $opportunity->id,
                'commission_type' => $commissionType,
                'base_amount' => $baseAmount,
                'commission_percentage' => $commissionPercentage,
                'commission_amount' => $commissionAmount,
                'bonus_amount' => $bonusAmount,
                'total_commission' => $totalCommission,
                'status' => $status,
                'payment_date' => $status === 'pagada' ? now()->subDays(rand(1, 60)) : null,
                'payment_method' => $status === 'pagada' ? $paymentMethods[array_rand($paymentMethods)] : null,
                'payment_reference' => $status === 'pagada' ? 'COM-' . rand(100, 999) . '-2024' : null,
                'notes' => 'Comisión generada automáticamente para pruebas.',
                'approved_by' => in_array($status, ['aprobada', 'pagada']) ? $admin->id : null,
                'approved_at' => in_array($status, ['aprobada', 'pagada']) ? now()->subDays(rand(1, 30)) : null,
                'paid_by' => $status === 'pagada' ? $admin->id : null,
                'paid_at' => $status === 'pagada' ? now()->subDays(rand(1, 60)) : null,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ];

            Commission::create($commissionData);
        }
    }
}
