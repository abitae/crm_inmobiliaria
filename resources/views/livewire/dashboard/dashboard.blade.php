<div class="min-h-screen bg-gray-50">
    <!-- Header del Dashboard -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard CRM Inmobiliario</h1>
                    <p class="text-sm text-gray-600">Vista general de tu negocio inmobiliario</p>
                </div>
                
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                <div>
                    <flux:input size="xs" type="date" label="Fecha inicio" wire:model.live="startDate" />
                </div>
                <div>
                    <flux:input size="xs" type="date" label="Fecha fin" wire:model.live="endDate" />
                </div>
                <div class="md:col-span-2 flex gap-2 justify-end">
                    <flux:button size="xs" variant="outline" icon="x-mark" wire:click="clearFilters" wire:loading.attr="disabled">Limpiar</flux:button>
                    <flux:button size="xs" icon="magnifying-glass" wire:click="refreshDashboard" wire:loading.attr="disabled">Aplicar</flux:button>
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

            <!-- Fila 3: Actividad y Tareas -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Actividad Reciente -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Actividad Reciente</h3>
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    </div>
                    <div class="space-y-4 max-h-80 overflow-y-auto">
                        @forelse($recentActivities as $activity)
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
                        @forelse($pendingTasks as $task)
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
                        @forelse($upcomingClosings as $closing)
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Instancias persistentes de gráficos
    const charts = { opportunities: null, clients: null, sellers: null, performance: null };
    // Debounce de recarga cuando cambian fechas
    let refreshTimer = null;
    window.addEventListener('dashboard-schedule-refresh', () => {
        if (refreshTimer) clearTimeout(refreshTimer);
        refreshTimer = setTimeout(() => {
            window.Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'))?.call('loadDashboardData');
        }, 400);
    });

    // Indicadores de carga global
    window.addEventListener('dashboard-loading', (e) => {
        const isLoading = e.detail === true;
        const buttons = document.querySelectorAll('button[wire\\:click="refreshDashboard"], button[wire\\:click="clearFilters"]');
        buttons.forEach(btn => {
            if (isLoading) btn.setAttribute('disabled', 'disabled'); else btn.removeAttribute('disabled');
        });
    });
    // Gráfico de Oportunidades por Etapa
    const opportunitiesCtx = document.getElementById('opportunitiesChart');
    if (opportunitiesCtx) {
        const opportunitiesData = @json($opportunitiesByStage);
        charts.opportunities = new Chart(opportunitiesCtx, {
            type: 'doughnut',
            data: {
                labels: opportunitiesData.map(item => item.stage),
                datasets: [{
                    data: opportunitiesData.map(item => item.count),
                    backgroundColor: [
                        '#3B82F6', // blue
                        '#10B981', // green
                        '#F59E0B', // yellow
                        '#EF4444', // red
                        '#8B5CF6', // purple
                        '#F97316', // orange
                        '#06B6D4'  // cyan
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' oportunidades';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true
                }
            }
        });
    }

    // Gráfico de Clientes por Estado
    const clientsCtx = document.getElementById('clientsChart');
    if (clientsCtx) {
        const clientsData = @json($clientsByStatus);
        charts.clients = new Chart(clientsCtx, {
            type: 'pie',
            data: {
                labels: clientsData.map(item => item.status),
                datasets: [{
                    data: clientsData.map(item => item.count),
                    backgroundColor: [
                        '#3B82F6', // blue
                        '#10B981', // green
                        '#F59E0B', // yellow
                        '#EF4444', // red
                        '#8B5CF6'  // purple
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' clientes';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true
                }
            }
        });
    }

    // Gráfico de Oportunidades Cerradas por Vendedor
    const sellersCtx = document.getElementById('sellersChart');
    if (sellersCtx) {
        const sellersData = @json($closedOpportunitiesBySeller);
        charts.sellers = new Chart(sellersCtx, {
            type: 'bar',
            data: {
                labels: sellersData.map(item => item.name),
                datasets: [{
                    label: 'Ventas Totales (S/)',
                    data: sellersData.map(item => item.total_sales),
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: 'rgba(34, 197, 94, 1)'
                }, {
                    label: 'Oportunidades Cerradas',
                    data: sellersData.map(item => item.closed_opportunities),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    yAxisID: 'y1',
                    hoverBackgroundColor: 'rgba(59, 130, 246, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Ventas Totales (S/)',
                            font: {
                                weight: '600'
                            }
                        },
                        grid: {
                            drawOnChartArea: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'S/ ' + value.toLocaleString();
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Oportunidades Cerradas',
                            font: {
                                weight: '600'
                            }
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Vendedores',
                            font: {
                                weight: '600'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                weight: '600'
                            },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 0) {
                                    return 'Ventas: S/ ' + context.parsed.y.toLocaleString();
                                } else {
                                    return 'Oportunidades: ' + context.parsed.y;
                                }
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    // Gráfico de Rendimiento por Asesor
    const performanceCtx = document.getElementById('performanceChart');
    if (performanceCtx) {
        const performanceData = @json($advisorPerformance);
        charts.performance = new Chart(performanceCtx, {
            type: 'radar',
            data: {
                labels: performanceData.map(item => item.name),
                datasets: [{
                    label: 'Oportunidades Totales',
                    data: performanceData.map(item => item.total_opportunities),
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    pointBorderColor: '#ffffff',
                    pointHoverBackgroundColor: '#ffffff',
                    pointHoverBorderColor: 'rgba(59, 130, 246, 1)',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }, {
                    label: 'Oportunidades Ganadas',
                    data: performanceData.map(item => item.won_opportunities),
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                    pointBorderColor: '#ffffff',
                    pointHoverBackgroundColor: '#ffffff',
                    pointHoverBorderColor: 'rgba(34, 197, 94, 1)',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        pointLabels: {
                            font: {
                                size: 11,
                                weight: '500'
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                weight: '600'
                            },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.r;
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }
    // Actualizar gráficos cuando Livewire informe nuevos datos
    window.addEventListener('dashboard-data-updated', (event) => {
        const data = event.detail || {};
        if (charts.opportunities && Array.isArray(data.opportunitiesByStage)) {
            charts.opportunities.data.labels = data.opportunitiesByStage.map(i => i.stage);
            charts.opportunities.data.datasets[0].data = data.opportunitiesByStage.map(i => i.count);
            charts.opportunities.update();
        }
        if (charts.clients && Array.isArray(data.clientsByStatus)) {
            charts.clients.data.labels = data.clientsByStatus.map(i => i.status);
            charts.clients.data.datasets[0].data = data.clientsByStatus.map(i => i.count);
            charts.clients.update();
        }
        if (charts.sellers && Array.isArray(data.closedOpportunitiesBySeller)) {
            const sellers = data.closedOpportunitiesBySeller;
            charts.sellers.data.labels = sellers.map(i => i.name);
            charts.sellers.data.datasets[0].data = sellers.map(i => i.total_sales);
            charts.sellers.data.datasets[1].data = sellers.map(i => i.closed_opportunities);
            charts.sellers.update();
        }
        if (charts.performance && Array.isArray(data.advisorPerformance)) {
            const perf = data.advisorPerformance;
            charts.performance.data.labels = perf.map(i => i.name);
            charts.performance.data.datasets[0].data = perf.map(i => i.total_opportunities);
            charts.performance.data.datasets[1].data = perf.map(i => i.won_opportunities);
            charts.performance.update();
        }
    });
});
</script>
