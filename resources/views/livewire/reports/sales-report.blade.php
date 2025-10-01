<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header -->
    <div class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard de Ventas</h1>
                    <p class="text-sm text-gray-600 mt-1">Análisis en tiempo real y métricas de rendimiento</p>
                </div>
                <div class="flex space-x-3">
                    <flux:button size="sm" icon="arrow-down-tray" wire:click="exportReport" class="bg-blue-600 hover:bg-blue-700">
                        Exportar PDF
                    </flux:button>
                    <flux:button size="sm" icon="table-cells" variant="outline" wire:click="exportExcel">
                        Exportar Excel
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtros Compactos -->
        <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-sm border border-gray-200/50 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div>
                    <flux:input size="xs" type="date" label="Desde" wire:model.live="startDate" />
                </div>
                <div>
                    <flux:input size="xs" type="date" label="Hasta" wire:model.live="endDate" max="{{ now()->format('Y-m-d') }}" readonly />
                </div>
                <div>
                    <flux:select size="xs" label="Proyecto" wire:model.live="projectFilter">
                        <option value="">Todos</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" label="Asesor" wire:model.live="advisorFilter">
                        <option value="">Todos</option>
                        @foreach($advisors as $advisor)
                            <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:input size="xs" icon="magnifying-glass" type="search" label="Buscar" placeholder="Cliente o proyecto" wire:model.live="search" />
                </div>
                <div>
                    <flux:select size="xs" icon="users" label="Cliente" wire:model.live="clientFilter">
                        <option value="">Todos</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <flux:select size="xs" label="Estado" wire:model.live="statusFilter">
                        <option value="">Todos</option>
                        <option value="registrado">Registrado</option>
                        <option value="reservado">Reservado</option>
                        <option value="cuotas">Cuotas</option>
                        <option value="pagado">Pagado</option>
                        <option value="transferido">Transferido</option>
                        <option value="cancelado">Cancelado</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" label="Etapa" wire:model.live="typeFilter">
                        <option value="">Todas</option>
                        <option value="calificado">Calificado</option>
                        <option value="visita">Visita</option>
                        <option value="cierre">Cierre</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- Métricas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-700">Ventas Totales</p>
                        <p class="text-3xl font-bold text-blue-900">S/ {{ number_format($salesData['total_sales'] ?? 0) }}</p>
                        <p class="text-sm text-blue-600 mt-1">{{ $salesData['total_opportunities'] ?? 0 }} negocios</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                        <flux:icon name="currency-dollar" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-700">Promedio por Venta</p>
                        <p class="text-3xl font-bold text-green-900">S/ {{ number_format($salesData['average_deal_size'] ?? 0) }}</p>
                        <p class="text-sm text-green-600 mt-1">Valor promedio</p>
                    </div>
                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                        <flux:icon name="chart-bar" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-700">Tasa Conversión</p>
                        <p class="text-3xl font-bold text-purple-900">{{ $conversionMetrics['win_rate'] ?? 0 }}%</p>
                        <p class="text-sm text-purple-600 mt-1">{{ $conversionMetrics['won_opportunities'] ?? 0 }} de {{ $conversionMetrics['total_opportunities'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                        <flux:icon name="funnel" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-orange-700">Clientes Únicos</p>
                        <p class="text-3xl font-bold text-orange-900">{{ number_format($salesData['total_clients'] ?? 0) }}</p>
                        <p class="text-sm text-orange-600 mt-1">Clientes activos</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center">
                        <flux:icon name="user-group" class="w-6 h-6 text-white" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico Destacado -->
        <div class="mb-8">
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 p-8">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Tendencia de Ventas</h3>
                    <p class="text-sm text-gray-600">Evolución de los últimos 6 meses</p>
                </div>
                <div class="h-80">
                    <canvas id="salesByMonthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabla Minimalista de Ventas -->
        <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200/50">
                <h3 class="text-lg font-semibold text-gray-900">Ventas Recientes</h3>
            </div>
            
            @if($isLoading)
                <div class="p-8 text-center">
                    <div class="inline-flex items-center space-x-2">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                        <span class="text-gray-600">Cargando datos...</span>
                    </div>
                </div>
            @else
            <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Proyecto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Asesor</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase">Valor</th>
                        </tr>
                    </thead>
                        <tbody class="divide-y divide-gray-100">
                        @forelse($salesDetails as $sale)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                            <span class="text-sm font-semibold text-white">
                                            {{ strtoupper(substr($sale->client->name ?? 'C', 0, 1)) }}
                                        </span>
                                    </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $sale->client->name ?? '' }}</div>
                                        <div class="text-xs text-gray-500">{{ $sale->client->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                                <td class="px-4 py-3">
                                <div class="text-sm text-gray-900">{{ $sale->project->name ?? 'N/A' }}</div>
                            </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ $sale->advisor->name ?? 'N/A' }}</div>
                            </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-500">
                                {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y') : 'N/A' }}
                                    </div>
                            </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="text-sm font-semibold text-gray-900">S/ {{ number_format($sale->sale_value) }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                        <flux:icon name="chart-bar" class="w-12 h-12 text-gray-300 mb-3" />
                                        <p class="text-sm">No hay datos de ventas para el período seleccionado</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($salesDetails->hasPages())
                <div class="px-6 py-4 border-t border-gray-200/50">
                {{ $salesDetails->links() }}
            </div>
                @endif
            @endif
        </div>

        <script>
            document.addEventListener('livewire:init', () => {
                let salesByMonthChart = null;

                const renderChart = (salesChartData) => {
                    console.log('Rendering chart with data:', salesChartData);
                    
                    const monthCtx = document.getElementById('salesByMonthChart');

                    // Gráfico de ventas por mes
                    if (monthCtx) {
                        if (salesByMonthChart) {
                            salesByMonthChart.destroy();
                        }
                        
                        if (salesChartData && salesChartData.labels && salesChartData.labels.length > 0) {
                            salesByMonthChart = new Chart(monthCtx, {
                                type: 'line',
                                data: {
                                    labels: salesChartData.labels,
                                    datasets: [
                                        {
                                            label: 'Ventas (S/)',
                                            data: salesChartData.sales || [],
                                            borderColor: '#2563eb',
                                            backgroundColor: 'rgba(37, 99, 235, 0.15)',
                                            tension: 0.3,
                                            fill: true,
                                        },
                                        {
                                            label: 'Negocios',
                                            data: salesChartData.deals || [],
                                            borderColor: '#10b981',
                                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                                            tension: 0.3,
                                            fill: true,
                                            yAxisID: 'y1',
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: { 
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function(value) {
                                                    return 'S/ ' + value.toLocaleString();
                                                }
                                            }
                                        },
                                        y1: { 
                                            beginAtZero: true, 
                                            position: 'right', 
                                            grid: { drawOnChartArea: false } 
                                        }
                                    },
                                    plugins: { 
                                        legend: { position: 'bottom' },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    if (context.datasetIndex === 0) {
                                                        return 'Ventas: S/ ' + context.parsed.y.toLocaleString();
                                                    }
                                                    return 'Negocios: ' + context.parsed.y;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        } else {
                            // Mostrar mensaje si no hay datos
                            monthCtx.style.display = 'none';
                            const noDataDiv = monthCtx.parentNode.querySelector('.no-data') || 
                                monthCtx.parentNode.insertAdjacentHTML('beforeend', 
                                    '<div class="no-data flex items-center justify-center h-full text-gray-500"><p>No hay datos disponibles</p></div>');
                        }
                    }
                };

                // Render inicial con datos del servidor
                const initialSalesData = @json($salesChartData ?? ['labels'=>[], 'sales'=>[], 'deals'=>[]]);
                
                console.log('Initial data:', initialSalesData);
                renderChart(initialSalesData);

                // Escuchar actualizaciones desde Livewire
                window.addEventListener('sales-report-update-charts', (e) => {
                    console.log('Chart update event received:', e.detail);
                    renderChart(e.detail.salesChartData);
                });
            });
        </script>

        <!-- Resumen por Asesor - Minimalista -->
        <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200/50">
                <h3 class="text-lg font-semibold text-gray-900">Rendimiento por Asesor</h3>
            </div>
            
            @if($isLoading)
                <div class="p-8 text-center">
                    <div class="inline-flex items-center space-x-2">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                        <span class="text-gray-600">Cargando datos...</span>
                    </div>
                </div>
            @else
            <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Asesor</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase">Ventas</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase">Valor Total</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase">Comisión</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase">Conversión</th>
                        </tr>
                    </thead>
                        <tbody class="divide-y divide-gray-100">
                        @forelse($advisorSummary as $advisor)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                            <span class="text-sm font-semibold text-white">
                                            {{ strtoupper(substr($advisor->name ?? 'A', 0, 1)) }}
                                        </span>
                                    </div>
                                        <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $advisor->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $advisor->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="text-sm font-semibold text-gray-900">{{ $advisor->sales_count ?? 0 }}</div>
                            </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="text-sm font-semibold text-gray-900">S/ {{ number_format($advisor->total_value ?? 0) }}</div>
                            </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="text-sm font-semibold text-green-600">S/ {{ number_format($advisor->total_commission ?? 0) }}</div>
                            </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ $advisor->conversion_rate ?? 0 }}%</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="user-group" class="w-12 h-12 text-gray-300 mb-3" />
                                        <p class="text-sm">No hay datos de asesores disponibles</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
