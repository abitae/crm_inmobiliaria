<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Tareas</h1>
                    <p class="text-sm text-gray-600">Gestión de tareas del CRM</p>
                </div>
                <div class="flex space-x-2">
                    <flux:button icon="plus" size="xs" color="primary" wire:click="$dispatch('show-info', { message: 'Crear tarea aún no implementado' })">
                        Nueva Tarea
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <flux:input size="xs" icon="magnifying-glass" type="search" label="Buscar" placeholder="Título o descripción" wire:model.debounce.400ms="search" />
                </div>
                <div>
                    <flux:select size="xs" icon="users" label="Cliente" wire:model.live="clientFilter">
                        <option value="">Todos los clientes</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" label="Estado" wire:model.live="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_progreso">En progreso</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" label="Prioridad" wire:model.live="priorityFilter">
                        <option value="">Todas las prioridades</option>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </flux:select>
                </div>
                <div>
                    <flux:button size="xs" variant="outline" wire:click="clearFilters">
                        Limpiar filtros
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Tabla de Tareas Compacta -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Vencimiento</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Título</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Cliente</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Prioridad</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Estado</th>
                            <th class="px-2 py-2 text-right font-semibold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($tasks as $task)
                            <tr wire:key="task-{{ $task->id }}" class="hover:bg-gray-50">
                                <td class="px-2 py-2 whitespace-nowrap text-gray-600">
                                    @if($task->due_date)
                                        <div class="font-medium {{ $task->due_date->isPast() && $task->status !== 'completada' ? 'text-red-600' : '' }}">
                                            {{ $task->due_date->format('d/m/Y') }}
                                        </div>
                                        @if($task->due_date->isPast() && $task->status !== 'completada')
                                            <div class="text-[10px] text-red-400">Vencida</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">Sin fecha</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    <div class="font-medium text-gray-900">{{ $task->title }}</div>
                                    @if($task->description)
                                        <div class="text-[10px] text-gray-400 truncate max-w-xs">{{ Str::limit($task->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-600">
                                    {{ $task->client?->name ?? 'N/A' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium
                                        {{ $task->priority === 'urgente'
                                            ? 'bg-red-100 text-red-800'
                                            : ($task->priority === 'alta'
                                                ? 'bg-orange-100 text-orange-800'
                                                : ($task->priority === 'media'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium
                                        {{ $task->status === 'pendiente'
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : ($task->status === 'en_progreso'
                                                ? 'bg-blue-100 text-blue-800'
                                                : ($task->status === 'completada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex justify-end space-x-1">
                                        <flux:button size="xs" variant="outline" wire:click="$dispatch('show-info', { message: 'Detalle aún no implementado' })">
                                            <flux:icon name="eye" class="w-3 h-3" />
                                        </flux:button>
                                        <flux:button size="xs" variant="danger" color="danger" wire:click="confirmDelete({{ $task->id }})">
                                            <flux:icon name="trash" class="w-3 h-3" />
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="clipboard-document-list" class="w-12 h-12 text-gray-300 mb-2" />
                                        <p>No se encontraron tareas</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            @if ($tasks->hasPages())
                <div class="bg-white px-2 py-2 border-t border-gray-200">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>

    <flux:modal wire:model="showDeleteModal" class="w-96">
        <div class="p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-2">Confirmar eliminación</h3>
            <p class="text-sm text-gray-600">¿Seguro que deseas eliminar esta tarea? Esta acción no se puede deshacer.</p>
            <div class="mt-4 flex justify-end space-x-2">
                <flux:button size="xs" variant="outline" icon="x-mark" wire:click="closeDeleteModal">Cancelar</flux:button>
                <flux:button size="xs" color="danger" icon="trash" wire:click="deleteTask">Eliminar</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
