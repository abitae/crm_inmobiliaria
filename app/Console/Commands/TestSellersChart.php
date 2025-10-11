<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Livewire\Dashboard\Dashboard;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestSellersChart extends Command
{
    protected $signature = 'test:sellers-chart';
    protected $description = 'Probar específicamente el gráfico de vendedores';

    public function handle()
    {
        $this->info('🔍 Probando gráfico de vendedores...');
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

        // Verificar datos específicos de vendedores
        $sellersData = $dashboard->chartData['closedOpportunitiesBySeller'];

        $this->info("📊 Datos de vendedores:");
        $this->info("   Total vendedores: " . count($sellersData));
        
        foreach ($sellersData as $index => $seller) {
            $this->info("   Vendedor " . ($index + 1) . ":");
            $this->info("     - Nombre: " . ($seller->name ?? 'Sin nombre'));
            $this->info("     - Ventas: S/ " . number_format($seller->total_sales ?? 0, 2));
            $this->info("     - Oportunidades: " . ($seller->closed_opportunities ?? 0));
            $this->info("     - Promedio por venta: S/ " . number_format($seller->average_sale ?? 0, 2));
        }

        $this->newLine();
        $this->info('💡 Verificaciones:');
        $this->info('   1. Las ventas están en valores altos (millones)');
        $this->info('   2. Las oportunidades están en valores bajos (1-10)');
        $this->info('   3. El gráfico debe mostrar ambas escalas correctamente');
        $this->info('   4. Las barras de oportunidades deben ser visibles');
        
        $this->newLine();
        $this->info('🌐 Visita http://localhost:8000/dashboard para ver el gráfico');
        $this->info('   El gráfico "Rendimiento de Vendedores" debe mostrar:');
        $this->info('   - Barras azules para ventas (escala izquierda)');
        $this->info('   - Barras verdes para oportunidades (escala derecha)');
    }
}
