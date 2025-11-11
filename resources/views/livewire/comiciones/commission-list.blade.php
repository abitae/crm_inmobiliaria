<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Comisiones</h1>
                    <p class="text-sm text-gray-600">Gestión de comisiones de asesores</p>
                </div>
                <div class="flex space-x-2">
                    <flux:button icon="plus" size="xs" color="primary" wire:click="openCreateModal">
                        Nueva Comisión
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar comisiones..." />
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="aprobada">Aprobada</option>
                        <option value="pagada">Pagada</option>
                        <option value="cancelada">Cancelada</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="typeFilter">
                        <option value="">Todos los tipos</option>
                        <option value="venta">Venta</option>
                        <option value="reserva">Reserva</option>
                        <option value="seguimiento">Seguimiento</option>
                        <option value="bono">Bono</option>
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

        <!-- Tabla de Comisiones -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asesor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Base</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comisión</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($commissions as $commission)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $commission->advisor->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $commission->project->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($commission->commission_type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    S/ {{ number_format($commission->base_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    S/ {{ number_format($commission->commission_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    S/ {{ number_format($commission->total_commission, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $commission->status_color }}-100 text-{{ $commission->status_color }}-800">
                                        {{ ucfirst($commission->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <flux:button size="xs" wire:click="openDetailModal({{ $commission->id }})">Ver</flux:button>
                                        @if($commission->status === 'pendiente')
                                            <flux:button size="xs" color="success" wire:click="openApproveModal({{ $commission->id }})">Aprobar</flux:button>
                                        @endif
                                        @if($commission->status === 'aprobada')
                                            <flux:button size="xs" color="primary" wire:click="openPayModal({{ $commission->id }})">Pagar</flux:button>
                                        @endif
                                        @if(in_array($commission->status, ['pendiente', 'aprobada']))
                                            <flux:button size="xs" color="danger" wire:click="cancelCommission({{ $commission->id }})">Cancelar</flux:button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No se encontraron comisiones
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $commissions->links() }}
            </div>
        </div>
    </div>

    <!-- Modal de Formulario -->
    @if($showFormModal)
        <flux:modal wire:model="showFormModal" name="commission-form">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">
                    {{ $editingCommission ? 'Editar Comisión' : 'Nueva Comisión' }}
                </h2>
                <form wire:submit.prevent="{{ $editingCommission ? 'updateCommission' : 'createCommission' }}">
                    <div class="space-y-4">
                        <flux:select wire:model="advisor_id" label="Asesor" required>
                            <option value="">Seleccionar asesor</option>
                            @foreach($advisors as $advisor)
                                <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="project_id" label="Proyecto" required>
                            <option value="">Seleccionar proyecto</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="unit_id" label="Unidad">
                            <option value="">Seleccionar unidad</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->unit_number }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="opportunity_id" label="Oportunidad">
                            <option value="">Seleccionar oportunidad</option>
                            @foreach($opportunities as $opportunity)
                                <option value="{{ $opportunity->id }}">{{ $opportunity->id }} - {{ $opportunity->client->name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="commission_type" label="Tipo de Comisión" required>
                            <option value="venta">Venta</option>
                            <option value="reserva">Reserva</option>
                            <option value="seguimiento">Seguimiento</option>
                            <option value="bono">Bono</option>
                        </flux:select>

                        <flux:input wire:model="base_amount" type="number" step="0.01" label="Monto Base" required />
                        <flux:input wire:model="commission_percentage" type="number" step="0.01" label="Porcentaje de Comisión (%)" required />
                        <flux:input wire:model="bonus_amount" type="number" step="0.01" label="Bono Adicional" />

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
    @if($showDetailModal && $editingCommission)
        <flux:modal wire:model="showDetailModal" name="commission-detail">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Detalle de Comisión</h2>
                <div class="space-y-2">
                    <p><strong>Asesor:</strong> {{ $editingCommission->advisor->name }}</p>
                    <p><strong>Proyecto:</strong> {{ $editingCommission->project->name }}</p>
                    <p><strong>Tipo:</strong> {{ ucfirst($editingCommission->commission_type) }}</p>
                    <p><strong>Monto Base:</strong> S/ {{ number_format($editingCommission->base_amount, 2) }}</p>
                    <p><strong>Porcentaje:</strong> {{ $editingCommission->commission_percentage }}%</p>
                    <p><strong>Comisión:</strong> S/ {{ number_format($editingCommission->commission_amount, 2) }}</p>
                    <p><strong>Bono:</strong> S/ {{ number_format($editingCommission->bonus_amount, 2) }}</p>
                    <p><strong>Total:</strong> S/ {{ number_format($editingCommission->total_commission, 2) }}</p>
                    <p><strong>Estado:</strong> {{ ucfirst($editingCommission->status) }}</p>
                </div>
                <div class="flex justify-end mt-6">
                    <flux:button wire:click="closeModals">Cerrar</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    <!-- Modal de Aprobación -->
    @if($showApproveModal && $editingCommission)
        <flux:modal wire:model="showApproveModal" name="commission-approve">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Aprobar Comisión</h2>
                <p class="mb-4">¿Está seguro de aprobar esta comisión?</p>
                <div class="flex justify-end space-x-2 mt-6">
                    <flux:button type="button" wire:click="closeModals">Cancelar</flux:button>
                    <flux:button type="button" color="success" wire:click="approveCommission">Aprobar</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    <!-- Modal de Pago -->
    @if($showPayModal && $editingCommission)
        <flux:modal wire:model="showPayModal" name="commission-pay">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Pagar Comisión</h2>
                <form wire:submit.prevent="payCommission">
                    <div class="space-y-4">
                        <flux:input wire:model="payment_date" type="date" label="Fecha de Pago" required />
                        <flux:input wire:model="payment_method" label="Método de Pago" required />
                        <flux:input wire:model="payment_reference" label="Referencia de Pago" />
                        <div class="flex justify-end space-x-2 mt-6">
                            <flux:button type="button" wire:click="closeModals">Cancelar</flux:button>
                            <flux:button type="submit" color="primary">Pagar</flux:button>
                        </div>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>
