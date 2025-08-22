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
                    <flux:button icon="plus" size="xs" color="primary" wire:click="createClient">
                        Nuevo Cliente
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                                Estado
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fuente
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
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600">
                                                {{ strtoupper(substr($client->first_name, 0, 1) . substr($client->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $client->first_name }} {{ $client->last_name }}
                                            </div>
                                            <div class="text-xs text-gray-500">ID: {{ $client->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $client->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $client->phone }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $client->status === 'active'
                                        ? 'bg-green-100 text-green-800'
                                        : ($client->status === 'inactive'
                                            ? 'bg-gray-100 text-gray-800'
                                            : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($client->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($client->source) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $client->last_interaction ? $client->last_interaction->diffForHumans() : 'Nunca' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <flux:button size="xs" variant="outline"
                                            wire:click="viewClient({{ $client->id }})">
                                            <flux:icon name="eye" class="w-3 h-3" />
                                        </flux:button>
                                        <flux:button size="xs" variant="outline"
                                            wire:click="editClient({{ $client->id }})">
                                            <flux:icon name="pencil" class="w-3 h-3" />
                                        </flux:button>
                                        <flux:button size="xs" variant="outline" color="danger"
                                            wire:click="deleteClient({{ $client->id }})">
                                            <flux:icon name="trash" class="w-3 h-3" />
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="users" class="w-12 h-12 text-gray-300 mb-2" />
                                        <p>No se encontraron clientes</p>
                                        <flux:button size="xs" color="primary" class="mt-2"
                                            wire:click="createClient">
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
                            ¿Estás seguro de que quieres eliminar este cliente? Esta acción no se puede deshacer.
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
