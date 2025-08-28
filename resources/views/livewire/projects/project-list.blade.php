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
                        Exportar
                    </flux:button>
                    <flux:button icon="plus" size="sm" color="primary" wire:click="openCreateModal"
                        class="w-full sm:w-auto justify-center">
                        Nuevo Proyecto
                    </flux:button>
                </div>
            </div>
        </div>
    </div>


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
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar proyectos..."
                        class="w-full" />
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <flux:select size="xs" wire:model.live="statusFilter" class="w-full">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="suspendido">Suspendido</option>
                        <option value="finalizado">Finalizado</option>
                    </flux:select>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                    <flux:select size="xs" wire:model.live="typeFilter" class="w-full">
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
                    <flux:select size="xs" wire:model.live="stageFilter" class="w-full">
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
                    <flux:select size="xs" wire:model.live="locationFilter" class="w-full">
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
                    <flux:select size="xs" wire:model.live="orderBy" class="w-full">
                        <option value="created_at">Fecha de creación</option>
                        <option value="name">Nombre</option>
                        <option value="start_date">Fecha de inicio</option>
                        <option value="status">Estado</option>
                    </flux:select>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Dirección</label>
                    <flux:select size="xs" wire:model.live="orderDirection" class="w-full">
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
                                        <flux:button icon="eye" size="xs" variant="outline"
                                            wire:click="viewProject({{ $project->id }})" class="justify-center">
                                            Ver
                                        </flux:button>
                                        <flux:button icon="pencil" size="xs" variant="outline"
                                            wire:click="openEditModal({{ $project->id }})" class="justify-center">
                                            Editar
                                        </flux:button>
                                        <flux:button icon="user-plus" size="xs" variant="outline"
                                            wire:click="openAssignAdvisorModal({{ $project->id }})"
                                            class="justify-center">
                                            Asesor
                                        </flux:button>
                                        <flux:button icon="trash" size="xs" color="danger" variant="outline"
                                            wire:click="openDeleteModal({{ $project->id }})" class="justify-center">
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
                                        size="xs" class="w-full" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <span class="text-red-500">*</span> Descripción
                                    </label>
                                    <flux:textarea wire:model="description" rows="3"
                                        placeholder="Describa las características principales del proyecto">
                                    </flux:textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <span class="text-red-500">*</span> Tipo
                                        </label>
                                        <flux:select wire:model="project_type" size="xs" class="w-full">
                                            <option value="">Seleccionar tipo</option>
                                            <option value="lotes">Lotes</option>
                                            <option value="casas">Casas</option>
                                            <option value="departamentos">Departamentos</option>
                                            <option value="oficinas">Oficinas</option>
                                            <option value="mixto">Mixto</option>
                                        </flux:select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <span class="text-red-500">*</span> Etapa
                                        </label>
                                        <flux:select wire:model="stage" size="xs" class="w-full">
                                            <option value="">Seleccionar etapa</option>
                                            <option value="preventa">Preventa</option>
                                            <option value="lanzamiento">Lanzamiento</option>
                                            <option value="venta_activa">Venta Activa</option>
                                            <option value="cierre">Cierre</option>
                                        </flux:select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <span class="text-red-500">*</span> Estado Legal
                                        </label>
                                        <flux:select wire:model="legal_status" size="xs" class="w-full">
                                            <option value="">Seleccionar estado</option>
                                            <option value="con_titulo">Con Título</option>
                                            <option value="en_tramite">En Trámite</option>
                                            <option value="habilitado">Habilitado</option>
                                        </flux:select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <span class="text-red-500">*</span> Estado
                                        </label>
                                        <flux:select wire:model="status" size="xs" class="w-full">
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                            <option value="suspendido">Suspendido</option>
                                            <option value="finalizado">Finalizado</option>
                                        </flux:select>
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
                                        size="xs" class="w-full" />
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Distrito</label>
                                        <flux:input wire:model="district" placeholder="Distrito" size="xs"
                                            class="w-full" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Provincia</label>
                                        <flux:input wire:model="province" placeholder="Provincia" size="xs"
                                            class="w-full" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Región</label>
                                        <flux:input wire:model="region" placeholder="Región" size="xs"
                                            class="w-full" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">País</label>
                                        <flux:input wire:model="country" placeholder="País" size="xs"
                                            class="w-full" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Latitud</label>
                                        <flux:input wire:model="latitude" type="number" step="any"
                                            placeholder="0.000000" size="xs" class="w-full" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Longitud</label>
                                        <flux:input wire:model="longitude" type="number" step="any"
                                            placeholder="0.000000" size="xs" class="w-full" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de
                                            Inicio</label>
                                        <flux:input wire:model="start_date" type="date" size="xs"
                                            class="w-full" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de
                                            Fin</label>
                                        <flux:input wire:model="end_date" type="date" size="xs"
                                            class="w-full" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de
                                            Entrega</label>
                                        <flux:input wire:model="delivery_date" type="date" size="xs"
                                            class="w-full" />
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
                                    <flux:button icon="x-mark" size="xs" color="danger" variant="outline"
                                        wire:click="removeImagePortada" class="absolute top-2 right-2">
                                    </flux:button>
                                </div>
                            @endif
                            <div
                                class="border-2 border-dashed border-purple-200 rounded-lg p-4 text-center hover:border-purple-300 transition-colors duration-200">
                                <flux:input type="file" wire:model="imagePortadaFile" accept="image/*"
                                    class="w-full text-sm" />
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
                                    <flux:button icon="x-mark" size="xs" color="danger" variant="outline"
                                        wire:click="removeVideoPortada" class="absolute top-2 right-2">
                                    </flux:button>
                                </div>
                            @endif
                            <div
                                class="border-2 border-dashed border-purple-200 rounded-lg p-4 text-center hover:border-purple-300 transition-colors duration-200">
                                <flux:input type="file" wire:model="videoPortadaFile" accept="video/*"
                                    class="w-full text-sm" />
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
                        <flux:button icon="x-mark" size="xs" variant="outline" wire:click="closeModals">
                            Cancelar
                        </flux:button>
                        <flux:button icon="{{ $editingProject ? 'check' : 'plus' }}" type="submit" size="xs"
                            color="primary">
                            {{ $editingProject ? 'Actualizar Proyecto' : 'Crear Proyecto' }}
                        </flux:button>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal de Asignación de Asesor -->
    <flux:modal variant='flyout' wire:model="showAssignAdvisorModal" size="md">
        <div class="p-6">
            <div class="mb-6">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4 mx-auto">
                    <flux:icon name="user-plus" class="h-6 w-6 text-blue-600" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Asignar Asesor al Proyecto</h3>
                <p class="text-sm text-gray-500 text-center">
                    @if ($selectedProject)
                        <strong>{{ $selectedProject->name }}</strong>
                    @endif
                </p>
            </div>

            <form>
                <div class="space-y-6">
                    <!-- Selección de Asesor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Seleccionar Asesor
                        </label>
                        <flux:select wire:model="selectedAdvisorId" size="xs" class="w-full">
                            <option value="">Seleccionar un asesor</option>
                            @php
                                $availableAdvisors = collect($advisors)->filter(function ($advisor) use (
                                    $currentAdvisors,
                                ) {
                                    return !collect($currentAdvisors)->contains('id', $advisor->id);
                                });
                            @endphp
                            @if ($availableAdvisors->count() > 0)
                                @foreach ($availableAdvisors as $advisor)
                                    <option value="{{ $advisor->id }}">
                                        {{ $advisor->name }} {{ $advisor->last_name }}
                                        ({{ $advisor->email }})
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No hay asesores disponibles para asignar</option>
                            @endif
                        </flux:select>
                        @if ($availableAdvisors->count() === 0)
                            <p class="text-xs text-amber-600 mt-1">
                                <flux:icon name="information-circle" class="w-4 h-4 inline mr-1" />
                                Todos los asesores ya están asignados a este proyecto
                            </p>
                        @endif
                    </div>

                    <!-- Opción de Asesor Principal -->
                    <div class="flex items-center">
                        <flux:checkbox wire:model="isPrimaryAdvisor" id="isPrimaryAdvisor" />
                        <label for="isPrimaryAdvisor" class="ml-2 text-sm text-gray-700">
                            Marcar como asesor principal
                        </label>
                    </div>

                    <!-- Resumen de la Asignación -->
                    @if ($selectedAdvisorId)
                        @php
                            $selectedAdvisor = collect($advisors)->firstWhere('id', $selectedAdvisorId);
                        @endphp
                        @if ($selectedAdvisor)
                            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                <h4 class="text-sm font-medium text-green-700 mb-2">Resumen de la Asignación</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Asesor:</span>
                                        <span class="font-medium text-gray-800">{{ $selectedAdvisor->name }}
                                            {{ $selectedAdvisor->last_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Email:</span>
                                        <span class="text-gray-800">{{ $selectedAdvisor->email }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Tipo:</span>
                                        <span class="text-gray-800">
                                            @if ($isPrimaryAdvisor)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Asesor Principal
                                                </span>
                                            @else
                                                <span class="text-gray-800">Asesor Regular</span>
                                            @endif
                                        </span>
                                    </div>
                                    @if ($advisorNotes)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Notas:</span>
                                            <span class="text-gray-800">{{ $advisorNotes }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Notas del Asesor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notas de Asignación
                        </label>
                        <flux:textarea wire:model="advisorNotes" rows="3"
                            placeholder="Agregar notas sobre la asignación del asesor (opcional)">
                        </flux:textarea>
                        @error('advisorNotes')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Asesores Actuales -->
                    @if ($selectedProject)
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-blue-700">Asesores Asignados</h4>
                                <div class="flex items-center space-x-2">
                                    <flux:button icon="arrow-path" size="xs" variant="outline"
                                        wire:click="refreshAdvisorsList" class="hover:bg-blue-100">
                                    </flux:button>
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ count($currentAdvisors) }}
                                        asesor{{ count($currentAdvisors) != 1 ? 'es' : '' }}
                                    </span>
                                </div>
                            </div>
                            @if (count($currentAdvisors) > 0)
                                <div class="overflow-hidden">
                                    <table class="min-w-full divide-y divide-blue-200">
                                        <thead class="bg-blue-50">
                                            <tr>
                                                <th
                                                    class="px-3 py-2 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                                    Asesor
                                                </th>
                                                <th
                                                    class="px-3 py-2 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                                    Estado
                                                </th>
                                                <th
                                                    class="px-3 py-2 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                                    Fecha
                                                </th>
                                                <th
                                                    class="px-3 py-2 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                                    Notas
                                                </th>
                                                <th
                                                    class="px-3 py-2 text-right text-xs font-medium text-blue-700 uppercase tracking-wider">
                                                    Acciones
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-blue-100">
                                            @foreach ($currentAdvisors as $advisor)
                                                <tr wire:key="advisor-{{ $advisor->id }}"
                                                    class="hover:bg-blue-50 transition-colors duration-150">
                                                    <td class="px-3 py-3 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <flux:icon name="user"
                                                                class="w-4 h-4 text-blue-600 mr-2" />
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900">
                                                                    {{ $advisor->name }} {{ $advisor->last_name }}
                                                                </div>
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $advisor->email }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-3 whitespace-nowrap">
                                                        @if ($advisor->pivot && $advisor->pivot->is_primary)
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                Principal
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                Regular
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                                                        @if ($advisor->pivot && $advisor->pivot->assigned_at)
                                                            {{ \Carbon\Carbon::parse($advisor->pivot->assigned_at)->format('d/m/Y') }}
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-3 text-sm text-gray-500 max-w-xs">
                                                        @if ($advisor->pivot && $advisor->pivot->notes)
                                                            <div class="truncate"
                                                                title="{{ $advisor->pivot->notes }}">
                                                                {{ $advisor->pivot->notes }}
                                                            </div>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                    <td
                                                        class="px-3 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                        <flux:button icon="trash" size="xs" color="danger"
                                                            variant="outline"
                                                            wire:click="removeAdvisor({{ $advisor->id }})"
                                                            class="hover:bg-red-50">
                                                        </flux:button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <flux:icon name="user-group" class="w-8 h-8 text-blue-400 mx-auto mb-2" />
                                    <p class="text-sm text-blue-600">No hay asesores asignados a este proyecto</p>
                                    <p class="text-xs text-blue-500 mt-1">Selecciona un asesor para comenzar</p>
                                    <flux:button icon="arrow-path" size="xs" variant="outline"
                                        wire:click="refreshAdvisorsList" class="mt-2 hover:bg-blue-100">
                                        Refrescar Lista
                                    </flux:button>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Información del Proyecto -->
                    @if ($selectedProject)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Información del Proyecto</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Tipo:</span>
                                    <span
                                        class="ml-2 font-medium">{{ ucfirst($selectedProject->project_type) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Etapa:</span>
                                    <span
                                        class="ml-2 font-medium">{{ ucfirst(str_replace('_', ' ', $selectedProject->stage)) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Estado:</span>
                                    <span class="ml-2 font-medium">{{ ucfirst($selectedProject->status) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Ubicación:</span>
                                    <span class="ml-2 font-medium">{{ $selectedProject->district }},
                                        {{ $selectedProject->province }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <span class="text-red-500">*</span> Campos obligatorios
                    </div>

                    <div class="flex space-x-3">
                        <flux:button icon="x-mark" type="button" size="xs" variant="outline" wire:click="closeModals">
                            Cancelar
                        </flux:button>
                        <flux:button icon="user-plus" type="button" size="xs" color="primary"
                            wire:click="confirmAssignAdvisor">
                            Asignar Asesor
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
                    <flux:button size="xs" variant="outline" wire:click="closeModals">
                        Cancelar
                    </flux:button>
                    <flux:button size="xs" color="danger" wire:click="deleteProject">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Script para SweetAlert2 -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-success', (message) => {
                window.showSuccess(message);
            });

            Livewire.on('show-info', (message) => {
                window.showInfo(message);
            });

            Livewire.on('show-error', (message) => {
                window.showError(message);
            });

            Livewire.on('show-confirm', (message, title, action, ...params) => {
                window.showConfirm(message, title).then((result) => {
                    if (result.isConfirmed) {
                        if (params && params.length > 0) {
                            @this.call(action, ...params);
                        } else {
                            @this.call(action);
                        }
                    }
                });
            });
        });
    </script>
</div>
