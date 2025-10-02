<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Mejorado y Reordenado del Proyecto -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-0 mb-8 overflow-hidden">
            <!-- Primera fila: Icono, Nombre, Destacado, Estado y Etapa -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 px-8 pt-8 pb-4">
                <div class="flex items-center gap-5 min-w-0">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center shadow-md shrink-0">
                        <flux:icon name="home-modern" class="w-9 h-9 text-blue-600" />
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight truncate"
                                title="{{ $project->name }}">
                                {{ $project->name }}
                            </h1>
                            @if ($project->featured)
                                <span
                                    class="px-2 py-0.5 bg-yellow-200 text-yellow-800 text-xs rounded-full font-semibold shadow-sm">Destacado</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mt-2 text-gray-500 text-sm">
                            <flux:icon name="map-pin" class="w-4 h-4 text-blue-400" />
                            <span class="truncate max-w-xs md:max-w-md" title="{{ $project->full_address }}">
                                {{ $project->full_address }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2 min-w-[160px]">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold shadow-sm
                        {{ $project->status === 'activo'
                            ? 'bg-green-100 text-green-800'
                            : ($project->status === 'inactivo'
                                ? 'bg-red-100 text-red-800'
                                : 'bg-yellow-100 text-yellow-800') }}">
                        <flux:icon
                            name="{{ $project->status === 'activo' ? 'check-circle' : ($project->status === 'inactivo' ? 'x-circle' : 'exclamation-circle') }}"
                            class="w-4 h-4 mr-1" />
                        {{ ucfirst($project->status) }}
                    </span>
                    <span class="inline-flex items-center text-xs text-gray-500 mt-1">
                        <flux:icon name="flag" class="w-3 h-3 mr-1" />
                        {{ ucfirst($project->stage) }}
                    </span>
                </div>
            </div>
            <!-- Segunda fila: Métricas del Proyecto (mejoradas en una sola fila) -->
            <div class="flex flex-wrap md:flex-nowrap gap-4 px-8 pb-4">
                <div class="flex-1 bg-blue-50 p-4 rounded-lg flex flex-col items-center min-w-[120px]">
                    <h3 class="text-sm font-semibold text-blue-900">Unidades Totales</h3>
                    <p class="text-2xl md:text-3xl font-bold text-blue-600">{{ $project->total_units }}</p>
                </div>
                <div class="flex-1 bg-gray-50 p-4 rounded-lg flex flex-col items-center min-w-[120px]">
                    <h3 class="text-sm font-semibold text-gray-900">Vendidas</h3>
                    <p class="text-2xl md:text-3xl font-bold text-gray-600">{{ $project->sold_units }}</p>
                </div>
                <div class="flex-1 bg-green-50 p-4 rounded-lg flex flex-col items-center min-w-[120px]">
                    <h3 class="text-sm font-semibold text-green-900">Disponibles</h3>
                    <p class="text-2xl md:text-3xl font-bold text-green-600">{{ $project->available_units }}</p>
                </div>
                <div class="flex-1 bg-yellow-50 p-4 rounded-lg flex flex-col items-center min-w-[120px]">
                    <h3 class="text-sm font-semibold text-yellow-900">Reservadas</h3>
                    <p class="text-2xl md:text-3xl font-bold text-yellow-600">{{ $project->reserved_units }}</p>
                </div>
                <div class="flex-1 bg-orange-50 p-4 rounded-lg flex flex-col items-center min-w-[120px]">
                    <h3 class="text-sm font-semibold text-orange-900">Progreso</h3>
                    <p class="text-2xl md:text-3xl font-bold text-orange-600">{{ $project->progress_percentage }}%
                    </p>
                </div>
            </div>
            <!-- Tercera fila: Información Resumida del Proyecto -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 rounded-xl mx-8 p-6 mb-8 border border-gray-100 shadow-sm">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600">Tipo de Proyecto:</span>
                        <span class="text-sm text-gray-900">{{ ucfirst($project->project_type) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600">Estado Legal:</span>
                        <span
                            class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $project->legal_status)) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600">Fecha de Inicio:</span>
                        <span class="text-sm text-gray-900">
                            {{ $project->start_date ? $project->start_date->format('d/m/Y') : 'No definida' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600">Fecha de Entrega:</span>
                        <span class="text-sm text-gray-900">
                            {{ $project->delivery_date ? $project->delivery_date->format('d/m/Y') : 'No definida' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Barra de Progreso -->
        <div class="mb-6 mx-8">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Vendidas: {{ $project->sold_units }}</span>
                <span>Reservadas: {{ $project->reserved_units }}</span>
                <span>Bloqueadas: {{ $project->blocked_units }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-green-500 to-blue-500 h-3 rounded-full"
                    style="width: {{ $project->progress_percentage }}%"></div>
            </div>
        </div>
        <!-- Detalles del Proyecto Mejorado: 3 columnas (Imágenes, Videos, Documentos) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 w-full">
            <!-- Columna 1: Imágenes -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm flex flex-col h-full">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <flux:icon name="photo" class="w-5 h-5 text-blue-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Imágenes del Proyecto</h3>
                </div>
                <div class="flex-1 flex flex-col justify-between">
                    @if ($project->path_images && is_array($project->path_images) && count($project->path_images) > 0)
                        <div
                            class="flex items-center justify-between bg-blue-50 rounded-lg p-3 hover:shadow transition-shadow mb-2">
                            <div class="flex items-center space-x-3 flex-1">
                                <div
                                    class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center shadow-sm">
                                    <flux:icon name="photo" class="w-5 h-5 text-blue-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-blue-900">Imágenes</p>
                                    <p class="text-xs text-blue-600">
                                        {{ count($project->path_images) }}
                                        archivo{{ count($project->path_images) == 1 ? '' : 's' }}
                                        disponible{{ count($project->path_images) == 1 ? '' : 's' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <flux:button size="xs" icon="eye" wire:click="openMediaModal('images')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                                    title="Ver galería de imágenes">
                                    Ver
                                </flux:button>
                                <flux:button size="xs" icon="plus" wire:click="addImages()"
                                    class="bg-green-600 hover:bg-green-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                                    title="Agregar imágenes">
                                    Agregar
                                </flux:button>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <flux:icon name="photo" class="w-8 h-8 text-gray-400" />
                            </div>
                            <p class="text-sm text-gray-500">No hay imágenes disponibles</p>
                            <flux:button size="xs" icon="plus" wire:click="addImages()"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                                title="Agregar imágenes">
                                Agregar
                            </flux:button>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Columna 2: Videos -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm flex flex-col h-full">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <flux:icon name="play" class="w-5 h-5 text-purple-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Videos del Proyecto</h3>
                </div>
                <div class="flex-1 flex flex-col justify-between">
                    @if ($project->path_videos && is_array($project->path_videos) && count($project->path_videos) > 0)
                        <div
                            class="flex items-center justify-between bg-purple-50 rounded-lg p-3 hover:shadow transition-shadow mb-2">
                            <div class="flex items-center space-x-3 flex-1">
                                <div
                                    class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center shadow-sm">
                                    <flux:icon name="play" class="w-5 h-5 text-purple-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-purple-900">Videos</p>
                                    <p class="text-xs text-purple-600">
                                        {{ count($project->path_videos) }}
                                        archivo{{ count($project->path_videos) == 1 ? '' : 's' }}
                                        disponible{{ count($project->path_videos) == 1 ? '' : 's' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <flux:button size="xs" icon="eye" wire:click="openMediaModal('videos')"
                                    class="bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                                    title="Ver galería de videos">
                                    Ver
                                </flux:button>
                                <flux:button size="xs" icon="plus" wire:click="addVideos()"
                                    class="bg-green-600 hover:bg-green-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                                    title="Agregar videos">
                                    Agregar
                                </flux:button>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <flux:icon name="play" class="w-8 h-8 text-gray-400" />
                            </div>
                            <p class="text-sm text-gray-500">No hay videos disponibles</p>
                            <flux:button size="xs" icon="plus" wire:click="addVideos()"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                                title="Agregar videos">
                                Agregar
                            </flux:button>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Columna 3: Documentos -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm flex flex-col h-full">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <flux:icon name="document" class="w-5 h-5 text-orange-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Documentos del Proyecto</h3>
                </div>
                <div class="flex-1 flex flex-col justify-between">
                    @if ($project->path_documents && is_array($project->path_documents) && count($project->path_documents) > 0)
                        <div
                            class="flex items-center justify-between bg-orange-50 rounded-lg p-3 hover:shadow transition-shadow mb-2">
                            <div class="flex items-center space-x-3 flex-1">
                                <div
                                    class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center shadow-sm">
                                    <flux:icon name="document" class="w-5 h-5 text-orange-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-orange-900">Documentos</p>
                                    <p class="text-xs text-orange-600">
                                        {{ count($project->path_documents) }}
                                        archivo{{ count($project->path_documents) == 1 ? '' : 's' }}
                                        disponible{{ count($project->path_documents) == 1 ? '' : 's' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <flux:button size="xs" icon="eye" wire:click="openPdfModal()"
                                    class="bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                                    title="Ver documentos PDF">
                                    Ver PDFs
                                </flux:button>
                                <flux:button size="xs" icon="plus" wire:click="addDocuments()"
                                    class="bg-green-600 hover:bg-green-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                                    title="Agregar documentos">
                                    Agregar
                                </flux:button>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <flux:icon name="document" class="w-8 h-8 text-gray-400" />
                            </div>
                            <p class="text-sm text-gray-500">No hay documentos disponibles</p>
                            <flux:button size="xs" icon="plus" wire:click="addDocuments()"
                                class="bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                                title="Agregar documentos">
                                Agregar
                            </flux:button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Lista de Unidades -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <flux:icon name="building-office-2" class="w-5 h-5 text-indigo-600" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Unidades del Proyecto</h2>
                            <p class="text-sm text-gray-600">Total: {{ $filteredUnits->total() }} unidades
                                disponibles
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center space-x-1 text-sm text-gray-600">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span>Disponible</span>
                        </div>
                        <div class="flex items-center space-x-1 text-sm text-gray-600">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <span>Reservado</span>
                        </div>
                        <div class="flex items-center space-x-1 text-sm text-gray-600">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span>Vendido</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <flux:button size="xs" icon="plus" wire:click="addUnit()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                            title="Agregar unidad">
                            Agregar
                        </flux:button>
                        <flux:button size="xs" icon="arrow-up-tray" wire:click="importUnits()"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                            title="Importar unidades desde Excel">
                            Importar
                        </flux:button>
                    </div>
                </div>

                <!-- Buscador y Filtros -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <!-- Buscador -->
                    <div class="lg:col-span-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <flux:icon name="magnifying-glass" class="h-5 w-5 text-gray-400" />
                            </div>
                            <flux:input wire:model.live="search" type="text" placeholder="Buscar unidades..." />
                        </div>
                    </div>

                    <!-- Filtro de Estado -->
                    <div>
                        <flux:select wire:model.live="statusFilter" placeholder="Todos los estados">
                            @foreach ($statusOptions as $status)
                                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <!-- Filtro de Tipo -->
                    <div>
                        <flux:select wire:model.live="typeFilter" placeholder="Todos los tipos">
                            @foreach ($typeOptions as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <!-- Botón Limpiar Filtros -->
                    <div>
                        <flux:button wire:click="clearFilters" title="Limpiar filtros">
                            Limpiar
                        </flux:button>
                    </div>
                </div>


            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unidad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Área (m²)
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio Base
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio Final
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($filteredUnits as $unit)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        Numero: {{ $unit->unit_number }}
                                        @if ($unit->tower)
                                            <span class="text-gray-500">- Torre {{ $unit->tower }}</span>
                                        @endif
                                        @if ($unit->floor)
                                            <span class="text-gray-500">- Piso {{ $unit->floor }}</span>
                                        @endif
                                    </div>
                                    @if ($unit->unit_manzana)
                                        <div class="text-xs text-gray-500">{{ $unit->unit_manzana }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $unit->unit_type === 'casa'
                                        ? 'bg-blue-100 text-blue-800'
                                        : ($unit->unit_type === 'departamento'
                                            ? 'bg-green-100 text-green-800'
                                            : ($unit->unit_type === 'lote'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($unit->unit_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($unit->area, 2) }}
                                    @if ($unit->balcony_area > 0 || $unit->terrace_area > 0 || $unit->garden_area > 0)
                                        <div class="text-xs text-gray-500">
                                            Total: {{ number_format($unit->total_area, 2) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $unit->status === 'disponible'
                                        ? 'bg-green-100 text-green-800'
                                        : ($unit->status === 'reservado'
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : ($unit->status === 'vendido'
                                                ? 'bg-blue-100 text-blue-800'
                                                : 'bg-red-100 text-red-800')) }}">
                                        {{ ucfirst($unit->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($unit->base_price)
                                        <span class="font-medium">S/{{ number_format($unit->base_price, 2) }}</span>
                                        <div class="text-xs text-gray-500">
                                            S/{{ number_format($unit->price_per_square_meter, 2) }}/m²
                                        </div>
                                    @else
                                        <span class="text-gray-400">No definido</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($unit->final_price)
                                        <span class="font-medium">S/{{ number_format($unit->final_price, 2) }}</span>
                                        @if ($unit->discount_percentage > 0)
                                            <div class="text-xs text-red-600">
                                                -{{ $unit->discount_percentage }}%
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">No definido</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <flux:button size="xs" icon="eye"
                                        wire:click="selectUnit({{ $unit->id }})">

                                    </flux:button>
                                    <flux:button size="xs" icon="pencil"
                                        wire:click="editUnit({{ $unit->id }})">
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500 py-8">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron
                                            unidades
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            @if ($search || $statusFilter || $typeFilter)
                                                Intenta ajustar los filtros de búsqueda.
                                            @else
                                                No hay unidades registradas para este proyecto.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if ($filteredUnits->hasPages())
                <div class="px-8 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Mostrando
                            <span class="font-medium">{{ $filteredUnits->firstItem() }}</span>
                            a
                            <span class="font-medium">{{ $filteredUnits->lastItem() }}</span>
                            de
                            <span class="font-medium">{{ $filteredUnits->total() }}</span>
                            resultados
                        </div>
                        <div>
                            {{ $filteredUnits->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Detalles de Unidad usando flux:modal -->
    <flux:modal wire:model="showUnitDetails" size="2xl">
        @if ($showUnitDetails && $selectedUnit)
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Detalles de la Unidad {{ $selectedUnit->unit_number }}
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Información Básica -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Información Básica</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><span class="font-medium">Tipo:</span> {{ ucfirst($selectedUnit->unit_type) }}</p>
                            <p><span class="font-medium">Número:</span> {{ $selectedUnit->unit_number }}</p>
                            @if ($selectedUnit->tower)
                                <p><span class="font-medium">Torre:</span> {{ $selectedUnit->tower }}</p>
                            @endif
                            @if ($selectedUnit->block)
                                <p><span class="font-medium">Bloque:</span> {{ $selectedUnit->block }}</p>
                            @endif
                            @if ($selectedUnit->floor)
                                <p><span class="font-medium">Piso:</span> {{ $selectedUnit->floor }}</p>
                            @endif
                            @if ($selectedUnit->unit_manzana)
                                <p><span class="font-medium">Manzana:</span> {{ $selectedUnit->unit_manzana }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Características -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Características</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><span class="font-medium">Área:</span> {{ number_format($selectedUnit->area, 2) }}
                                m²
                            </p>
                            @if ($selectedUnit->bedrooms)
                                <p><span class="font-medium">Dormitorios:</span> {{ $selectedUnit->bedrooms }}</p>
                            @endif
                            @if ($selectedUnit->bathrooms)
                                <p><span class="font-medium">Baños:</span> {{ $selectedUnit->bathrooms }}</p>
                            @endif
                            @if ($selectedUnit->parking_spaces)
                                <p><span class="font-medium">Estacionamientos:</span>
                                    {{ $selectedUnit->parking_spaces }}</p>
                            @endif
                            @if ($selectedUnit->storage_rooms)
                                <p><span class="font-medium">Bodegas:</span> {{ $selectedUnit->storage_rooms }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Áreas Adicionales -->
                    @if ($selectedUnit->balcony_area > 0 || $selectedUnit->terrace_area > 0 || $selectedUnit->garden_area > 0)
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Áreas Adicionales</h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                @if ($selectedUnit->balcony_area > 0)
                                    <p><span class="font-medium">Balcón:</span>
                                        {{ number_format($selectedUnit->balcony_area, 2) }} m²</p>
                                @endif
                                @if ($selectedUnit->terrace_area > 0)
                                    <p><span class="font-medium">Terraza:</span>
                                        {{ number_format($selectedUnit->terrace_area, 2) }} m²</p>
                                @endif
                                @if ($selectedUnit->garden_area > 0)
                                    <p><span class="font-medium">Jardín:</span>
                                        {{ number_format($selectedUnit->garden_area, 2) }} m²</p>
                                @endif
                                <p class="font-medium text-gray-900">
                                    Área Total: {{ number_format($selectedUnit->total_area, 2) }} m²
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Precios -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Información de Precios</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            @if ($selectedUnit->base_price)
                                <p><span class="font-medium">Precio Base:</span>
                                    ${{ number_format($selectedUnit->base_price, 2) }}</p>
                            @endif
                            @if ($selectedUnit->total_price)
                                <p><span class="font-medium">Precio Total:</span>
                                    ${{ number_format($selectedUnit->total_price, 2) }}</p>
                            @endif
                            @if ($selectedUnit->discount_percentage > 0)
                                <p><span class="font-medium">Descuento:</span>
                                    {{ $selectedUnit->discount_percentage }}%</p>
                            @endif
                            @if ($selectedUnit->final_price)
                                <p class="font-medium text-lg text-green-600">
                                    Precio Final: ${{ number_format($selectedUnit->final_price, 2) }}
                                </p>
                            @endif
                            @if ($selectedUnit->commission_percentage > 0)
                                <p><span class="font-medium">Comisión:</span>
                                    {{ $selectedUnit->commission_percentage }}%</p>
                            @endif
                        </div>
                    </div>
                </div>



                <!-- Notas -->
                @if ($selectedUnit->notes)
                    <div class="mt-6">
                        <h4 class="font-medium text-gray-900 mb-3">Notas</h4>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-yellow-800">{{ $selectedUnit->notes }}</p>
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex justify-end">
                    <button wire:click="closeUnitDetails"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- Modal de Medios del Proyecto -->
    <flux:modal wire:model="showMediaModal" class="w-full max-w-5xl">
        @if ($showMediaModal)
            <div class="p-6">
                <!-- Mensajes de éxito y error -->
                @if (session()->has('message'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('message') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @php
                    $mediaArray = $this->getMediaArray();
                    $currentMedia = $mediaArray[$currentMediaIndex] ?? null;
                @endphp

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 rounded-lg flex items-center justify-center
                        {{ $mediaType === 'images' ? 'bg-blue-100' : ($mediaType === 'videos' ? 'bg-purple-100' : 'bg-green-100') }}">
                            @if ($mediaType === 'images')
                                <flux:icon name="photo" class="w-5 h-5 text-blue-600" />
                            @elseif($mediaType === 'videos')
                                <flux:icon name="play" class="w-5 h-5 text-purple-600" />
                            @else
                                <flux:icon name="document" class="w-5 h-5 text-green-600" />
                            @endif
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">
                                {{ ucfirst($mediaType) }} del Proyecto
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ count($mediaArray) }} archivos disponibles
                            </p>
                        </div>
                    </div>

                    <!-- Botón de eliminar archivo actual -->
                    @if ($currentMedia && in_array($mediaType, ['images', 'videos', 'documents']))
                        <button wire:click="deleteMedia({{ $currentMediaIndex }})"
                            class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            title="Eliminar este archivo" wire:loading.attr="disabled" wire:target="deleteMedia">
                            <flux:icon name="trash" class="w-4 h-4 mr-2" wire:loading.remove
                                wire:target="deleteMedia" />
                            <flux:icon name="arrow-path" class="w-4 h-4 mr-2 animate-spin" wire:loading
                                wire:target="deleteMedia" />
                            <span wire:loading.remove wire:target="deleteMedia">Eliminar</span>
                            <span wire:loading wire:target="deleteMedia">Eliminando...</span>
                        </button>
                    @endif
                </div>

                @if ($currentMedia && count($mediaArray) > 0)
                    <!-- Contenido Principal -->
                    <div class="mb-6">
                        @if ($mediaType === 'images')
                            <div class="relative">
                                <img src="{{ asset('storage/' . $currentMedia['path']) }}" alt="Imagen del proyecto"
                                    class="w-full h-96 object-cover rounded-lg shadow-lg">

                                <!-- Controles de Navegación -->
                                @if (count($mediaArray) > 1)
                                    <button wire:click="previousMedia"
                                        class="absolute left-4 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full flex items-center justify-center transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="nextMedia"
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full flex items-center justify-center transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @elseif($mediaType === 'videos')
                            <div class="relative">
                                <video controls class="w-full h-96 object-cover rounded-lg shadow-lg">
                                    <source src="{{ asset('storage/' . $currentMedia['path']) }}" type="video/mp4">
                                    Tu navegador no soporta el elemento de video.
                                </video>
                            </div>
                        @elseif($mediaType === 'documents')
                            <div class="text-center py-12">
                                <div
                                    class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">
                                    {{ $currentMedia['title'] ?? 'Documento sin título' }}
                                </h4>
                                @if (isset($currentMedia['descripcion']) && $currentMedia['descripcion'])
                                    <p class="text-sm text-gray-600 mb-4">{{ $currentMedia['descripcion'] }}</p>
                                @endif
                                <div class="flex justify-center space-x-3">
                                    <a href="{{ asset('storage/' . $currentMedia['path']) }}" target="_blank"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <flux:icon name="eye" class="w-4 h-4 mr-2" />
                                        Ver
                                    </a>
                                    <button wire:click="downloadDocument({{ $currentMediaIndex }})"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <flux:icon name="arrow-down-tray" class="w-4 h-4 mr-2" />
                                        Descargar
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Miniaturas -->
                    @if (count($mediaArray) > 1)
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Galería</h4>
                            <div class="grid grid-cols-6 gap-2">
                                @foreach ($mediaArray as $index => $media)
                                    <div class="relative group">
                                        <!-- Botón de eliminar individual -->
                                        @if (in_array($mediaType, ['images', 'videos', 'documents']))
                                            <button wire:click="deleteMedia({{ $index }})"
                                                class="absolute top-1 right-1 z-10 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                                title="Eliminar este archivo" wire:loading.attr="disabled"
                                                wire:target="deleteMedia">
                                                <flux:icon name="x-mark" class="w-3 h-3" wire:loading.remove
                                                    wire:target="deleteMedia" />
                                                <flux:icon name="arrow-path" class="w-3 h-3 animate-spin" wire:loading
                                                    wire:target="deleteMedia" />
                                            </button>
                                        @endif

                                        <!-- Miniatura clickeable -->
                                        <button wire:click="selectMedia({{ $index }})"
                                            class="relative w-full h-20 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-lg overflow-hidden
                                                   {{ $index === $currentMediaIndex ? 'ring-2 ring-indigo-500' : '' }}">
                                            @if ($mediaType === 'images')
                                                <img src="{{ asset('storage/' . $media['path']) }}"
                                                    alt="Miniatura {{ $index + 1 }}"
                                                    class="w-full h-20 object-cover group-hover:scale-105 transition-transform duration-200">
                                                <!-- Tooltip con título de la imagen -->
                                                <div
                                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                                    <div
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-center">
                                                        <p class="text-xs text-white font-medium px-2 leading-tight">
                                                            {{ $media['title'] ?? 'Imagen' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @elseif($mediaType === 'videos')
                                                <div
                                                    class="w-full h-20 bg-gray-200 flex items-center justify-center group-hover:bg-gray-300 transition-colors">
                                                    <flux:icon name="play" class="w-6 h-6 text-gray-600" />
                                                </div>
                                                <!-- Tooltip con título del video -->
                                                <div
                                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                                    <div
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-center">
                                                        <p class="text-xs text-white font-medium px-2 leading-tight">
                                                            {{ $media['title'] ?? 'Video' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @elseif($mediaType === 'documents')
                                                <div
                                                    class="w-full h-20 bg-gray-200 flex items-center justify-center group-hover:bg-gray-300 transition-colors">
                                                    <flux:icon name="document" class="w-6 h-6 text-gray-600" />
                                                </div>
                                                <!-- Tooltip con título del documento -->
                                                <div
                                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                                    <div
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-center">
                                                        <p class="text-xs text-white font-medium px-2 leading-tight">
                                                            {{ $media['title'] ?? 'Documento' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <!-- Botón de descarga en hover -->
                                                <div
                                                    class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button wire:click="downloadDocument({{ $index }})"
                                                        class="w-6 h-6 bg-green-600 hover:bg-green-700 text-white rounded-full flex items-center justify-center"
                                                        title="Descargar {{ $media['title'] ?? 'Documento' }}">
                                                        <flux:icon name="arrow-down-tray" class="w-3 h-3" />
                                                    </button>
                                                </div>
                                            @else
                                                <div
                                                    class="w-full h-20 bg-gray-200 flex items-center justify-center group-hover:bg-gray-300 transition-colors">
                                                    <flux:icon name="document" class="w-6 h-6 text-gray-600" />
                                                </div>
                                            @endif

                                            @if ($index === $currentMediaIndex)
                                                <div
                                                    class="absolute inset-0 bg-indigo-500 bg-opacity-20 flex items-center justify-center">
                                                    <div
                                                        class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                                                        <flux:icon name="check" class="w-3 h-3 text-white" />
                                                    </div>
                                                </div>
                                            @endif
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif (count($mediaArray) === 1)
                        <!-- Mensaje cuando solo hay un archivo -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-600">
                                    Solo hay un archivo disponible. Puedes eliminarlo usando el botón "Eliminar" en
                                    la
                                    parte superior.
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Información del archivo -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Archivo {{ $currentMediaIndex + 1 }} de {{ count($mediaArray) }}</span>
                            <span>
                                {{ $currentMedia['title'] ?? basename($currentMedia['path']) }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <flux:icon name="photo" class="w-8 h-8 text-gray-400" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay medios disponibles</h3>
                        <p class="text-sm text-gray-500">Este proyecto no tiene {{ $mediaType }} cargados.</p>
                    </div>
                @endif
            </div>
        @endif
    </flux:modal>

    <!-- Modal específico para visualización de documentos PDF -->
    <flux:modal wire:model="showPdfModal" class="w-full max-w-6xl">
        @if ($showPdfModal)
            <div class="p-6">
                @php
                    $pdfArray = $this->getPdfDocuments();
                    $currentPdf = $pdfArray[$currentPdfIndex] ?? null;
                @endphp

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <flux:icon name="document" class="w-5 h-5 text-orange-600" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Documentos PDF del Proyecto</h3>
                            <p class="text-sm text-gray-600">
                                {{ count($pdfArray) }} documentos PDF disponibles
                            </p>
                        </div>
                    </div>

                    <!-- Botón de eliminar documento actual -->
                    @if ($currentPdf)
                        <button wire:click="deleteMedia({{ $currentPdfIndex }})"
                            class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            title="Eliminar este documento" wire:loading.attr="disabled" wire:target="deleteMedia">
                            <flux:icon name="trash" class="w-4 h-4 mr-2" wire:loading.remove
                                wire:target="deleteMedia" />
                            <flux:icon name="arrow-path" class="w-4 h-4 mr-2 animate-spin" wire:loading
                                wire:target="deleteMedia" />
                            <span wire:loading.remove wire:target="deleteMedia">Eliminar</span>
                            <span wire:loading wire:target="deleteMedia">Eliminando...</span>
                        </button>
                    @endif
                </div>

                @if ($currentPdf && count($pdfArray) > 0)
                    <!-- Contenido Principal -->
                    <div class="mb-6">
                        <div class="relative bg-gray-100 rounded-lg p-4">
                            <!-- Visor de PDF -->
                            <div class="w-full h-96 bg-white rounded-lg shadow-inner flex items-center justify-center">
                                <iframe
                                    src="{{ asset('storage/' . $currentPdf['path']) }}#toolbar=0&navpanes=0&scrollbar=0"
                                    class="w-full h-full rounded-lg" frameborder="0">
                                    <div class="text-center py-12">
                                        <div
                                            class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <flux:icon name="document" class="w-8 h-8 text-gray-400" />
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No se puede mostrar el PDF
                                        </h3>
                                        <p class="text-sm text-gray-500 mb-4">Tu navegador no soporta la visualización
                                            de PDFs.</p>
                                        <a href="{{ asset('storage/' . $currentPdf['path']) }}" target="_blank"
                                            class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                            <flux:icon name="arrow-down-tray" class="w-4 h-4 mr-2" />
                                            Descargar PDF
                                        </a>
                                    </div>
                                </iframe>
                            </div>

                            <!-- Controles de Navegación -->
                            @if (count($pdfArray) > 1)
                                <button wire:click="previousPdf"
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full flex items-center justify-center transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <button wire:click="nextPdf"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full flex items-center justify-center transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <!-- Información del documento -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ $currentPdf['title'] }}</h4>
                            <p class="text-sm text-gray-600 mb-2">{{ $currentPdf['descripcion'] }}</p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>Tamaño: {{ $currentPdf['size'] ?? 'N/A' }}</span>
                                <span>Modificado: {{ $currentPdf['modified'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de documentos PDF -->
                    @if (count($pdfArray) > 1)
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Documentos PDF disponibles</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach ($pdfArray as $index => $pdf)
                                    <div class="relative group">
                                        <button wire:click="selectPdf({{ $index }})"
                                            class="w-full p-3 border border-gray-200 rounded-lg hover:border-orange-300 hover:bg-orange-50 transition-all {{ $index === $currentPdfIndex ? 'border-orange-500 bg-orange-50' : '' }}">
                                            <div class="flex flex-col items-center text-center">
                                                <div
                                                    class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-2">
                                                    <flux:icon name="document" class="w-6 h-6 text-orange-600" />
                                                </div>
                                                <p
                                                    class="text-xs font-medium text-gray-900 leading-tight line-clamp-2">
                                                    {{ $pdf['title'] }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $pdf['size'] ?? 'N/A' }}
                                                </p>
                                            </div>

                                            @if ($index === $currentPdfIndex)
                                                <div
                                                    class="absolute inset-0 bg-orange-500 bg-opacity-20 flex items-center justify-center">
                                                    <div
                                                        class="w-6 h-6 bg-orange-600 rounded-full flex items-center justify-center">
                                                        <flux:icon name="check" class="w-3 h-3 text-white" />
                                                    </div>
                                                </div>
                                            @endif
                                        </button>

                                        <!-- Botón de descarga individual -->
                                        <button wire:click="downloadPdf({{ $index }})"
                                            class="absolute top-1 right-1 w-6 h-6 bg-orange-600 hover:bg-orange-700 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                                            title="Descargar {{ $pdf['title'] }}">
                                            <flux:icon name="arrow-down-tray" class="w-3 h-3" />
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Información del archivo -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Documento {{ $currentPdfIndex + 1 }} de {{ count($pdfArray) }}</span>
                            <div class="flex space-x-2">
                                <a href="{{ asset('storage/' . $currentPdf['path']) }}" target="_blank"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-700">
                                    <flux:icon name="eye" class="w-4 h-4 mr-1" />
                                    Ver en nueva pestaña
                                </a>
                                <button wire:click="downloadPdf({{ $currentPdfIndex }})"
                                    class="inline-flex items-center text-orange-600 hover:text-orange-700">
                                    <flux:icon name="arrow-down-tray" class="w-4 h-4 mr-1" />
                                    Descargar
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <flux:icon name="document" class="w-8 h-8 text-gray-400" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay documentos PDF disponibles</h3>
                        <p class="text-sm text-gray-500">Este proyecto no tiene documentos PDF cargados.</p>
                    </div>
                @endif
            </div>
        @endif
    </flux:modal>

    <!-- Modal para Agregar Imágenes -->
    <flux:modal wire:model="showAddImagesModal" size="2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <flux:icon name="photo" class="w-5 h-5 text-blue-600" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Agregar Imágenes al Proyecto</h3>
                </div>
            </div>

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="saveImages">
                <div class="space-y-4">
                    <!-- Selector de archivos -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Imágenes
                        </label>
                        <input type="file" wire:model="newImages" multiple accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />

                    </div>

                    <!-- Lista de imágenes seleccionadas -->
                    @if (count($newImages) > 0)
                        <div class="space-y-4">
                            <h4 class="text-lg font-medium text-gray-900">Configurar Imágenes</h4>
                            @foreach ($newImages as $index => $image)
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="flex items-start space-x-4">
                                        <!-- Vista previa de la imagen -->
                                        <div class="flex-shrink-0">
                                            @if ($image->getMimeType() && str_starts_with($image->getMimeType(), 'image/'))
                                                <img src="{{ $image->temporaryUrl() }}" alt="Vista previa"
                                                    class="w-20 h-20 object-cover rounded-lg border border-gray-300">
                                            @else
                                                <div
                                                    class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <flux:icon name="photo" class="w-8 h-8 text-gray-400" />
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Campos de configuración -->
                                        <div class="flex-1 space-y-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Título de la imagen {{ $index + 1 }}
                                                </label>
                                                <input type="text" wire:model="imageTitles.{{ $index }}"
                                                    placeholder="Título opcional"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                @error("imageTitles.{$index}")
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Descripción (opcional)
                                                </label>
                                                <textarea wire:model="imageDescriptions.{{ $index }}" placeholder="Descripción de la imagen" rows="2"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                                @error("imageDescriptions.{$index}")
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="text-xs text-gray-500">
                                                <p><strong>Archivo:</strong> {{ $image->getClientOriginalName() }}
                                                </p>
                                                <p><strong>Tamaño:</strong>
                                                    {{ number_format($image->getSize() / 1024, 2) }} KB</p>
                                                <p><strong>Tipo:</strong> {{ $image->getMimeType() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" wire:click="closeAddImagesModal"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span wire:loading.remove wire:target="saveImages">Guardar Imágenes</span>
                        <span wire:loading wire:target="saveImages" class="flex items-center">
                            <flux:icon name="arrow-path" class="w-4 h-4 mr-2 animate-spin" />
                            Guardando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal para Agregar Videos -->
    <flux:modal wire:model="showAddVideosModal" size="2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <flux:icon name="play" class="w-5 h-5 text-purple-600" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Agregar Videos al Proyecto</h3>
                </div>
            </div>

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="saveVideos">
                <div class="space-y-4">
                    <!-- Selector de archivos -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Videos
                        </label>
                        <input type="file" wire:model="newVideos" multiple accept="video/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" />
                        @error('newVideos.*')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Formatos soportados: MP4, AVI, MOV, WMV, FLV, WEBM. Tamaño máximo: 100MB por archivo.
                        </p>
                    </div>

                    <!-- Lista de videos seleccionados -->
                    @if (count($newVideos) > 0)
                        <div class="space-y-4">
                            <h4 class="text-lg font-medium text-gray-900">Configurar Videos</h4>
                            @foreach ($newVideos as $index => $video)
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="flex items-start space-x-4">
                                        <!-- Icono de video -->
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-20 h-20 bg-purple-100 rounded-lg flex items-center justify-center">
                                                <flux:icon name="play" class="w-8 h-8 text-purple-600" />
                                            </div>
                                        </div>

                                        <!-- Campos de configuración -->
                                        <div class="flex-1 space-y-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Título del video {{ $index + 1 }}
                                                </label>
                                                <input type="text" wire:model="videoTitles.{{ $index }}"
                                                    placeholder="Título opcional"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                                @error("videoTitles.{$index}")
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Descripción (opcional)
                                                </label>
                                                <textarea wire:model="videoDescriptions.{{ $index }}" placeholder="Descripción del video" rows="2"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                                                @error("videoDescriptions.{$index}")
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="text-xs text-gray-500">
                                                <p><strong>Archivo:</strong> {{ $video->getClientOriginalName() }}
                                                </p>
                                                <p><strong>Tamaño:</strong>
                                                    {{ number_format($video->getSize() / 1024 / 1024, 2) }} MB</p>
                                                <p><strong>Tipo:</strong> {{ $video->getMimeType() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" wire:click="closeAddVideosModal"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-purple-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <span wire:loading.remove wire:target="saveVideos">Guardar Videos</span>
                        <span wire:loading wire:target="saveVideos" class="flex items-center">
                            <flux:icon name="arrow-path" class="w-4 h-4 mr-2 animate-spin" />
                            Guardando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal para Agregar Documentos -->
    <flux:modal wire:model="showAddDocumentsModal" size="2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <flux:icon name="document" class="w-5 h-5 text-orange-600" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Agregar Documentos al Proyecto</h3>
                </div>
            </div>

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="saveDocuments">
                <div class="space-y-4">
                    <!-- Selector de archivos -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Documentos
                        </label>
                        <input type="file" wire:model="newDocuments" multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" />
                        @error('newDocuments.*')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Formatos soportados: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, RTF. Tamaño máximo: 50MB por
                            archivo.
                        </p>
                    </div>

                    <!-- Lista de documentos seleccionados -->
                    @if (count($newDocuments) > 0)
                        <div class="space-y-4">
                            <h4 class="text-lg font-medium text-gray-900">Configurar Documentos</h4>
                            @foreach ($newDocuments as $index => $document)
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="flex items-start space-x-4">
                                        <!-- Icono de documento -->
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-20 h-20 bg-orange-100 rounded-lg flex items-center justify-center">
                                                <flux:icon name="document" class="w-8 h-8 text-orange-600" />
                                            </div>
                                        </div>

                                        <!-- Campos de configuración -->
                                        <div class="flex-1 space-y-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Título del Documento
                                                </label>
                                                <input type="text"
                                                    wire:model="documentTitles.{{ $index }}"
                                                    placeholder="Ingrese un título descriptivo"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" />
                                                @error('documentTitles.' . $index)
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Descripción
                                                </label>
                                                <textarea wire:model="documentDescriptions.{{ $index }}"
                                                    placeholder="Agregue una descripción del documento (opcional)" rows="2"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"></textarea>
                                                @error('documentDescriptions.' . $index)
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Información del archivo -->
                                            <div class="text-xs text-gray-500">
                                                <p><strong>Archivo:</strong> {{ $document->getClientOriginalName() }}
                                                </p>
                                                <p><strong>Tamaño:</strong>
                                                    {{ number_format($document->getSize() / 1024, 2) }} KB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <flux:button type="button" wire:click="closeAddDocumentsModal" variant="outline" color="gray"
                        icon="x-mark" size="xs">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" color="orange" size="xs" icon="plus" wire:loading.remove
                        wire:target="saveDocuments">
                        <span wire:loading.remove wire:target="saveDocuments">Guardar Documentos</span>
                        <span wire:loading wire:target="saveDocuments" class="flex items-center">
                            <flux:icon name="arrow-path" class="w-4 h-4 mr-2 animate-spin" />
                            Guardando...
                        </span>
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal para Agregar Unidades -->
    <flux:modal variant='flyout' wire:model="showAddUnitModal" size="xl">
        <div class="p-0 md:p-0">
            <!-- Encabezado mejorado -->
            <div
                class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-xl">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-200 rounded-lg flex items-center justify-center shadow">
                        <flux:icon name="{{ $isEditing ? 'pencil' : 'plus' }}" class="w-6 h-6 text-blue-700" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-blue-900">
                            {{ $isEditing ? 'Editar Unidad' : 'Agregar Nueva Unidad' }}</h3>
                        <p class="text-sm text-gray-500">
                            {{ $isEditing ? 'Modifica los datos de la unidad seleccionada.' : 'Completa los datos para registrar una nueva unidad en el proyecto.' }}
                        </p>
                    </div>
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition"
                    @click="closeAddUnitModal">
                    <flux:icon name="x-mark" class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="saveUnit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 px-6 py-6">
                    <!-- Información básica -->
                    <div class="space-y-6 bg-white rounded-lg shadow-sm p-5 border border-gray-100">
                        <h4 class="text-lg font-semibold text-blue-800 border-b pb-2 flex items-center gap-2">
                            <flux:icon name="information-circle" class="w-5 h-5 text-blue-400" /> Información
                            Básica
                        </h4>

                        <div class="space-y-3">
                            <flux:input label="Número de Unidad *" size="sm" type="text"
                                wire:model="unit_number" placeholder="Ej: A-101">
                            </flux:input>

                            <flux:input label="Manzana" size="sm" type="text" wire:model="unit_manzana"
                                placeholder="Ej: Manzana 1, A, B, C">
                            </flux:input>

                            <flux:select label="Tipo de Unidad *" size="sm" wire:model="unit_type">
                                <option value="">Seleccionar tipo</option>
                                <option value="departamento">Departamento</option>
                                <option value="casa">Casa</option>
                                <option value="lote">Lote</option>
                                <option value="oficina">Oficina</option>
                                <option value="local">Local</option>
                            </flux:select>

                            <div class="grid grid-cols-2 gap-3">
                                <flux:input label="Torre" size="sm" type="text" wire:model="tower"
                                    placeholder="Ej: A">
                                </flux:input>
                                <flux:input label="Bloque" size="sm" type="text" wire:model="block"
                                    placeholder="Ej: 1">
                                </flux:input>
                            </div>

                            <flux:input label="Piso" size="sm" type="number" wire:model="floor"
                                min="0" placeholder="Ej: 5">
                            </flux:input>

                            <flux:input label="Área (m²) *" size="sm" type="number" wire:model="area"
                                step="0.01" min="0.01" placeholder="Ej: 120.50">
                            </flux:input>

                            <flux:select label="Estado *" size="sm" wire:model="status">
                                <option value="">Seleccionar estado</option>
                                <option value="disponible">Disponible</option>
                                <option value="reservado">Reservado</option>
                                <option value="vendido">Vendido</option>
                                <option value="bloqueado">Bloqueado</option>
                                <option value="en_construccion">En Construcción</option>
                            </flux:select>
                        </div>

                        <!-- Características -->
                        <div class="space-y-3 pt-4">
                            <h4 class="text-base font-semibold text-blue-800 border-b pb-2 flex items-center gap-2">
                                <flux:icon name="adjustments-horizontal" class="w-5 h-5 text-blue-400" />
                                Características
                            </h4>

                            <div class="grid grid-cols-2 gap-3">
                                <flux:input label="Dormitorios" size="sm" type="number" wire:model="bedrooms"
                                    min="0" placeholder="Ej: 3">
                                </flux:input>
                                <flux:input label="Baños" size="sm" type="number" wire:model="bathrooms"
                                    min="0" placeholder="Ej: 2">
                                </flux:input>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <flux:input label="Estacionamientos" size="sm" type="number"
                                    wire:model="parking_spaces" min="0" placeholder="Ej: 1">
                                </flux:input>
                                <flux:input label="Depósitos" size="sm" type="number"
                                    wire:model="storage_rooms" min="0" placeholder="Ej: 1">
                                </flux:input>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <flux:input label="Balcón (m²)" size="sm" type="number"
                                    wire:model="balcony_area" step="0.01" min="0" placeholder="Ej: 8.5">
                                </flux:input>
                                <flux:input label="Terraza (m²)" size="sm" type="number"
                                    wire:model="terrace_area" step="0.01" min="0" placeholder="Ej: 15.0">
                                </flux:input>
                                <flux:input label="Jardín (m²)" size="sm" type="number"
                                    wire:model="garden_area" step="0.01" min="0" placeholder="Ej: 25.0">
                                </flux:input>
                            </div>
                        </div>
                    </div>

                    <!-- Precios y Comisiones -->
                    <div
                        class="space-y-6 bg-white rounded-lg shadow-sm p-5 border border-gray-100 flex flex-col justify-between">
                        <div>
                            <h4 class="text-lg font-semibold text-blue-800 border-b pb-2 flex items-center gap-2">
                                <flux:icon name="currency-dollar" class="w-5 h-5 text-blue-400" /> Precios y
                                Comisiones
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
                                <flux:input label="Precio Base por m² *" size="sm" type="number"
                                    wire:model="base_price" step="0.01" min="0.01" placeholder="Ej: 2500.00">
                                </flux:input>
                                <flux:input label="Precio Total *" size="sm" type="number"
                                    wire:model="total_price" step="0.01" min="0.01"
                                    placeholder="Ej: 300000.00">
                                </flux:input>
                                <flux:input label="Descuento (%)" size="sm" type="number"
                                    wire:model="discount_percentage" step="0.01" min="0" max="100"
                                    placeholder="Ej: 5.00">
                                </flux:input>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                                <flux:input label="Comisión (%)" size="sm" type="number"
                                    wire:model="commission_percentage" step="0.01" min="0" max="100"
                                    placeholder="Ej: 3.00">
                                </flux:input>
                                <flux:textarea label="Notas" wire:model="notes" rows="3"
                                    placeholder="Notas adicionales sobre la unidad">
                                </flux:textarea>
                            </div>
                        </div>
                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
                            <flux:button icon="x-mark" variant="outline" size="sm"
                                wire:click="closeAddUnitModal">
                                Cancelar
                            </flux:button>
                            <flux:button type="submit" color="primary" size="sm"
                                icon="{{ $isEditing ? 'check' : 'plus' }}">
                                {{ $isEditing ? 'Actualizar Unidad' : 'Crear Unidad' }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal compacto para Importar Unidades -->
    <flux:modal wire:model="showImportUnitsModal" size="md">
        <div class="p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-base font-semibold text-gray-900">Importar Unidades</h3>
                <flux:button icon="x-mark" variant="ghost" wire:click="closeImportUnitsModal" size="xs">

                </flux:button>
            </div>
            <div class="text-xs text-gray-600 mb-2">
                Sube un archivo <b>CSV</b> con las unidades del proyecto.<br>
                <span class="text-blue-700">Campos requeridos:</span> <b>numero_unidad, tipo, area, precio_base,
                    precio_total</b>.<br>
                <span class="text-blue-700">Tipos válidos:</span> lote, casa, departamento, oficina, local.<br>
                <span class="text-blue-700">Estados válidos:</span> disponible, reservado, vendido, bloqueado,
                en_construccion.<br>
                <span class="text-blue-700">Importante:</span> Solo se aceptan archivos CSV.
            </div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-700">Descarga la plantilla:</span>
                <flux:button wire:click="downloadTemplate" variant="outline" size="xs" icon="arrow-down-tray">
                    Plantilla CSV
                </flux:button>
            </div>
            <div class="mb-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">
                    Archivo CSV
                </label>
                <flux:input type="file" wire:model="importFile" accept=".csv" class="w-full" />
                @error('importFile')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            @if ($importProgress > 0)
                <div class="mb-2">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-600">Progreso</span>
                        <span class="text-gray-900 font-medium">{{ $importProgress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1">
                        <div class="bg-blue-600 h-1 rounded-full transition-all duration-300"
                            style="width: {{ $importProgress }}%"></div>
                    </div>
                    @if ($importStatus)
                        <p class="text-xs text-gray-600 mt-1">{{ $importStatus }}</p>
                    @endif
                </div>
            @endif
            @if ($importSuccessCount > 0 || $importErrorCount > 0)
                <div class="mb-2 space-y-1">
                    @if ($importSuccessCount > 0)
                        <div class="flex items-center text-green-600 text-xs">
                            <flux:icon name="check-circle" class="h-4 w-4 mr-1" />
                            {{ $importSuccessCount }} importadas
                        </div>
                    @endif
                    @if ($importErrorCount > 0)
                        <div class="flex items-center text-red-600 text-xs">
                            <flux:icon name="exclamation-triangle" class="h-4 w-4 mr-1" />
                            {{ $importErrorCount }} errores
                        </div>
                    @endif
                    @if (!empty($importErrors))
                        <div class="bg-red-50 border border-red-200 rounded p-2 max-h-20 overflow-y-auto">
                            @foreach ($importErrors as $error)
                                <p class="text-xs text-red-700 mb-0.5">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
            <div class="flex justify-end space-x-2 pt-2 border-t mt-2">
                <flux:button icon="x-mark" variant="ghost" wire:click="closeImportUnitsModal" size="xs">
                    Cancelar
                </flux:button>
                <flux:button icon="arrow-up-tray" wire:click="processImport"
                    :disabled="!$importFile || $importProgress > 0" variant="primary" size="xs">
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
