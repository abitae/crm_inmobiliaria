<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Console\Command;

class TestLeaderChart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:leader-chart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el gráfico de rendimiento de líderes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎯 Probando gráfico de rendimiento de líderes...');
        $this->newLine();

        $dashboardService = new DashboardService();

        // Probar con Admin (debe ver todos los líderes)
        $this->testLeaderChartForUser('Admin', 'abel.arana@hotmail.com', $dashboardService);
        
        // Probar con Líder (debe ver solo su propio rendimiento)
        $this->testLeaderChartForUser('Líder', 'maria.gonzalez@crm.com', $dashboardService);

        $this->newLine();
        $this->info('✅ Pruebas del gráfico de líderes completadas exitosamente');
    }

    private function testLeaderChartForUser(string $roleName, string $email, DashboardService $dashboardService): void
    {
        $this->info("👤 Probando gráfico de líderes para {$roleName}...");
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ Usuario {$email} no encontrado");
            return;
        }

        $this->info("   Usuario: {$user->name} ({$user->email})");
        $this->info("   Rol: {$user->getRoleName()}");

        // Obtener datos del gráfico de líderes
        $leaderPerformance = $dashboardService->getLeaderPerformance([], $user);
        
        $this->info("   📊 Datos del gráfico de líderes:");
        $this->info("      - Total líderes: " . count($leaderPerformance));
        
        if (count($leaderPerformance) > 0) {
            $this->info("      - Líderes encontrados:");
            foreach ($leaderPerformance as $leader) {
                $this->info("        • {$leader['name']}:");
                $this->info("          - Ventas del líder: S/ " . number_format($leader['leader_sales']));
                $this->info("          - Ventas del equipo: S/ " . number_format($leader['team_sales']));
                $this->info("          - Total ventas: S/ " . number_format($leader['total_sales']));
                $this->info("          - Oportunidades cerradas: " . $leader['closed_opportunities']);
                $this->info("          - Miembros del equipo: " . $leader['team_members']);
                $this->info("          - Promedio por venta: S/ " . number_format($leader['average_sale']));
            }
        } else {
            $this->info("      - No se encontraron líderes para este usuario");
        }

        $this->info("   ✅ Gráfico de líderes cargado correctamente para {$roleName}");
        $this->newLine();
    }
}
