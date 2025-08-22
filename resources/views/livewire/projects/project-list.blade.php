<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Proyectos</h1>
                    <p class="text-sm text-gray-600">Gestión de proyectos inmobiliarios</p>
                </div>
                <div class="flex space-x-2">
                    <flux:button size="xs" wire:click="exportProjects">
                        <flux:icon name="arrow-down-tray" class="w-4 h-4 mr-1" />
                        Exportar
                    </flux:button>
                    <flux:button size="xs" color="primary" wire:click="createProject">
                        <flux:icon name="plus" class="w-4 h-4 mr-1" />
                        Nuevo Proyecto
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
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar proyectos..." />
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="active">Activo</option>
                        <option value="completed">Completado</option>
                        <option value="on_hold">En pausa</option>
                        <option value="cancelled">Cancelado</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="typeFilter">
                        <option value="">Todos los tipos</option>
                        <option value="residential">Residencial</option>
                        <option value="commercial">Comercial</option>
                        <option value="industrial">Industrial</option>
                        <option value="land">Terreno</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="locationFilter">
                        <option value="">Todas las ubicaciones</option>
                        <option value="lima">Lima</option>
                        <option value="arequipa">Arequipa</option>
                        <option value="trujillo">Trujillo</option>
                        <option value="piura">Piura</option>
                    </flux:select>
                </div>
                <div>
                    <flux:button size="xs" variant="outline" wire:click="clearFilters">
                        Limpiar filtros
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Vista de Tarjetas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($projects as $project)
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Imagen del Proyecto -->
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500 relative">
                        @if ($project->image)
                            <img src="{{ $project->image }}" alt="{{ $project->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full">
                                <flux:icon name="building-office" class="w-16 h-16 text-white opacity-80" />
                            </div>
                        @endif
                        <div class="absolute top-3 right-3">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $project->status === 'active'
                                ? 'bg-green-100 text-green-800'
                                : ($project->status === 'completed'
                                    ? 'bg-blue-100 text-blue-800'
                                    : ($project->status === 'on_hold'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-red-100 text-red-800')) }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Contenido -->
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $project->name }}</h3>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($project->type) }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project->description }}</p>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <flux:icon name="map-pin" class="w-4 h-4 mr-2" />
                                <span class="truncate">{{ $project->location }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <flux:icon name="currency-dollar" class="w-4 h-4 mr-2" />
                                <span>S/ {{ number_format($project->starting_price) }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <flux:icon name="home" class="w-4 h-4 mr-2" />
                                <span>{{ $project->units_count ?? 0 }} unidades</span>
                            </div>
                        </div>

                        <!-- Progreso de Venta -->
                        @if ($project->units_count > 0)
                            <div class="mb-4">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Progreso de venta</span>
                                    <span>{{ $project->sold_units ?? 0 }}/{{ $project->units_count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full"
                                        style="width: {{ (($project->sold_units ?? 0) / $project->units_count) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Acciones -->
                        <div class="flex space-x-2">
                            <flux:button size="xs" variant="outline" wire:click="viewProject({{ $project->id }})"
                                class="flex-1">
                                <flux:icon name="eye" class="w-3 h-3 mr-1" />
                                Ver
                            </flux:button>
                            <flux:button size="xs" variant="outline" wire:click="editProject({{ $project->id }})"
                                class="flex-1">
                                <flux:icon name="pencil" class="w-3 h-3 mr-1" />
                                Editar
                            </flux:button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="flex flex-col items-center">
                        <flux:icon name="building-office" class="w-16 h-16 text-gray-300 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay proyectos</h3>
                        <p class="text-gray-500 mb-4">Comienza creando tu primer proyecto inmobiliario</p>
                        <flux:button size="xs" color="primary" wire:click="createProject">
                            Crear primer proyecto
                        </flux:button>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if ($projects->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $projects->links() }}
            </div>
        @endif
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <flux:icon name="exclamation-triangle" class="h-6 w-6 text-red-600" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Confirmar eliminación</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            ¿Estás seguro de que quieres eliminar este proyecto? Esta acción no se puede deshacer.
                        </p>
                    </div>
                    <div class="flex justify-center space-x-3 mt-4">
                        <flux:button size="xs" variant="outline" wire:click="cancelDelete">
                            Cancelar
                        </flux:button>
                        <flux:button size="xs" color="danger" wire:click="confirmDelete">
                            Eliminar
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
