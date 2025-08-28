<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Oportunidades</h1>
                    <p class="text-sm text-gray-600">Seguimiento de ventas y leads</p>
                </div>
                <div class="flex space-x-2">
                    <flux:button icon="arrow-down-tray" size="xs" wire:click="exportOpportunities">
                        Exportar
                    </flux:button>
                    <flux:button icon="plus" size="xs" color="primary" wire:click="openCreateModal">
                        Nueva Oportunidad
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Mensajes de éxito -->
        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <flux:icon name="x-mark" class="w-4 h-4" />
                </button>
            </div>
        @endif

        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar oportunidades..." />
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="stageFilter">
                        <option value="">Todas las etapas</option>
                        <option value="captado">Captado</option>
                        <option value="calificado">Calificado</option>
                        <option value="contacto">Contacto</option>
                        <option value="propuesta">Propuesta</option>
                        <option value="visita">Visita</option>
                        <option value="negociacion">Negociación</option>
                        <option value="cierre">Cierre</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="activa">Activa</option>
                        <option value="ganada">Ganada</option>
                        <option value="perdida">Perdida</option>
                        <option value="cancelada">Cancelada</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="projectFilter">
                        <option value="">Todos los proyectos</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="advisorFilter">
                        <option value="">Todos los asesores</option>
                        @foreach($advisors as $advisor)
                            <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:button icon="x-mark" size="xs" variant="outline" wire:click="clearFilters">
                        Limpiar filtros
                    </flux:button>
                </div>
            </div>


        </div>

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <flux:icon name="funnel" class="w-4 h-4 text-blue-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <flux:icon name="check-circle" class="w-4 h-4 text-green-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Ganadas</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['won'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <flux:icon name="clock" class="w-4 h-4 text-yellow-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">En Proceso</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['in_progress'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <flux:icon name="currency-dollar" class="w-4 h-4 text-purple-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Valor Total</p>
                        <p class="text-lg font-semibold text-gray-900">S/ {{ number_format($stats['total_value'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Oportunidades -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Proyecto
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Etapa
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Asesor
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha Cierre
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($opportunities as $opportunity)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-blue-600">
                                            {{ strtoupper(substr($opportunity->client->name ?? 'C', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $opportunity->client->name ?? '' }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $opportunity->client->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $opportunity->project->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $opportunity->project->type ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $opportunity->stage === 'won' ? 'bg-green-100 text-green-800' : 
                                       ($opportunity->stage === 'lost' ? 'bg-red-100 text-red-800' : 
                                       ($opportunity->stage === 'closing' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    {{ ucfirst($opportunity->stage) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">S/ {{ number_format($opportunity->expected_value) }}</div>
                                <div class="text-xs text-gray-500">{{ $opportunity->probability }}% probabilidad</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $opportunity->advisor->name ?? 'Sin asignar' }}</div>
                                <div class="text-xs text-gray-500">{{ $opportunity->source }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $opportunity->expected_close_date ? \Carbon\Carbon::parse($opportunity->expected_close_date)->format('M d, Y') : 'Sin fecha' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <flux:button icon="eye" size="xs" variant="outline" wire:click="openDetailModal({{ $opportunity->id }})" />
                                    <flux:button icon="pencil" size="xs" variant="outline" wire:click="openEditModal({{ $opportunity->id }})" />
                                    <flux:button icon="trash" size="xs" variant="outline" color="danger" wire:click="openDeleteModal({{ $opportunity->id }})" />
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="funnel" class="w-12 h-12 text-gray-300 mb-2" />
                                    <p>No se encontraron oportunidades</p>
                                    <flux:button icon="plus" size="xs" color="primary" class="mt-2" wire:click="openCreateModal">
                                        Crear primera oportunidad
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($opportunities->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $opportunities->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    @if($showDeleteModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <flux:icon name="exclamation-triangle" class="h-6 w-6 text-red-600" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Confirmar eliminación</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        ¿Estás seguro de que quieres eliminar esta oportunidad? Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="flex justify-center space-x-3 mt-4">
                    <flux:button icon="x-mark" size="xs" variant="outline" wire:click="cancelDelete">
                        Cancelar
                    </flux:button>
                    <flux:button icon="trash" size="xs" color="danger" wire:click="confirmDelete">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Componentes adicionales -->
    <livewire:opportunities.opportunity-form />
    <livewire:opportunities.opportunity-detail />
</div>
