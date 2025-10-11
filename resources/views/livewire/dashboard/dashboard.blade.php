<div class="min-h-screen bg-gray-50">
    <!-- Header del Dashboard -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard CRM Inmobiliario</h1>
                    <p class="text-sm text-gray-600">
                        Vista general de tu negocio inmobiliario
                        @auth
                            - {{ auth()->user()->getRoleName() ? ucfirst(auth()->user()->getRoleName()) : 'Usuario' }}
                        @endauth
                    </p>
                </div>
                @auth
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ $this->getUserRoleDescription() }}</p>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Mensajes de notificación -->
        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        @endif
        
        @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif
        
        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-end">
                <div>
                    <flux:input 
                        size="xs" 
                        type="date" 
                        label="Fecha inicio" 
                        wire:model.live="startDate"
                        placeholder="Selecciona fecha de inicio"
                    />
                </div>
                <div>
                    <flux:input 
                        size="xs" 
                        type="date" 
                        label="Fecha fin" 
                        wire:model.live="endDate"
                        placeholder="Selecciona fecha de fin"
                    />
                </div>
            </div>
        </div>

        <!-- Tarjetas de Estadísticas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Clientes -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Clientes</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['clients']['total'] ?? 0) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-3 border-t border-blue-200">
                    <div class="text-sm">
                        <span class="text-blue-700 font-semibold">+{{ $stats['clients']['new_this_month'] ?? 0 }}</span>
                        <span class="text-blue-600"> este mes</span>
                    </div>
                </div>
            </div>

            <!-- Total Proyectos -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Proyectos</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['projects']['total'] ?? 0) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-3 border-t border-green-200">
                    <div class="text-sm">
                        <span class="text-green-700 font-semibold">{{ $stats['projects']['active'] ?? 0 }}</span>
                        <span class="text-green-600"> activos</span>
                    </div>
                </div>
            </div>

            <!-- Total Oportunidades -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Oportunidades</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['opportunities']['total'] ?? 0) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 px-6 py-3 border-t border-yellow-200">
                    <div class="text-sm">
                        <span class="text-yellow-700 font-semibold">{{ $stats['opportunities']['active'] ?? 0 }}</span>
                        <span class="text-yellow-600"> activas</span>
                    </div>
                </div>
            </div>

            <!-- Tasa de Conversión -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Tasa Conversión</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['opportunities']['conversion_rate'] ?? 0 }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-3 border-t border-purple-200">
                    <div class="text-sm">
                        <span class="text-purple-700 font-semibold">S/ {{ number_format($stats['opportunities']['total_value'] ?? 0) }}</span>
                        <span class="text-purple-600"> valor total</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección Principal de Gráficos -->
        <div class="space-y-8">
            <!-- Fila 1: Gráficos de Distribución -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Gráfico de Oportunidades por Etapa -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Oportunidades por Etapa</h3>
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    </div>
                    <div class="h-80">
                        <canvas id="opportunitiesChart"></canvas>
                    </div>
                </div>

                <!-- Gráfico de Clientes por Estado -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Distribución de Clientes</h3>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <div class="h-80">
                        <canvas id="clientsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Fila 2: Gráfico de Rendimiento de Vendedores a ancho completo -->
            <div class="grid grid-cols-1 gap-8">
                <!-- Gráfico de Oportunidades Cerradas por Vendedor -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Rendimiento de Vendedores</h3>
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    </div>
                    <div class="h-80">
                        <canvas id="sellersChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Fila 3: Gráfico de Rendimiento de Asesores a ancho completo -->
            <div class="grid grid-cols-1 gap-8">
                <!-- Gráfico de Rendimiento de Asesores -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Rendimiento de Asesores</h3>
                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                    </div>
                    <div class="h-80">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Fila 4: Gráfico de Rendimiento de Líderes a ancho completo -->
            <div class="grid grid-cols-1 gap-8">
                <!-- Gráfico de Rendimiento de Líderes -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Rendimiento de Líderes</h3>
                        <div class="w-3 h-3 bg-indigo-500 rounded-full"></div>
                    </div>
                    <div class="h-80">
                        <canvas id="leadersChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Fila 5: Actividad y Tareas -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Actividad Reciente -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Actividad Reciente</h3>
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    </div>
                    <div class="space-y-4 max-h-80 overflow-y-auto">
                        @forelse($listData['recentActivities'] as $activity)
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $activity['description'] ?? 'Actividad' }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-gray-500 py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm">No hay actividad reciente</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tareas Pendientes -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Tareas Pendientes</h3>
                        <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">Ver todas</a>
                    </div>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @forelse($listData['pendingTasks'] as $task)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-{{ $task['priority'] === 'urgente' ? 'red' : ($task['priority'] === 'alta' ? 'orange' : 'blue') }}-500 rounded-full"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $task['title'] }}</p>
                                    <p class="text-xs text-gray-500">Vence: {{ \Carbon\Carbon::parse($task['due_date'])->format('M d') }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $task['priority'] === 'urgente' ? 'red' : ($task['priority'] === 'alta' ? 'orange' : 'blue') }}-100 text-{{ $task['priority'] === 'urgente' ? 'red' : ($task['priority'] === 'alta' ? 'orange' : 'blue') }}-800">
                                {{ ucfirst($task['priority']) }}
                            </span>
                        </div>
                        @empty
                        <div class="text-center text-gray-500 py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-sm">No hay tareas pendientes</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Oportunidades que Cierran Pronto -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Cierres Próximos</h3>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @forelse($listData['upcomingClosings'] as $closing)
                        <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-900">{{ $closing['client']['name'] ?? 'Cliente' }}</p>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($closing['expected_close_date'])->format('M d') }}</span>
                            </div>
                            <p class="text-xs text-gray-600">{{ $closing['project']['name'] ?? 'Proyecto' }}</p>
                            <p class="text-sm font-semibold text-green-600 mt-1">S/ {{ number_format($closing['close_value'] ?? 0) }}</p>
                        </div>
                        @empty
                        <div class="text-center text-gray-500 py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm">No hay cierres próximos</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir el archivo JavaScript de gráficos -->
