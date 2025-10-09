<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <!-- Header mejorado -->
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center py-6 gap-4">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <flux:icon name="building-office-2" class="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Oportunidades</h1>
                            <p class="text-sm text-gray-600 mt-1">Gestión integral de oportunidades</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <flux:button size="xs" icon="arrow-down-tray" variant="outline" wire:click="exportOpportunities">
                        Exportar
                    </flux:button>
                    <flux:button icon="plus" size="xs" color="primary" wire:click="openFormModal">
                        Nueva Oportunidad
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 xs:px-6 lg:px-8 py-6">
        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-xs border border-gray-200 p-4 mb-6 mt-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar oportunidades..." />
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="stageFilter">
                        <option value="">Todas las etapas</option>
                        <option value="calificado">Calificado</option>
                        <option value="visita">Visita</option>
                        <option value="cierre">Cierre</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="registrado">Registrado</option>
                        <option value="reservado">Reservado</option>
                        <option value="cuotas">Cuotas</option>
                        <option value="pagado">Pagado</option>
                        <option value="transferido">Transferido</option>
                        <option value="cancelado">Cancelado</option>
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


        <!-- Tabla de Oportunidades -->
        <div class="bg-white rounded-lg shadow-xs border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente / Proyecto
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
                                            <div class="text-xs font-medium text-gray-900">
                                                {{ $opportunity->client->name ?? '' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $opportunity->project->name ?? 'N/A' }}
                                                <br>
                                                {{ $opportunity->unit->unit_manzana ?? '' }} -
                                                {{ $opportunity->unit->unit_number ?? '' }}
                                            </div>
                                        </div>
                                    </div>
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
                                    <div class="text-xs font-medium text-gray-900">S/
                                        {{ number_format($opportunity->expected_value) }}</div>
                                    <div class="text-xs text-gray-500">{{ $opportunity->probability }}% probabilidad
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-xs text-gray-900">
                                        {{ $opportunity->advisor->name ?? 'Sin asignar' }}</div>
                                    <div class="text-xs text-gray-500">{{ $opportunity->source }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                                    {{ $opportunity->expected_close_date ? \Carbon\Carbon::parse($opportunity->expected_close_date)->format('M d, Y') : 'Sin fecha' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs font-medium">
                                    <div class="flex space-x-2">
                                        <flux:button icon="arrow-up" size="xs" variant="outline" color="success"
                                            wire:click="agregarActividad({{ $opportunity->id }})" />
                                        <flux:button icon="plus" size="xs" variant="outline" color="success"
                                            wire:click="agregarTarea({{ $opportunity->id }})" />
                                        <flux:button icon="eye" size="xs" variant="outline"
                                            wire:click="openDetailModal({{ $opportunity->id }})" />
                                        <flux:button icon="pencil" size="xs" variant="outline"
                                            wire:click="openFormModal({{ $opportunity->id }})" />
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
                                            wire:click="openFormModal">
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
                <div class="bg-white px-4 py-3 border-t border-gray-200 xs:px-6">
                    {{ $opportunities->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Formulario de Oportunidad -->
    <flux:modal variant="flyout" wire:model.self="showFormModal">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $isEditing ? 'Editar Oportunidad' : 'Nueva Oportunidad' }}
                </h3>
            </div>

            <form wire:submit.prevent="saveOpportunity">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Cliente -->
                    <div>
                        <flux:field>
                            <flux:select label="Cliente *" size="xs" wire:model="client_id">
                                <option value="">Seleccionar cliente</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </flux:select>
                            @error('client_id')
                                {{ $message }}
                            @enderror
                        </flux:field>

                    </div>

                    <!-- Proyecto -->
                    <div>
                        <flux:field>
                            <flux:select label="Proyecto *" size="xs" wire:model.live="project_id">
                                <option value="">Seleccionar proyecto</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="project_id" />
                        </flux:field>
                    </div>

                    <!-- Unidad -->
                    <div>
                        <flux:field>
                            <flux:select label="Unidad" size="xs" wire:model.live="unit_id">
                                <option value="">Seleccionar unidad</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">
                                        {{ $unit->unit_manzana ? $unit->unit_manzana : '' }} -
                                        {{ $unit->unit_number }} - S/ {{ number_format($unit->final_price) }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="unit_id" />
                        </flux:field>
                    </div>

                    <!-- Asesor -->
                    <div>
                        <flux:field>
                            <flux:select label="Asesor *" size="xs" wire:model="advisor_id">
                                <option value="">Seleccionar asesor</option>
                                @foreach ($advisors as $advisor)
                                    <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="advisor_id" />
                        </flux:field>
                    </div>

                    <!-- Etapa -->
                    <div>
                        <flux:field>
                            <flux:select label="Etapa *" size="xs" wire:model="stage">
                                <option value="calificado">Calificado</option>
                                <option value="visita">Visita</option>
                                <option value="cierre">Cierre</option>
                            </flux:select>
                            <flux:error name="stage" />
                        </flux:field>
                    </div>

                    <!-- Estado -->
                    <div>
                        <flux:field>
                            <flux:select label="Estado *" size="xs" wire:model="status">
                                <option value="registrado">Registrado</option>
                                <option value="reservado">Reservado</option>
                                <option value="cuotas">Cuotas</option>
                                <option value="pagado">Pagado</option>
                                <option value="transferido">Transferido</option>
                                <option value="cancelado">Cancelado</option>
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>
                    </div>

                    <!-- Probabilidad -->
                    <div>
                        <flux:field>
                            <flux:input label="Probabilidad (%) *" size="xs" type="number"
                                wire:model="probability" min="0" max="100" />
                            <flux:error name="probability" />
                        </flux:field>
                    </div>

                    <!-- Valor Esperado -->
                    <div>
                        <flux:field>
                            <flux:input label="Valor Esperado (S/) *" size="xs" type="number"
                                wire:model="expected_value" step="0.01" min="0" />
                            <flux:error name="expected_value" />
                        </flux:field>
                    </div>

                    <!-- Fecha de Cierre -->
                    <div>
                        <flux:field>
                            <flux:input label="Fecha de Cierre *" size="xs" type="date"
                                wire:model="expected_close_date" />
                            <flux:error name="expected_close_date" />
                        </flux:field>
                    </div>

                    <!-- Origen -->
                    <div>
                        <flux:field>
                            <flux:input label="Origen" size="xs" wire:model="source"
                                placeholder="Ej: Website, Referido, etc." />
                            <flux:error name="source" />
                        </flux:field>
                    </div>

                    <!-- Campaña -->
                    <div>
                        <flux:field>
                            <flux:input label="Campaña" size="xs" wire:model="campaign"
                                placeholder="Ej: Facebook Ads, Google Ads, etc." />
                            <flux:error name="campaign" />
                        </flux:field>
                    </div>
                </div>

                <!-- Notas -->
                <div class="mt-6">
                    <flux:field>
                        <flux:label>Notas</flux:label>
                        <flux:textarea wire:model="notes" rows="3"
                            placeholder="Información adicional sobre la oportunidad..."></flux:textarea>
                        <flux:error name="notes" />
                    </flux:field>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <flux:button icon="x-mark" size="xs" variant="outline" wire:click="closeModals">
                        Cancelar
                    </flux:button>
                    <flux:button icon="check" size="xs" color="primary" type="submit">
                        {{ $isEditing ? 'Actualizar Oportunidad' : 'Crear Oportunidad' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal de Detalle de Oportunidad -->
    <flux:modal variant="flyout" wire:model.self="showDetailModal">
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
                        <ul class="text-xs text-gray-700 space-y-1">
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
                        <ul class="text-xs text-gray-700 space-y-1">
                            <li><span class="font-semibold text-indigo-800">Valor esperado:</span> <span
                                    class="text-green-700 font-bold">S/
                                    {{ number_format($selectedOpportunity->expected_value, 2) }}</span></li>
                            <li><span class="font-semibold text-indigo-800">Fecha de cierre:</span>
                                {{ $selectedOpportunity->expected_close_date ? $selectedOpportunity->expected_close_date->format('d/m/Y') : 'N/A' }}
                            </li>
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
                        <ul class="text-xs text-gray-700 space-y-1">
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
                            class="bg-yellow-50 border border-yellow-100 rounded p-3 text-xs text-yellow-900 min-h-[48px]">
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
                <div class="overflow-x-auto rounded shadow border border-gray-100 bg-white">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-1 text-left font-semibold text-gray-600 uppercase">Actividad</th>
                                <th class="px-2 py-1 text-left font-semibold text-gray-600 uppercase">Fecha</th>
                                <th class="px-2 py-1 text-left font-semibold text-gray-600 uppercase">Estado</th>
                                <th class="px-2 py-1 text-left font-semibold text-gray-600 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($selectedOpportunity->activities as $activity)
                                <tr wire:key="activity-{{ $activity->id }}" class="hover:bg-blue-50 transition">
                                    <td class="px-2 py-1 whitespace-nowrap font-medium text-gray-800">
                                        {{ $activity->title }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-gray-600">
                                        {{ $activity->start_date->format('d/m/Y') }}

                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        <span class="inline-block px-1 py-0.5 rounded text-xs font-semibold">
                                            {{ ucfirst($activity->status) }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        <flux:button icon="eye" size="xs" variant="outline"
                                            wire:click="viewActivity({{ $activity->id }})">
                                        </flux:button>

                                        <flux:button icon="trash" size="xs" variant="danger" color="danger"
                                            wire:click="deleteActivity({{ $activity->id }})">
                                        </flux:button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-2 py-2 text-center text-gray-400">No hay actividades
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

    <!-- Modal de Agregar Actividad -->
    <flux:modal variant="flyout" wire:model.self="showActivityModal">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Agregar Actividad</h3>
            </div>

            @if ($selectedOpportunity)
                <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                    <p class="text-xs text-blue-800">
                        <strong>Cliente:</strong> {{ $selectedOpportunity->client->name ?? 'N/A' }}<br>
                        <strong>Proyecto:</strong> {{ $selectedOpportunity->project->name ?? 'N/A' }}<br>
                        <strong>Oportunidad:</strong> {{ $selectedOpportunity->id }}
                    </p>
                </div>
            @endif

            <form wire:submit.prevent="saveActivity">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título -->
                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:label>Título *</flux:label>
                            <flux:input size="xs" wire:model="activity_title"
                                placeholder="Título de la actividad" />
                            <flux:error name="activity_title" />
                        </flux:field>
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:label>Descripción</flux:label>
                            <flux:textarea wire:model="activity_description" rows="3"
                                placeholder="Descripción de la actividad..."></flux:textarea>
                            <flux:error name="activity_description" />
                        </flux:field>
                    </div>

                    <!-- Tipo de Actividad -->
                    <div>
                        <flux:field>
                            <flux:label>Tipo de Actividad *</flux:label>
                            <flux:select size="xs" wire:model="activity_type">
                                <option value="llamada">Llamada</option>
                                <option value="reunion">Reunión</option>
                                <option value="visita">Visita</option>
                                <option value="seguimiento">Seguimiento</option>
                                <option value="tarea">Tarea</option>
                            </flux:select>
                            <flux:error name="activity_type" />
                        </flux:field>
                    </div>

                    <!-- Prioridad -->
                    <div>
                        <flux:field>
                            <flux:label>Prioridad *</flux:label>
                            <flux:select size="xs" wire:model="activity_priority">
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </flux:select>
                            <flux:error name="activity_priority" />
                        </flux:field>
                    </div>

                    <!-- Fecha de Inicio -->
                    <div>
                        <flux:field>
                            <flux:label>Fecha de Inicio *</flux:label>
                            <flux:input size="xs" type="date" wire:model="activity_start_date" />
                            <flux:error name="activity_start_date" />
                        </flux:field>
                    </div>

                    <!-- Duración -->
                    <div>
                        <flux:field>
                            <flux:label>Duración (minutos) *</flux:label>
                            <flux:input size="xs" type="number" wire:model="activity_duration" min="1"
                                max="1440" />
                            <flux:error name="activity_duration" />
                        </flux:field>
                    </div>

                    <!-- Ubicación -->
                    <div>
                        <flux:field>
                            <flux:label>Ubicación</flux:label>
                            <flux:input size="xs" wire:model="activity_location"
                                placeholder="Ubicación de la actividad" />
                            <flux:error name="activity_location" />
                        </flux:field>
                    </div>
                </div>

                <!-- Notas -->
                <div class="mt-6">
                    <flux:field>
                        <flux:label>Notas</flux:label>
                        <flux:textarea wire:model="activity_notes" rows="3" placeholder="Notas adicionales...">
                        </flux:textarea>
                        <flux:error name="activity_notes" />
                    </flux:field>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <flux:button icon="x-mark" size="xs" variant="outline" wire:click="closeModals">
                        Cancelar
                    </flux:button>
                    <flux:button icon="plus" size="xs" color="primary" type="submit">
                        Crear Actividad
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal de Agregar Tarea -->
    <flux:modal variant="flyout" wire:model.self="showTaskModal">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Agregar Tarea</h3>
            </div>

            @if ($selectedOpportunity)
                <div class="mb-4 p-4 bg-green-50 rounded-lg">
                    <p class="text-xs text-green-800">
                        <strong>Cliente:</strong> {{ $selectedOpportunity->client->name ?? 'N/A' }}<br>
                        <strong>Proyecto:</strong> {{ $selectedOpportunity->project->name ?? 'N/A' }}<br>
                        <strong>Oportunidad:</strong> {{ $selectedOpportunity->id }}
                    </p>
                </div>
            @endif

            <form wire:submit.prevent="saveTask">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título -->
                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:label>Título *</flux:label>
                            <flux:input size="xs" wire:model="task_title" placeholder="Título de la tarea" />
                            <flux:error name="task_title" />
                        </flux:field>
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:label>Descripción</flux:label>
                            <flux:textarea wire:model="task_description" rows="3"
                                placeholder="Descripción de la tarea..."></flux:textarea>
                            <flux:error name="task_description" />
                        </flux:field>
                    </div>

                    <!-- Prioridad -->
                    <div>
                        <flux:field>
                            <flux:label>Prioridad *</flux:label>
                            <flux:select size="xs" wire:model="task_priority">
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </flux:select>
                            <flux:error name="task_priority" />
                        </flux:field>
                    </div>

                    <!-- Fecha de Vencimiento -->
                    <div>
                        <flux:field>
                            <flux:label>Fecha de Vencimiento *</flux:label>
                            <flux:input size="xs" type="date" wire:model="task_due_date" />
                            <flux:error name="task_due_date" />
                        </flux:field>
                    </div>
                </div>

                <!-- Notas -->
                <div class="mt-6">
                    <flux:field>
                        <flux:label>Notas</flux:label>
                        <flux:textarea wire:model="task_notes" rows="3" placeholder="Notas adicionales...">
                        </flux:textarea>
                        <flux:error name="task_notes" />
                    </flux:field>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <flux:button icon="x-mark" size="xs" variant="outline" wire:click="closeModals">
                        Cancelar
                    </flux:button>
                    <flux:button icon="plus" size="xs" color="success" type="submit">
                        Crear Tarea
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal de Confirmación de Eliminación -->
    <flux:modal wire:model.self="showDeleteModal">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <flux:icon name="exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Confirmar eliminación</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-xs text-gray-500">
                    ¿Estás seguro de que quieres eliminar esta oportunidad? Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="flex justify-center space-x-3 mt-4">
                <flux:button icon="x-mark" size="xs" variant="outline" wire:click="closeModals">
                    Cancelar
                </flux:button>
                <flux:button icon="trash" size="xs" color="danger" wire:click="deleteOpportunity">
                    Eliminar
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
