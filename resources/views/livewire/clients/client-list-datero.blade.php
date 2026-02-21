<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Clientes Datero</h1>
                    <p class="text-sm text-gray-600">Gestión de clientes del CRM</p>
                </div>
                <div class="flex space-x-2">
                    <flux:button icon="user-group" size="xs" color="primary"
                        href="{{ route('clients.registro-masivo') }}">
                        Registro masivo
                    </flux:button>
                    <flux:button icon="arrow-down-tray" size="xs" variant="outline" wire:click="exportClients">
                        Exportar Excel
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Notificaciones - Parte superior derecha -->
        <div id="notification-container" class="fixed top-4 right-4 z-[9999] hidden">
            <div id="notification-content"
                class="bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all duration-300 translate-x-full opacity-0">
                <div class="p-6 border-l-4" id="notification-border">
                    <div class="flex items-start space-x-4">
                        <div id="notification-icon" class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl"></div>
                        </div>
                        <div class="flex-1">
                            <h3 id="notification-title" class="text-lg font-semibold mb-1"></h3>
                            <p id="notification-message" class="text-gray-600 text-sm"></p>
                        </div>
                        <button id="notification-close"
                            class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                <div>
                    <flux:input size="xs" wire:model.live.debounce.500ms="search" placeholder="Buscar clientes..."
                        type="search" />
                    @if ($search && strlen(trim($search)) < $searchMinLength)
                        <div class="mt-1 text-[10px] text-gray-400">
                            Escribe al menos {{ $searchMinLength }} caracteres para filtrar.
                        </div>
                    @endif
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="nuevo">Nuevo</option>
                        <option value="contacto_inicial">Contacto inicial</option>
                        <option value="en_seguimiento">En seguimiento</option>
                        <option value="cierre">Cierre</option>
                        <option value="perdido">Perdido</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="sourceFilter">
                        <option value="">Todas las fuentes</option>
                        <option value="redes_sociales">Redes sociales</option>
                        <option value="ferias">Ferias</option>
                        <option value="referidos">Referidos</option>
                        <option value="formulario_web">Formulario web</option>
                        <option value="publicidad">Publicidad</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="typeFilter">
                        <option value="">Todos los tipos</option>
                        <option value="inversor">Inversor</option>
                        <option value="comprador">Comprador</option>
                        <option value="empresa">Empresa</option>
                        <option value="constructor">Constructor</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="cityFilter">
                        <option value="">Todas las ciudades</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="vendedorFilter" title="Dateros a tu cargo">
                        <option value="">Todos los dateros</option>
                        @foreach ($vendedores as $datero)
                            <option value="{{ $datero->id }}">{{ $datero->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:button size="xs" variant="outline" wire:click="clearFilters">
                        Limpiar filtros
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Tabla de Clientes Compacta -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto" wire:loading.class="opacity-60"
                wire:target="search,statusFilter,sourceFilter,typeFilter,cityFilter,vendedorFilter,clearFilters">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Cliente</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Contacto</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Ciudad</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Asesor asignado</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Últ. Interacción</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($clients as $client)
                            <tr wire:key="client-{{ $client->id }}" class="hover:bg-gray-50">
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-blue-600">
                                                {{ strtoupper(substr($client->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $client->name }}</div>
                                            <div class="text-[10px] text-gray-400">
                                                ID: {{ $client->id }}
                                                @if ($client->document_type && $client->document_number)
                                                    | {{ $client->document_type }}: {{ $client->document_number }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-gray-900">{{ $client->phone ?: '-' }}</div>
                                    <div class="text-[10px] text-gray-400">
                                        @if ($client->birth_date)
                                            Nac: {{ $client->birth_date->format('d/m/Y') }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-900">
                                    {{ $client->city?->name ?? '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-gray-900">
                                        {{ $client->assignedAdvisor ? $client->assignedAdvisor->name : 'Sin asignar' }}
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-500">
                                    @if ($client->activities && $client->activities->count() > 0)
                                        {{ $client->activities->first()->title ?? 'Sin actividad' }}
                                        <br>
                                        {{ optional($client->activities->first()->start_date)->format('d/m/Y') }}
                                    @else
                                        Sin actividad
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap font-medium">
                                    <div class="flex space-x-1">
                                        <flux:button size="xs" variant="outline"
                                            wire:click="openActivityModal({{ $client->id }})"
                                            title="Nueva actividad">
                                            <flux:icon name="calendar" class="w-3 h-3" />
                                        </flux:button>
                                        <flux:button size="xs" variant="outline"
                                            wire:click="openTaskModal({{ $client->id }})" title="Nueva tarea">
                                            <flux:icon name="clipboard-document-list" class="w-3 h-3" />
                                        </flux:button>
                                        <flux:button size="xs" variant="outline"
                                            wire:click="openReservationModal({{ $client->id }})"
                                            title="Nueva reserva">
                                            <flux:icon name="bookmark" class="w-3 h-3" />
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="users" class="w-12 h-12 text-gray-300 mb-2" />
                                        <p>No se encontraron clientes</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-2 py-1 text-[11px] text-gray-500" wire:loading
                wire:target="search,statusFilter,sourceFilter,typeFilter,cityFilter,vendedorFilter,clearFilters">
                Buscando...
            </div>
            <!-- Paginación -->
            @if ($clients->hasPages())
                <div class="bg-white px-2 py-2 border-t border-gray-200">
                    {{ $clients->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Nueva Actividad -->
    <flux:modal wire:model="showActivityModal" size="md">
        <div class="p-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-base font-semibold text-gray-900">Nueva actividad</h3>
            </div>
            <form wire:submit.prevent="createActivity">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="col-span-2">
                        <flux:input label="Titulo" wire:model="activity_title" size="xs"
                            placeholder="Titulo de la actividad *" class="w-full" />
                        @error('activity_title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <flux:select label="Tipo" wire:model="activity_type" size="xs" class="w-full">
                            <option value="llamada">Llamada</option>
                            <option value="reunion">Reunion</option>
                            <option value="visita">Visita</option>
                            <option value="seguimiento">Seguimiento</option>
                            <option value="tarea">Tarea</option>
                        </flux:select>
                    </div>
                    <div>
                        <flux:select label="Prioridad" wire:model="activity_priority" size="xs" class="w-full">
                            <option value="baja">Baja</option>
                            <option value="media">Media</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </flux:select>
                    </div>
                    <div>
                        <flux:select label="Estado" wire:model="activity_status" size="xs" class="w-full">
                            <option value="programada">Programada</option>
                            <option value="en_progreso">En progreso</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </flux:select>
                    </div>
                    <div>
                        <flux:input label="Fecha y hora" type="datetime-local" wire:model="activity_start_date"
                            size="xs" class="w-full" />
                        @error('activity_start_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-2">
                        <div class="text-gray-900">
                            Asesor:
                            {{ $activity_assigned_to ? optional($advisors->firstWhere('id', $activity_assigned_to))->name : '-' }}
                        </div>
                    </div>
                    <div class="col-span-2">
                        <flux:textarea label="Notas" wire:model="activity_notes" rows="2"
                            placeholder="Notas adicionales"
                            class="w-full text-xs px-2 py-1 border border-gray-200 rounded">
                        </flux:textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-100">
                    <flux:button type="button" variant="outline" size="xs" wire:click="closeActionModals">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" color="primary" size="xs" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed">
                        Guardar
                    </flux:button>
                </div>
            </form>

            <div class="mt-4 border-t border-gray-100 pt-3">
                <h4 class="text-xs font-semibold text-gray-700 uppercase">Actividades del cliente</h4>
                <div class="mt-2 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-[11px]">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-1 text-left font-semibold text-gray-500 uppercase">Fecha</th>
                                <th class="px-2 py-1 text-left font-semibold text-gray-500 uppercase">Titulo</th>
                                <th class="px-2 py-1 text-left font-semibold text-gray-500 uppercase">Tipo</th>
                                <th class="px-2 py-1 text-left font-semibold text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @if ($clientActivities && count($clientActivities) > 0)
                                @foreach ($clientActivities as $activity)
                                    <tr>
                                        <td class="px-2 py-1 text-gray-600 whitespace-nowrap">
                                            {{ optional($activity->start_date)->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-2 py-1 text-gray-900">{{ $activity->title }}</td>
                                        <td class="px-2 py-1 text-gray-600">
                                            {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                        </td>
                                        <td class="px-2 py-1 text-gray-600">
                                            {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="px-2 py-2 text-gray-400">
                                        Sin actividades registradas
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($clientActivities instanceof \Illuminate\Pagination\LengthAwarePaginator && $clientActivities->hasPages())
                    <div class="mt-2">
                        {{ $clientActivities->links() }}
                    </div>
                @endif
            </div>
        </div>
    </flux:modal>

    <!-- Modal de Nueva Tarea -->
    <flux:modal wire:model="showTaskModal" size="md">
        <div class="p-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-base font-semibold text-gray-900">Nueva tarea</h3>
            </div>
            <form wire:submit.prevent="createTask">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="col-span-2">
                        <flux:input label="Titulo" wire:model="task_title" size="xs"
                            placeholder="Titulo de la tarea *" class="w-full" />
                        @error('task_title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <flux:select label="Tipo" wire:model="task_type" size="xs" class="w-full">
                            <option value="seguimiento">Seguimiento</option>
                            <option value="visita">Visita</option>
                            <option value="llamada">Llamada</option>
                            <option value="documento">Documento</option>
                            <option value="otros">Otros</option>
                        </flux:select>
                    </div>
                    <div>
                        <flux:select label="Prioridad" wire:model="task_priority" size="xs" class="w-full">
                            <option value="baja">Baja</option>
                            <option value="media">Media</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </flux:select>
                    </div>
                    <div>
                        <flux:select label="Estado" wire:model="task_status" size="xs" class="w-full">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_progreso">En progreso</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </flux:select>
                    </div>
                    <div>
                        <flux:input label="Vence" type="datetime-local" wire:model="task_due_date" size="xs"
                            class="w-full" />
                    </div>
                    <div class="col-span-2">
                        <div class="text-gray-900">
                            Asesor:
                            {{ $task_assigned_to ? optional($advisors->firstWhere('id', $task_assigned_to))->name : '-' }}
                        </div>
                    </div>
                    <div class="col-span-2">
                        <flux:textarea label="Notas" wire:model="task_notes" rows="2"
                            placeholder="Notas adicionales"
                            class="w-full text-xs px-2 py-1 border border-gray-200 rounded">
                        </flux:textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-100">
                    <flux:button type="button" variant="outline" size="xs" wire:click="closeActionModals">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" color="primary" size="xs" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed">
                        Guardar
                    </flux:button>
                </div>
            </form>

            <div class="mt-4 border-t border-gray-100 pt-3">
                <h4 class="text-xs font-semibold text-gray-700 uppercase">Tareas del cliente</h4>
                <div class="mt-2 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-[11px]">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-1 text-left font-semibold text-gray-500 uppercase">Vence</th>
                                <th class="px-2 py-1 text-left font-semibold text-gray-500 uppercase">Titulo</th>
                                <th class="px-2 py-1 text-left font-semibold text-gray-500 uppercase">Prioridad</th>
                                <th class="px-2 py-1 text-left font-semibold text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @if ($clientTasks && count($clientTasks) > 0)
                                @foreach ($clientTasks as $task)
                                    <tr>
                                        <td class="px-2 py-1 text-gray-600 whitespace-nowrap">
                                            {{ optional($task->due_date)->format('d/m/Y') ?? 'Sin fecha' }}
                                        </td>
                                        <td class="px-2 py-1 text-gray-900">{{ $task->title }}</td>
                                        <td class="px-2 py-1 text-gray-600">
                                            {{ ucfirst($task->priority) }}
                                        </td>
                                        <td class="px-2 py-1 text-gray-600">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="px-2 py-2 text-gray-400">
                                        Sin tareas registradas
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($clientTasks instanceof \Illuminate\Pagination\LengthAwarePaginator && $clientTasks->hasPages())
                    <div class="mt-2">
                        {{ $clientTasks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </flux:modal>

    <!-- Modal de Nueva Reserva -->
    <flux:modal wire:model="showReservationModal" size="md">
        <div class="p-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-base font-semibold text-gray-900">Nueva reserva</h3>
            </div>
            <form wire:submit.prevent="createReservationFromClient">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="col-span-2 text-xs text-gray-600">
                        Cliente: <span
                            class="font-medium text-gray-900">{{ $reservationClientName ?: 'Sin cliente' }}</span>
                    </div>
                    <div>
                        <flux:select label="Proyecto" wire:model.live="reservation_project_id" size="xs"
                            class="w-full">
                            <option value="">Seleccionar proyecto</option>
                            @foreach ($reservation_projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('reservation_project_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <flux:select label="Unidad" wire:model="reservation_unit_id" size="xs" class="w-full">
                            <option value="">Seleccionar unidad</option>
                            @foreach ($reservation_units as $unit)
                                <option value="{{ $unit->id }}">
                                    {{ $unit->unit_manzana }} - {{ $unit->unit_number }} - S/
                                    {{ number_format($unit->final_price) }}
                                </option>
                            @endforeach
                        </flux:select>
                        @error('reservation_unit_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <flux:input label="Monto de reserva" type="number" step="0.01"
                            wire:model="reservation_amount" size="xs" class="w-full" />
                        @error('reservation_amount')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-gray-500">
                    Al crear, la reserva quedará en estado <span class="font-medium">activa</span> con fecha automática
                    y vencimiento al final del día. Luego podrás confirmarla desde el módulo de reservas.
                </div>
                <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-100">
                    <flux:button type="button" variant="outline" size="xs" wire:click="closeReservationModal">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" color="primary" size="xs" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed">
                        Guardar
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>


</div>
