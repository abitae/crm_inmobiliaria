<div>
    @if($showModal && $opportunity)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Detalles de la Oportunidad</h3>
                    <p class="text-sm text-gray-600">Información completa de la oportunidad</p>
                </div>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <flux:icon name="x-mark" class="w-6 h-6" />
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Información Principal -->
                <div class="space-y-6">
                    <!-- Cliente -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Cliente</h4>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-blue-600">
                                    {{ strtoupper(substr($opportunity->client->name ?? 'C', 0, 1)) }}
                                </span>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $opportunity->client->name ?? '' }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $opportunity->client->email ?? '' }}</div>
                                <div class="text-xs text-gray-500">{{ $opportunity->client->phone ?? '' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Proyecto -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Proyecto</h4>
                        <div class="text-sm text-gray-900">{{ $opportunity->project->name ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">{{ $opportunity->project->type ?? '' }}</div>
                        <div class="text-xs text-gray-500">{{ $opportunity->project->location ?? '' }}</div>
                    </div>

                    <!-- Unidad (si existe) -->
                    @if($opportunity->unit)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Unidad</h4>
                        <div class="text-sm text-gray-900">{{ $opportunity->unit->name }}</div>
                        <div class="text-xs text-gray-500">{{ $opportunity->unit->type }} - {{ $opportunity->unit->status }}</div>
                        <div class="text-xs text-gray-500">S/ {{ number_format($opportunity->unit->price ?? 0) }}</div>
                    </div>
                    @endif

                    <!-- Asesor -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Asesor Asignado</h4>
                        <div class="text-sm text-gray-900">{{ $opportunity->advisor->name ?? 'Sin asignar' }}</div>
                        <div class="text-xs text-gray-500">{{ $opportunity->advisor->email ?? '' }}</div>
                    </div>
                </div>

                <!-- Información de la Oportunidad -->
                <div class="space-y-6">
                    <!-- Estado y Etapa -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Estado y Etapa</h4>
                        <div class="flex space-x-2 mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $opportunity->status === 'ganada' ? 'bg-green-100 text-green-800' : 
                                   ($opportunity->status === 'perdida' ? 'bg-red-100 text-red-800' : 
                                   ($opportunity->status === 'cancelada' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800')) }}">
                                {{ ucfirst($opportunity->status) }}
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ ucfirst($opportunity->stage) }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500">Probabilidad: {{ $opportunity->probability }}%</div>
                    </div>

                    <!-- Valor y Fechas -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Valor y Fechas</h4>
                        <div class="text-sm text-gray-900">Valor Esperado: S/ {{ number_format($opportunity->expected_value) }}</div>
                        @if($opportunity->close_value)
                            <div class="text-sm text-gray-900">Valor de Cierre: S/ {{ number_format($opportunity->close_value) }}</div>
                        @endif
                        <div class="text-xs text-gray-500">Fecha de Cierre Esperada: {{ $opportunity->expected_close_date ? \Carbon\Carbon::parse($opportunity->expected_close_date)->format('d/m/Y') : 'Sin fecha' }}</div>
                        @if($opportunity->actual_close_date)
                            <div class="text-xs text-gray-500">Fecha de Cierre Real: {{ \Carbon\Carbon::parse($opportunity->actual_close_date)->format('d/m/Y') }}</div>
                        @endif
                    </div>

                    <!-- Origen y Campaña -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Origen</h4>
                        <div class="text-sm text-gray-900">{{ ucfirst($opportunity->source ?? 'No especificado') }}</div>
                        @if($opportunity->campaign)
                            <div class="text-xs text-gray-500">Campaña: {{ $opportunity->campaign }}</div>
                        @endif
                    </div>

                    <!-- Fechas de Creación -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Fechas</h4>
                        <div class="text-xs text-gray-500">Creada: {{ $opportunity->created_at->format('d/m/Y H:i') }}</div>
                        <div class="text-xs text-gray-500">Última actualización: {{ $opportunity->updated_at->format('d/m/Y H:i') }}</div>
                        @if($opportunity->createdBy)
                            <div class="text-xs text-gray-500">Creada por: {{ $opportunity->createdBy->name }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notas -->
            @if($opportunity->notes)
            <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Notas</h4>
                <div class="text-sm text-gray-900">{{ $opportunity->notes }}</div>
            </div>
            @endif

            <!-- Razones de Cierre/Pérdida -->
            @if($opportunity->close_reason || $opportunity->lost_reason)
            <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-700 mb-2">
                    {{ $opportunity->status === 'ganada' ? 'Razón del Cierre' : 'Razón de la Pérdida' }}
                </h4>
                <div class="text-sm text-gray-900">
                    {{ $opportunity->close_reason ?: $opportunity->lost_reason }}
                </div>
            </div>
            @endif

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <flux:button icon="x-mark" size="sm" variant="outline" wire:click="closeModal">
                    Cerrar
                </flux:button>
                <flux:button icon="pencil" size="sm" color="primary" wire:click="$dispatch('edit-opportunity', { id: {{ $opportunity->id }} })">
                    Editar
                </flux:button>
            </div>
        </div>
    </div>
    @endif
</div>
