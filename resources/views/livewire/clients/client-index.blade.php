<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Todos los clientes</h1>
                    <p class="text-sm text-gray-600">Listado general con filtros por ciudad, modo de alta, asesor asignado y creado por.</p>
                </div>
                <div class="flex items-center gap-2">
                    <flux:button size="xs" icon="arrow-down-tray" variant="outline" wire:click="exportViewClients">
                        Exportar vista
                    </flux:button>
                    <flux:button size="xs" icon="document-duplicate" variant="outline" wire:click="exportAllClients">
                        Exportar total
                    </flux:button>
                    <flux:button size="xs" icon="arrow-up-tray" variant="outline" wire:click="openImportModal">
                        Importar Excel
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="lg:col-span-2">
                    <flux:input
                        size="xs"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Buscar por nombre, documento o teléfono..."
                        type="search"
                    />
                    @if ($search && strlen(trim($search)) < $searchMinLength)
                        <div class="mt-1 text-[10px] text-gray-400">
                            Escribe al menos {{ $searchMinLength }} caracteres para filtrar.
                        </div>
                    @endif
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
                    <flux:input
                        size="xs"
                        type="date"
                        wire:model.live="createdFromFilter"
                        label="Creado desde"
                    />
                </div>
                <div>
                    <flux:input
                        size="xs"
                        type="date"
                        wire:model.live="createdToFilter"
                        label="Creado hasta"
                    />
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="createModeFilter">
                        <option value="">Modo de alta</option>
                        <option value="dni">DNI</option>
                        <option value="phone">Teléfono</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="assignedAdvisorFilter">
                        <option value="">Asesor asignado</option>
                        @foreach ($advisors as $advisor)
                            <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="createdByFilter">
                        <option value="">Creado por</option>
                        @foreach ($advisors as $advisor)
                            <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="flex items-end">
                    <flux:button size="xs" variant="outline" wire:click="clearFilters">
                        Limpiar filtros
                    </flux:button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div
                class="overflow-x-auto"
                wire:loading.class="opacity-60"
                wire:target="search,cityFilter,createdFromFilter,createdToFilter,createModeFilter,assignedAdvisorFilter,createdByFilter,clearFilters,openEditModal,openChangeAdvisorModal,saveEditClient,saveAdvisorAssignment,openReleaseModal,confirmReleaseClient"
            >
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Cliente</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Contacto</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Ciudad</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Modo alta</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Asesor asignado</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Creado por</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Fecha creación</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Últ. interacción</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($clients as $client)
                            <tr wire:key="client-index-{{ $client->id }}" class="hover:bg-gray-50">
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-blue-600">
                                                {{ strtoupper(substr($client->name ?? '', 0, 2)) }}
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
                                    @if ($client->birth_date)
                                        <div class="text-[10px] text-gray-400">Nac: {{ $client->birth_date->format('d/m/Y') }}</div>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-900">
                                    {{ $client->city?->name ?? '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-600">
                                    {{ $client->create_mode ? ucfirst($client->create_mode) : '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-900">
                                    {{ $client->assignedAdvisor?->name ?? 'Sin asignar' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-600">
                                    {{ $client->createdBy?->name ?? '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-600">
                                    {{ $client->created_at?->format('d/m/Y H:i') ?? '-' }}
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
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex flex-wrap items-center gap-1">
                                        @can('edit_clients')
                                            <flux:button
                                                size="xs"
                                                variant="outline"
                                                icon="pencil-square"
                                                wire:click="openEditModal({{ $client->id }})"
                                            >
                                                Editar
                                            </flux:button>
                                            <flux:button
                                                size="xs"
                                                variant="outline"
                                                icon="users"
                                                wire:click="openChangeAdvisorModal({{ $client->id }})"
                                            >
                                                Asesor
                                            </flux:button>
                                        @endcan
                                        @can('delete_clients')
                                            <flux:button
                                                size="xs"
                                                variant="outline"
                                                color="danger"
                                                icon="lock-open"
                                                wire:click="openReleaseModal({{ $client->id }})"
                                            >
                                                Liberar
                                            </flux:button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
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
            <div
                class="px-2 py-1 text-[11px] text-gray-500"
                wire:loading
                wire:target="search,cityFilter,createdFromFilter,createdToFilter,createModeFilter,assignedAdvisorFilter,createdByFilter,clearFilters,openEditModal,openChangeAdvisorModal,saveEditClient,saveAdvisorAssignment,openReleaseModal,confirmReleaseClient"
            >
                Buscando...
            </div>
            @if ($clients->hasPages())
                <div class="bg-white px-2 py-2 border-t border-gray-200">
                    {{ $clients->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Importar Excel -->
    <flux:modal wire:model="showImportModal" class="overflow-visible" size="xl">
        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Importar Excel y consultar clientes</h3>
            <p class="text-sm text-gray-600 mb-4">
                El archivo debe tener las columnas: <strong>NOMBRE CLIENTE</strong>, <strong>DNI CLIENTE</strong>, <strong>CELULAR CLIENTE</strong>.
                Se buscará cada fila por DNI; si DNI está vacío se buscará por teléfono. El resultado indicará si el cliente está registrado y mostrará documento, nombre, asesor asignado y creado por.
            </p>
            <div class="space-y-4">
                <div>
                    <flux:input
                        type="file"
                        wire:model="importFile"
                        accept=".xlsx,.xls"
                        placeholder="Selecciona un archivo Excel"
                    />
                    <p class="mt-1 text-xs text-gray-500">Solo .xlsx o .xls (máx. 10 MB)</p>
                    @error('importFile')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @if (count($importResults) > 0)
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto max-h-80">
                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Nº</th>
                                        <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Estado</th>
                                        <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Documento</th>
                                        <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Nombre</th>
                                        <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Asesor asignado</th>
                                        <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Creado por</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach ($importResults as $r)
                                        <tr wire:key="import-result-{{ $r['row_number'] }}" class="{{ $r['status'] === 'no registrado' ? 'bg-red-50' : '' }}">
                                            <td class="px-2 py-2 whitespace-nowrap text-gray-600">{{ $r['row_number'] }}</td>
                                            <td class="px-2 py-2 whitespace-nowrap">
                                                @if ($r['status'] === 'no registrado')
                                                    <span class="font-medium text-red-700">No registrado</span>
                                                @else
                                                    <span class="font-medium text-green-700">Registrado</span>
                                                @endif
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap text-gray-900">{{ $r['document'] ?? '-' }}</td>
                                            <td class="px-2 py-2 whitespace-nowrap text-gray-900">{{ $r['name'] ?? '-' }}</td>
                                            <td class="px-2 py-2 whitespace-nowrap text-gray-600">{{ $r['assigned_advisor'] ?? '-' }}</td>
                                            <td class="px-2 py-2 whitespace-nowrap text-gray-600">{{ $r['created_by'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
            <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-gray-100">
                @if (count($importResults) > 0)
                    <flux:button
                        variant="outline"
                        size="xs"
                        icon="arrow-down-tray"
                        wire:click="exportImportResults"
                    >
                        Exportar resultado
                    </flux:button>
                @endif
                <flux:button variant="outline" size="xs" wire:click="closeImportModal">
                    Cerrar
                </flux:button>
                <flux:button
                    color="primary"
                    size="xs"
                    wire:click="processImport"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                >
                    <span wire:loading.remove wire:target="processImport">Procesar archivo</span>
                    <span wire:loading wire:target="processImport">Procesando...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal editar cliente -->
    <flux:modal wire:model="showEditModal" class="max-h-[90vh]" size="xl">
        <div class="flex max-h-[inherit] flex-col p-4">
            <h3 class="mb-1 text-lg font-semibold text-gray-900">Editar cliente</h3>
            <p class="mb-4 text-sm text-gray-600">Actualiza los datos del cliente y el asesor asignado.</p>
            @if ($errors->any())
                <div class="mb-3 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-800">
                    <ul class="list-disc space-y-0.5 pl-4">
                        @foreach (array_unique($errors->all()) as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto pr-1">
                <div>
                    <flux:radio.group wire:model.live="create_mode" label="Modo de alta" size="xs">
                        <flux:radio value="dni" label="Por DNI" />
                        <flux:radio value="phone" label="Por teléfono" />
                    </flux:radio.group>
                </div>
                @if ($create_mode === 'dni')
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <flux:select wire:model.live="document_type" label="Tipo de documento" size="xs">
                            @foreach ($documentTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                        <flux:input
                            wire:model.live="document_number"
                            label="Número de documento"
                            size="xs"
                            placeholder="Número"
                        />
                    </div>
                @endif
                <flux:input wire:model="name" label="Nombre completo" size="xs" required />
                <flux:input wire:model="phone" label="Teléfono" size="xs" mask="999999999" placeholder="Ej: 999999999" />
                <flux:input wire:model.live="birth_date" label="Fecha de nacimiento" type="date" size="xs" />
                <div>
                    <flux:label class="text-xs">Dirección</flux:label>
                    <flux:textarea wire:model="address" rows="2" placeholder="Dirección" size="xs" class="mt-0.5" />
                </div>
                <flux:select wire:model="city_id" label="Ciudad" size="xs">
                    <option value="">Seleccione ciudad</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </flux:select>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <flux:select wire:model="client_type" label="Tipo de cliente" size="xs">
                        @foreach ($clientTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="source" label="Fuente" size="xs">
                        @foreach ($sources as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="status" label="Estado" size="xs">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="score" label="Score (0-100)" type="number" size="xs" min="0" max="100" />
                </div>
                <div>
                    <flux:label class="text-xs">Notas</flux:label>
                    <flux:textarea wire:model="notes" rows="2" placeholder="Notas adicionales" size="xs" class="mt-0.5" />
                </div>
                <flux:select wire:model="assigned_advisor_id" label="Asesor asignado" size="xs">
                    <option value="">Sin asignar</option>
                    @foreach ($advisors as $advisor)
                        <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div class="mt-4 flex justify-end gap-2 border-t border-gray-100 pt-3">
                <flux:button variant="outline" size="xs" wire:click="closeEditModal">Cancelar</flux:button>
                <flux:button
                    color="primary"
                    size="xs"
                    wire:click="saveEditClient"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                >
                    <span wire:loading.remove wire:target="saveEditClient">Guardar cambios</span>
                    <span wire:loading wire:target="saveEditClient">Guardando...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal cambiar asesor -->
    <flux:modal wire:model="showAdvisorModal" size="md">
        <div class="p-4">
            <h3 class="mb-1 text-lg font-semibold text-gray-900">Cambiar asesor asignado</h3>
            <p class="mb-4 text-sm text-gray-600">
                Cliente: <span class="font-medium text-gray-900">{{ $advisorModalClientName }}</span>
            </p>
            <flux:select wire:model="advisorModalAssignedId" label="Asesor asignado" size="xs">
                <option value="">Sin asignar</option>
                @foreach ($advisors as $advisor)
                    <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                @endforeach
            </flux:select>
            @error('advisorModalAssignedId')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            <div class="mt-4 flex justify-end gap-2 border-t border-gray-100 pt-3">
                <flux:button variant="outline" size="xs" wire:click="closeAdvisorModal">Cancelar</flux:button>
                <flux:button
                    color="primary"
                    size="xs"
                    wire:click="saveAdvisorAssignment"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                >
                    <span wire:loading.remove wire:target="saveAdvisorAssignment">Guardar</span>
                    <span wire:loading wire:target="saveAdvisorAssignment">Guardando...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal confirmar liberar cliente -->
    <flux:modal wire:model="showReleaseModal" size="md">
        <div class="p-4">
            <h3 class="mb-1 text-lg font-semibold text-gray-900">Liberar cliente</h3>
            <p class="text-sm text-gray-600">
                Vas a eliminar de forma lógica al cliente
                <span class="font-semibold text-gray-900">{{ $releaseClientName }}</span>
                (ID {{ $releaseClientId }}). Dejará de aparecer en los listados habituales. Esta acción requiere tu confirmación.
            </p>
            <p class="mt-2 text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-md px-2 py-1.5">
                Si no estás seguro, pulsa Cancelar. Para continuar, pulsa Liberar definitivamente.
            </p>
            <div class="mt-4 flex justify-end gap-2 border-t border-gray-100 pt-3">
                <flux:button variant="outline" size="xs" icon="x-mark" wire:click="closeReleaseModal">
                    Cancelar
                </flux:button>
                <flux:button
                    size="xs"
                    color="danger"
                    icon="lock-open"
                    wire:click="confirmReleaseClient"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                >
                    <span wire:loading.remove wire:target="confirmReleaseClient">Liberar definitivamente</span>
                    <span wire:loading wire:target="confirmReleaseClient">Liberando...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
