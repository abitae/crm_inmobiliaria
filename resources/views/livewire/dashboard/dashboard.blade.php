<div class="min-h-screen bg-gray-50">
    <!-- Header del Dashboard -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Dashboard CRM</h1>
                    <p class="text-sm text-gray-600">Vista general de tu negocio inmobiliario</p>
                </div>
                <div class="flex space-x-2">
                    <flux:button icon="arrow-path" size="xs" variant="outline" wire:click="refreshDashboard">
                        Actualizar
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Clientes -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Clientes</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['clients']['total'] ?? 0) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-green-600 font-medium">+{{ $stats['clients']['new_this_month'] ?? 0 }}</span>
                        <span class="text-gray-500">este mes</span>
                    </div>
                </div>
            </div>

            <!-- Total Proyectos -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Proyectos</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['projects']['total'] ?? 0) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-green-600 font-medium">{{ $stats['projects']['active'] ?? 0 }}</span>
                        <span class="text-gray-500">activos</span>
                    </div>
                </div>
            </div>

            <!-- Total Oportunidades -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Oportunidades</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['opportunities']['total'] ?? 0) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-green-600 font-medium">{{ $stats['opportunities']['active'] ?? 0 }}</span>
                        <span class="text-gray-500">activas</span>
                    </div>
                </div>
            </div>

            <!-- Tasa de Conversión -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Tasa Conversión</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['opportunities']['conversion_rate'] ?? 0 }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-green-600 font-medium">S/ {{ number_format($stats['opportunities']['total_value'] ?? 0) }}</span>
                        <span class="text-gray-500">valor total</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos y Contenido Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Gráfico de Oportunidades por Etapa -->
            <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Oportunidades por Etapa</h3>
                <div class="h-64">
                    <!-- Aquí iría el gráfico con Chart.js o similar -->
                    <div class="flex items-center justify-center h-full text-gray-500">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p>Gráfico de Oportunidades</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actividad Reciente</h3>
                <div class="space-y-4">
                    @forelse($recentActivities as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">{{ $activity['description'] ?? 'Actividad' }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-4">
                        <p>No hay actividad reciente</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tareas Pendientes y Oportunidades -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Tareas Pendientes -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Tareas Pendientes</h3>
                    <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">Ver todas</a>
                </div>
                <div class="space-y-3">
                    @forelse($pendingTasks as $task)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-{{ $task['priority'] === 'urgente' ? 'red' : ($task['priority'] === 'alta' ? 'orange' : 'blue') }}-500 rounded-full"></div>
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
                    <div class="text-center text-gray-500 py-4">
                        <p>No hay tareas pendientes</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Oportunidades que Cierran Pronto -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Cierres Próximos</h3>
                    <a href="{{ route('opportunities.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">Ver todas</a>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingClosings as $opportunity)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $opportunity['client']['first_name'] ?? '' }} {{ $opportunity['client']['last_name'] ?? '' }}</p>
                            <p class="text-xs text-gray-500">{{ $opportunity['project']['name'] ?? 'Proyecto' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">S/ {{ number_format($opportunity['expected_value'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($opportunity['expected_close_date'])->format('M d') }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-4">
                        <p>No hay cierres próximos</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
