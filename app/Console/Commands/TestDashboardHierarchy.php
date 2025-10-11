<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Console\Command;

class TestDashboardHierarchy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:dashboard-hierarchy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el dashboard con diferentes roles de usuario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Probando dashboard con diferentes roles...');
        $this->newLine();

        $dashboardService = new DashboardService();

        // Probar con Admin
        $this->testUserRole('Admin', 'abel.arana@hotmail.com', $dashboardService);
        
        // Probar con Líder
        $this->testUserRole('Líder', 'maria.gonzalez@crm.com', $dashboardService);
        
        // Probar con Vendedor
        $this->testUserRole('Vendedor', 'ana.martinez@crm.com', $dashboardService);
        
        // Probar con Datero
        $this->testUserRole('Datero', 'pedro.ramirez@crm.com', $dashboardService);

        $this->newLine();
        $this->info('✅ Pruebas del dashboard completadas exitosamente');
    }

    private function testUserRole(string $roleName, string $email, DashboardService $dashboardService): void
    {
        $this->info("👤 Probando dashboard para {$roleName}...");
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ Usuario {$email} no encontrado");
            return;
        }

        $this->info("   Usuario: {$user->name} ({$user->email})");
        $this->info("   Rol: {$user->getRoleName()}");

        // Obtener estadísticas del dashboard
        $stats = $dashboardService->getDashboardStats([], $user);
        
        $this->info("   📊 Estadísticas:");
        $this->info("      - Clientes: {$stats['clients']['total']}");
        $this->info("      - Proyectos: {$stats['projects']['total']}");
        $this->info("      - Oportunidades: {$stats['opportunities']['total']}");
        $this->info("      - Tareas: {$stats['tasks']['total']}");

        // Obtener oportunidades por etapa
        $opportunitiesByStage = $dashboardService->getOpportunitiesByStage([], $user);
        $this->info("   📈 Oportunidades por etapa: " . count($opportunitiesByStage) . " etapas");

        // Obtener clientes por estado
        $clientsByStatus = $dashboardService->getClientsByStatus([], $user);
        $this->info("   👥 Clientes por estado: " . count($clientsByStatus) . " estados");

        // Obtener rendimiento de asesores
        $advisorPerformance = $dashboardService->getAdvisorPerformance([], $user);
        $this->info("   🎯 Rendimiento de asesores: " . count($advisorPerformance) . " asesores");

        // Obtener oportunidades cerradas por vendedor
        $closedOpportunities = $dashboardService->getClosedOpportunitiesBySeller([], $user);
        $this->info("   💰 Oportunidades cerradas: " . count($closedOpportunities) . " vendedores");

        $this->info("   ✅ Dashboard cargado correctamente para {$roleName}");
        $this->newLine();
    }
}
