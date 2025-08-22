<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Reportes de Ventas</h1>
                    <p class="text-sm text-gray-600">Análisis y métricas de ventas</p>
                </div>
                <div class="flex space-x-2">
                    <x-filament::button size="xs" wire:click="exportReport">
                        <x-filament::icon name="heroicon-o-arrow-down-tray" class="w-4 h-4 mr-1" />
                        Exportar PDF
                    </x-filament::button>
                    <x-filament::button size="xs" wire:click="exportExcel">
                        <x-filament::icon name="heroicon-o-table-cells" class="w-4 h-4 mr-1" />
                        Exportar Excel
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtros de Fecha -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Desde</label>
                    <x-filament::input.wrapper>
                        <x-filament::input 
                            type="date" 
                            wire:model.live="dateFrom" 
                            size="xs"
                        />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Hasta</label>
                    <x-filament::input.wrapper>
                        <x-filament::input 
                            type="date" 
                            wire:model.live="dateTo" 
                            size="xs"
                        />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Proyecto</label>
                    <x-filament::select wire:model.live="projectFilter" size="xs">
                        <option value="">Todos los proyectos</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </x-filament::select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asesor</label>
                    <x-filament::select wire:model.live="advisorFilter" size="xs">
                        <option value="">Todos los asesores</option>
                        @foreach($advisors as $advisor)
                            <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                        @endforeach
                    </x-filament::select>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <x-filament::button size="xs" color="primary" wire:click="generateReport">
                    <x-filament::icon name="heroicon-o-chart-bar" class="w-4 h-4 mr-1" />
                    Generar Reporte
                </x-filament::button>
            </div>
        </div>

        <!-- Métricas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <x-filament::icon name="heroicon-o-currency-dollar" class="w-6 h-6 text-blue-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Ventas Totales</p>
                        <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($metrics['total_sales'] ?? 0) }}</p>
                        <p class="text-sm text-green-600">+{{ $metrics['sales_growth'] ?? 0 }}% vs mes anterior</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <x-filament::icon name="heroicon-o-shopping-cart" class="w-6 h-6 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Unidades Vendidas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($metrics['units_sold'] ?? 0) }}</p>
                        <p class="text-sm text-green-600">+{{ $metrics['units_growth'] ?? 0 }}% vs mes anterior</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <x-filament::icon name="heroicon-o-funnel" class="w-6 h-6 text-yellow-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Tasa Conversión</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $metrics['conversion_rate'] ?? 0 }}%</p>
                        <p class="text-sm text-green-600">+{{ $metrics['conversion_growth'] ?? 0 }}% vs mes anterior</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <x-filament::icon name="heroicon-o-user-group" class="w-6 h-6 text-purple-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Clientes Nuevos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($metrics['new_clients'] ?? 0) }}</p>
                        <p class="text-sm text-green-600">+{{ $metrics['clients_growth'] ?? 0 }}% vs mes anterior</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Gráfico de Ventas por Mes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Ventas por Mes</h3>
                    <x-filament::select wire:model.live="salesChartPeriod" size="xs">
                        <option value="6">Últimos 6 meses</option>
                        <option value="12">Últimos 12 meses</option>
                        <option value="24">Últimos 24 meses</option>
                    </x-filament::select>
                </div>
                <div class="h-64">
                    <!-- Aquí iría el gráfico con Chart.js -->
                    <div class="flex items-center justify-center h-full text-gray-500">
                        <div class="text-center">
                            <x-filament::icon name="heroicon-o-chart-bar" class="w-16 h-16 mx-auto mb-4 text-gray-300" />
                            <p>Gráfico de Ventas Mensuales</p>
                            <p class="text-sm text-gray-400">S/ {{ number_format($metrics['total_sales'] ?? 0) }} total</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Ventas por Proyecto -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Ventas por Proyecto</h3>
                    <x-filament::select wire:model.live="projectChartLimit" size="xs">
                        <option value="5">Top 5</option>
                        <option value="10">Top 10</option>
                        <option value="all">Todos</option>
                    </x-filament::select>
                </div>
                <div class="h-64">
                    <!-- Aquí iría el gráfico de barras -->
                    <div class="flex items-center justify-center h-full text-gray-500">
                        <div class="text-center">
                            <x-filament::icon name="heroicon-o-building-office" class="w-16 h-16 mx-auto mb-4 text-gray-300" />
                            <p>Gráfico de Ventas por Proyecto</p>
                            <p class="text-sm text-gray-400">{{ count($projectSales ?? []) }} proyectos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Ventas Detalladas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Ventas Detalladas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Proyecto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unidad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Asesor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha Venta
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Comisión
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($salesDetails as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-blue-600">
                                            {{ strtoupper(substr($sale->client->first_name ?? 'C', 0, 1) . substr($sale->client->last_name ?? 'L', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $sale->client->first_name ?? '' }} {{ $sale->client->last_name ?? '' }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $sale->client->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $sale->project->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $sale->project->type ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $sale->unit->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $sale->advisor->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                S/ {{ number_format($sale->sale_value) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                S/ {{ number_format($sale->commission_value ?? 0) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <x-filament::icon name="heroicon-o-chart-bar" class="w-12 h-12 text-gray-300 mb-2" />
                                    <p>No hay datos de ventas para el período seleccionado</p>
                                    <x-filament::button size="xs" color="primary" class="mt-2" wire:click="generateReport">
                                        Generar reporte
                                    </x-filament::button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($salesDetails->hasPages())
            <div class="bg-white px-6 py-3 border-t border-gray-200">
                {{ $salesDetails->links() }}
            </div>
            @endif
        </div>

        <!-- Resumen por Asesor -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Resumen por Asesor</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Asesor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ventas
                            </th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor Total
                            </th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Comisiones
                            </th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tasa Conversión
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($advisorSummary as $advisor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-green-600">
                                            {{ strtoupper(substr($advisor->name ?? 'A', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $advisor->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $advisor->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $advisor->sales_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                S/ {{ number_format($advisor->total_value ?? 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                S/ {{ number_format($advisor->total_commission ?? 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                {{ $advisor->conversion_rate ?? 0 }}%
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No hay datos de asesores disponibles
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
