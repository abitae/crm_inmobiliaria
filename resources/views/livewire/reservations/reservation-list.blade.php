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
                    <flux:button icon="plus" size="xs" variant="primary" color="blue" wire:click="openCreateModal">
                        Nueva Reserva
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-8 gap-4">
                <div class="xl:col-span-2">
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
                    <flux:select size="xs" wire:model.live="paymentStatusFilter">
                        <option value="">Todos los pagos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="pagado">Pagado</option>
                        <option value="parcial">Parcial</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="projectFilter">
                        <option value="">Todos los proyectos</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="clientFilter">
                        <option value="">Todos los clientes</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
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
                    <flux:button size="xs" variant="primary" color="zinc" wire:click="clearFilters">Limpiar</flux:button>
                </div>
            </div>
        </div>

        <!-- Tabla de Reservas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número / Cliente</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto / Unidad</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto / Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reservations as $reservation)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $reservation->reservation_number }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $reservation->client->name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ $reservation->project->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1">Unidad: {{ $reservation->unit->unit_number ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">S/ {{ number_format($reservation->reservation_amount, 2) }}</div>
                                    <div class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $reservation->status_color }}-100 text-{{ $reservation->status_color }}-800">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reservation->expiration_date ? $reservation->expiration_date->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-1">
                                        <flux:button size="xs" variant="primary" color="zinc" wire:click="openDetailModal({{ $reservation->id }})" icon="eye"/>
                                        @if($reservation->status === 'activa')
                                        <flux:button size="xs" variant="primary" color="blue" wire:click="openCreateModal({{ $reservation->id }})" icon="pencil"/>
                                            <flux:button size="xs" variant="primary" color="amber" wire:click="openConfirmationModal({{ $reservation->id }})" icon="arrow-up-tray" title="Subir imagen de confirmación"/>
                                            <flux:button size="xs" variant="primary" color="red" wire:click="cancelReservation({{ $reservation->id }})" icon="trash"/>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">
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
        <flux:modal variant="flyout" wire:model="showFormModal" name="reservation-form">
            <div class="p-4">
                <h2 class="text-base font-semibold mb-3">
                    {{ $editingReservation ? 'Editar Reserva' : 'Nueva Reserva' }}
                </h2>
                <form wire:submit.prevent="{{ $editingReservation ? 'updateReservation' : 'createReservation' }}">
                    <div class="space-y-3 max-h-[80vh] overflow-y-auto pr-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <flux:select size="xs" wire:model="client_id" label="Cliente" required>
                                <option value="">Seleccionar cliente</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </flux:select>

                            <flux:select size="xs" wire:model="project_id" label="Proyecto" required>
                                <option value="">Seleccionar proyecto</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <flux:select size="xs" wire:model="unit_id" label="Unidad" required>
                                <option value="">Seleccionar unidad</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_number }}</option>
                                @endforeach
                            </flux:select>

                            <flux:select size="xs" wire:model="advisor_id" label="Asesor" required>
                                <option value="">Seleccionar asesor</option>
                                @foreach($advisors as $advisor)
                                    <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <flux:select size="xs" wire:model="reservation_type" label="Tipo de Reserva" required :disabled="!$editingReservation">
                                <option value="pre_reserva">Pre-reserva</option>
                                <option value="reserva_firmada">Reserva Firmada</option>
                                <option value="reserva_confirmada">Reserva Confirmada</option>
                            </flux:select>

                            <flux:select size="xs" wire:model="status" label="Estado" required>
                                <option value="activa">Activa</option>
                                <option value="confirmada">Confirmada</option>
                                <option value="cancelada">Cancelada</option>
                                <option value="vencida">Vencida</option>
                                <option value="convertida_venta">Convertida a Venta</option>
                            </flux:select>
                        </div>
                        @if(!$editingReservation)
                        <div class="bg-blue-50 border border-blue-200 rounded p-2 text-xs text-blue-700">
                            El tipo será "Pre-reserva" y el estado "Activa" al crear. Si subes una imagen, cambiará a "Confirmada".
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <flux:input size="xs" wire:model="reservation_date" type="date" label="Fecha de Reserva" required />
                            <flux:input size="xs" wire:model="expiration_date" type="date" label="Fecha de Vencimiento" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <flux:input size="xs" wire:model="reservation_amount" type="number" step="0.01" label="Monto de Reserva" required />
                            <flux:input size="xs" wire:model="reservation_percentage" type="number" step="0.01" label="Porcentaje (%)" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <flux:select size="xs" wire:model="payment_status" label="Estado de Pago" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="pagado">Pagado</option>
                                <option value="parcial">Parcial</option>
                            </flux:select>

                            <flux:input size="xs" wire:model="payment_method" label="Método de Pago" />
                        </div>

                        <flux:input size="xs" wire:model="payment_reference" label="Referencia de Pago" />

                        <!-- Campo de Imagen -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Imagen</label>
                            @if($imagePreview)
                                <div class="mb-1">
                                    <img src="{{ $imagePreview }}" alt="Vista previa" class="w-24 h-24 object-cover rounded border border-gray-300">
                                </div>
                            @elseif($editingReservation && $editingReservation->image)
                                <div class="mb-1">
                                    <img src="{{ $editingReservation->image_url }}" alt="Imagen actual" class="w-24 h-24 object-cover rounded border border-gray-300">
                                </div>
                            @endif
                            <flux:input size="xs" wire:model="image" type="file" accept="image/*" label="Subir Imagen" />
                            <p class="mt-0.5 text-xs text-gray-500">JPEG, PNG, JPG, GIF, WEBP (máx. 10MB)</p>
                        </div>

                        <flux:textarea size="xs" wire:model="notes" label="Notas" rows="2" />
                        <flux:textarea size="xs" wire:model="terms_conditions" label="Términos y Condiciones" rows="2" />

                        <div class="flex justify-end space-x-2 pt-3 border-t">
                            <flux:button size="xs" type="button" variant="primary" color="zinc" wire:click="closeModals">Cancelar</flux:button>
                            <flux:button size="xs" type="submit" variant="primary" color="blue">Guardar</flux:button>
                        </div>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif

    <!-- Modal de Detalle -->
    @if($showDetailModal && $editingReservation)
        <flux:modal wire:model="showDetailModal" name="reservation-detail">
            <div class="p-4">
                <h2 class="text-base font-semibold mb-3">Detalle de Reserva</h2>
                <div class="space-y-3 max-h-[80vh] overflow-y-auto">
                    @if($editingReservation->image)
                        <div class="mb-3">
                            <img src="{{ $editingReservation->image_url }}" alt="Imagen de reserva" class="w-full max-w-sm h-auto rounded border border-gray-300">
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-600">Número de Reserva</p>
                            <p class="text-sm font-semibold">{{ $editingReservation->reservation_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Tipo</p>
                            <p class="text-sm font-semibold">{{ ucfirst(str_replace('_', ' ', $editingReservation->reservation_type)) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Cliente</p>
                            <p class="text-sm font-semibold">{{ $editingReservation->client->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Proyecto</p>
                            <p class="text-sm font-semibold">{{ $editingReservation->project->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Unidad</p>
                            <p class="text-sm font-semibold">{{ $editingReservation->unit->unit_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Asesor</p>
                            <p class="text-sm font-semibold">{{ $editingReservation->advisor->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Monto de Reserva</p>
                            <p class="text-sm font-semibold">S/ {{ number_format($editingReservation->reservation_amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Porcentaje</p>
                            <p class="text-sm font-semibold">{{ $editingReservation->reservation_percentage ? number_format($editingReservation->reservation_percentage, 2) . '%' : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Estado</p>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $editingReservation->status_color }}-100 text-{{ $editingReservation->status_color }}-800">
                                {{ ucfirst($editingReservation->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Estado de Pago</p>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $editingReservation->payment_status_color }}-100 text-{{ $editingReservation->payment_status_color }}-800">
                                {{ ucfirst($editingReservation->payment_status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Fecha de Reserva</p>
                            <p class="text-sm font-semibold">{{ $editingReservation->reservation_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Fecha de Vencimiento</p>
                            <p class="text-sm font-semibold">{{ $editingReservation->expiration_date ? $editingReservation->expiration_date->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        @if($editingReservation->payment_method)
                        <div>
                            <p class="text-xs text-gray-600">Método de Pago</p>
                            <p class="text-sm font-semibold">{{ $editingReservation->payment_method }}</p>
                        </div>
                        @endif
                        @if($editingReservation->payment_reference)
                        <div>
                            <p class="text-xs text-gray-600">Referencia de Pago</p>
                            <p class="text-sm font-semibold">{{ $editingReservation->payment_reference }}</p>
                        </div>
                        @endif
                    </div>
                    
                    @if($editingReservation->notes)
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Notas</p>
                            <p class="text-xs bg-gray-50 p-2 rounded border">{{ $editingReservation->notes }}</p>
                        </div>
                    @endif
                    
                    @if($editingReservation->terms_conditions)
                        <div>
                            <p class="text-xs text-gray-600 mb-1">Términos y Condiciones</p>
                            <p class="text-xs bg-gray-50 p-2 rounded border">{{ $editingReservation->terms_conditions }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3 pt-3 border-t">
                        <div>
                            <p class="text-xs text-gray-600">Creado por</p>
                            <p class="text-sm font-semibold">{{ $editingReservation->createdBy->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $editingReservation->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($editingReservation->updatedBy)
                        <div>
                            <p class="text-xs text-gray-600">Actualizado por</p>
                            <p class="text-sm font-semibold">{{ $editingReservation->updatedBy->name }}</p>
                            <p class="text-xs text-gray-500">{{ $editingReservation->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="flex justify-end pt-3 border-t">
                    <flux:button size="xs" variant="primary" color="zinc" wire:click="closeModals">Cerrar</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    <!-- Modal de Confirmación con Imagen -->
    @if($showConfirmationModal && $confirmingReservation)
        <flux:modal variant="flyout" wire:model="showConfirmationModal" name="confirmation-modal">
            <div class="p-4">
                <h2 class="text-base font-semibold mb-3">Confirmar Reserva - Subir Comprobante</h2>
                <form wire:submit.prevent="submitConfirmation">
                    <div class="space-y-3 max-h-[80vh] overflow-y-auto pr-2">
                        <div class="bg-blue-50 border border-blue-200 rounded p-2 mb-3">
                            <p class="text-xs text-blue-800">
                                <strong>Reserva:</strong> {{ $confirmingReservation->reservation_number }} | 
                                <strong>Cliente:</strong> {{ $confirmingReservation->client->name }} | 
                                <strong>Proyecto:</strong> {{ $confirmingReservation->project->name }} | 
                                <strong>Unidad:</strong> {{ $confirmingReservation->unit->unit_number ?? 'N/A' }}
                            </p>
                        </div>

                        <!-- Campo de Imagen (Requerido) -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                Imagen del Comprobante de Pago <span class="text-red-500">*</span>
                            </label>
                            @if($confirmation_imagePreview)
                                <div class="mb-1">
                                    <img src="{{ $confirmation_imagePreview }}" alt="Vista previa" class="w-full max-w-sm h-auto object-cover rounded border border-gray-300">
                                </div>
                            @elseif($confirmingReservation->image)
                                <div class="mb-1">
                                    <img src="{{ $confirmingReservation->image_url }}" alt="Imagen actual" class="w-full max-w-sm h-auto object-cover rounded border border-gray-300">
                                    <p class="text-xs text-gray-500 mt-0.5">Imagen actual (se reemplazará)</p>
                                </div>
                            @endif
                            <flux:input size="xs" wire:model="confirmation_image" type="file" accept="image/*" label="Subir Imagen del Comprobante" required />
                            <p class="mt-0.5 text-xs text-gray-500">JPEG, PNG, JPG, GIF, WEBP (máx. 10MB)</p>
                            @error('confirmation_image') <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <flux:input size="xs" wire:model="confirmation_reservation_date" type="date" label="Fecha de Reserva" required />
                            <flux:input size="xs" wire:model="confirmation_expiration_date" type="date" label="Fecha de Vencimiento" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <flux:input size="xs" wire:model="confirmation_reservation_amount" type="number" step="0.01" label="Monto de Reserva" required />
                            <flux:input size="xs" wire:model="confirmation_reservation_percentage" type="number" step="0.01" label="Porcentaje (%)" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <flux:select size="xs" wire:model="confirmation_payment_status" label="Estado de Pago" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="pagado">Pagado</option>
                                <option value="parcial">Parcial</option>
                            </flux:select>
                            <flux:input size="xs" wire:model="confirmation_payment_method" label="Método de Pago" />
                        </div>

                        <flux:input size="xs" wire:model="confirmation_payment_reference" label="Referencia de Pago" />

                        <div class="bg-yellow-50 border border-yellow-200 rounded p-2 mt-3">
                            <p class="text-xs text-yellow-800">
                                <strong>Nota:</strong> Al guardar, la reserva cambiará automáticamente a estado "Confirmada" y la unidad se marcará como "Reservada".
                            </p>
                        </div>

                        <div class="flex justify-end space-x-2 pt-3 border-t">
                            <flux:button size="xs" type="button" variant="primary" color="zinc" wire:click="closeModals">Cancelar</flux:button>
                            <flux:button size="xs" type="submit" variant="primary" color="green">Confirmar y Guardar</flux:button>
                        </div>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>

