<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Clientes</h1>
                    <p class="text-sm text-gray-600">Gestión de clientes del CRM</p>
                </div>
                <div class="flex space-x-2">
                    <flux:button icon="arrow-down-tray" size="xs" wire:click="exportClients">
                        Exportar
                    </flux:button>
                    <flux:button icon="plus" size="xs" color="primary" wire:click="openCreateModal">
                        Nuevo Cliente
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- SweetAlert2 se maneja a través de JavaScript -->

        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar clientes..." />
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="nuevo">Nuevo</option>
                        <option value="contacto_inicial">Contacto inicial</option>
                        <option value="en_seguimiento">En seguimiento</option>
                        <option value="cierre">Cierre</option>
                        <option value="perdido">Perdido</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="sourceFilter">
                        <option value="">Todas las fuentes</option>
                        <option value="redes_sociales">Redes sociales</option>
                        <option value="ferias">Ferias</option>
                        <option value="referidos">Referidos</option>
                        <option value="formulario_web">Formulario web</option>
                        <option value="publicidad">Publicidad</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="typeFilter">
                        <option value="">Todos los tipos</option>
                        <option value="inversor">Inversor</option>
                        <option value="comprador">Comprador</option>
                        <option value="empresa">Empresa</option>
                        <option value="constructor">Constructor</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="advisorFilter">
                        @if (Auth::user()->isAdmin() || Auth::user()->isLider())
                            <option value="">Todos los asesores</option>
                        @endif
                        @foreach ($advisors as $advisor)
                            <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:button size="xs" variant="outline" wire:click="clearFilters">
                        Limpiar filtros
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contacto
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Documento
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo / Score
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado / Fuente
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Última Interacción
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($clients as $client)
                            <tr wire:key="client-{{ $client->id }}" class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600">
                                                {{ strtoupper(substr($client->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $client->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">ID: {{ $client->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $client->phone ?: 'Sin teléfono' }}</div>
                                    <div class="text-xs text-gray-500">
                                        @if ($client->birth_date)
                                            Nacimiento: {{ $client->birth_date->format('d/m/Y') }}
                                        @else
                                            Sin fecha de nacimiento
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-medium">{{ $client->document_type ?: 'Sin tipo' }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $client->document_number ?: 'Sin número' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <div class="text-sm text-gray-900">
                                            <span class="font-medium">{{ $client->client_type_formatted }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Score: <span class="font-medium">{{ $client->score }}/100</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <div>
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $client->status === 'nuevo'
                                                ? 'bg-blue-100 text-blue-800'
                                                : ($client->status === 'contacto_inicial'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : ($client->status === 'en_seguimiento'
                                                        ? 'bg-green-100 text-green-800'
                                                        : ($client->status === 'cierre'
                                                            ? 'bg-purple-100 text-purple-800'
                                                            : 'bg-red-100 text-red-800'))) }}">
                                                {{ ucfirst(str_replace('_', ' ', $client->status)) }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <span class="font-medium">Fuente:</span>
                                            {{ ucfirst(str_replace('_', ' ', $client->source)) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $client->last_interaction ? $client->last_interaction->diffForHumans() : 'Nunca' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <flux:button size="xs" variant="outline"
                                            wire:click="openCreateModal({{ $client->id }})">
                                            <flux:icon name="pencil" class="w-3 h-3" />
                                        </flux:button>
                                        <flux:button size="xs" variant="outline" color="danger"
                                            wire:click="confirmDelete({{ $client->id }})">
                                            <flux:icon name="trash" class="w-3 h-3" />
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="users" class="w-12 h-12 text-gray-300 mb-2" />
                                        <p>No se encontraron clientes</p>
                                        <flux:button size="xs" color="primary" class="mt-2"
                                            wire:click="openCreateModal">
                                            Crear primer cliente
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if ($clients->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $clients->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Creación/Edición de Cliente Minimalista -->
    <flux:modal variant="flyout" wire:model="showFormModal" size="md">
        <div class="p-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-base font-semibold text-gray-900">
                    {{ $editingClient ? 'Editar Cliente' : 'Nuevo Cliente' }}
                </h3>
            </div>

            <form wire:submit.prevent="{{ $editingClient ? 'updateClient' : 'createClient' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">

                    <div class="col-span-2">
                        <!-- Número de Documento -->
                        <flux:input.group>
                            <flux:select wire:model="document_type" size="xs">
                                <flux:select.option selected>DNI</flux:select.option>
                                <flux:select.option>RUC</flux:select.option>
                                <flux:select.option>CE</flux:select.option>
                                <flux:select.option>PASAPORTE</flux:select.option>
                            </flux:select>
                            <flux:input wire:model="document_number" size="xs" placeholder="N° Documento *"
                                class="w-full" />
                            <flux:button size="xs" icon="eye" wire:click="searchClient"></flux:button>
                        </flux:input.group>
                    </div>
                    <!-- Nombre -->
                    <div class="col-span-2">
                        <flux:input label="Nombre completo" wire:model="name" size="xs"
                            placeholder="Nombre completo *" class="w-full" />
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div class="col-span-2">
                        <flux:input label="Fecha de nacimiento" type="date" wire:model="birth_date"
                            size="xs" placeholder="Fecha de nacimiento" class="w-full" />
                    </div>

                    <!-- Teléfono -->
                    <div class="col-span-2">
                        <flux:input label="Teléfono" wire:model="phone" size="xs" placeholder="Teléfono"
                            class="w-full" />

                    </div>

                    <!-- Tipo de Cliente -->
                    <div>
                        <flux:select label="Tipo Cliente" wire:model="client_type" size="xs" class="w-full">
                            <option value="">Tipo Cliente *</option>
                            <option value="inversor">Inversor</option>
                            <option value="comprador">Comprador</option>
                            <option value="empresa">Empresa</option>
                            <option value="constructor">Constructor</option>
                        </flux:select>

                    </div>

                    <!-- Fuente -->
                    <div>
                        <flux:select label="Fuente" wire:model="source" size="xs" class="w-full">
                            <option value="">Fuente *</option>
                            <option value="redes_sociales">Redes Sociales</option>
                            <option value="ferias">Ferias</option>
                            <option value="referidos">Referidos</option>
                            <option value="formulario_web">Formulario Web</option>
                            <option value="publicidad">Publicidad</option>
                        </flux:select>

                    </div>

                    <!-- Estado -->
                    <div>
                        <flux:select label="Estado" wire:model="status" size="xs" class="w-full">
                            <option value="">Estado *</option>
                            <option value="nuevo">Nuevo</option>
                            <option value="contacto_inicial">Contacto Inicial</option>
                            <option value="en_seguimiento">En Seguimiento</option>
                            <option value="cierre">Cierre</option>
                            <option value="perdido">Perdido</option>
                        </flux:select>

                    </div>

                    <!-- Score -->
                    <div>
                        <flux:input label="Score" type="number" wire:model="score" min="0" max="100"
                            size="xs" placeholder="Score *" class="w-full" />

                    </div>

                    <!-- Asesor Asignado -->
                    <div>
                        <flux:select label="Asesor" wire:model="assigned_advisor_id" size="xs" class="w-full">
                            <option value="">Asesor</option>
                            @foreach ($advisors as $advisor)
                                <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                            @endforeach
                        </flux:select>

                    </div>

                    <!-- Dirección -->
                    <div class="col-span-2">
                        <flux:input label="Dirección" wire:model="address" size="xs" placeholder="Dirección"
                            class="w-full" />

                    </div>


                    <!-- Notas -->
                    <div class="col-span-2">
                        <flux:textarea label="Notas" wire:model="notes" rows="2" placeholder="Notas"
                            class="w-full text-xs px-2 py-1 border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-blue-400">
                        </flux:textarea>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-100">
                    <flux:button type="button" variant="outline" size="xs" wire:click="closeModals">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" color="primary" size="xs" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed">
                        <span wire:loading.remove>
                            {{ $editingClient ? 'Actualizar' : 'Crear' }}
                        </span>
                        <span wire:loading>
                            <flux:icon name="arrow-path" class="w-4 h-4 animate-spin" />
                            {{ $editingClient ? 'Actualizando...' : 'Creando...' }}
                        </span>
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal de Confirmación de Eliminación -->
    <flux:modal wire:model="showDeleteModal" size="sm">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <flux:icon name="exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Confirmar eliminación</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    ¿Estás seguro de que quieres eliminar este cliente? Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="flex justify-center space-x-3 mt-4">
                <flux:button size="xs" variant="outline" wire:click="closeModals">
                    Cancelar
                </flux:button>
                <flux:button size="xs" color="danger" wire:click="deleteClient" wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed">
                    <span wire:loading.remove>
                        Eliminar
                    </span>
                    <span wire:loading>
                        <flux:icon name="arrow-path" class="w-4 h-4 animate-spin" />
                        Eliminando...
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Script para SweetAlert2 -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Escuchar eventos de éxito
            Livewire.on('show-success', (event) => {
                window.showSuccess(event.message);
            });

            // Escuchar eventos de confirmación
            Livewire.on('show-confirm', (event) => {
                window.showConfirm(event.message, event.title).then((result) => {
                    if (result.isConfirmed) {
                        // Ejecutar la acción confirmada
                        if (event.action === 'deleteClient') {
                            @this.deleteClient();
                        }
                    }
                });
            });

            // Escuchar eventos de error
            Livewire.on('show-error', (event) => {
                window.showError(event.message);
            });

            // Escuchar eventos de información
            Livewire.on('show-info', (event) => {
                window.showInfo(event.message);
            });
        });
    </script>
</div>
