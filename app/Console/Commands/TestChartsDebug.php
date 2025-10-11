<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Livewire\Dashboard\Dashboard;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestChartsDebug extends Command
{
    protected $signature = 'test:charts-debug';
    protected $description = 'Debug de gráficos del dashboard';

    public function handle()
    {
        $this->info('🔍 Debug de gráficos del dashboard...');
        $this->newLine();

        // Probar con admin
        $user = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->first();

        if (!$user) {
            $this->error('No se encontró usuario admin');
            return;
        }

        $this->info("👤 Usuario: {$user->name} ({$user->email})");

        // Autenticar usuario
        Auth::login($user);

        // Crear instancia del dashboard
        $dashboard = app(Dashboard::class);
        $dashboardService = app(\App\Services\DashboardService::class);
        $dashboard->boot($dashboardService);
        $dashboard->mount();

        // Cargar datos
        $dashboard->loadDashboardData();

        // Verificar datos de gráficos
        $chartData = $dashboard->chartData;

        $this->info("📊 Datos de gráficos:");
        foreach ($chartData as $key => $data) {
            $this->info("   - {$key}: " . (is_array($data) ? count($data) . ' elementos' : 'No es array'));
            
            if (is_array($data) && count($data) > 0) {
                $this->info("     Muestra: " . json_encode(array_slice($data, 0, 2)));
            }
        }

        // Verificar si hay datos válidos
        $hasValidData = false;
        foreach ($chartData as $data) {
            if (is_array($data) && count($data) > 0) {
                $hasValidData = true;
                break;
            }
        }

        if ($hasValidData) {
            $this->info("✅ Hay datos válidos para los gráficos");
        } else {
            $this->warn("⚠️  No hay datos válidos para los gráficos");
        }

        $this->newLine();
        $this->info('💡 Para ver los gráficos:');
        $this->info('   1. Visita http://localhost:8000/dashboard');
        $this->info('   2. Abre las herramientas de desarrollador (F12)');
        $this->info('   3. Revisa la consola para mensajes de Chart.js');
        $this->info('   4. Verifica que no haya errores de JavaScript');
    }
}
