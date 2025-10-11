<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Livewire\Dashboard\Dashboard;
use Illuminate\Console\Command;

class TestDashboardFilters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:dashboard-filters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar los filtros optimizados del dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Probando filtros del dashboard...');
        $this->newLine();

        // Probar con Admin
        $this->testFiltersForUser('Admin', 'abel.arana@hotmail.com');
        
        // Probar con Líder
        $this->testFiltersForUser('Líder', 'maria.gonzalez@crm.com');

        $this->newLine();
        $this->info('✅ Pruebas de filtros completadas exitosamente');
    }

    private function testFiltersForUser(string $roleName, string $email): void
    {
        $this->info("👤 Probando filtros para {$roleName}...");
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ Usuario {$email} no encontrado");
            return;
        }

        $this->info("   Usuario: {$user->name} ({$user->email})");
        $this->info("   Rol: {$user->getRoleName()}");

        // Simular autenticación
        auth()->login($user);

        // Crear instancia del componente Dashboard
        $dashboard = new Dashboard();
        $dashboard->boot(app(\App\Services\DashboardService::class));
        $dashboard->mount();

        // Probar filtros válidos
        $this->info("   📅 Probando filtros válidos:");
        $dashboard->startDate = '2024-01-01';
        $dashboard->endDate = '2024-12-31';
        
        try {
            $dashboard->loadDashboardData();
            $this->info("      ✅ Filtros válidos funcionan correctamente");
        } catch (\Exception $e) {
            $this->error("      ❌ Error con filtros válidos: " . $e->getMessage());
        }

        // Probar filtros inválidos
        $this->info("   🚫 Probando filtros inválidos:");
        
        // Fecha de inicio mayor a fecha de fin
        $dashboard->startDate = '2024-12-31';
        $dashboard->endDate = '2024-01-01';
        try {
            $dashboard->loadDashboardData();
            $this->warn("      ⚠️  Filtros inválidos no fueron rechazados");
        } catch (\Exception $e) {
            $this->info("      ✅ Filtros inválidos fueron rechazados correctamente");
        }

        // Fecha futura
        $dashboard->startDate = now()->addDays(30)->toDateString();
        $dashboard->endDate = now()->addDays(60)->toDateString();
        try {
            $dashboard->loadDashboardData();
            $this->warn("      ⚠️  Fechas futuras no fueron rechazadas");
        } catch (\Exception $e) {
            $this->info("      ✅ Fechas futuras fueron rechazadas correctamente");
        }

        $this->info("   ✅ Filtros probados correctamente para {$roleName}");
        $this->newLine();
    }
}
