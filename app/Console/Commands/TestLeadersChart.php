<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Livewire\Dashboard\Dashboard;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestLeadersChart extends Command
{
    protected $signature = 'test:leaders-chart';
    protected $description = 'Probar específicamente el gráfico de líderes';

    public function handle()
    {
        $this->info('🔍 Probando gráfico de líderes...');
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

        // Verificar datos específicos de líderes
        $leadersData = $dashboard->chartData['leaderPerformance'];

        $this->info("📊 Datos de líderes:");
        $this->info("   Total líderes: " . count($leadersData));
        
        foreach ($leadersData as $index => $leader) {
            $this->info("   Líder " . ($index + 1) . ":");
            
            // Manejar tanto arrays como objetos
            if (is_array($leader)) {
                $this->info("     - Nombre: " . ($leader['name'] ?? 'Sin nombre'));
                $this->info("     - Ventas Totales: S/ " . number_format($leader['total_sales'] ?? 0, 2));
                $this->info("     - Ventas Líder: S/ " . number_format($leader['leader_sales'] ?? 0, 2));
                $this->info("     - Ventas Equipo: S/ " . number_format($leader['team_sales'] ?? 0, 2));
                $this->info("     - Oportunidades: " . ($leader['closed_opportunities'] ?? 0));
                $this->info("     - Miembros del Equipo: " . ($leader['team_members'] ?? 0));
                $this->info("     - Promedio por Venta: S/ " . number_format($leader['average_sale'] ?? 0, 2));
            } else {
                $this->info("     - Nombre: " . ($leader->name ?? 'Sin nombre'));
                $this->info("     - Ventas Totales: S/ " . number_format($leader->total_sales ?? 0, 2));
                $this->info("     - Ventas Líder: S/ " . number_format($leader->leader_sales ?? 0, 2));
                $this->info("     - Ventas Equipo: S/ " . number_format($leader->team_sales ?? 0, 2));
                $this->info("     - Oportunidades: " . ($leader->closed_opportunities ?? 0));
                $this->info("     - Miembros del Equipo: " . ($leader->team_members ?? 0));
                $this->info("     - Promedio por Venta: S/ " . number_format($leader->average_sale ?? 0, 2));
            }
        }

        $this->newLine();
        $this->info('💡 Análisis:');
        $this->info('   1. Carlos Rodríguez: Solo ventas directas (líder)');
        $this->info('   2. María González: Solo ventas de equipo');
        $this->info('   3. El gráfico debe mostrar ambas escalas correctamente');
        $this->info('   4. Las oportunidades deben ser visibles');
        
        $this->newLine();
        $this->info('🌐 Visita http://localhost:8000/dashboard para ver el gráfico');
        $this->info('   El gráfico "Rendimiento de Líderes" debe mostrar:');
        $this->info('   - Barras azules para ventas del líder (escala izquierda)');
        $this->info('   - Barras verdes para ventas del equipo (escala izquierda)');
        $this->info('   - Barras naranjas para oportunidades (escala derecha)');
    }
}
