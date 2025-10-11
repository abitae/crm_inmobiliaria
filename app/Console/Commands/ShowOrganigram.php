<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ShowOrganigram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'organigram:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mostrar el organigrama visual de la organización';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏢 ORGANIGRAMA DE LA ORGANIZACIÓN');
        $this->newLine();

        $admin = User::where('email', 'abel.arana@hotmail.com')->first();
        
        if (!$admin) {
            $this->error('❌ No se encontró el administrador');
            return;
        }

        $this->displayOrganigram($admin, 0);
        
        $this->newLine();
        $this->info('📊 RESUMEN DE JERARQUÍAS:');
        $this->displaySummary();
    }

    private function displayOrganigram(User $user, int $level): void
    {
        $indent = str_repeat('  ', $level);
        $icon = $this->getUserIcon($user);
        $role = $user->getRoleName();
        $status = $user->isActive() ? '🟢' : '🔴';
        
        $this->line("{$indent}{$icon} {$status} {$user->name} ({$role})");
        
        if ($user->email !== 'abel.arana@hotmail.com') {
            $this->line("{$indent}    📧 {$user->email}");
        }

        // Mostrar subordinados
        $subordinados = $user->subordinados()->orderBy('name')->get();
        
        foreach ($subordinados as $subordinado) {
            $this->displayOrganigram($subordinado, $level + 1);
        }
    }

    private function getUserIcon(User $user): string
    {
        if ($user->hasRole('admin')) {
            return '👑';
        } elseif ($user->hasRole('lider')) {
            return '👥';
        } elseif ($user->hasRole('vendedor')) {
            return '💼';
        } elseif ($user->hasRole('datero')) {
            return '📊';
        }
        
        return '👤';
    }

    private function displaySummary(): void
    {
        $admin = User::role('admin')->count();
        $lideres = User::role('lider')->count();
        $vendedores = User::role('vendedor')->count();
        $dateros = User::role('datero')->count();
        
        $vendedoresSinLider = User::role('vendedor')->whereNull('lider_id')->count();
        $daterosSinVendedor = User::role('datero')->whereNull('lider_id')->count();
        
        $this->table(
            ['Rol', 'Total', 'Con Líder', 'Sin Líder', 'Activos'],
            [
                ['👑 Admin', $admin, '-', '-', User::role('admin')->where('is_active', true)->count()],
                ['👥 Líderes', $lideres, $lideres, '0', User::role('lider')->where('is_active', true)->count()],
                ['💼 Vendedores', $vendedores, $vendedores - $vendedoresSinLider, $vendedoresSinLider, User::role('vendedor')->where('is_active', true)->count()],
                ['📊 Dateros', $dateros, $dateros - $daterosSinVendedor, $daterosSinVendedor, User::role('datero')->where('is_active', true)->count()],
            ]
        );
        
        $this->newLine();
        $this->info("📈 MÉTRICAS DE EQUIPOS:");
        
        $lideres = User::role('lider')->get();
        foreach ($lideres as $lider) {
            $vendedoresEquipo = $lider->subordinados()->role('vendedor')->count();
            $daterosEquipo = 0;
            
            foreach ($lider->subordinados()->role('vendedor')->get() as $vendedor) {
                $daterosEquipo += $vendedor->subordinados()->role('datero')->count();
            }
            
            $this->line("  - {$lider->name}: {$vendedoresEquipo} vendedores, {$daterosEquipo} dateros");
        }
    }
}
