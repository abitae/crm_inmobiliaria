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
                            <h1 class="text-2xl font-bold text-gray-900">Proyectos Inmobiliarios</h1>
                            <p class="text-sm text-gray-600 mt-1">Gestión integral de proyectos y propiedades</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <flux:button icon="arrow-down-tray" size="sm" variant="outline" wire:click="exportProjects"
                        class="w-full sm:w-auto justify-center">
                        <flux:icon name="arrow-down-tray" class="w-4 h-4 mr-2" />
                        Exportar
                    </flux:button>
                    <flux:button icon="plus" size="sm" color="primary" wire:click="openCreateModal"
                        class="w-full sm:w-auto justify-center">
                        <flux:icon name="plus" class="w-4 h-4 mr-2" />
                        Nuevo Proyecto
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje de Éxito mejorado -->
    @if (session()->has('message'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-sm relative"
                role="alert">
                <div class="flex items-center">
                    <flux:icon name="check-circle" class="w-5 h-5 text-green-600 mr-3" />
                    <span class="font-medium">{{ session('message') }}</span>
                </div>
                <button type="button"
                    class="absolute top-2 right-2 text-green-600 hover:text-green-800 transition-colors duration-200"
                    onclick="this.parentElement.parentElement.style.display='none'">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtros y Búsqueda mejorados -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Filtros de Búsqueda</h3>
                <p class="text-sm text-gray-600">Refina los resultados según tus necesidades</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Búsqueda</label>
                    <flux:input size="sm" wire:model.live="search" placeholder="Buscar proyectos..."
                        class="w-full" />
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <flux:select size="sm" wire:model.live="statusFilter" class="w-full">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="suspendido">Suspendido</option>
                        <option value="finalizado">Finalizado</option>
                    </flux:select>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                    <flux:select size="sm" wire:model.live="typeFilter" class="w-full">
                        <option value="">Todos los tipos</option>
                        <option value="lotes">Lotes</option>
                        <option value="casas">Casas</option>
                        <option value="departamentos">Departamentos</option>
                        <option value="oficinas">Oficinas</option>
                        <option value="mixto">Mixto</option>
                    </flux:select>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Etapa</label>
                    <flux:select size="sm" wire:model.live="stageFilter" class="w-full">
                        <option value="">Todas las etapas</option>
                        <option value="preventa">Preventa</option>
                        <option value="lanzamiento">Lanzamiento</option>
                        <option value="venta_activa">Venta Activa</option>
                        <option value="cierre">Cierre</option>
                    </flux:select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Ubicación</label>
                    <flux:select size="sm" wire:model.live="locationFilter" class="w-full">
                        <option value="">Todas las ubicaciones</option>
                        <option value="lima">Lima</option>
                        <option value="arequipa">Arequipa</option>
                        <option value="trujillo">Trujillo</option>
                        <option value="piura">Piura</option>
                        <option value="chiclayo">Chiclayo</option>
                        <option value="cusco">Cusco</option>
                    </flux:select>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Ordenar por</label>
                    <flux:select size="sm" wire:model.live="orderBy" class="w-full">
                        <option value="created_at">Fecha de creación</option>
                        <option value="name">Nombre</option>
                        <option value="start_date">Fecha de inicio</option>
                        <option value="status">Estado</option>
                    </flux:select>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Dirección</label>
                    <flux:select size="sm" wire:model.live="orderDirection" class="w-full">
                        <option value="desc">Descendente</option>
                        <option value="asc">Ascendente</option>
                    </flux:select>
                </div>
            </div>

            <div
                class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-4 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <label class="flex items-center text-sm text-gray-700">
                        <input type="checkbox" wire:model.live="withAvailableUnits"
                            class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2">Solo con unidades disponibles</span>
                    </label>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <flux:button size="sm" variant="outline" wire:click="clearFilters"
                        class="w-full sm:w-auto justify-center">
                        <flux:icon name="x-mark" class="w-4 h-4 mr-2" />
                        Limpiar filtros
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Lista de Proyectos mejorada -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Proyectos Encontrados</h3>
                    <span class="text-sm text-gray-600">{{ $projects->total() }} proyectos</span>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($projects as $project)
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex flex-col lg:flex-row gap-6">
                            <!-- Imagen del proyecto -->
                            <div class="flex-shrink-0">
                                <div
                                    class="w-24 h-24 rounded-lg overflow-hidden bg-gray-200 flex items-center justify-center">
                                    @if ($project->path_image_portada)
                                        <img src="{{ $project->path_image_portada }}" alt="{{ $project->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <flux:icon name="building-office-2" class="w-12 h-12 text-gray-400" />
                                    @endif
                                </div>
                            </div>

                            <!-- Información del proyecto -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-xl font-semibold text-gray-900 mb-2">
                                                    {{ $project->name }}
                                                </h4>
                                                <p class="text-gray-600 mb-3 line-clamp-2">
                                                    {{ $project->description ?: 'Sin descripción disponible' }}
                                                </p>

                                                <!-- Badges de estado -->
                                                <div class="flex flex-wrap gap-2 mb-3">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $project->status === 'activo'
                                                            ? 'bg-green-100 text-green-800'
                                                            : ($project->status === 'inactivo'
                                                                ? 'bg-gray-100 text-gray-800'
                                                                : ($project->status === 'suspendido'
                                                                    ? 'bg-yellow-100 text-yellow-800'
                                                                    : 'bg-red-100 text-red-800')) }}">
                                                        {{ ucfirst($project->status) }}
                                                    </span>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ ucfirst(str_replace('_', ' ', $project->stage)) }}
                                                    </span>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        {{ ucfirst(str_replace('_', ' ', $project->project_type)) }}
                                                    </span>
                                                </div>

                                                <!-- Información de ubicación -->
                                                <div class="flex items-center text-sm text-gray-600 mb-2">
                                                    <flux:icon name="map-pin" class="w-4 h-4 mr-2 text-gray-400" />
                                                    <span>{{ $project->full_address }}</span>
                                                </div>

                                                <!-- Fechas del proyecto -->
                                                <div
                                                    class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm text-gray-600">
                                                    @if ($project->start_date)
                                                        <div class="flex items-center">
                                                            <flux:icon name="calendar"
                                                                class="w-4 h-4 mr-2 text-gray-400" />
                                                            <span><strong>Inicio:</strong>
                                                                {{ $project->start_date->format('d/m/Y') }}</span>
                                                        </div>
                                                    @endif
                                                    @if ($project->end_date)
                                                        <div class="flex items-center">
                                                            <flux:icon name="calendar"
                                                                class="w-4 h-4 mr-2 text-gray-400" />
                                                            <span><strong>Fin:</strong>
                                                                {{ $project->end_date->format('d/m/Y') }}</span>
                                                        </div>
                                                    @endif
                                                    @if ($project->delivery_date)
                                                        <div class="flex items-center">
                                                            <flux:icon name="calendar"
                                                                class="w-4 h-4 mr-2 text-gray-400" />
                                                            <span><strong>Entrega:</strong>
                                                                {{ $project->delivery_date->format('d/m/Y') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Acciones del proyecto -->
                                    <div class="flex flex-col sm:flex-row gap-2 lg:flex-col">
                                        <flux:button size="xs" variant="outline"
                                            wire:click="viewProject({{ $project->id }})" class="justify-center">
                                            <flux:icon name="eye" class="w-4 h-4 mr-2" />
                                            Ver
                                        </flux:button>
                                        <flux:button size="xs" variant="outline"
                                            wire:click="openEditModal({{ $project->id }})" class="justify-center">
                                            <flux:icon name="pencil" class="w-4 h-4 mr-2" />
                                            Editar
                                        </flux:button>
                                        <flux:button size="xs" variant="outline"
                                            wire:click="openAssignAdvisorModal({{ $project->id }})"
                                            class="justify-center">
                                            <flux:icon name="user-plus" class="w-4 h-4 mr-2" />
                                            Asesor
                                        </flux:button>
                                        <flux:button size="xs" color="danger" variant="outline"
                                            wire:click="openDeleteModal({{ $project->id }})" class="justify-center">
                                            <flux:icon name="trash" class="w-4 h-4 mr-2" />
                                            Eliminar
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <flux:icon name="building-office-2" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron proyectos</h3>
                        <p class="text-gray-600 mb-6">Intenta ajustar los filtros de búsqueda o crear un nuevo
                            proyecto.</p>
                        <flux:button icon="plus" size="sm" color="primary" wire:click="openCreateModal">
                            <flux:icon name="plus" class="w-4 h-4 mr-2" />
                            Crear Primer Proyecto
                        </flux:button>
                    </div>
                @endforelse
            </div>

            <!-- Paginación mejorada -->
            @if ($projects->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Mostrando {{ $projects->firstItem() }} a {{ $projects->lastItem() }} de
                            {{ $projects->total() }} resultados
                        </div>
                        <div class="flex justify-center">
                            {{ $projects->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Creación/Edición Mejorado -->
    <flux:modal variant="flyout" wire:model="showFormModal" class="max-w-6xl">
        <div class="p-6">
            <!-- Header del Modal -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">
                        {{ $editingProject ? 'Editar Proyecto' : 'Crear Nuevo Proyecto' }}
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $editingProject ? 'Modifica la información del proyecto existente' : 'Completa la información para crear un nuevo proyecto' }}
                    </p>
                </div>

                <!-- Indicador de Progreso -->
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-700">Progreso del Formulario</div>
                        <div class="text-xs text-gray-500">Campos completados</div>
                    </div>
                    <div class="relative">
                        <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
                            <div
                                class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                {{ $editingProject ? '100%' : '0%' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="{{ $editingProject ? 'updateProject' : 'createProject' }}">
                <!-- Barra de Validación -->
                <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-blue-800">Validación del Formulario</h4>
                        <div class="text-sm text-blue-600">
                            {{ $editingProject ? 'Modo edición' : 'Modo creación' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 rounded-full {{ $name ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                            <span class="{{ $name ? 'text-green-700' : 'text-gray-500' }}">Nombre</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 rounded-full {{ $description ? 'bg-green-500' : 'bg-gray-300' }}">
                            </div>
                            <span class="{{ $description ? 'text-green-700' : 'text-gray-500' }}">Descripción</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 rounded-full {{ $project_type ? 'bg-green-500' : 'bg-gray-300' }}">
                            </div>
                            <span class="{{ $project_type ? 'text-green-700' : 'text-gray-500' }}">Tipo</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 rounded-full {{ $address ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                            <span class="{{ $address ? 'text-green-700' : 'text-gray-500' }}">Dirección</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Información Básica -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl border border-gray-200 p-6">
                            <div class="flex items-center space-x-3 mb-6">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <flux:icon name="information-circle" class="w-5 h-5 text-blue-600" />
                                </div>
                                <h4 class="text-lg font-semibold text-gray-800">Información Básica</h4>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <span class="text-red-500">*</span> Nombre del Proyecto
                                    </label>
                                    <flux:input wire:model="name" placeholder="Ingrese el nombre del proyecto"
                                        size="sm" class="w-full" />
                                    @error('name')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <span class="text-red-500">*</span> Descripción
                                    </label>
                                    <textarea wire:model="description" rows="3"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        placeholder="Describa las características principales del proyecto"></textarea>
                                    @error('description')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <span class="text-red-500">*</span> Tipo
                                        </label>
                                        <flux:select wire:model="project_type" size="sm" class="w-full">
                                            <option value="">Seleccionar tipo</option>
                                            <option value="lotes">Lotes</option>
                                            <option value="casas">Casas</option>
                                            <option value="departamentos">Departamentos</option>
                                            <option value="oficinas">Oficinas</option>
                                            <option value="mixto">Mixto</option>
                                        </flux:select>
                                        @error('project_type')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <span class="text-red-500">*</span> Etapa
                                        </label>
                                        <flux:select wire:model="stage" size="sm" class="w-full">
                                            <option value="">Seleccionar etapa</option>
                                            <option value="preventa">Preventa</option>
                                            <option value="lanzamiento">Lanzamiento</option>
                                            <option value="venta_activa">Venta Activa</option>
                                            <option value="cierre">Cierre</option>
                                        </flux:select>
                                        @error('stage')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <span class="text-red-500">*</span> Estado Legal
                                        </label>
                                        <flux:select wire:model="legal_status" size="sm" class="w-full">
                                            <option value="">Seleccionar estado</option>
                                            <option value="con_titulo">Con Título</option>
                                            <option value="en_tramite">En Trámite</option>
                                            <option value="habilitado">Habilitado</option>
                                        </flux:select>
                                        @error('legal_status')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <span class="text-red-500">*</span> Estado
                                        </label>
                                        <flux:select wire:model="status" size="sm" class="w-full">
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                            <option value="suspendido">Suspendido</option>
                                            <option value="finalizado">Finalizado</option>
                                        </flux:select>
                                        @error('status')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ubicación y Fechas -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl border border-gray-200 p-6">
                            <div class="flex items-center space-x-3 mb-6">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <flux:icon name="map-pin" class="w-5 h-5 text-green-600" />
                                </div>
                                <h4 class="text-lg font-semibold text-gray-800">Ubicación y Fechas</h4>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <span class="text-red-500">*</span> Dirección
                                    </label>
                                    <flux:input wire:model="address" placeholder="Ingrese la dirección completa"
                                        size="sm" class="w-full" />
                                    @error('address')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Distrito</label>
                                        <flux:input wire:model="district" placeholder="Distrito" size="sm"
                                            class="w-full" />
                                        @error('district')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Provincia</label>
                                        <flux:input wire:model="province" placeholder="Provincia" size="sm"
                                            class="w-full" />
                                        @error('province')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Región</label>
                                        <flux:input wire:model="region" placeholder="Región" size="sm"
                                            class="w-full" />
                                        @error('region')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">País</label>
                                        <flux:input wire:model="country" placeholder="País" size="sm"
                                            class="w-full" />
                                        @error('country')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Latitud</label>
                                        <flux:input wire:model="latitude" type="number" step="any"
                                            placeholder="0.000000" size="sm" class="w-full" />
                                        @error('latitude')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Longitud</label>
                                        <flux:input wire:model="longitude" type="number" step="any"
                                            placeholder="0.000000" size="sm" class="w-full" />
                                        @error('longitude')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de
                                            Inicio</label>
                                        <flux:input wire:model="start_date" type="date" size="sm"
                                            class="w-full" />
                                        @error('start_date')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de
                                            Fin</label>
                                        <flux:input wire:model="end_date" type="date" size="sm"
                                            class="w-full" />
                                        @error('end_date')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de
                                            Entrega</label>
                                        <flux:input wire:model="delivery_date" type="date" size="sm"
                                            class="w-full" />
                                        @error('delivery_date')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Multimedia de Portada -->
                <div class="mt-8 bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <flux:icon name="photo" class="w-5 h-5 text-purple-600" />
                        </div>
                        <h4 class="text-lg font-semibold text-gray-800">Multimedia de Portada</h4>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Imagen de Portada -->
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Imagen de Portada</label>
                            @if ($path_image_portada)
                                <div class="relative">
                                    <img src="{{ $path_image_portada }}" alt="Imagen de portada"
                                        class="w-full h-32 object-cover rounded-lg border border-purple-200">
                                    <button type="button" wire:click="removeImagePortada"
                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors duration-200">
                                        <flux:icon name="x-mark" class="w-4 h-4" />
                                    </button>
                                </div>
                            @endif
                            <div
                                class="border-2 border-dashed border-purple-200 rounded-lg p-4 text-center hover:border-purple-300 transition-colors duration-200">
                                <input type="file" wire:model="imagePortadaFile" accept="image/*"
                                    class="w-full text-sm">
                                <p class="text-xs text-gray-500 mt-2">Formatos: JPG, PNG, GIF. Máximo 2MB</p>
                            </div>
                        </div>

                        <!-- Video de Portada -->
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Video de Portada</label>
                            @if ($path_video_portada)
                                <div class="relative">
                                    <video controls
                                        class="w-full h-32 object-cover rounded-lg border border-purple-200">
                                        <source src="{{ $path_video_portada }}" type="video/mp4">
                                        Tu navegador no soporta el elemento video.
                                    </video>
                                    <button type="button" wire:click="removeVideoPortada"
                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors duration-200">
                                        <flux:icon name="x-mark" class="w-4 h-4" />
                                    </button>
                                </div>
                            @endif
                            <div
                                class="border-2 border-dashed border-purple-200 rounded-lg p-4 text-center hover:border-purple-300 transition-colors duration-200">
                                <input type="file" wire:model="videoPortadaFile" accept="video/*"
                                    class="w-full text-sm">
                                <p class="text-xs text-gray-500 mt-2">Formatos: MP4, AVI, MOV, WMV. Máximo 10MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <span class="text-red-500">*</span> Campos obligatorios
                    </div>

                    <div class="flex space-x-3">
                        <flux:button type="button" size="sm" variant="outline" wire:click="closeModals">
                            <flux:icon name="x-mark" class="w-4 h-4 mr-2" />
                            Cancelar
                        </flux:button>
                        <flux:button type="submit" size="sm" color="primary">
                            <flux:icon name="{{ $editingProject ? 'check' : 'plus' }}" class="w-4 h-4 mr-2" />
                            {{ $editingProject ? 'Actualizar Proyecto' : 'Crear Proyecto' }}
                        </flux:button>
                    </div>
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
