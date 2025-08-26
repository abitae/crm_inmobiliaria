<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header del Proyecto -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $project->name }}</h1>
                    <p class="text-gray-600 mt-1">{{ $project->full_address }}</p>
                </div>
                <div class="text-right">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        {{ $project->status === 'activo'
                            ? 'bg-green-100 text-green-800'
                            : ($project->status === 'inactivo'
                                ? 'bg-red-100 text-red-800'
                                : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($project->status) }}
                    </span>
                    <p class="text-sm text-gray-500 mt-1">{{ ucfirst($project->stage) }}</p>
                </div>
            </div>

            <!-- Información del Proyecto -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-900">Unidades Totales</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $project->total_units }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-green-900">Disponibles</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $project->available_units }}</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-orange-900">Progreso</h3>
                    <p class="text-3xl font-bold text-orange-600">{{ $project->progress_percentage }}%</p>
                </div>
            </div>

            <!-- Barra de Progreso -->
            <div class="mb-4">
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

            <!-- Detalles del Proyecto -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Información del Proyecto</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Tipo de Proyecto</span>
                            <span class="text-sm text-gray-900">{{ ucfirst($project->project_type) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Estado Legal</span>
                            <span
                                class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $project->legal_status)) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Fecha de Inicio</span>
                            <span
                                class="text-sm text-gray-900">{{ $project->start_date ? $project->start_date->format('d/m/Y') : 'No definida' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-gray-600">Fecha de Entrega</span>
                            <span
                                class="text-sm text-gray-900">{{ $project->delivery_date ? $project->delivery_date->format('d/m/Y') : 'No definida' }}</span>
                        </div>
                    </div>

                    <!-- Precios del Proyecto -->
                    @if ($project->prices->count() > 0)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Precios del Proyecto</h4>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach ($project->prices as $price)
                                    <div
                                        class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <h5 class="text-sm font-medium text-green-900">Precio por m²</h5>
                                            <div
                                                class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="text-center mb-3">
                                            <p class="text-2xl font-bold text-green-600">
                                                ${{ number_format($price->price_per_sqm, 2) }}</p>
                                            <p class="text-xs text-green-700">por metro cuadrado</p>
                                        </div>

                                        @if ($price->discount_percentage > 0)
                                            <div class="bg-red-100 border border-red-200 rounded-lg p-2 mb-3">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-3 h-3 text-red-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                                        </path>
                                                    </svg>
                                                    <span class="text-xs font-medium text-red-800">Descuento:
                                                        {{ $price->discount_percentage }}%</span>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="text-xs text-gray-600">
                                            <div class="flex justify-between">
                                                <span>Válido desde:</span>
                                                <span
                                                    class="font-medium">{{ $price->valid_from->format('d/m/Y') }}</span>
                                            </div>
                                            @if ($price->valid_until)
                                                <div class="flex justify-between mt-1">
                                                    <span>Válido hasta:</span>
                                                    <span
                                                        class="font-medium">{{ $price->valid_until->format('d/m/Y') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Asesores Asignados</h3>
                    </div>
                    @if ($project->advisors->count() > 0)
                        <div class="space-y-3">
                            @foreach ($project->advisors as $advisor)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                            {{ substr($advisor->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $advisor->name }}</p>
                                            <p class="text-xs text-gray-500">Asesor</p>
                                        </div>
                                    </div>
                                    @if ($advisor->pivot->is_primary)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Principal
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <flux:icon name="user" class="w-8 h-8 text-gray-400" />
                            </div>
                            <p class="text-sm text-gray-500">No hay asesores asignados</p>
                            <p class="text-xs text-gray-400 mt-1">Asigna un asesor para comenzar</p>
                        </div>
                    @endif
                </div>

                <!-- Documentos del Proyecto -->
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Documentos del Proyecto</h3>
                    </div>
                    <div class="space-y-3">

                        @if ($project->path_images && is_array($project->path_images) && count($project->path_images) > 0)
                            <div
                                class="flex items-center justify-between bg-blue-50 rounded-lg p-3 hover:shadow transition-shadow">
                                <div class="flex items-center space-x-3 flex-1">
                                    <div
                                        class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center shadow-sm">
                                        <flux:icon name="play" class="w-5 h-5 text-blue-600" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-blue-900">Imágenes del Proyecto</p>
                                        <p class="text-xs text-blue-600">{{ count($project->path_images) }}
                                            archivo{{ count($project->path_images) == 1 ? '' : 's' }}
                                            disponible{{ count($project->path_images) == 1 ? '' : 's' }}</p>
                                    </div>
                                </div>
                                <flux:button size="xs" icon="eye" wire:click="openMediaModal('images')"
                                    class="ml-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                                    title="Ver galería de imágenes">
                                    Ver
                                </flux:button>
                            </div>
                        @endif
                        @if ($project->path_videos && is_array($project->path_videos) && count($project->path_videos) > 0)
                            <div
                                class="flex items-center justify-between bg-purple-50 rounded-lg p-3 hover:shadow transition-shadow">
                                <div class="flex items-center space-x-3 flex-1">
                                    <div
                                        class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center shadow-sm">
                                        <flux:icon name="play" class="w-5 h-5 text-purple-600" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-purple-900">Videos del Proyecto</p>
                                        <p class="text-xs text-purple-600">
                                            {{ count($project->path_videos) }}
                                            archivo{{ count($project->path_videos) == 1 ? '' : 's' }}
                                            disponible{{ count($project->path_videos) == 1 ? '' : 's' }}
                                        </p>
                                    </div>
                                </div>
                                <flux:button size="xs" icon="eye" wire:click="openMediaModal('videos')"
                                    class="ml-4 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md px-3 py-1 transition-colors"
                                    title="Ver galería de videos">
                                    Ver
                                </flux:button>
                            </div>
                        @endif

                        @if ($project->path_documents && is_array($project->path_documents) && count($project->path_documents) > 0)
                            <!-- Tabla de Documentos -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Lista de Documentos</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th
                                                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Documento
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($project->path_documents as $document)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                        <div class="flex items-center space-x-2">
                                                            <div
                                                                class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                                                <svg class="w-4 h-4 text-green-600" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                                    </path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm font-medium text-gray-900">
                                                                    {{ \Illuminate\Support\Str::limit($document['title'] ?? 'Documento sin título', 20) }}
                                                                </p>
                                                                <p class="text-xs text-gray-500">
                                                                    {{ basename($document['path'] ?? '') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                                                        <div class="flex items-center space-x-2">
                                                            <flux:button size="xs" icon="eye"
                                                                href="{{ asset($document['path']) }}"
                                                                target="_blank">
                                                            </flux:button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div
                                    class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <flux:icon name="file" class="w-8 h-8 text-gray-400" />
                                </div>
                                <p class="text-sm text-gray-500">No hay documentos disponibles</p>
                                <p class="text-xs text-gray-400 mt-1">Agrega documentos al proyecto para comenzar</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>



        <!-- Lista de Unidades -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Unidades del Proyecto</h2>
                            <p class="text-sm text-gray-600">Total: {{ $filteredUnits->total() }} unidades disponibles
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
                </div>

                <!-- Buscador y Filtros -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <!-- Buscador -->
                    <div class="lg:col-span-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input wire:model.live="search" type="text"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Buscar unidades...">
                        </div>
                    </div>

                    <!-- Filtro de Estado -->
                    <div>
                        <select wire:model.live="statusFilter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Todos los estados</option>
                            @foreach ($statusOptions as $status)
                                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro de Tipo -->
                    <div>
                        <select wire:model.live="typeFilter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Todos los tipos</option>
                            @foreach ($typeOptions as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Botón Limpiar Filtros -->
                    <div>
                        <button wire:click="clearFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Limpiar
                        </button>
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
                                        ${{ number_format($unit->base_price, 2) }}
                                        <div class="text-xs text-gray-500">
                                            ${{ number_format($unit->price_per_square_meter, 2) }}/m²
                                        </div>
                                    @else
                                        <span class="text-gray-400">No definido</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($unit->final_price)
                                        <span class="font-medium">${{ number_format($unit->final_price, 2) }}</span>
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
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron unidades
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            @if ($search || $statusFilter || $typeFilter || $priceMin || $priceMax)
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
    <flux:modal wire:model="showUnitDetails" size="2xl" :show="$showUnitDetails && $selectedUnit"
        @close="closeUnitDetails">
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
                            <p><span class="font-medium">Área:</span> {{ number_format($selectedUnit->area, 2) }} m²
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
                                <p><span class="font-medium">Bodegas:</span> {{ $selectedUnit->storage_rooms }}</p>
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

                <!-- Historial de Precios -->
                @if ($selectedUnit->prices->count() > 0)
                    <div class="mt-6">
                        <h4 class="font-medium text-gray-900 mb-3">Historial de Precios</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach ($selectedUnit->prices as $price)
                                    <div class="bg-white p-3 rounded border">
                                        <p class="font-medium text-gray-900">
                                            ${{ number_format($price->price_per_sqm, 2) }}/m²</p>
                                        <p class="text-sm text-gray-600">Base:
                                            ${{ number_format($price->base_price, 2) }}</p>
                                        <p class="text-sm text-gray-600">Final:
                                            ${{ number_format($price->final_price, 2) }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $price->valid_from->format('d/m/Y') }}
                                            @if ($price->valid_until)
                                                - {{ $price->valid_until->format('d/m/Y') }}
                                            @endif
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

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
    <flux:modal wire:model="showMediaModal" class="w-full max-w-5xl" @close="closeMediaModal">
        @if ($showMediaModal)
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 rounded-lg flex items-center justify-center
                        {{ $mediaType === 'images' ? 'bg-blue-100' : ($mediaType === 'videos' ? 'bg-purple-100' : 'bg-green-100') }}">
                            @if ($mediaType === 'images')
                                <flux:icon name="play" class="w-5 h-5 text-blue-600" />
                            @elseif($mediaType === 'videos')
                                <flux:icon name="play" class="w-5 h-5 text-purple-600" />
                            @else
                                <flux:icon name="file" class="w-5 h-5 text-green-600" />
                            @endif
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">
                                {{ ucfirst($mediaType) }} del Proyecto
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ count($this->getMediaArray()) }} archivos disponibles
                            </p>
                        </div>
                    </div>
                </div>

                @php
                    $mediaArray = $this->getMediaArray();
                    $currentMedia = $mediaArray[$currentMediaIndex] ?? null;
                @endphp

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
                                @if(isset($currentMedia['descripcion']) && $currentMedia['descripcion'])
                                    <p class="text-sm text-gray-600 mb-4">{{ $currentMedia['descripcion'] }}</p>
                                @endif
                                <div class="space-y-3">
                                    <a href="{{ asset($currentMedia['path']) }}" target="_blank"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Visualizar Documento
                                    </a>
                                    <a href="{{ asset($currentMedia['path']) }}" download
                                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                        </svg>
                                        Descargar Documento
                                    </a>
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
                                    <button wire:click="selectMedia({{ $index }})"
                                        class="relative group focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-lg overflow-hidden
                                               {{ $index === $currentMediaIndex ? 'ring-2 ring-indigo-500' : '' }}">
                                        @if ($mediaType === 'images')
                                            <img src="{{ asset('storage/' . $media['path']) }}"
                                                alt="Miniatura {{ $index + 1 }}"
                                                class="w-full h-20 object-cover group-hover:scale-105 transition-transform duration-200">
                                            <!-- Tooltip con título de la imagen -->
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-center">
                                                    <p class="text-xs text-white font-medium px-2 leading-tight">
                                                        {{ $media['title'] ?? 'Imagen' }}
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($mediaType === 'videos')
                                            <div
                                                class="w-full h-20 bg-gray-200 flex items-center justify-center group-hover:bg-gray-300 transition-colors">
                                                <svg class="w-6 h-6 text-gray-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <!-- Tooltip con título del video -->
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-center">
                                                    <p class="text-xs text-white font-medium px-2 leading-tight">
                                                        {{ $media['title'] ?? 'Video' }}
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($mediaType === 'documents')
                                            <div
                                                class="w-full h-20 bg-gray-200 flex items-center justify-center group-hover:bg-gray-300 transition-colors">
                                                <svg class="w-6 h-6 text-gray-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <!-- Tooltip con título del documento -->
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-center">
                                                    <p class="text-xs text-white font-medium px-2 leading-tight">
                                                        {{ $media['title'] ?? 'Documento' }}
                                                    </p>
                                                </div>
                                            </div>
                                        @else
                                            <div
                                                class="w-full h-20 bg-gray-200 flex items-center justify-center group-hover:bg-gray-300 transition-colors">
                                                <svg class="w-6 h-6 text-gray-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @endif

                                        @if ($index === $currentMediaIndex)
                                            <div
                                                class="absolute inset-0 bg-indigo-500 bg-opacity-20 flex items-center justify-center">
                                                <div
                                                    class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        @endif
                                    </button>
                                @endforeach
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
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay medios disponibles</h3>
                        <p class="text-sm text-gray-500">Este proyecto no tiene {{ $mediaType }} cargados.</p>
                    </div>
                @endif
            </div>
        @endif
    </flux:modal>
</div>
