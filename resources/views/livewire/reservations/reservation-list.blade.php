<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Reservas</h1>
                    <p class="text-sm text-gray-600">Gestión de reservas inmobiliarias</p>
                </div>
                <div class="flex space-x-2">
                    <flux:button icon="plus" size="xs" color="primary" wire:click="openCreateModal">
                        Nueva Reserva
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
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar reservas..." />
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="activa">Activa</option>
                        <option value="confirmada">Confirmada</option>
                        <option value="cancelada">Cancelada</option>
                        <option value="vencida">Vencida</option>
                        <option value="convertida_venta">Convertida a Venta</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="typeFilter">
                        <option value="">Todos los tipos</option>
                        <option value="pre_reserva">Pre-reserva</option>
                        <option value="reserva_firmada">Reserva Firmada</option>
                        <option value="reserva_confirmada">Reserva Confirmada</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="paymentStatusFilter">
                        <option value="">Todos los pagos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="pagado">Pagado</option>
                        <option value="parcial">Parcial</option>
                    </flux:select>
                </div>
                @if(auth()->user()->isAdmin() || auth()->user()->isLider())
                <div>
                    <flux:select size="xs" wire:model.live="advisorFilter">
                        <option value="">Todos los asesores</option>
                        @foreach($advisors as $advisor)
                            <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                @endif
                <div>
                    <flux:button size="xs" wire:click="clearFilters">Limpiar</flux:button>
                </div>
            </div>
        </div>

        <!-- Tabla de Reservas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reservations as $reservation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $reservation->reservation_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reservation->client->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reservation->project->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reservation->unit->unit_number ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    S/ {{ number_format($reservation->reservation_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $reservation->status_color }}-100 text-{{ $reservation->status_color }}-800">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reservation->expiration_date ? $reservation->expiration_date->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <flux:button size="xs" wire:click="openDetailModal({{ $reservation->id }})" icon="eye"/>
                                        <flux:button size="xs" color="primary" wire:click="openCreateModal({{ $reservation->id }})" icon="plus"/>
                                        @if($reservation->status === 'activa')
                                            <flux:button size="xs" color="success" wire:click="confirmReservation({{ $reservation->id }})" icon="check"/>
                                            <flux:button size="xs" color="danger" wire:click="cancelReservation({{ $reservation->id }})" icon="trash"/>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No se encontraron reservas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reservations->links() }}
            </div>
        </div>
    </div>

    <!-- Modal de Formulario -->
    @if($showFormModal)
        <flux:modal wire:model="showFormModal" name="reservation-form">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">
                    {{ $editingReservation ? 'Editar Reserva' : 'Nueva Reserva' }}
                </h2>
                <form wire:submit.prevent="{{ $editingReservation ? 'updateReservation' : 'createReservation' }}">
                    <div class="space-y-4">
                        <flux:select wire:model="client_id" label="Cliente" required>
                            <option value="">Seleccionar cliente</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="project_id" label="Proyecto" required>
                            <option value="">Seleccionar proyecto</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="unit_id" label="Unidad" required>
                            <option value="">Seleccionar unidad</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->unit_number }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="advisor_id" label="Asesor" required>
                            <option value="">Seleccionar asesor</option>
                            @foreach($advisors as $advisor)
                                <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:input wire:model="reservation_amount" type="number" step="0.01" label="Monto de Reserva" required />
                        <flux:input wire:model="reservation_percentage" type="number" step="0.01" label="Porcentaje (%)" />
                        <flux:input wire:model="reservation_date" type="date" label="Fecha de Reserva" required />
                        <flux:input wire:model="expiration_date" type="date" label="Fecha de Vencimiento" />

                        <div class="flex justify-end space-x-2 mt-6">
                            <flux:button type="button" wire:click="closeModals">Cancelar</flux:button>
                            <flux:button type="submit" color="primary">Guardar</flux:button>
                        </div>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif

    <!-- Modal de Detalle -->
    @if($showDetailModal && $editingReservation)
        <flux:modal wire:model="showDetailModal" name="reservation-detail">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Detalle de Reserva</h2>
                <div class="space-y-2">
                    <p><strong>Número:</strong> {{ $editingReservation->reservation_number }}</p>
                    <p><strong>Cliente:</strong> {{ $editingReservation->client->name }}</p>
                    <p><strong>Proyecto:</strong> {{ $editingReservation->project->name }}</p>
                    <p><strong>Unidad:</strong> {{ $editingReservation->unit->unit_number ?? 'N/A' }}</p>
                    <p><strong>Monto:</strong> S/ {{ number_format($editingReservation->reservation_amount, 2) }}</p>
                    <p><strong>Estado:</strong> {{ ucfirst($editingReservation->status) }}</p>
                    <p><strong>Fecha de Reserva:</strong> {{ $editingReservation->reservation_date->format('d/m/Y') }}</p>
                    <p><strong>Fecha de Vencimiento:</strong> {{ $editingReservation->expiration_date ? $editingReservation->expiration_date->format('d/m/Y') : 'N/A' }}</p>
                </div>
                <div class="flex justify-end mt-6">
                    <flux:button wire:click="closeModals">Cerrar</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>

