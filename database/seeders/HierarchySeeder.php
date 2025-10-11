<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class HierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Estableciendo jerarquías y equipos...');

        // Obtener usuarios por roles
        $admin = User::where('email', 'abel.arana@hotmail.com')->first();
        $lideres = User::role('lider')->get();
        $vendedores = User::role('vendedor')->get();
        $dateros = User::role('datero')->get();

        if (!$admin) {
            throw new \Exception('No se encontró el usuario administrador.');
        }

        // Establecer equipos de trabajo
        $this->createTeams($admin, $lideres, $vendedores, $dateros);

        // Crear métricas de equipo
        $this->createTeamMetrics($admin, $lideres, $vendedores, $dateros);

        $this->command->info('Jerarquías y equipos establecidos exitosamente');
    }

    private function createTeams($admin, $lideres, $vendedores, $dateros): void
    {
        $this->command->info('Creando equipos de trabajo...');

        // Equipo 1: María González (Líder)
        $lider1 = $lideres->where('email', 'maria.gonzalez@crm.com')->first();
        if ($lider1) {
            $this->command->info("Equipo 1: {$lider1->name} (Líder)");
            
            // Vendedores del equipo 1
            $vendedoresEquipo1 = $vendedores->where('lider_id', $lider1->id);
            foreach ($vendedoresEquipo1 as $vendedor) {
                $this->command->info("  - {$vendedor->name} (Vendedor)");
                
                // Dateros del vendedor
                $daterosVendedor = $dateros->where('lider_id', $vendedor->id);
                foreach ($daterosVendedor as $datero) {
                    $this->command->info("    - {$datero->name} (Datero)");
                }
            }
        }

        // Equipo 2: Carlos Rodríguez (Líder)
        $lider2 = $lideres->where('email', 'carlos.rodriguez@crm.com')->first();
        if ($lider2) {
            $this->command->info("Equipo 2: {$lider2->name} (Líder)");
            
            // Vendedores del equipo 2
            $vendedoresEquipo2 = $vendedores->where('lider_id', $lider2->id);
            foreach ($vendedoresEquipo2 as $vendedor) {
                $this->command->info("  - {$vendedor->name} (Vendedor)");
                
                // Dateros del vendedor
                $daterosVendedor = $dateros->where('lider_id', $vendedor->id);
                foreach ($daterosVendedor as $datero) {
                    $this->command->info("    - {$datero->name} (Datero)");
                }
            }
        }

        // Vendedores sin líder
        $vendedoresSinLider = $vendedores->where('lider_id', null);
        foreach ($vendedoresSinLider as $vendedor) {
            $this->command->info("Vendedor independiente: {$vendedor->name}");
            
            // Dateros del vendedor independiente
            $daterosVendedor = $dateros->where('lider_id', $vendedor->id);
            foreach ($daterosVendedor as $datero) {
                $this->command->info("  - {$datero->name} (Datero)");
            }
        }

        // Dateros sin vendedor
        $daterosSinVendedor = $dateros->where('lider_id', null);
        foreach ($daterosSinVendedor as $datero) {
            $this->command->info("Datero independiente: {$datero->name}");
        }
    }

    private function createTeamMetrics($admin, $lideres, $vendedores, $dateros): void
    {
        $this->command->info('Generando métricas de equipo...');

        // Métricas por equipo
        foreach ($lideres as $lider) {
            $vendedoresEquipo = $vendedores->where('lider_id', $lider->id);
            $totalVendedores = $vendedoresEquipo->count();
            
            $totalDateros = 0;
            foreach ($vendedoresEquipo as $vendedor) {
                $daterosVendedor = $dateros->where('lider_id', $vendedor->id);
                $totalDateros += $daterosVendedor->count();
            }

            $this->command->info("Métricas del equipo {$lider->name}:");
            $this->command->info("  - Vendedores: {$totalVendedores}");
            $this->command->info("  - Dateros: {$totalDateros}");
            $this->command->info("  - Total miembros: " . ($totalVendedores + $totalDateros + 1));
        }

        // Métricas generales
        $totalLideres = $lideres->count();
        $totalVendedores = $vendedores->count();
        $totalDateros = $dateros->count();
        $vendedoresSinLider = $vendedores->where('lider_id', null)->count();
        $daterosSinVendedor = $dateros->where('lider_id', null)->count();

        $this->command->info('Métricas generales:');
        $this->command->info("  - Total líderes: {$totalLideres}");
        $this->command->info("  - Total vendedores: {$totalVendedores}");
        $this->command->info("  - Total dateros: {$totalDateros}");
        $this->command->info("  - Vendedores sin líder: {$vendedoresSinLider}");
        $this->command->info("  - Dateros sin vendedor: {$daterosSinVendedor}");
    }
}
