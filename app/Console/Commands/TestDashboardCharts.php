<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Livewire\Dashboard\Dashboard;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestDashboardCharts extends Command
{
    protected $signature = 'test:dashboard-charts';
    protected $description = 'Probar la visualización de gráficos del dashboard';

    public function handle()
    {
        $this->info('🧪 Probando visualización de gráficos del dashboard...');
        $this->newLine();

        // Probar con diferentes roles
        $roles = ['admin', 'lider', 'vendedor', 'datero'];
        
        foreach ($roles as $role) {
            $user = User::whereHas('roles', function($query) use ($role) {
                $query->where('name', $role);
            })->first();

            if (!$user) {
                $this->warn("No se encontró usuario con rol: {$role}");
                continue;
            }

            $this->info("👤 Probando gráficos para {$role}...");
            $this->info("   Usuario: {$user->name} ({$user->email})");

            // Autenticar usuario
            Auth::login($user);

            // Crear instancia del dashboard usando Livewire
            $dashboard = app(Dashboard::class);
            
            // Inicializar el servicio manualmente
            $dashboardService = app(\App\Services\DashboardService::class);
            $dashboard->boot($dashboardService);
            
            $dashboard->mount();

            // Cargar datos
            $dashboard->loadDashboardData();

            // Verificar datos de gráficos
            $chartData = $dashboard->chartData;

            $this->info("   📊 Datos de gráficos:");
            $this->info("      - Oportunidades por etapa: " . count($chartData['opportunitiesByStage']) . " etapas");
            $this->info("      - Clientes por estado: " . count($chartData['clientsByStatus']) . " estados");
            $this->info("      - Rendimiento de asesores: " . count($chartData['advisorPerformance']) . " asesores");
            $this->info("      - Oportunidades cerradas: " . count($chartData['closedOpportunitiesBySeller']) . " vendedores");
            $this->info("      - Rendimiento de líderes: " . count($chartData['leaderPerformance']) . " líderes");

            // Verificar que los datos no estén vacíos
            $hasData = false;
            foreach ($chartData as $key => $data) {
                if (is_array($data) && count($data) > 0) {
                    $hasData = true;
                    break;
                }
            }

            if ($hasData) {
                $this->info("   ✅ Gráficos tienen datos para mostrar");
            } else {
                $this->warn("   ⚠️  Gráficos sin datos");
            }

            // Mostrar muestra de datos
            if (count($chartData['opportunitiesByStage']) > 0) {
                $this->info("   📈 Muestra de oportunidades por etapa:");
                foreach (array_slice($chartData['opportunitiesByStage'], 0, 3) as $item) {
                    $this->info("      - {$item['stage']}: {$item['count']}");
                }
            }

            if (count($chartData['clientsByStatus']) > 0) {
                $this->info("   👥 Muestra de clientes por estado:");
                foreach (array_slice($chartData['clientsByStatus'], 0, 3) as $item) {
                    $this->info("      - {$item['status']}: {$item['count']}");
                }
            }

            $this->newLine();
        }

        $this->info('✅ Pruebas de gráficos completadas');
        $this->newLine();
        $this->info('💡 Para ver los gráficos en acción, visita el dashboard en tu navegador');
        $this->info('   Los gráficos deberían mostrarse correctamente con los datos cargados');
    }
}