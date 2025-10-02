<div class="max-w-4xl mx-auto p-4">
    <div class="bg-white border border-gray-200 rounded-lg">
        <!-- Header Minimalista -->
        <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                    <flux:icon name="user-plus" class="w-5 h-5 text-gray-600" />
                    Registro de Clientes
                </h1>
                <p class="text-gray-600 mt-1 text-sm">
                    Complete el formulario para registrar un nuevo cliente
                </p>
            </div>
            <div class="flex gap-2 mt-4">
                <flux:button size="sm" icon="list-bullet" href="{{ route('clients.index') }}" variant="outline">
                    Lista
                </flux:button>
                <flux:button size="sm" icon="qr-code" wire:click="verQR" variant="outline">
                    QR
                </flux:button>
            </div>
        </div>

        <!-- Mensajes -->
        @if ($showSuccessMessage)
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mx-6 mt-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <flux:icon name="check-circle" class="w-5 h-5 text-green-500 mr-3" />
                        <div>
                            <h4 class="text-sm font-medium text-green-800">Éxito</h4>
                            <p class="text-sm text-green-600 mt-1">{{ $successMessage }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeMessages" class="text-green-500 hover:text-green-700">
                        <flux:icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        @endif

        @if ($showErrorMessage)
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mx-6 mt-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <flux:icon name="exclamation-triangle" class="w-5 h-5 text-red-500 mr-3" />
                        <div>
                            <h4 class="text-sm font-medium text-red-800">Error</h4>
                            <p class="text-sm text-red-600 mt-1">{{ $errorMessage }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeMessages" class="text-red-500 hover:text-red-700">
                        <flux:icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        @endif

        <!-- Formulario -->
        <div class="p-6">
            <form wire:submit.prevent="save" class="space-y-6">
                <!-- Información Personal -->
                <div class="space-y-4">
                    <h2 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                        Información Personal
                    </h2>

                    <div class="grid grid-cols-1 gap-4">
                        <!-- Documento -->
                        <div class="flex gap-2">
                            <flux:input.group class="flex items-end w-full">
                                <flux:select wire:model.live="document_type" class="w-32" label="Tipo">
                                    @foreach ($documentTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:input class="flex-1" label="Número de documento" placeholder="Número de documento"
                                    wire:model.live="document_number" />
                                @if ($document_type == 'DNI')
                                    <flux:button icon="magnifying-glass" wire:click="buscarDocumento" variant="outline"
                                        label="Buscar" />
                                @endif
                            </flux:input.group>
                        </div>

                        <!-- Nombre -->
                        <flux:input label="Nombre Completo" wire:model.live="name"
                            placeholder="Nombre completo del cliente" />

                        <!-- Teléfono -->
                        <flux:input label="Teléfono" wire:model="phone" placeholder="Ej: 999999999" />

                        <!-- Fecha de Nacimiento -->
                        <flux:input label="Fecha de Nacimiento" type="date" wire:model.live="birth_date" />

                        <!-- Dirección -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                            <textarea wire:model="address" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror"
                                placeholder="Dirección completa"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Información Comercial -->
                <div class="space-y-4">
                    <h2 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                        Información Comercial
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tipo de Cliente -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Cliente</label>
                            <select wire:model="client_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('client_type') border-red-500 @enderror">
                                @foreach ($clientTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fuente -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fuente</label>
                            <select wire:model="source"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('source') border-red-500 @enderror">
                                @foreach ($sources as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select wire:model="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Score -->
                        <flux:input label="Score (0-100)" type="number" wire:model="score" min="0"
                            max="100" />
                    </div>

                    <!-- Notas -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <textarea wire:model="notes" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"
                            placeholder="Información adicional sobre el cliente"></textarea>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <flux:button type="button" variant="outline" wire:click="resetForm">
                        Limpiar
                    </flux:button>
                    <flux:button type="submit" color="primary">
                        Registrar Cliente
                    </flux:button>
                </div>
            </form>
        </div>

        <!-- Modal QR -->
        <flux:modal wire:model="showQRModal" class="w-full max-w-sm">
            <div class="p-6 text-center">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center justify-center gap-2">
                    <flux:icon name="qr-code" class="w-5 h-5 text-gray-600" />
                    Mi Código QR
                </h2>
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    {!! $qrcode !!}
                </div>
                <flux:button type="button" variant="outline" class="mt-4 w-full" wire:click="closeQRModal">
                    Cerrar
                </flux:button>
            </div>
        </flux:modal>
    </div>
</div>
