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
                    <flux:button icon="user-group" size="xs" color="primary"
                        href="{{ route('clients.registro-masivo') }}">
                        Registro masivo
                    </flux:button>
                    <flux:button icon="plus" size="xs" color="primary" wire:click="openCreateModal">
                        Nuevo Cliente
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
                        @if (Auth::user()->isAdmin())
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

        <!-- Tabla de Clientes Compacta -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Cliente</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Contacto</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Tipo/Score</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Estado/Fuente</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Asesor</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Últ. Interacción</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($clients as $client)
                            <tr wire:key="client-{{ $client->id }}" class="hover:bg-gray-50">
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-blue-600">
                                                {{ strtoupper(substr($client->name, 0, 2)) }}
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
                                    <div class="text-[10px] text-gray-400">
                                        @if ($client->birth_date)
                                            Nac: {{ $client->birth_date->format('d/m/Y') }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div>
                                        <span class="font-medium">{{ $client->client_type_formatted }}</span>
                                    </div>
                                    <div class="text-[10px] text-gray-400">
                                        {{ $client->score }}/100
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium
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
                                    <div class="text-[10px] text-gray-400">
                                        {{ ucfirst(str_replace('_', ' ', $client->source)) }}
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-gray-900">
                                        Asesor: {{ $client->assignedAdvisor ? $client->assignedAdvisor->name : '-' }}
                                    </div>
                                    <div class="text-[10px] text-gray-400">
                                        Created: {{ $client->createdBy ? $client->createdBy->name : '-' }}
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-500">
                                    {{ optional($client->activities->last())->title ?? 'Sin actividad' }}
                                    <br>
                                    {{ optional(optional($client->activities->last())->start_date)->format('d/m/Y') }}

                                </td>
                                <td class="px-2 py-2 whitespace-nowrap font-medium">
                                    <div class="flex space-x-1">
                                        <flux:button size="xs" variant="outline"
                                            wire:click="openCreateModal({{ $client->id }})">
                                            <flux:icon name="pencil" class="w-3 h-3" />
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
                <div class="bg-white px-2 py-2 border-t border-gray-200">
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
                        <flux:input.group class="flex items-end w-full">
                            <flux:select wire:model.live="document_type" label="Tipo" size="xs"
                                class="w-full">
                                <option value="DNI">DNI</option>
                            </flux:select>
                            <flux:input mask="99999999" class="flex-1" label="Documento"
                                placeholder="Número de documento" wire:model="document_number" size="xs" />
                            @if ($document_type == 'DNI')
                                <flux:button icon="magnifying-glass" wire:click="buscarDocumento" variant="outline"
                                    label="Buscar" size="xs" class="self-end" />
                            @endif
                        </flux:input.group>
                    </div>
                    <!-- Nombre -->
                    <div class="col-span-2">
                        <flux:input label="Nombre completo" wire:model="name" size="xs" disabled
                            placeholder="Nombre completo *" class="w-full" />
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div class="col-span-2">
                        <flux:input label="Fecha de nacimiento" type="date" wire:model="birth_date" disabled
                            size="xs" placeholder="Fecha de nacimiento" class="w-full" />
                    </div>

                    <!-- Teléfono -->
                    <div class="col-span-2">
                        <flux:input mask="999999999" label="Teléfono" wire:model="phone" size="xs"
                            placeholder="Teléfono" class="w-full" />
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


</div>
