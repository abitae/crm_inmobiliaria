<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OpenDashboard extends Command
{
    protected $signature = 'open:dashboard';
    protected $description = 'Abrir el dashboard en el navegador para probar los gráficos';

    public function handle()
    {
        $this->info('🚀 Abriendo dashboard en el navegador...');
        $this->newLine();
        
        $url = 'http://localhost:8000/dashboard';
        
        $this->info("📱 URL del dashboard: {$url}");
        $this->newLine();
        
        $this->info('🔍 Para debuggear los gráficos:');
        $this->info('   1. Abre las herramientas de desarrollador (F12)');
        $this->info('   2. Ve a la pestaña "Console"');
        $this->info('   3. Busca mensajes que empiecen con "===" o "Chart"');
        $this->info('   4. Verifica que no haya errores en rojo');
        $this->newLine();
        
        $this->info('📊 Los gráficos deberían aparecer en:');
        $this->info('   - Oportunidades por Etapa (gráfico de barras)');
        $this->info('   - Distribución de Clientes (gráfico de barras)');
        $this->info('   - Rendimiento de Vendedores (gráfico de barras)');
        $this->info('   - Rendimiento de Asesores (gráfico de líneas)');
        $this->info('   - Rendimiento de Líderes (gráfico de barras)');
        $this->newLine();
        
        // Intentar abrir el navegador (Windows)
        if (PHP_OS_FAMILY === 'Windows') {
            $this->info('🌐 Abriendo navegador...');
            exec("start {$url}");
        } else {
            $this->info('🌐 Abre manualmente: {$url}');
        }
        
        $this->newLine();
        $this->info('💡 Si los gráficos no aparecen, revisa la consola del navegador');
        $this->info('   para ver los mensajes de debug que agregamos.');
    }
}
