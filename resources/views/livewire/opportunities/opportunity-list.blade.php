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


        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/dashboard">Dashboard</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="/opportunities">Oportunidades</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6 mt-6">
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
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="advisorFilter">
                        <option value="">Todos los asesores</option>
                        @foreach ($advisors as $advisor)
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 mt-6">
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
                        <p class="text-lg font-semibold text-gray-900">S/
                            {{ number_format($stats['total_value'] ?? 0) }}</p>
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
                                Etapa/Estado
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
                            <tr wire:key="opportunity-{{ $opportunity->id }}" class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-600">
                                                {{ strtoupper(substr($opportunity->client->name ?? 'C', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $opportunity->client->name ?? '' }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $opportunity->client->email ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $opportunity->project->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $opportunity->project->type ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2 flex-col">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $this->getStageColor($opportunity->stage) }}">
                                            {{ ucfirst($opportunity->stage) }}
                                        </span>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $this->getStatusColor($opportunity->status) }}">
                                            {{ ucfirst($opportunity->status) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">S/
                                        {{ number_format($opportunity->expected_value) }}</div>
                                    <div class="text-xs text-gray-500">{{ $opportunity->probability }}% probabilidad
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $opportunity->advisor->name ?? 'Sin asignar' }}</div>
                                    <div class="text-xs text-gray-500">{{ $opportunity->source }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $opportunity->expected_close_date ? \Carbon\Carbon::parse($opportunity->expected_close_date)->format('M d, Y') : 'Sin fecha' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <flux:button icon="eye" size="xs" variant="outline"
                                            wire:click="openDetailModal({{ $opportunity->id }})" />
                                        <flux:button icon="pencil" size="xs" variant="outline"
                                            wire:click="openEditModal({{ $opportunity->id }})" />
                                        <flux:button icon="arrow-up" size="xs" variant="outline"
                                            color="success" wire:click="openStageModal({{ $opportunity->id }})" />
                                        <flux:button icon="check-circle" size="xs" variant="outline"
                                            color="success" wire:click="openWinModal({{ $opportunity->id }})" />
                                        <flux:button icon="x-circle" size="xs" variant="outline" color="danger"
                                            wire:click="openLoseModal({{ $opportunity->id }})" />
                                        <flux:button icon="trash" size="xs" variant="danger" color="danger"
                                            wire:click="openDeleteModal({{ $opportunity->id }})" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="funnel" class="w-12 h-12 text-gray-300 mb-2" />
                                        <p>No se encontraron oportunidades</p>
                                        <flux:button icon="plus" size="xs" color="primary" class="mt-2"
                                            wire:click="openCreateModal">
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
            @if ($opportunities->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $opportunities->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Crear Oportunidad -->
    <flux:modal variant="flyout" wire:model="showCreateModal" max-width="4xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Nueva Oportunidad</h3>
            </div>

            <form wire:submit.prevent="createOpportunity">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Cliente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
                        <flux:select size="sm" wire:model="client_id" required>
                            <option value="">Seleccionar cliente</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }} - {{ $client->email }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('client_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Proyecto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proyecto *</label>
                        <flux:select size="sm" wire:model.live="project_id" required>
                            <option value="">Seleccionar proyecto</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('project_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Unidad -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidad</label>
                        <flux:select size="sm" wire:model.live="unit_id">
                            <option value="">Seleccionar unidad</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->unit_manzana ?? '' }} -
                                    {{ $unit->unit_number }} - S/ {{ number_format($unit->final_price) }}</option>
                            @endforeach
                        </flux:select>
                        @error('unit_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Asesor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Asesor *</label>
                        <flux:select size="sm" wire:model="advisor_id" required>
                            <option value="">Seleccionar asesor</option>
                            @foreach ($advisors as $advisor)
                                <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('advisor_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Etapa -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Etapa *</label>
                        <flux:select size="sm" wire:model="stage" required>
                            <option value="captado">Captado</option>
                            <option value="calificado">Calificado</option>
                            <option value="contacto">Contacto</option>
                            <option value="propuesta">Propuesta</option>
                            <option value="visita">Visita</option>
                            <option value="negociacion">Negociación</option>
                            <option value="cierre">Cierre</option>
                        </flux:select>
                        @error('stage')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                        <flux:select size="sm" wire:model="status" required>
                            <option value="activa">Activa</option>
                            <option value="ganada">Ganada</option>
                            <option value="perdida">Perdida</option>
                            <option value="cancelada">Cancelada</option>
                        </flux:select>
                        @error('status')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Probabilidad -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Probabilidad (%) *</label>
                        <flux:input size="sm" type="number" wire:model="probability" min="0"
                            max="100" required />
                        @error('probability')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Valor Esperado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Valor Esperado (S/) *</label>
                        <flux:input size="sm" type="number" wire:model="expected_value" step="0.01"
                            min="0" required />
                        @error('expected_value')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Fecha de Cierre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Cierre *</label>
                        <flux:input size="sm" type="date" wire:model="expected_close_date" required />
                        @error('expected_close_date')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Origen -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Origen</label>
                        <flux:input size="sm" wire:model="source" placeholder="Ej: Website, Referido, etc." />
                        @error('source')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Campaña -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Campaña</label>
                        <flux:input size="sm" wire:model="campaign"
                            placeholder="Ej: Facebook Ads, Google Ads, etc." />
                        @error('campaign')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Notas -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                    <textarea wire:model="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Información adicional sobre la oportunidad..."></textarea>
                    @error('notes')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <flux:button icon="x-mark" size="sm" variant="outline" wire:click="closeModals">
                        Cancelar
                    </flux:button>
                    <flux:button icon="check" size="sm" color="primary" type="submit">
                        Crear Oportunidad
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal de Editar Oportunidad -->
    <flux:modal variant="flyout" wire:model="showEditModal" max-width="4xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Editar Oportunidad</h3>
            </div>

            <form wire:submit.prevent="updateOpportunity">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Cliente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
                        <flux:select size="sm" wire:model="client_id" required>
                            <option value="">Seleccionar cliente</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }} - {{ $client->email }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('client_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Proyecto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proyecto *</label>
                        <flux:select size="sm" wire:model.live="project_id" required>
                            <option value="">Seleccionar proyecto</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('project_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Unidad -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidad</label>
                        <flux:select size="sm" wire:model.live="unit_id">
                            <option value="">Seleccionar unidad</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->unit_manzana ?? '' }} -
                                    {{ $unit->unit_number }} - S/ {{ number_format($unit->final_price) }}</option>
                            @endforeach
                        </flux:select>
                        @error('unit_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Asesor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Asesor *</label>
                        <flux:select size="sm" wire:model="advisor_id" required>
                            <option value="">Seleccionar asesor</option>
                            @foreach ($advisors as $advisor)
                                <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('advisor_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Etapa -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Etapa *</label>
                        <flux:select size="sm" wire:model="stage" required>
                            <option value="captado">Captado</option>
                            <option value="calificado">Calificado</option>
                            <option value="contacto">Contacto</option>
                            <option value="propuesta">Propuesta</option>
                            <option value="visita">Visita</option>
                            <option value="negociacion">Negociación</option>
                            <option value="cierre">Cierre</option>
                        </flux:select>
                        @error('stage')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                        <flux:select size="sm" wire:model="status" required>
                            <option value="activa">Activa</option>
                            <option value="ganada">Ganada</option>
                            <option value="perdida">Perdida</option>
                            <option value="cancelada">Cancelada</option>
                        </flux:select>
                        @error('status')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Probabilidad -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Probabilidad (%) *</label>
                        <flux:input size="sm" type="number" wire:model="probability" min="0"
                            max="100" required />
                        @error('probability')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Valor Esperado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Valor Esperado (S/) *</label>
                        <flux:input size="sm" type="number" wire:model="expected_value" step="0.01"
                            min="0" required />
                        @error('expected_value')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Fecha de Cierre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Cierre *</label>
                        <flux:input size="sm" type="date" wire:model="expected_close_date" required />
                        @error('expected_close_date')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Origen -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Origen</label>
                        <flux:input size="sm" wire:model="source" placeholder="Ej: Website, Referido, etc." />
                        @error('source')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Campaña -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Campaña</label>
                        <flux:input size="sm" wire:model="campaign"
                            placeholder="Ej: Facebook Ads, Google Ads, etc." />
                        @error('campaign')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Notas -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                    <textarea wire:model="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Información adicional sobre la oportunidad..."></textarea>
                    @error('notes')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <flux:button icon="x-mark" size="sm" variant="outline" wire:click="closeModals">
                        Cancelar
                    </flux:button>
                    <flux:button icon="check" size="sm" color="primary" type="submit">
                        Actualizar Oportunidad
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal de Detalle de Oportunidad -->
    <flux:modal variant="flyout" wire:model="showDetailModal" class="w-1/3">
        <div class="p-0">
            <div
                class="flex items-center justify-between px-8 pt-8 pb-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white rounded-t-lg">
                <h3 class="text-2xl font-bold text-blue-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"></path>
                    </svg>
                    Detalle de Oportunidad
                </h3>
            </div>
        </div>
        @if ($selectedOpportunity)
            <div class="px-8 py-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-blue-400"></span>
                            <span class="text-xs text-gray-500 uppercase tracking-wider">Información General</span>
                        </div>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li><span class="font-semibold text-blue-800">Cliente:</span>
                                {{ $selectedOpportunity->client->name ?? 'N/A' }}</li>
                            <li><span class="font-semibold text-blue-800">Proyecto:</span>
                                {{ $selectedOpportunity->project->name ?? 'N/A' }}</li>
                            <li><span class="font-semibold text-blue-800">Etapa actual:</span> <span
                                    class="inline-block px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-xs font-semibold">{{ ucfirst($selectedOpportunity->stage) }}</span>
                            </li>
                            <li><span class="font-semibold text-blue-800">Estado:</span>
                                <span
                                    class="inline-block px-2 py-0.5 rounded 
                                    @if ($selectedOpportunity->status === 'activa') bg-green-100 text-green-700 
                                    @elseif($selectedOpportunity->status === 'ganada') bg-blue-100 text-blue-700 
                                    @elseif($selectedOpportunity->status === 'perdida') bg-red-100 text-red-700 
                                    @else bg-gray-100 text-gray-700 @endif
                                    text-xs font-semibold">
                                    {{ ucfirst($selectedOpportunity->status ?? 'N/A') }}
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-indigo-400"></span>
                            <span class="text-xs text-gray-500 uppercase tracking-wider">Detalles</span>
                        </div>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li><span class="font-semibold text-indigo-800">Valor esperado:</span> <span
                                    class="text-green-700 font-bold">S/
                                    {{ number_format($selectedOpportunity->expected_value, 2) }}</span></li>
                            <li><span class="font-semibold text-indigo-800">Fecha de cierre:</span>
                                {{ $selectedOpportunity->expected_close_date->format('d/m/Y') }}</li>
                            <li><span class="font-semibold text-indigo-800">Origen:</span>
                                {{ $selectedOpportunity->source ?? 'N/A' }}</li>
                            <li><span class="font-semibold text-indigo-800">Campaña:</span>
                                {{ $selectedOpportunity->campaign ?? 'N/A' }}</li>
                        </ul>
                    </div>
                </div>
                <div>
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-teal-400"></span>
                            <span class="text-xs text-gray-500 uppercase tracking-wider">Adicional</span>
                        </div>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li><span class="font-semibold text-teal-800">Asesor:</span>
                                {{ $selectedOpportunity->advisor->name ?? 'N/A' }}</li>
                            <li><span class="font-semibold text-teal-800">Probabilidad:</span>
                                <span
                                    class="inline-block px-2 py-0.5 rounded bg-teal-100 text-teal-700 text-xs font-semibold">
                                    {{ $selectedOpportunity->probability ?? 'N/A' }}%
                                </span>
                            </li>
                            <li><span class="font-semibold text-teal-800">Fecha de creación:</span>
                                {{ $selectedOpportunity->created_at->format('d/m/Y') }}</li>
                            <li><span class="font-semibold text-teal-800">Fecha de actualización:</span>
                                {{ $selectedOpportunity->updated_at->format('d/m/Y') }}</li>
                        </ul>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-yellow-400"></span>
                            <span class="text-xs text-gray-500 uppercase tracking-wider">Notas</span>
                        </div>
                        <div
                            class="bg-yellow-50 border border-yellow-100 rounded p-3 text-sm text-yellow-900 min-h-[48px]">
                            {{ $selectedOpportunity->notes ?? 'Sin notas adicionales.' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-8 pb-6">
                <div class="mb-2 flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-purple-400"></span>
                    <span class="text-xs text-gray-500 uppercase tracking-wider">Actividades recientes</span>
                </div>
                <div class="overflow-x-auto rounded-lg shadow border border-gray-100 bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Actividad</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Fecha</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Estado</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($selectedOpportunity->activities as $activity)
                                <tr wire:key="activity-{{ $activity->id }}" class="hover:bg-blue-50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-800">
                                        {{ $activity->title }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                        {{ $activity->start_date }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            class="inline-block px-2 py-0.5 rounded 
                                            text-xs font-semibold">
                                            {{ ucfirst($activity->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <flux:button icon="eye" size="xs" variant="outline"
                                            wire:click="viewActivity({{ $activity->id }})">
                                        </flux:button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-gray-400">No hay actividades
                                        registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end space-x-3 px-8 pb-8 mt-4">
                <flux:button icon="x-mark" variant="outline" size="xs"
                    wire:click="$set('showDetailModal', false)">
                    Cerrar
                </flux:button>
            </div>
        @endif
    </flux:modal>
    <!-- Modal de Cambiar Etapa -->
    <flux:modal variant="flyout" wire:model="showStageModal" class="w-1/3">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Cambiar Etapa de Oportunidad</h3>
            </div>

            @if ($selectedOpportunity)
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">
                        <strong>Cliente:</strong> {{ $selectedOpportunity->client->name ?? 'N/A' }}<br>
                        <strong>Proyecto:</strong> {{ $selectedOpportunity->project->name ?? 'N/A' }}<br>
                        <strong>Etapa actual:</strong> <span
                            class="font-medium">{{ ucfirst($selectedOpportunity->stage) }}</span>
                    </p>
                </div>
            @endif

            <form wire:submit.prevent="advanceStage">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nueva Etapa *</label>
                    <flux:select size="sm" wire:model="newStage" required>
                        <option value="">Seleccionar nueva etapa</option>
                        <option value="captado">Captado</option>
                        <option value="calificado">Calificado</option>
                        <option value="contacto">Contacto</option>
                        <option value="propuesta">Propuesta</option>
                        <option value="visita">Visita</option>
                        <option value="negociacion">Negociación</option>
                        <option value="cierre">Cierre</option>
                    </flux:select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notas del Cambio</label>
                    <textarea wire:model="stageNotes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Razón del cambio de etapa..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <flux:button icon="x-mark" size="sm" variant="outline" wire:click="closeModals">
                        Cancelar
                    </flux:button>
                    <flux:button icon="arrow-up" size="sm" color="primary" type="submit">
                        Cambiar Etapa
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal de Marcar como Ganada -->
    <flux:modal wire:model="showWinModal" class="w-96">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Marcar Oportunidad como Ganada</h3>
            </div>

            @if ($selectedOpportunity)
                <div class="mb-4 p-4 bg-green-50 rounded-lg">
                    <p class="text-sm text-green-800">
                        <strong>Cliente:</strong> {{ $selectedOpportunity->client->name ?? 'N/A' }}<br>
                        <strong>Proyecto:</strong> {{ $selectedOpportunity->project->name ?? 'N/A' }}<br>
                        <strong>Valor esperado:</strong> S/ {{ number_format($selectedOpportunity->expected_value) }}
                    </p>
                </div>
            @endif

            <form wire:submit.prevent="markAsWon">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valor de Cierre (S/) *</label>
                    <flux:input size="sm" type="number" wire:model="winValue" step="0.01" min="0"
                        required />
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Razón del Éxito</label>
                    <textarea wire:model="winReason" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="¿Por qué se ganó esta oportunidad?"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <flux:button icon="x-mark" size="sm" variant="outline" wire:click="closeModals">
                        Cancelar
                    </flux:button>
                    <flux:button icon="check-circle" size="sm" color="success" type="submit">
                        Marcar como Ganada
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal de Marcar como Perdida -->
    <flux:modal wire:model="showLoseModal" class="w-96">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Marcar Oportunidad como Perdida</h3>
            </div>

            @if ($selectedOpportunity)
                <div class="mb-4 p-4 bg-red-50 rounded-lg">
                    <p class="text-sm text-red-800">
                        <strong>Cliente:</strong> {{ $selectedOpportunity->client->name ?? 'N/A' }}<br>
                        <strong>Proyecto:</strong> {{ $selectedOpportunity->project->name ?? 'N/A' }}<br>
                        <strong>Valor esperado:</strong> S/ {{ number_format($selectedOpportunity->expected_value) }}
                    </p>
                </div>
            @endif

            <form wire:submit.prevent="markAsLost">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Razón de la Pérdida *</label>
                    <textarea wire:model="loseReason" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="¿Por qué se perdió esta oportunidad?" required></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <flux:button icon="x-mark" size="sm" variant="outline" wire:click="closeModals">
                        Cancelar
                    </flux:button>
                    <flux:button icon="x-circle" size="sm" color="danger" type="submit">
                        Marcar como Perdida
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal de Confirmación de Eliminación -->
    <flux:modal wire:model="showDeleteModal" class="w-96">
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
                <flux:button icon="x-mark" size="sm" variant="outline" wire:click="closeModals">
                    Cancelar
                </flux:button>
                <flux:button icon="trash" size="sm" color="danger" wire:click="deleteOpportunity">
                    Eliminar
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