<script src="{{ asset('js/dashboard-charts-simple.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Dashboard cargado, inicializando gráficos...');
    
    // Datos de gráficos desde Livewire
    const chartData = {
        opportunitiesByStage: @json($chartData['opportunitiesByStage']),
        clientsByStatus: @json($chartData['clientsByStatus']),
        closedOpportunitiesBySeller: @json($chartData['closedOpportunitiesBySeller']),
        advisorPerformance: @json($chartData['advisorPerformance']),
        leaderPerformance: @json($chartData['leaderPerformance'])
    };
    
    console.log('📊 Datos de gráficos:', chartData);
    
    // Función simple para inicializar gráficos
    function initCharts() {
        console.log('🚀 Inicializando gráficos...');
        console.log('Chart disponible:', typeof Chart !== 'undefined');
        console.log('DashboardChartsSimple disponible:', typeof window.DashboardChartsSimple !== 'undefined');
        
        if (typeof Chart !== 'undefined' && window.DashboardChartsSimple) {
            console.log('✅ Chart.js y DashboardChartsSimple disponibles, inicializando gráficos...');
            try {
                window.DashboardChartsSimple.initChartsSimple(chartData);
                console.log('✅ Gráficos inicializados correctamente');
            } catch (error) {
                console.error('❌ Error al inicializar gráficos:', error);
            }
        } else {
            console.error('❌ Chart.js o DashboardChartsSimple no están disponibles');
            console.log('Chart disponible:', typeof Chart !== 'undefined');
            console.log('DashboardChartsSimple disponible:', typeof window.DashboardChartsSimple !== 'undefined');
        }
    }
    
    // Event listeners para actualizaciones
    window.addEventListener('dashboard-data-updated', (event) => {
        console.log('🔄 Actualizando gráficos desde evento...');
        if (window.DashboardChartsSimple) {
            window.DashboardChartsSimple.initChartsSimple(event.detail || chartData);
        }
    });

    document.addEventListener('livewire:updated', (event) => {
        console.log('🔄 Livewire actualizado, refrescando gráficos...');
        setTimeout(() => {
            if (window.DashboardChartsSimple) {
                window.DashboardChartsSimple.initChartsSimple(chartData);
            }
        }, 200);
    });
    
    // Manejar indicadores de carga
    window.addEventListener('dashboard-loading', (e) => {
        const isLoading = e.detail === true;
        const loadingIndicator = document.querySelector('.loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.style.display = isLoading ? 'block' : 'none';
        }
    });
    
    // Inicializar gráficos inmediatamente
    console.log('🚀 Iniciando proceso de carga de gráficos...');
    setTimeout(() => initCharts(), 100);
});
</script>
