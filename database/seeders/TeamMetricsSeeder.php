<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Opportunity;
use App\Models\Commission;
use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamMetricsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Generando métricas de equipo...');

        $admin = User::where('email', 'abel.arana@hotmail.com')->first();
        if (!$admin) {
            throw new \Exception('No se encontró el usuario administrador.');
        }

        // Generar métricas para cada equipo
        $this->generateTeamMetrics($admin);

        $this->command->info('Métricas de equipo generadas exitosamente');
    }

    private function generateTeamMetrics($admin): void
    {
        $lideres = User::role('lider')->get();
        $vendedores = User::role('vendedor')->get();
        $dateros = User::role('datero')->get();

        // Verificar que existan usuarios con roles
        if ($lideres->isEmpty() && $vendedores->isEmpty() && $dateros->isEmpty()) {
            $this->command->warn('No se encontraron usuarios con roles específicos. Saltando generación de métricas de equipo.');
            return;
        }

        foreach ($lideres as $lider) {
            $this->generateLiderMetrics($lider, $vendedores, $dateros, $admin);
        }

        // Métricas para vendedores sin líder
        $vendedoresSinLider = $vendedores->where('lider_id', null);
        foreach ($vendedoresSinLider as $vendedor) {
            $this->generateVendedorMetrics($vendedor, $dateros, $admin);
        }

        // Métricas para dateros sin vendedor
        $daterosSinVendedor = $dateros->where('lider_id', null);
        foreach ($daterosSinVendedor as $datero) {
            $this->generateDateroMetrics($datero, $admin);
        }
    }

    private function generateLiderMetrics($lider, $vendedores, $dateros, $admin): void
    {
        $vendedoresEquipo = $vendedores->where('lider_id', $lider->id);
        $totalVendedores = $vendedoresEquipo->count();
        
        $totalDateros = 0;
        $totalClientes = 0;
        $totalOportunidades = 0;
        $totalComisiones = 0;
        $totalActividades = 0;

        foreach ($vendedoresEquipo as $vendedor) {
            $daterosVendedor = $dateros->where('lider_id', $vendedor->id);
            $totalDateros += $daterosVendedor->count();

            // Métricas del vendedor
            $clientesVendedor = Client::where('assigned_advisor_id', $vendedor->id)->count();
            $oportunidadesVendedor = Opportunity::where('advisor_id', $vendedor->id)->count();
            $comisionesVendedor = Commission::where('advisor_id', $vendedor->id)->count();
            $actividadesVendedor = Activity::where('advisor_id', $vendedor->id)->count();

            $totalClientes += $clientesVendedor;
            $totalOportunidades += $oportunidadesVendedor;
            $totalComisiones += $comisionesVendedor;
            $totalActividades += $actividadesVendedor;

            // Métricas de dateros del vendedor
            foreach ($daterosVendedor as $datero) {
                $clientesDatero = Client::where('assigned_advisor_id', $datero->id)->count();
                $oportunidadesDatero = Opportunity::where('advisor_id', $datero->id)->count();
                $actividadesDatero = Activity::where('advisor_id', $datero->id)->count();

                $totalClientes += $clientesDatero;
                $totalOportunidades += $oportunidadesDatero;
                $totalActividades += $actividadesDatero;
            }
        }

        $this->command->info("Métricas del equipo {$lider->name}:");
        $this->command->info("  - Vendedores: {$totalVendedores}");
        $this->command->info("  - Dateros: {$totalDateros}");
        $this->command->info("  - Clientes: {$totalClientes}");
        $this->command->info("  - Oportunidades: {$totalOportunidades}");
        $this->command->info("  - Comisiones: {$totalComisiones}");
        $this->command->info("  - Actividades: {$totalActividades}");
    }

    private function generateVendedorMetrics($vendedor, $dateros, $admin): void
    {
        $daterosVendedor = $dateros->where('lider_id', $vendedor->id);
        $totalDateros = $daterosVendedor->count();

        $clientesVendedor = Client::where('assigned_advisor_id', $vendedor->id)->count();
        $oportunidadesVendedor = Opportunity::where('advisor_id', $vendedor->id)->count();
        $comisionesVendedor = Commission::where('advisor_id', $vendedor->id)->count();
        $actividadesVendedor = Activity::where('advisor_id', $vendedor->id)->count();

        $totalClientes = $clientesVendedor;
        $totalOportunidades = $oportunidadesVendedor;
        $totalComisiones = $comisionesVendedor;
        $totalActividades = $actividadesVendedor;

        // Métricas de dateros del vendedor
        foreach ($daterosVendedor as $datero) {
            $clientesDatero = Client::where('assigned_advisor_id', $datero->id)->count();
            $oportunidadesDatero = Opportunity::where('advisor_id', $datero->id)->count();
            $actividadesDatero = Activity::where('advisor_id', $datero->id)->count();

            $totalClientes += $clientesDatero;
            $totalOportunidades += $oportunidadesDatero;
            $totalActividades += $actividadesDatero;
        }

        $this->command->info("Métricas del vendedor independiente {$vendedor->name}:");
        $this->command->info("  - Dateros: {$totalDateros}");
        $this->command->info("  - Clientes: {$totalClientes}");
        $this->command->info("  - Oportunidades: {$totalOportunidades}");
        $this->command->info("  - Comisiones: {$totalComisiones}");
        $this->command->info("  - Actividades: {$totalActividades}");
    }

    private function generateDateroMetrics($datero, $admin): void
    {
        $clientesDatero = Client::where('assigned_advisor_id', $datero->id)->count();
        $oportunidadesDatero = Opportunity::where('advisor_id', $datero->id)->count();
        $actividadesDatero = Activity::where('advisor_id', $datero->id)->count();

        $this->command->info("Métricas del datero independiente {$datero->name}:");
        $this->command->info("  - Clientes: {$clientesDatero}");
        $this->command->info("  - Oportunidades: {$oportunidadesDatero}");
        $this->command->info("  - Actividades: {$actividadesDatero}");
    }
}
