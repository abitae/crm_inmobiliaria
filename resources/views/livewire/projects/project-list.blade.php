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
    <flux:modal variant="flyout" wire:model="showFormModal" size="4xl" class="max-w-7xl">
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
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-700">Ubicación y Fechas</h4>

                        <div>

                            <flux:input wire:model="address" placeholder="Dirección completa" size="xs" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>

                                <flux:input wire:model="district" placeholder="Distrito" size="xs" />
                            </div>
                            <div>

                                <flux:input wire:model="province" placeholder="Provincia" size="xs" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>

                                <flux:input wire:model="region" placeholder="Región" size="xs" />
                            </div>
                            <div>

                                <flux:input wire:model="country" placeholder="País" size="xs" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>

                                <flux:input wire:model="latitude" type="number" step="any"
                                    placeholder="0.000000" size="xs" />
                            </div>
                            <div>

                                <flux:input wire:model="longitude" type="number" step="any"
                                    placeholder="0.000000" size="xs" />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>

                                <flux:input wire:model="start_date" type="date" size="xs" />
                            </div>
                            <div>

                                <flux:input wire:model="end_date" type="date" size="xs" />
                            </div>
                            <div>

                                <flux:input wire:model="delivery_date" type="date" size="xs" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos Multimedia -->
                <div class="mt-8 space-y-6">
                    <!-- Información de archivos -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-800 mb-2">Información sobre archivos (opcionales)</h4>
                        <div class="text-sm text-blue-700 space-y-1">
                            <div><strong>Imágenes:</strong> JPG, PNG, GIF (máx. 2MB) - <em>Opcional</em></div>
                            <div><strong>Videos:</strong> MP4, AVI, MOV, WMV (máx. 10MB) - <em>Opcional</em></div>
                            <div><strong>Documentos:</strong> PDF, DOC, DOCX (máx. 5MB) - <em>Opcional</em></div>
                        </div>
                    </div>

                    <!-- Imágenes -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-medium text-gray-700">Imágenes del Proyecto (Opcional)</h4>
                            <flux:button icon="plus" type="button" size="xs" variant="outline"
                                wire:click="addImage" class="flex items-center">
                                Agregar Imagen
                            </flux:button>
                        </div>

                        @forelse($path_images as $index => $image)
                            <div class="grid grid-cols-2 gap-4 mb-3 p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <div class="block text-sm font-medium text-gray-700 mb-1">Tipo (opcional)</div>
                                    <flux:input wire:model="path_images.{{ $index }}.type"
                                        placeholder="exterior, interior, etc." size="xs" />
                                </div>
                                <div>
                                    <div class="block text-sm font-medium text-gray-700 mb-1">Archivo</div>
                                    <input type="file" wire:model="imageFiles.{{ $index }}"
                                        accept="image/*" class="w-full text-xs">
                                </div>
                                <div class="col-span-2 flex items-end space-x-2">
                                    <div class="flex-1">
                                        <div class="block text-sm font-medium text-gray-700 mb-1">Vista previa</div>
                                        @if ($image['path'] && !$imageFiles[$index])
                                            <img src="{{ $image['path'] }}" alt="{{ $image['name'] }}"
                                                class="w-20 h-20 object-cover rounded border">
                                        @elseif($imageFiles[$index])
                                            <div class="text-xs text-green-600">Archivo seleccionado:
                                                {{ $imageFiles[$index]->getClientOriginalName() }}</div>
                                        @else
                                            <div class="text-xs text-gray-500">Sin archivo</div>
                                        @endif
                                    </div>
                                    <flux:button type="button" size="xs" color="danger"
                                        wire:click="removeImage({{ $index }})">
                                        <flux:icon name="trash" class="w-4 h-4" />
                                    </flux:button>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No hay imágenes agregadas</p>
                        @endforelse
                    </div>

                    <!-- Videos -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-medium text-gray-700">Videos del Proyecto (Opcional)</h4>
                            <flux:button icon="plus" type="button" size="xs" variant="outline"
                                wire:click="addVideo" class="flex items-center">
                                Agregar Video
                            </flux:button>
                        </div>

                        @forelse($path_videos as $index => $video)
                            <div class="grid grid-cols-2 gap-4 mb-3 p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <div class="block text-sm font-medium text-gray-700 mb-1">Tipo (opcional)</div>
                                    <flux:input wire:model="path_videos.{{ $index }}.type"
                                        placeholder="tour, presentación, etc." size="xs" />
                                </div>
                                <div>
                                    <div class="block text-sm font-medium text-gray-700 mb-1">Archivo</div>
                                    <input type="file" wire:model="videoFiles.{{ $index }}"
                                        accept="video/*" class="w-full text-xs">
                                </div>
                                <div class="col-span-2 flex items-end space-x-2">
                                    <div class="flex-1">
                                        <div class="block text-sm font-medium text-gray-700 mb-1">Estado</div>
                                        @if ($video['path'] && !$videoFiles[$index])
                                            <div class="text-xs text-blue-600">Video existente: {{ $video['name'] }}
                                            </div>
                                        @elseif($videoFiles[$index])
                                            <div class="text-xs text-green-600">Archivo seleccionado:
                                                {{ $videoFiles[$index]->getClientOriginalName() }}</div>
                                        @else
                                            <div class="text-xs text-gray-500">Sin archivo</div>
                                        @endif
                                    </div>
                                    <flux:button type="button" size="xs" color="danger"
                                        wire:click="removeVideo({{ $index }})">
                                        <flux:icon name="trash" class="w-4 h-4" />
                                    </flux:button>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-xs">No hay videos agregados</p>
                        @endforelse
                    </div>

                    <!-- Documentos -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-medium text-gray-700">Documentos del Proyecto (Opcional)</h4>
                            <flux:button icon="plus" type="button" size="xs" variant="outline"
                                wire:click="addDocument" class="flex items-center">
                                Agregar Documento
                            </flux:button>
                        </div>

                        @forelse($path_documents as $index => $document)
                            <div class="grid grid-cols-2 gap-4 mb-3 p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <div class="block text-sm font-medium text-gray-700 mb-1">Tipo (opcional)</div>
                                    <flux:input wire:model="path_documents.{{ $index }}.type"
                                        placeholder="brochure, plano, etc." size="xs" />
                                </div>
                                <div>
                                    <div class="block text-sm font-medium text-gray-700 mb-1">Archivo</div>
                                    <input type="file" wire:model="documentFiles.{{ $index }}"
                                        accept=".pdf,.doc,.docx" class="w-full text-xs">
                                </div>
                                <div class="col-span-2 flex items-end space-x-2">
                                    <div class="flex-1">
                                        <div class="block text-sm font-medium text-gray-700 mb-1">Estado</div>
                                        @if ($document['path'] && !$documentFiles[$index])
                                            <div class="text-xs text-blue-600">Documento existente:
                                                {{ $document['name'] }}</div>
                                        @elseif($documentFiles[$index])
                                            <div class="text-xs text-green-600">Archivo seleccionado:
                                                {{ $documentFiles[$index]->getClientOriginalName() }}</div>
                                        @else
                                            <div class="text-xs text-gray-500">Sin archivo</div>
                                        @endif
                                    </div>
                                    <flux:button type="button" size="xs" color="danger"
                                        wire:click="removeDocument({{ $index }})">
                                        <flux:icon name="trash" class="w-4 h-4" />
                                    </flux:button>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No hay documentos agregados</p>
                        @endforelse
                    </div>
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
