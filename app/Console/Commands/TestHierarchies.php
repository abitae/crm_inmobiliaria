<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class TestHierarchies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:hierarchies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar y validar las jerarquías de usuarios implementadas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Probando jerarquías de usuarios...');
        $this->newLine();

        // Probar jerarquías
        $this->testAdminHierarchy();
        $this->testLiderHierarchy();
        $this->testVendedorHierarchy();
        $this->testDateroHierarchy();
        $this->testIndependentUsers();

        $this->newLine();
        $this->info('✅ Pruebas de jerarquías completadas exitosamente');
    }

    private function testAdminHierarchy(): void
    {
        $this->info('👑 Probando jerarquía de Admin...');
        
        $admin = User::where('email', 'abel.arana@hotmail.com')->first();
        
        if (!$admin) {
            $this->error('❌ Admin no encontrado');
            return;
        }

        $this->checkUserRole($admin, 'admin');
        $this->checkUserLeader($admin, null);
        $this->checkUserSubordinates($admin, 'lideres');
        
        $this->info('✅ Admin validado correctamente');
        $this->newLine();
    }

    private function testLiderHierarchy(): void
    {
        $this->info('👥 Probando jerarquía de Líderes...');
        
        $lideres = User::role('lider')->get();
        
        if ($lideres->isEmpty()) {
            $this->error('❌ No se encontraron líderes');
            return;
        }

        foreach ($lideres as $lider) {
            $this->info("  - Probando {$lider->name}...");
            $this->checkUserRole($lider, 'lider');
            $this->checkUserLeader($lider, 'admin');
            $this->checkUserSubordinates($lider, 'vendedores');
        }
        
        $this->info('✅ Líderes validados correctamente');
        $this->newLine();
    }

    private function testVendedorHierarchy(): void
    {
        $this->info('💼 Probando jerarquía de Vendedores...');
        
        $vendedores = User::role('vendedor')->get();
        
        if ($vendedores->isEmpty()) {
            $this->error('❌ No se encontraron vendedores');
            return;
        }

        foreach ($vendedores as $vendedor) {
            $this->info("  - Probando {$vendedor->name}...");
            $this->checkUserRole($vendedor, 'vendedor');
            
            if ($vendedor->lider_id) {
                $this->checkUserLeader($vendedor, 'lider');
            } else {
                $this->info("    ⚠️  Vendedor sin líder (permitido)");
            }
            
            $this->checkUserSubordinates($vendedor, 'dateros');
        }
        
        $this->info('✅ Vendedores validados correctamente');
        $this->newLine();
    }

    private function testDateroHierarchy(): void
    {
        $this->info('📊 Probando jerarquía de Dateros...');
        
        $dateros = User::role('datero')->get();
        
        if ($dateros->isEmpty()) {
            $this->error('❌ No se encontraron dateros');
            return;
        }

        foreach ($dateros as $datero) {
            $this->info("  - Probando {$datero->name}...");
            $this->checkUserRole($datero, 'datero');
            
            if ($datero->lider_id) {
                $leader = User::find($datero->lider_id);
                if ($leader && $leader->hasRole('vendedor')) {
                    $this->info("    ✅ Reporta a vendedor: {$leader->name}");
                } else {
                    $this->error("    ❌ Líder no es vendedor");
                }
            } else {
                $this->info("    ⚠️  Datero sin vendedor (permitido)");
            }
            
            $this->checkUserSubordinates($datero, 'ninguno');
        }
        
        $this->info('✅ Dateros validados correctamente');
        $this->newLine();
    }

    private function testIndependentUsers(): void
    {
        $this->info('🔓 Probando usuarios independientes...');
        
        // Vendedores sin líder
        $vendedoresSinLider = User::role('vendedor')->whereNull('lider_id')->get();
        $this->info("  - Vendedores sin líder: {$vendedoresSinLider->count()}");
        
        // Dateros sin vendedor
        $daterosSinVendedor = User::role('datero')->whereNull('lider_id')->get();
        $this->info("  - Dateros sin vendedor: {$daterosSinVendedor->count()}");
        
        $this->info('✅ Usuarios independientes validados');
        $this->newLine();
    }

    private function checkUserRole(User $user, string $expectedRole): void
    {
        if ($user->hasRole($expectedRole)) {
            $this->info("    ✅ Rol correcto: {$expectedRole}");
        } else {
            $this->error("    ❌ Rol incorrecto. Esperado: {$expectedRole}, Actual: {$user->getRoleName()}");
        }
    }

    private function checkUserLeader(User $user, ?string $expectedLeaderType): void
    {
        if ($expectedLeaderType === null) {
            if ($user->lider_id === null) {
                $this->info("    ✅ Sin líder asignado (correcto)");
            } else {
                $this->error("    ❌ Debería no tener líder");
            }
        } else {
            if ($user->lider_id) {
                $leader = User::find($user->lider_id);
                if ($leader && $leader->hasRole($expectedLeaderType)) {
                    $this->info("    ✅ Líder correcto: {$leader->name} ({$expectedLeaderType})");
                } else {
                    $this->error("    ❌ Líder incorrecto. Esperado: {$expectedLeaderType}");
                }
            } else {
                $this->error("    ❌ Debería tener líder {$expectedLeaderType}");
            }
        }
    }

    private function checkUserSubordinates(User $user, string $expectedType): void
    {
        $subordinates = $user->subordinados;
        
        if ($expectedType === 'ninguno') {
            if ($subordinates->isEmpty()) {
                $this->info("    ✅ Sin subordinados (correcto)");
            } else {
                $this->error("    ❌ No debería tener subordinados");
            }
        } else {
            $expectedCount = $this->getExpectedSubordinatesCount($user, $expectedType);
            $actualCount = $subordinates->count();
            
            if ($actualCount === $expectedCount) {
                $this->info("    ✅ Subordinados correctos: {$actualCount} {$expectedType}");
            } else {
                $this->warn("    ⚠️  Subordinados: {$actualCount} (esperado: {$expectedCount})");
            }
        }
    }

    private function getExpectedSubordinatesCount(User $user, string $type): int
    {
        if ($user->hasRole('admin')) {
            return User::role('lider')->count();
        } elseif ($user->hasRole('lider')) {
            return User::role('vendedor')->where('lider_id', $user->id)->count();
        } elseif ($user->hasRole('vendedor')) {
            return User::role('datero')->where('lider_id', $user->id)->count();
        }
        
        return 0;
    }
}
