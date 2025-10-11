<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TestChartLoading extends Command
{
    protected $signature = 'test:chart-loading';
    protected $description = 'Verificar la carga de scripts de gráficos';

    public function handle()
    {
        $this->info('🔍 Verificando carga de scripts de gráficos...');
        $this->newLine();

        // Verificar si Chart.js está incluido en el head
        $headFile = resource_path('views/partials/head.blade.php');
        if (File::exists($headFile)) {
            $headContent = File::get($headFile);
            if (strpos($headContent, 'chart.js') !== false) {
                $this->info('✅ Chart.js está incluido en head.blade.php');
            } else {
                $this->error('❌ Chart.js NO está incluido en head.blade.php');
            }
        } else {
            $this->error('❌ Archivo head.blade.php no encontrado');
        }

        // Verificar si el archivo de gráficos existe
        $chartsFile = public_path('js/dashboard-charts.js');
        if (File::exists($chartsFile)) {
            $this->info('✅ Archivo dashboard-charts.js existe');
            $fileSize = File::size($chartsFile);
            $this->info("   Tamaño: {$fileSize} bytes");
        } else {
            $this->error('❌ Archivo dashboard-charts.js NO existe');
        }

        // Verificar el dashboard
        $dashboardFile = resource_path('views/livewire/dashboard/dashboard.blade.php');
        if (File::exists($dashboardFile)) {
            $dashboardContent = File::get($dashboardFile);
            
            if (strpos($dashboardContent, 'dashboard-charts.js') !== false) {
                $this->info('✅ dashboard-charts.js está incluido en el dashboard');
            } else {
                $this->error('❌ dashboard-charts.js NO está incluido en el dashboard');
            }

            if (strpos($dashboardContent, 'DOMContentLoaded') !== false) {
                $this->info('✅ Evento DOMContentLoaded está presente');
            } else {
                $this->error('❌ Evento DOMContentLoaded NO está presente');
            }

            if (strpos($dashboardContent, 'waitForCharts') !== false) {
                $this->info('✅ Función waitForCharts está presente');
            } else {
                $this->error('❌ Función waitForCharts NO está presente');
            }
        } else {
            $this->error('❌ Archivo dashboard.blade.php no encontrado');
        }

        $this->newLine();
        $this->info('💡 Para debuggear en el navegador:');
        $this->info('   1. Abre http://localhost:8000/dashboard');
        $this->info('   2. Presiona F12 para abrir DevTools');
        $this->info('   3. Ve a la pestaña "Console"');
        $this->info('   4. Busca estos mensajes:');
        $this->info('      - "🚀 Dashboard cargado, inicializando gráficos..."');
        $this->info('      - "📊 Datos de gráficos: {...}"');
        $this->info('      - "⏳ Esperando Chart.js y DashboardCharts..."');
        $this->info('      - "✅ Chart.js y DashboardCharts disponibles"');
        $this->info('   5. Si no aparecen, hay un problema de carga');
    }
}
