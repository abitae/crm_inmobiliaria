<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Mis Comisiones</h1>
                    <p class="text-sm text-gray-600">Consulta tus comisiones y pagos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Comisiones</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pendientes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pendiente'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pagadas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pagada'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Pagado</p>
                        <p class="text-2xl font-semibold text-gray-900">S/ {{ number_format($stats['total_pagado'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar por proyecto o cliente..." />
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="aprobada">Aprobada</option>
                        <option value="pagada">Pagada</option>
                        <option value="cancelada">Cancelada</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="typeFilter">
                        <option value="">Todos los tipos</option>
                        <option value="venta">Venta</option>
                        <option value="reserva">Reserva</option>
                        <option value="seguimiento">Seguimiento</option>
                        <option value="bono">Bono</option>
                    </flux:select>
                </div>
                <div>
                    <flux:button size="xs" wire:click="clearFilters">Limpiar Filtros</flux:button>
                </div>
            </div>
        </div>

        <!-- Tabla de Comisiones -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Base</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comisión</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($commissions as $commission)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $commission->project->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($commission->commission_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    S/ {{ number_format($commission->base_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    S/ {{ number_format($commission->commission_amount, 2) }}
                                    <span class="text-xs text-gray-500">({{ $commission->commission_percentage }}%)</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($commission->bonus_amount > 0)
                                        <span class="text-green-600 font-semibold">+ S/ {{ number_format($commission->bonus_amount, 2) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    S/ {{ number_format($commission->total_commission, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $commission->status_color }}-100 text-{{ $commission->status_color }}-800">
                                        {{ ucfirst($commission->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $commission->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <flux:button size="xs" wire:click="openDetailModal({{ $commission->id }})">
                                        Ver Detalle
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No tienes comisiones registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $commissions->links() }}
            </div>
        </div>

        <!-- Resumen de Totales -->
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen Financiero</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-blue-600">Total Pendiente por Cobrar</p>
                    <p class="text-2xl font-bold text-blue-900">S/ {{ number_format($stats['total_pendiente'], 2) }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-green-600">Total Pagado</p>
                    <p class="text-2xl font-bold text-green-900">S/ {{ number_format($stats['total_pagado'], 2) }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-purple-600">Total General</p>
                    <p class="text-2xl font-bold text-purple-900">S/ {{ number_format($stats['total_pagado'] + $stats['total_pendiente'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalle -->
    @if($showDetailModal && $selectedCommission)
        <flux:modal wire:model="showDetailModal" name="commission-detail-datero">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Detalle de Comisión</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Proyecto</p>
                            <p class="text-sm text-gray-900">{{ $selectedCommission->project->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tipo de Comisión</p>
                            <p class="text-sm text-gray-900">{{ ucfirst($selectedCommission->commission_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Estado</p>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $selectedCommission->status_color }}-100 text-{{ $selectedCommission->status_color }}-800">
                                {{ ucfirst($selectedCommission->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Fecha de Creación</p>
                            <p class="text-sm text-gray-900">{{ $selectedCommission->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Información Financiera</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Monto Base</p>
                                <p class="text-lg font-semibold text-gray-900">S/ {{ number_format($selectedCommission->base_amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Porcentaje</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $selectedCommission->commission_percentage }}%</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Comisión</p>
                                <p class="text-lg font-semibold text-gray-900">S/ {{ number_format($selectedCommission->commission_amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Bono</p>
                                <p class="text-lg font-semibold text-green-600">S/ {{ number_format($selectedCommission->bonus_amount, 2) }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-500">Total a Cobrar</p>
                                <p class="text-2xl font-bold text-purple-600">S/ {{ number_format($selectedCommission->total_commission, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    @if($selectedCommission->status === 'pagada')
                        <div class="border-t border-gray-200 pt-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Información de Pago</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Fecha de Pago</p>
                                    <p class="text-sm text-gray-900">{{ $selectedCommission->paid_at ? $selectedCommission->paid_at->format('d/m/Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Método de Pago</p>
                                    <p class="text-sm text-gray-900">{{ $selectedCommission->payment_method ?? 'N/A' }}</p>
                                </div>
                                @if($selectedCommission->payment_reference)
                                <div class="col-span-2">
                                    <p class="text-sm font-medium text-gray-500">Referencia de Pago</p>
                                    <p class="text-sm text-gray-900">{{ $selectedCommission->payment_reference }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($selectedCommission->notes)
                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-sm font-medium text-gray-500 mb-2">Notas</p>
                            <p class="text-sm text-gray-900">{{ $selectedCommission->notes }}</p>
                        </div>
                    @endif

                    @if($selectedCommission->unit)
                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-sm font-medium text-gray-500 mb-2">Lote</p>
                            <p class="text-sm text-gray-900">{{ $selectedCommission->unit->unit_number }}</p>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end mt-6">
                    <flux:button wire:click="closeDetailModal">Cerrar</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>

