<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Tareas</h1>
                    <p class="text-sm text-gray-600">Gestión de tareas y actividades</p>
                </div>
                <div class="flex space-x-2">
                    <x-filament::button size="xs" wire:click="exportTasks">
                        <x-filament::icon name="heroicon-o-arrow-down-tray" class="w-4 h-4 mr-1" />
                        Exportar
                    </x-filament::button>
                    <x-filament::button size="xs" color="primary" wire:click="createTask">
                        <x-filament::icon name="heroicon-o-plus" class="w-4 h-4 mr-1" />
                        Nueva Tarea
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <x-filament::input.wrapper>
                        <x-filament::input 
                            wire:model.live="search" 
                            placeholder="Buscar tareas..."
                            size="xs"
                        />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <x-filament::select wire:model.live="statusFilter" size="xs">
                        <option value="">Todos los estados</option>
                        <option value="pending">Pendiente</option>
                        <option value="in_progress">En progreso</option>
                        <option value="completed">Completada</option>
                        <option value="cancelled">Cancelada</option>
                    </x-filament::select>
                </div>
                <div>
                    <x-filament::select wire:model.live="priorityFilter" size="xs">
                        <option value="">Todas las prioridades</option>
                        <option value="low">Baja</option>
                        <option value="medium">Media</option>
                        <option value="high">Alta</option>
                        <option value="urgent">Urgente</option>
                    </x-filament::select>
                </div>
                <div>
                    <x-filament::select wire:model.live="assignedToFilter" size="xs">
                        <option value="">Todos los usuarios</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </x-filament::select>
                </div>
                <div>
                    <x-filament::button size="xs" variant="outline" wire:click="clearFilters">
                        Limpiar filtros
                    </x-filament::button>
                </div>
            </div>
        </div>

        <!-- Vista Kanban -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Pendientes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="bg-yellow-50 border-b border-yellow-200 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-yellow-800">Pendientes</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $tasks->where('status', 'pending')->count() }}
                        </span>
                    </div>
                </div>
                <div class="p-4 space-y-3 min-h-96">
                    @forelse($tasks->where('status', 'pending') as $task)
                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900 line-clamp-2">{{ $task->title }}</h4>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $task->priority === 'urgent' ? 'bg-red-100 text-red-800' : 
                                   ($task->priority === 'high' ? 'bg-orange-100 text-orange-800' : 
                                   ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')) }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $task->description }}</p>
                        
                        <div class="space-y-2 mb-3">
                            @if($task->due_date)
                            <div class="flex items-center text-xs text-gray-500">
                                <x-filament::icon name="heroicon-o-calendar" class="w-3 h-3 mr-1" />
                                <span>Vence: {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}</span>
                            </div>
                            @endif
                            @if($task->assigned_to)
                            <div class="flex items-center text-xs text-gray-500">
                                <x-filament::icon name="heroicon-o-user" class="w-3 h-3 mr-1" />
                                <span>{{ $task->assigned_to->name }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="flex space-x-2">
                            <x-filament::button size="xs" variant="outline" wire:click="startTask({{ $task->id }})" class="flex-1">
                                <x-filament::icon name="heroicon-o-play" class="w-3 h-3 mr-1" />
                                Iniciar
                            </x-filament::button>
                            <x-filament::button size="xs" variant="outline" wire:click="editTask({{ $task->id }})" class="flex-1">
                                <x-filament::icon name="heroicon-o-pencil" class="w-3 h-3 mr-1" />
                                Editar
                            </x-filament::button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 py-8">
                        <x-filament::icon name="heroicon-o-check-circle" class="w-8 h-8 mx-auto mb-2" />
                        <p class="text-sm">Sin tareas pendientes</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- En Progreso -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="bg-blue-50 border-b border-blue-200 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-blue-800">En Progreso</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $tasks->where('status', 'in_progress')->count() }}
                        </span>
                    </div>
                </div>
                <div class="p-4 space-y-3 min-h-96">
                    @forelse($tasks->where('status', 'in_progress') as $task)
                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900 line-clamp-2">{{ $task->title }}</h4>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $task->priority === 'urgent' ? 'bg-red-100 text-red-800' : 
                                   ($task->priority === 'high' ? 'bg-orange-100 text-orange-800' : 
                                   ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')) }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $task->description }}</p>
                        
                        <div class="space-y-2 mb-3">
                            @if($task->due_date)
                            <div class="flex items-center text-xs text-gray-500">
                                <x-filament::icon name="heroicon-o-calendar" class="w-3 h-3 mr-1" />
                                <span>Vence: {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}</span>
                            </div>
                            @endif
                            @if($task->assigned_to)
                            <div class="flex items-center text-xs text-gray-500">
                                <x-filament::icon name="heroicon-o-user" class="w-3 h-3 mr-1" />
                                <span>{{ $task->assigned_to->name }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="flex space-x-2">
                            <x-filament::button size="xs" variant="outline" wire:click="completeTask({{ $task->id }})" class="flex-1">
                                <x-filament::icon name="heroicon-o-check" class="w-3 h-3 mr-1" />
                                Completar
                            </x-filament::button>
                            <x-filament::button size="xs" variant="outline" wire:click="editTask({{ $task->id }})" class="flex-1">
                                <x-filament::icon name="heroicon-o-pencil" class="w-3 h-3 mr-1" />
                                Editar
                            </x-filament::button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 py-8">
                        <x-filament::icon name="heroicon-o-clock" class="w-8 h-8 mx-auto mb-2" />
                        <p class="text-sm">Sin tareas en progreso</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Completadas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="bg-green-50 border-b border-green-200 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-green-800">Completadas</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $tasks->where('status', 'completed')->count() }}
                        </span>
                    </div>
                </div>
                <div class="p-4 space-y-3 min-h-96">
                    @forelse($tasks->where('status', 'completed')->take(5) as $task)
                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm opacity-75">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900 line-clamp-2">{{ $task->title }}</h4>
                            <x-filament::icon name="heroicon-o-check-circle" class="w-4 h-4 text-green-600" />
                        </div>
                        <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $task->description }}</p>
                        
                        <div class="space-y-2 mb-3">
                            @if($task->completed_at)
                            <div class="flex items-center text-xs text-gray-500">
                                <x-filament::icon name="heroicon-o-calendar" class="w-3 h-3 mr-1" />
                                <span>Completada: {{ \Carbon\Carbon::parse($task->completed_at)->format('M d') }}</span>
                            </div>
                            @endif
                            @if($task->assigned_to)
                            <div class="flex items-center text-xs text-gray-500">
                                <x-filament::icon name="heroicon-o-user" class="w-3 h-3 mr-1" />
                                <span>{{ $task->assigned_to->name }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="flex space-x-2">
                            <x-filament::button size="xs" variant="outline" wire:click="reopenTask({{ $task->id }})" class="flex-1">
                                <x-filament::icon name="heroicon-o-arrow-path" class="w-3 h-3 mr-1" />
                                Reabrir
                            </x-filament::button>
                            <x-filament::button size="xs" variant="outline" wire:click="viewTask({{ $task->id }})" class="flex-1">
                                <x-filament::icon name="heroicon-o-eye" class="w-3 h-3 mr-1" />
                                Ver
                            </x-filament::button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 py-8">
                        <x-filament::icon name="heroicon-o-check-circle" class="w-8 h-8 mx-auto mb-2" />
                        <p class="text-sm">Sin tareas completadas</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Canceladas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="bg-red-50 border-b border-red-200 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-red-800">Canceladas</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $tasks->where('status', 'cancelled')->count() }}
                        </span>
                    </div>
                </div>
                <div class="p-4 space-y-3 min-h-96">
                    @forelse($tasks->where('status', 'cancelled')->take(5) as $task)
                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm opacity-75">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900 line-clamp-2">{{ $task->title }}</h4>
                            <x-filament::icon name="heroicon-o-x-circle" class="w-4 h-4 text-red-600" />
                        </div>
                        <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $task->description }}</p>
                        
                        <div class="space-y-2 mb-3">
                            @if($task->cancelled_at)
                            <div class="flex items-center text-xs text-gray-500">
                                <x-filament::icon name="heroicon-o-calendar" class="w-3 h-3 mr-1" />
                                <span>Cancelada: {{ \Carbon\Carbon::parse($task->cancelled_at)->format('M d') }}</span>
                            </div>
                            @endif
                            @if($task->assigned_to)
                            <div class="flex items-center text-xs text-gray-500">
                                <x-filament::icon name="heroicon-o-user" class="w-3 h-3 mr-1" />
                                <span>{{ $task->assigned_to->name }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="flex space-x-2">
                            <x-filament::button size="xs" variant="outline" wire:click="reopenTask({{ $task->id }})" class="flex-1">
                                <x-filament::icon name="heroicon-o-arrow-path" class="w-3 h-3 mr-1" />
                                Reabrir
                            </x-filament::button>
                            <x-filament::button size="xs" variant="outline" wire:click="viewTask({{ $task->id }})" class="flex-1">
                                <x-filament::icon name="heroicon-o-eye" class="w-3 h-3 mr-1" />
                                Ver
                            </x-filament::button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 py-8">
                        <x-filament::icon name="heroicon-o-x-circle" class="w-8 h-8 mx-auto mb-2" />
                        <p class="text-sm">Sin tareas canceladas</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Vista de Lista (Alternativa) -->
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Vista de Lista</h3>
                <x-filament::button size="xs" variant="outline" wire:click="toggleListView">
                    <x-filament::icon name="heroicon-o-list-bullet" class="w-4 h-4 mr-1" />
                    {{ $showListView ? 'Ocultar' : 'Mostrar' }} Lista
                </x-filament::button>
            </div>

            @if($showListView)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tarea
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prioridad
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Asignado a
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha Vencimiento
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tasks as $task)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                        <div class="text-xs text-gray-500 line-clamp-2">{{ $task->description }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                           ($task->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $task->priority === 'urgent' ? 'bg-red-100 text-red-800' : 
                                           ($task->priority === 'high' ? 'bg-orange-100 text-orange-800' : 
                                           ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')) }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $task->assigned_to->name ?? 'Sin asignar' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'Sin fecha' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <x-filament::button size="xs" variant="outline" wire:click="viewTask({{ $task->id }})">
                                            <x-filament::icon name="heroicon-o-eye" class="w-3 h-3" />
                                        </x-filament::button>
                                        <x-filament::button size="xs" variant="outline" wire:click="editTask({{ $task->id }})">
                                            <x-filament::icon name="heroicon-o-pencil" class="w-3 h-3" />
                                        </x-filament::button>
                                        <x-filament::button size="xs" variant="outline" color="danger" wire:click="deleteTask({{ $task->id }})">
                                            <x-filament::icon name="heroicon-o-trash" class="w-3 h-3" />
                                        </x-filament::button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                    <x-filament::icon name="heroicon-o-exclamation-triangle" class="h-6 w-6 text-red-600" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Confirmar eliminación</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        ¿Estás seguro de que quieres eliminar esta tarea? Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="flex justify-center space-x-3 mt-4">
                    <x-filament::button size="xs" variant="outline" wire:click="cancelDelete">
                        Cancelar
                    </x-filament::button>
                    <x-filament::button size="xs" color="danger" wire:click="confirmDelete">
                        Eliminar
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
