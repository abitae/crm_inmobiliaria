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
                    <flux:button icon="arrow-down-tray" size="xs" wire:click="exportProjects">
                        Exportar
                    </flux:button>
                    <flux:button icon="plus" size="xs" color="primary" wire:click="openCreateModal">
                        Nuevo Proyecto
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje de Éxito -->
    @if (session()->has('message'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3"
                    onclick="this.parentElement.style.display='none'">
                    <flux:icon name="x-mark" class="w-4 h-4" />
                </button>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                <div>
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar proyectos..." />
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="suspendido">Suspendido</option>
                        <option value="finalizado">Finalizado</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="typeFilter">
                        <option value="">Todos los tipos</option>
                        <option value="lotes">Lotes</option>
                        <option value="casas">Casas</option>
                        <option value="departamentos">Departamentos</option>
                        <option value="oficinas">Oficinas</option>
                        <option value="mixto">Mixto</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="stageFilter">
                        <option value="">Todas las etapas</option>
                        <option value="preventa">Preventa</option>
                        <option value="lanzamiento">Lanzamiento</option>
                        <option value="venta_activa">Venta Activa</option>
                        <option value="cierre">Cierre</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="locationFilter">
                        <option value="">Todas las ubicaciones</option>
                        <option value="lima">Lima</option>
                        <option value="arequipa">Arequipa</option>
                        <option value="trujillo">Trujillo</option>
                        <option value="piura">Piura</option>
                        <option value="chiclayo">Chiclayo</option>
                        <option value="cusco">Cusco</option>
                    </flux:select>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" wire:model.live="withAvailableUnits"
                            class="mr-2 rounded border-gray-300">
                        Solo con unidades disponibles
                    </label>
                </div>
                <div>
                    <flux:button size="xs" variant="outline" wire:click="clearFilters">
                        Limpiar filtros
                    </flux:button>
                </div>
            </div>

            <!-- Filtros de Ordenamiento -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-200">
                <div>
                    <flux:select size="xs" wire:model.live="orderBy">
                        <option value="created_at">Ordenar por</option>
                        <option value="name">Nombre</option>
                        <option value="created_at">Fecha de creación</option>
                        <option value="updated_at">Fecha de actualización</option>
                        <option value="start_date">Fecha de inicio</option>
                        <option value="total_units">Total de unidades</option>
                        <option value="available_units">Unidades disponibles</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="orderDirection">
                        <option value="desc">Descendente</option>
                        <option value="asc">Ascendente</option>
                    </flux:select>
                </div>
                <div class="col-span-2"></div>
            </div>
        </div>

        <!-- Vista de Tarjetas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($projects as $project)
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Imagen del Proyecto -->
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500 relative">
                        @if ($project->path_image_portada)
                            <img src="{{ $project->path_image_portada }}" alt="{{ $project->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full">
                                <flux:icon name="building-office" class="w-16 h-16 text-white opacity-80" />
                            </div>
                        @endif
                        <div class="absolute top-3 right-3">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $project->status === 'activo'
                                ? 'bg-green-100 text-green-800'
                                : ($project->status === 'finalizado'
                                    ? 'bg-blue-100 text-blue-800'
                                    : ($project->status === 'suspendido'
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
                                {{ ucfirst($project->project_type) }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project->description }}</p>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <flux:icon name="map-pin" class="w-4 h-4 mr-2" />
                                <span class="truncate">{{ $project->full_address }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <flux:icon name="currency-dollar" class="w-4 h-4 mr-2" />
                                <span>S/ {{ number_format($project->current_price?->price ?? 0) }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <flux:icon name="home" class="w-4 h-4 mr-2" />
                                <span>{{ $project->units_count ?? 0 }} unidades</span>
                            </div>
                        </div>

                        <!-- Progreso de Venta -->
                        @if ($project->total_units > 0)
                            <div class="mb-4">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Progreso de venta</span>
                                    <span>{{ $project->sold_units ?? 0 }}/{{ $project->total_units }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full"
                                        style="width: {{ $project->progress_percentage }}%">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Acciones -->
                        <div class="flex space-x-2">
                            <flux:button icon="eye" size="xs" variant="outline"
                                wire:click="viewProject({{ $project->id }})" class="flex-1">
                                Ver
                            </flux:button>
                            <flux:button icon="pencil" size="xs" variant="outline"
                                wire:click="openEditModal({{ $project->id }})" class="flex-1">
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

    <!-- Modal de Creación/Edición -->
    <flux:modal variant="flyout" wire:model="showFormModal" class="max-w-7xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $editingProject ? 'Editar Proyecto' : 'Crear Nuevo Proyecto' }}
                </h3>
            </div>

            <form wire:submit.prevent="{{ $editingProject ? 'updateProject' : 'createProject' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Información Básica -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-700">Información Básica</h4>

                        <div>

                            <flux:input wire:model="name" placeholder="Nombre del proyecto" size="xs" />
                        </div>

                        <div>

                            <textarea wire:model="description" rows="3"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Descripción del proyecto"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>

                                <flux:select wire:model="project_type" size="xs">
                                    <option value="">Seleccionar tipo</option>
                                    <option value="lotes">Lotes</option>
                                    <option value="casas">Casas</option>
                                    <option value="departamentos">Departamentos</option>
                                    <option value="oficinas">Oficinas</option>
                                    <option value="mixto">Mixto</option>
                                </flux:select>
                            </div>

                            <div>

                                <flux:select wire:model="stage" size="xs">
                                    <option value="">Seleccionar etapa</option>
                                    <option value="preventa">Preventa</option>
                                    <option value="lanzamiento">Lanzamiento</option>
                                    <option value="venta_activa">Venta Activa</option>
                                    <option value="cierre">Cierre</option>
                                </flux:select>
                            </div>
                        </div>

                        <div>

                            <flux:select wire:model="legal_status" size="xs">
                                <option value="">Seleccionar estado</option>
                                <option value="con_titulo">Con Título</option>
                                <option value="en_tramite">En Trámite</option>
                                <option value="habilitado">Habilitado</option>
                            </flux:select>
                        </div>

                        <div>

                            <flux:input wire:model="total_units" type="number" min="1" placeholder="0"
                                size="xs" />
                        </div>

                        <div>

                            <flux:select wire:model="status" size="xs">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="suspendido">Suspendido</option>
                                <option value="finalizado">Finalizado</option>
                            </flux:select>
                        </div>
                    </div>

                    <!-- Ubicación y Fechas -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100 shadow-sm">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <flux:icon name="map-pin" class="w-5 h-5 text-green-600" />
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Ubicación y Fechas</h4>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> Dirección Completa
                                </label>
                                <flux:input wire:model="address" placeholder="Ingrese la dirección completa del proyecto" 
                                    size="sm" class="w-full" />
                                @error('address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Distrito</label>
                                    <flux:input wire:model="district" placeholder="Distrito" size="sm" class="w-full" />
                                    @error('district') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Provincia</label>
                                    <flux:input wire:model="province" placeholder="Provincia" size="sm" class="w-full" />
                                    @error('province') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Región</label>
                                    <flux:input wire:model="region" placeholder="Región" size="sm" class="w-full" />
                                    @error('region') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">País</label>
                                    <flux:input wire:model="country" placeholder="País" size="sm" class="w-full" />
                                    @error('country') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Latitud</label>
                                    <flux:input wire:model="latitude" type="number" step="any"
                                        placeholder="0.000000" size="sm" class="w-full" />
                                    @error('latitude') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Longitud</label>
                                    <flux:input wire:model="longitude" type="number" step="any"
                                        placeholder="0.000000" size="sm" class="w-full" />
                                    @error('longitude') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inicio</label>
                                    <flux:input wire:model="start_date" type="date" size="sm" class="w-full" />
                                    @error('start_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Fin</label>
                                    <flux:input wire:model="end_date" type="date" size="sm" class="w-full" />
                                    @error('end_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Entrega</label>
                                    <flux:input wire:model="delivery_date" type="date" size="sm" class="w-full" />
                                    @error('delivery_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Unidades del Proyecto -->
                    <div class="mt-8 bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-6 border border-yellow-100">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <flux:icon name="home" class="w-5 h-5 text-yellow-600" />
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Unidades del Proyecto</h4>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Información General de Unidades -->
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <span class="text-red-500">*</span> Total de Unidades
                                    </label>
                                    <flux:input wire:model="total_units" type="number" min="1" placeholder="0"
                                        size="sm" class="w-full" />
                                    @error('total_units') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidades Disponibles</label>
                                        <flux:input wire:model="available_units" type="number" min="0" placeholder="0"
                                            size="sm" class="w-full" />
                                        @error('available_units') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidades Reservadas</label>
                                        <flux:input wire:model="reserved_units" type="number" min="0" placeholder="0"
                                            size="sm" class="w-full" />
                                        @error('reserved_units') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidades Vendidas</label>
                                        <flux:input wire:model="sold_units" type="number" min="0" placeholder="0"
                                            size="sm" class="w-full" />
                                        @error('sold_units') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidades Bloqueadas</label>
                                        <flux:input wire:model="blocked_units" type="number" min="0" placeholder="0"
                                            size="sm" class="w-full" />
                                        @error('blocked_units') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="bg-yellow-100 border border-yellow-200 rounded-lg p-4">
                                    <h6 class="font-medium text-yellow-800 mb-2 flex items-center">
                                        <flux:icon name="information-circle" class="w-4 h-4 mr-2" />
                                        Información de Unidades
                                    </h6>
                                    <div class="text-sm text-yellow-700 space-y-1">
                                        <div>• <strong>Total:</strong> Suma de todas las unidades del proyecto</div>
                                        <div>• <strong>Disponibles:</strong> Unidades que se pueden vender</div>
                                        <div>• <strong>Reservadas:</strong> Unidades con reserva temporal</div>
                                        <div>• <strong>Vendidas:</strong> Unidades ya comercializadas</div>
                                        <div>• <strong>Bloqueadas:</strong> Unidades no disponibles</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gestión de Unidades -->
                            <div class="space-y-5">
                                <div class="bg-white rounded-lg border border-yellow-200 p-4">
                                    <h6 class="font-medium text-gray-800 mb-3">Gestión de Unidades</h6>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="text-sm font-medium text-gray-700">Actualizar Conteos</div>
                                                <div class="text-xs text-gray-500">Recalcula automáticamente los conteos</div>
                                            </div>
                                            <flux:button icon="arrow-path" type="button" size="sm" variant="outline"
                                                wire:click="updateUnitCounts({{ $editingProject->id ?? 0 }})" 
                                                class="text-yellow-600 hover:bg-yellow-50">
                                                Actualizar
                                            </flux:button>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="text-sm font-medium text-gray-700">Gestionar Unidades</div>
                                                <div class="text-xs text-gray-500">Agregar, editar o eliminar unidades</div>
                                            </div>
                                            <flux:button icon="plus" type="button" size="sm" variant="outline"
                                                class="text-yellow-600 hover:bg-yellow-50">
                                                Gestionar
                                            </flux:button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estadísticas Rápidas -->
                                <div class="bg-white rounded-lg border border-yellow-200 p-4">
                                    <h6 class="font-medium text-gray-800 mb-3">Estadísticas del Proyecto</h6>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                                            <div class="text-2xl font-bold text-blue-600">
                                                {{ $editingProject ? round((($editingProject->sold_units + $editingProject->reserved_units) / max($editingProject->total_units, 1)) * 100) : 0 }}%
                                            </div>
                                            <div class="text-xs text-blue-700">Progreso de Venta</div>
                                        </div>
                                        <div class="text-center p-3 bg-green-50 rounded-lg">
                                            <div class="text-2xl font-bold text-green-600">
                                                {{ $editingProject ? $editingProject->available_units : 0 }}
                                            </div>
                                            <div class="text-xs text-green-700">Disponibles</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Unidades del Proyecto -->
                    <div class="mt-8 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-6 border border-emerald-100">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-emerald-100 rounded-lg">
                                <flux:icon name="building-office" class="w-5 h-5 text-emerald-600" />
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Información de Unidades del Proyecto</h4>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Tipos de Unidades -->
                            <div class="space-y-5">
                                <div class="bg-white rounded-lg border border-emerald-200 p-4">
                                    <h6 class="font-medium text-gray-800 mb-3">Tipos de Unidades Disponibles</h6>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                                                <span class="text-sm font-medium text-gray-700">Lotes</span>
                                            </div>
                                            <span class="text-sm text-gray-600">0 unidades</span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                                <span class="text-sm font-medium text-gray-700">Casas</span>
                                            </div>
                                            <span class="text-sm text-gray-600">0 unidades</span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
                                                <span class="text-sm font-medium text-gray-700">Departamentos</span>
                                            </div>
                                            <span class="text-sm text-gray-600">0 unidades</span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <span class="w-3 h-3 bg-orange-500 rounded-full"></span>
                                                <span class="text-sm font-medium text-gray-700">Oficinas</span>
                                            </div>
                                            <span class="text-sm text-gray-600">0 unidades</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones de Gestión -->
                            <div class="space-y-5">
                                <div class="bg-white rounded-lg border border-emerald-200 p-4">
                                    <h6 class="font-medium text-gray-800 mb-3">Acciones de Gestión</h6>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="text-sm font-medium text-gray-700">Crear Nueva Unidad</div>
                                                <div class="text-xs text-gray-500">Agregar unidad individual al proyecto</div>
                                            </div>
                                            <flux:button icon="plus" type="button" size="sm" variant="outline"
                                                class="text-emerald-600 hover:bg-emerald-50">
                                                Crear
                                            </flux:button>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="text-sm font-medium text-gray-700">Importar Unidades</div>
                                                <div class="text-xs text-gray-500">Importar desde Excel o CSV</div>
                                            </div>
                                            <flux:button icon="arrow-up-tray" type="button" size="sm" variant="outline"
                                                class="text-emerald-600 hover:bg-emerald-50">
                                                Importar
                                            </flux:button>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="text-sm font-medium text-gray-700">Exportar Lista</div>
                                                <div class="text-xs text-gray-500">Descargar lista de unidades</div>
                                            </div>
                                            <flux:button icon="arrow-down-tray" type="button" size="sm" variant="outline"
                                                class="text-emerald-600 hover:bg-emerald-50">
                                                Exportar
                                            </flux:button>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-emerald-100 border border-emerald-200 rounded-lg p-4">
                                    <h6 class="font-medium text-emerald-800 mb-2 flex items-center">
                                        <flux:icon name="information-circle" class="w-4 h-4 mr-2" />
                                        Información sobre Unidades
                                    </h6>
                                    <div class="text-sm text-emerald-700 space-y-1">
                                        <div>• <strong>Modelo Unit:</strong> Cada unidad tiene su propio precio y configuración</div>
                                        <div>• <strong>Modelo UnitPrice:</strong> Historial de cambios de precios por unidad</div>
                                        <div>• <strong>Gestión Individual:</strong> Los precios se configuran por unidad, no por proyecto</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Precios del Proyecto -->
                    <div class="mt-8 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6 border border-indigo-100">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <flux:icon name="currency-dollar" class="w-5 h-5 text-indigo-600" />
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800">Configuración de Precios del Proyecto</h4>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Configuración de Precios -->
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Moneda Principal</label>
                                    <flux:select wire:model="currency" size="sm" class="w-full">
                                        <option value="PEN">Soles (PEN)</option>
                                        <option value="USD">Dólares (USD)</option>
                                        <option value="EUR">Euros (EUR)</option>
                                    </flux:select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                                                    <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Precio Base Promedio</label>
                                    <flux:input wire:model="base_price" type="number" step="0.01" min="0" placeholder="0.00"
                                        size="sm" class="w-full" />
                                    <p class="text-xs text-gray-500 mt-1">Precio base promedio por unidad</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Precio Promedio por m²</label>
                                    <flux:input wire:model="price_per_sqm" type="number" step="0.01" min="0" placeholder="0.00"
                                        size="sm" class="w-full" />
                                    <p class="text-xs text-gray-500 mt-1">Precio promedio por metro cuadrado</p>
                                </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Descuento (%)</label>
                                        <flux:input wire:model="discount_percentage" type="number" step="0.1" min="0" max="100" placeholder="0.0"
                                            size="sm" class="w-full" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio Final</label>
                                        <flux:input wire:model="final_price" type="number" step="0.01" min="0" placeholder="0.00"
                                            size="sm" class="w-full" readonly />
                                    </div>
                                </div>

                                <div class="bg-indigo-100 border border-indigo-200 rounded-lg p-4">
                                    <h6 class="font-medium text-indigo-800 mb-2 flex items-center">
                                        <flux:icon name="information-circle" class="w-4 h-4 mr-2" />
                                        Información de Precios del Proyecto
                                    </h6>
                                    <div class="text-sm text-indigo-700 space-y-1">
                                        <div>• <strong>Precio Base:</strong> Precio promedio base por unidad del proyecto</div>
                                        <div>• <strong>Precio por m²:</strong> Precio promedio por metro cuadrado del proyecto</div>
                                        <div>• <strong>Nota:</strong> Los precios específicos se configuran por unidad individual</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gestión de Precios -->
                            <div class="space-y-5">
                                <div class="bg-white rounded-lg border border-indigo-200 p-4">
                                    <h6 class="font-medium text-gray-800 mb-3">Gestión de Unidades y Precios</h6>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="text-sm font-medium text-gray-700">Gestionar Unidades</div>
                                                <div class="text-xs text-gray-500">Configurar precios por unidad individual</div>
                                            </div>
                                            <flux:button icon="plus" type="button" size="sm" variant="outline"
                                                class="text-indigo-600 hover:bg-indigo-50">
                                                Gestionar
                                            </flux:button>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="text-sm font-medium text-gray-700">Historial de Precios</div>
                                                <div class="text-xs text-gray-500">Ver cambios de precios por unidad</div>
                                            </div>
                                            <flux:button icon="clock" type="button" size="sm" variant="outline"
                                                class="text-indigo-600 hover:bg-indigo-50">
                                                Ver Historial
                                            </flux:button>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-lg border border-indigo-200 p-4">
                                    <h6 class="font-medium text-gray-800 mb-3">Opciones de Pago del Proyecto</h6>
                                    <div class="space-y-3">
                                        <div class="flex items-center space-x-3">
                                            <input type="checkbox" wire:model="accepts_credit" id="accepts_credit" class="rounded border-gray-300">
                                            <label for="accepts_credit" class="text-sm text-gray-700">Acepta crédito hipotecario</label>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <input type="checkbox" wire:model="accepts_cash" id="accepts_cash" class="rounded border-gray-300">
                                            <label for="accepts_cash" class="text-sm text-gray-700">Acepta pago en efectivo</label>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <input type="checkbox" wire:model="accepts_transfer" id="accepts_transfer" class="rounded border-gray-300">
                                            <label for="accepts_transfer" class="text-sm text-gray-700">Acepta transferencias</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-lg border border-indigo-200 p-4">
                                    <h6 class="font-medium text-gray-800 mb-3">Promociones Activas</h6>
                                    <div class="space-y-3">
                                        <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                            <div class="text-sm font-medium text-green-800">Descuento por Pronto Pago</div>
                                            <div class="text-xs text-green-600">5% de descuento si se paga en 30 días</div>
                                        </div>
                                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                            <div class="text-sm font-medium text-blue-800">Financiamiento Directo</div>
                                            <div class="text-xs text-blue-600">Hasta 24 meses sin intereses</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imagen y Video de Portada -->
                <div class="mt-8 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <flux:icon name="photo" class="w-5 h-5 text-purple-600" />
                        </div>
                        <h4 class="text-lg font-semibold text-gray-800">Imagen y Video de Portada</h4>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Imagen de Portada -->
                        <div class="bg-white rounded-lg border border-purple-200 p-4">
                            <div class="flex items-center space-x-2 mb-3">
                                <flux:icon name="photo" class="w-4 h-4 text-purple-600" />
                                <h6 class="font-medium text-gray-800">Imagen de Portada</h6>
                            </div>
                            <div class="space-y-3">
                                @if($path_image_portada)
                                    <div class="relative">
                                        <img src="{{ $path_image_portada }}" alt="Imagen de portada" 
                                            class="w-full h-32 object-cover rounded-lg border border-purple-200">
                                        <button type="button" wire:click="removeImagePortada" 
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors duration-200">
                                            <flux:icon name="x-mark" class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endif
                                <div class="border-2 border-dashed border-purple-200 rounded-lg p-4 text-center hover:border-purple-300 transition-colors duration-200">
                                    <input type="file" wire:model="imagePortadaFile" accept="image/*" 
                                        class="w-full text-sm">
                                    <p class="text-xs text-gray-500 mt-2">Formatos: JPG, PNG, GIF. Máximo 2MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Video de Portada -->
                        <div class="bg-white rounded-lg border border-purple-200 p-4">
                            <div class="flex items-center space-x-2 mb-3">
                                <flux:icon name="video-camera" class="w-4 h-4 text-purple-600" />
                                <h6 class="font-medium text-gray-800">Video de Portada</h6>
                            </div>
                            <div class="space-y-3">
                                @if($path_video_portada)
                                    <div class="relative">
                                        <video controls class="w-full h-32 object-cover rounded-lg border border-purple-200">
                                            <source src="{{ $path_video_portada }}" type="video/mp4">
                                            Tu navegador no soporta el elemento video.
                                        </video>
                                        <button type="button" wire:click="removeVideoPortada" 
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors duration-200">
                                            <flux:icon name="x-mark" class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endif
                                <div class="border-2 border-dashed border-purple-200 rounded-lg p-4 text-center hover:border-purple-300 transition-colors duration-200">
                                    <input type="file" wire:model="videoPortadaFile" accept="video/*" 
                                        class="w-full text-sm">
                                    <p class="text-xs text-gray-500 mt-2">Formatos: MP4, AVI, MOV, WMV. Máximo 10MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos Multimedia -->
                <div class="mt-8 bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl p-6 border border-orange-100">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <flux:icon name="document-text" class="w-5 h-5 text-orange-600" />
                        </div>
                        <h4 class="text-lg font-semibold text-gray-800">Archivos Multimedia Adicionales</h4>
                    </div>

                    <!-- Información de archivos -->
                    <div class="bg-orange-100 border border-orange-200 rounded-xl p-4 mb-6">
                        <h5 class="font-medium text-orange-800 mb-3 flex items-center">
                            <flux:icon name="information-circle" class="w-4 h-4 mr-2" />
                            Información sobre archivos
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-orange-700">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                <span><strong>Portada:</strong> Imagen JPG, PNG, GIF (máx. 2MB)</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                <span><strong>Portada:</strong> Video MP4, AVI, MOV, WMV (máx. 10MB)</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                <span><strong>Imágenes:</strong> JPG, PNG, GIF (máx. 2MB) - <em>Título obligatorio</em></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                <span><strong>Videos:</strong> MP4, AVI, MOV, WMV (máx. 10MB) - <em>Título obligatorio</em></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                <span><strong>Documentos:</strong> PDF, DOC, DOCX (máx. 5MB) - <em>Título obligatorio</em></span>
                            </div>
                        </div>
                    </div>

                    <!-- Pestañas de Multimedia -->
                    <div class="bg-white rounded-xl border border-orange-200 overflow-hidden">
                        <!-- Navegación de pestañas -->
                        <div class="flex border-b border-orange-200">
                            <button type="button" onclick="showTab('images')" id="tab-images" 
                                class="flex-1 px-4 py-3 text-sm font-medium text-orange-700 bg-orange-50 border-r border-orange-200 hover:bg-orange-100 transition-colors duration-200">
                                🖼️ Imágenes ({{ count($path_images) }})
                            </button>
                            <button type="button" onclick="showTab('videos')" id="tab-videos" 
                                class="flex-1 px-4 py-3 text-sm font-medium text-gray-600 bg-white border-r border-orange-200 hover:bg-gray-50 transition-colors duration-200">
                                🎥 Videos ({{ count($path_videos) }})
                            </button>
                            <button type="button" onclick="showTab('documents')" id="tab-documents" 
                                class="flex-1 px-4 py-3 text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 transition-colors duration-200">
                                📄 Documentos ({{ count($path_documents) }})
                            </button>
                        </div>

                        <!-- Contenido de las pestañas -->
                        <div class="p-6">
                            <!-- Pestaña de Imágenes -->
                            <div id="content-images" class="tab-content">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-lg font-semibold text-gray-800">Imágenes del Proyecto</h5>
                                    <flux:button icon="plus" type="button" size="sm" color="primary"
                                        wire:click="addImage" class="flex items-center space-x-2">
                                        <flux:icon name="plus" class="w-4 h-4" />
                                        <span>Agregar Imagen</span>
                                    </flux:button>
                                </div>

                                @forelse($path_images as $index => $image)
                                    <div class="bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 rounded-lg p-4 mb-3">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <span class="text-red-500">*</span> Título
                                                </label>
                                                <flux:input wire:model="path_images.{{ $index }}.title"
                                                    placeholder="exterior, interior, etc." size="sm" class="w-full" />
                                                @error("path_images.{$index}.title") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Archivo</label>
                                                <input type="file" wire:model="imageFiles.{{ $index }}"
                                                    accept="image/*" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                                                <flux:input wire:model="path_images.{{ $index }}.descripcion"
                                                    placeholder="Descripción de la imagen" size="sm" class="w-full" />
                                            </div>
                                        </div>
                                        <div class="mt-4 flex items-center justify-between">
                                            <div class="flex-1">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Vista previa</label>
                                                @if (isset($image['path']) && $image['path'] && !isset($imageFiles[$index]))
                                                    <img src="{{ $image['path'] }}" alt="{{ $image['title'] ?? 'Imagen' }}"
                                                        class="w-16 h-16 object-cover rounded border">
                                                @elseif(isset($imageFiles[$index]) && $imageFiles[$index])
                                                    <div class="text-xs text-green-600 bg-green-50 p-2 rounded">
                                                        ✅ {{ $imageFiles[$index]->getClientOriginalName() }}
                                                    </div>
                                                @else
                                                    <div class="text-xs text-gray-500 bg-gray-50 p-2 rounded">📋 Sin archivo</div>
                                                @endif
                                            </div>
                                            <flux:button type="button" size="sm" color="danger"
                                                wire:click="removeImage({{ $index }})" 
                                                class="ml-4 p-2 rounded-lg hover:bg-red-100 transition-colors duration-200">
                                                <flux:icon name="trash" class="w-4 h-4" />
                                            </flux:button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <flux:icon name="photo" class="w-12 h-12 text-orange-300 mx-auto mb-3" />
                                        <p class="text-gray-500 text-sm">No hay imágenes agregadas</p>
                                        <p class="text-gray-400 text-xs mt-1">Haz clic en "Agregar Imagen" para comenzar</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Pestaña de Videos -->
                            <div id="content-videos" class="tab-content hidden">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-lg font-semibold text-gray-800">Videos del Proyecto</h5>
                                    <flux:button icon="plus" type="button" size="sm" color="primary"
                                        wire:click="addVideo" class="flex items-center space-x-2">
                                        <flux:icon name="plus" class="w-4 h-4" />
                                        <span>Agregar Video</span>
                                    </flux:button>
                                </div>

                                @forelse($path_videos as $index => $video)
                                    <div class="bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 rounded-lg p-4 mb-3">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <span class="text-red-500">*</span> Título
                                                </label>
                                                <flux:input wire:model="path_videos.{{ $index }}.title"
                                                    placeholder="tour, presentación, etc." size="sm" class="w-full" />
                                                @error("path_videos.{$index}.title") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Archivo</label>
                                                <input type="file" wire:model="videoFiles.{{ $index }}"
                                                    accept="video/*" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                                                <flux:input wire:model="path_videos.{{ $index }}.descripcion"
                                                    placeholder="Descripción del video" size="sm" class="w-full" />
                                            </div>
                                        </div>
                                        <div class="mt-4 flex items-center justify-between">
                                            <div class="flex-1">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                                @if (isset($video['path']) && $video['path'] && !isset($videoFiles[$index]))
                                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                        <div class="text-xs text-blue-600 font-medium">Video existente: {{ $video['title'] ?? 'Video' }}</div>
                                                    </div>
                                                @elseif(isset($videoFiles[$index]) && $videoFiles[$index])
                                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                                        <div class="text-xs text-green-600 font-medium">✅ {{ $videoFiles[$index]->getClientOriginalName() }}</div>
                                                    </div>
                                                @else
                                                    <div class="text-xs text-gray-500 bg-gray-50 p-2 rounded">📋 Sin archivo</div>
                                                @endif
                                            </div>
                                            <flux:button type="button" size="sm" color="danger"
                                                wire:click="removeVideo({{ $index }})" 
                                                class="ml-4 p-2 rounded-lg hover:bg-red-100 transition-colors duration-200">
                                                <flux:icon name="trash" class="w-4 h-4" />
                                            </flux:button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <flux:icon name="video-camera" class="w-12 h-12 text-orange-300 mx-auto mb-3" />
                                        <p class="text-gray-500 text-sm">No hay videos agregados</p>
                                        <p class="text-gray-400 text-xs mt-1">Haz clic en "Agregar Video" para comenzar</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Pestaña de Documentos -->
                            <div id="content-documents" class="tab-content hidden">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-lg font-semibold text-gray-800">Documentos del Proyecto</h5>
                                    <flux:button icon="plus" type="button" size="sm" color="primary"
                                        wire:click="addDocument" class="flex items-center space-x-2">
                                        <flux:icon name="plus" class="w-4 h-4" />
                                        <span>Agregar Documento</span>
                                    </flux:button>
                                </div>

                                @forelse($path_documents as $index => $document)
                                    <div class="bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 rounded-lg p-4 mb-3">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <span class="text-red-500">*</span> Título
                                                </label>
                                                <flux:input wire:model="path_documents.{{ $index }}.title"
                                                    placeholder="brochure, plano, etc." size="sm" class="w-full" />
                                                @error("path_documents.{$index}.title") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Archivo</label>
                                                <input type="file" wire:model="documentFiles.{{ $index }}"
                                                    accept=".pdf,.doc,.docx" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                                                <flux:input wire:model="path_documents.{{ $index }}.descripcion"
                                                    placeholder="Descripción del documento" size="sm" class="w-full" />
                                            </div>
                                        </div>
                                        <div class="mt-4 flex items-center justify-between">
                                            <div class="flex-1">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                                @if (isset($document['path']) && $document['path'] && !isset($documentFiles[$index]))
                                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                        <div class="text-xs text-blue-600 font-medium">Documento existente: {{ $document['title'] ?? 'Documento' }}</div>
                                                    </div>
                                                @elseif(isset($documentFiles[$index]) && $documentFiles[$index])
                                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                                        <div class="text-xs text-green-600 font-medium">✅ {{ $documentFiles[$index]->getClientOriginalName() }}</div>
                                                    </div>
                                                @else
                                                    <div class="text-xs text-gray-500 bg-gray-50 p-2 rounded">📋 Sin archivo</div>
                                                @endif
                                            </div>
                                            <flux:button type="button" size="sm" color="danger"
                                                wire:click="removeDocument({{ $index }})" 
                                                class="ml-4 p-2 rounded-lg hover:bg-red-100 transition-colors duration-200">
                                                <flux:icon name="trash" class="w-4 h-4" />
                                            </flux:button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <flux:icon name="document-text" class="w-12 h-12 text-orange-300 mx-auto mb-3" />
                                        <p class="text-gray-500 text-sm">No hay documentos agregados</p>
                                        <p class="text-gray-400 text-xs mt-1">Haz clic en "Agregar Documento" para comenzar</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Script para las pestañas -->
                    <script>
                        function showTab(tabName) {
                            // Ocultar todas las pestañas
                            document.querySelectorAll('.tab-content').forEach(content => {
                                content.classList.add('hidden');
                            });
                            
                            // Remover estilos activos de todas las pestañas
                            document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                                tab.classList.remove('bg-orange-50', 'text-orange-700');
                                tab.classList.add('bg-white', 'text-gray-600');
                            });
                            
                            // Mostrar la pestaña seleccionada
                            document.getElementById('content-' + tabName).classList.remove('hidden');
                            
                            // Estilo activo para la pestaña seleccionada
                            document.getElementById('tab-' + tabName).classList.remove('bg-white', 'text-gray-600');
                            document.getElementById('tab-' + tabName).classList.add('bg-orange-50', 'text-orange-700');
                        }
                        
                        // Mostrar la pestaña de imágenes por defecto
                        document.addEventListener('DOMContentLoaded', function() {
                            showTab('images');
                        });
                    </script>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <flux:button type="button" size="sm" variant="outline" wire:click="closeModals">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" size="sm" color="primary">
                        {{ $editingProject ? 'Actualizar Proyecto' : 'Crear Proyecto' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal de Confirmación de Eliminación -->
    <flux:modal wire:model="showDeleteModal" size="sm">
        <div class="p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <flux:icon name="exclamation-triangle" class="h-6 w-6 text-red-600" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmar eliminación</h3>
                <p class="text-sm text-gray-500 mb-6">
                    ¿Estás seguro de que quieres eliminar este proyecto? Esta acción no se puede deshacer.
                </p>

                <div class="flex justify-center space-x-3">
                    <flux:button size="sm" variant="outline" wire:click="closeModals">
                        Cancelar
                    </flux:button>
                    <flux:button size="sm" color="danger" wire:click="deleteProject">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
