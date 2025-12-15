<div class="max-w-4xl mx-auto p-2 sm:p-3">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <!-- Header Compacto Verde - Mobile Optimized -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 text-white px-3 sm:px-4 py-3 rounded-t-lg">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1">
                    <h1 class="text-base sm:text-lg font-semibold flex items-center gap-2">
                        <flux:icon name="user-plus" class="w-4 h-4 sm:w-5 sm:h-5" />
                        Registro Datero
                    </h1>
                    <p class="text-green-100 text-xs sm:text-sm mt-1">
                        Completa el formulario para registrar un nuevo cliente.
                    </p>
                </div>
                <div class="flex gap-2 justify-end sm:justify-start">
                    <flux:button size="xs" icon="qr-code" wire:click="verQR" variant="outline"
                        class="text-green-700 bg-white border-white hover:bg-green-50 text-xs px-2 py-1">
                        QR
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Mensajes Compactos - Mobile Optimized -->
        @if ($showSuccessMessage)
            <div class="bg-green-50 border-l-4 border-green-400 p-2 sm:p-3 mx-2 sm:mx-4 mt-2 sm:mt-3">
                <div class="flex items-start sm:items-center justify-between gap-2">
                    <div class="flex items-start sm:items-center flex-1 min-w-0">
                        <flux:icon name="check-circle"
                            class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5 sm:mt-0" />
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-medium text-green-800">Éxito</h4>
                            <p class="text-xs text-green-600 mt-1 break-words">{{ $successMessage }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeMessages"
                        class="text-green-500 hover:text-green-700 flex-shrink-0 p-1">
                        <flux:icon name="x-mark" class="w-3 h-3" />
                    </button>
                </div>
            </div>
        @endif

        @if ($showErrorMessage)
            <div class="bg-red-50 border-l-4 border-red-400 p-2 sm:p-3 mx-2 sm:mx-4 mt-2 sm:mt-3">
                <div class="flex items-start sm:items-center justify-between gap-2">
                    <div class="flex items-start sm:items-center flex-1 min-w-0">
                        <flux:icon name="exclamation-triangle"
                            class="w-4 h-4 text-red-500 mr-2 flex-shrink-0 mt-0.5 sm:mt-0" />
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-medium text-red-800">Error</h4>
                            <p class="text-xs text-red-600 mt-1 break-words">{{ $errorMessage }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeMessages"
                        class="text-red-500 hover:text-red-700 flex-shrink-0 p-1">
                        <flux:icon name="x-mark" class="w-3 h-3" />
                    </button>
                </div>
            </div>
        @endif

        <!-- Formulario Compacto - Mobile Optimized -->
        <div class="p-3 sm:p-4 pb-4 sm:pb-6">
            <form wire:submit.prevent="save" class="space-y-3 sm:space-y-4">
                <!-- Información Personal -->
                <div class="space-y-3">
                    <h2 class="text-sm sm:text-base font-medium text-gray-900 border-b border-gray-200 pb-1">
                        Información Personal
                    </h2>

                    <div class="grid grid-cols-1 gap-3">
                        <!-- Documento - Mobile Stack -->
                        <div class="space-y-2 sm:space-y-0 sm:flex sm:gap-2">
                            <div class="w-full">
                                <flux:input.group class="flex items-end w-full">
                                    <flux:select wire:model.live="document_type" label="Tipo" size="xs"
                                        class="w-full">
                                        <option value="DNI">DNI</option>
                                    </flux:select>
                                    <flux:input
                                        mask="99999999"
                                        class="flex-1"
                                        label="Documento"
                                        placeholder="Número de documento"
                                        wire:model="document_number"
                                        size="xs"
                                        inputmode="numeric"
                                        autocomplete="off"
                                    />
                                    @if ($document_type == 'DNI')
                                        <flux:button icon="magnifying-glass" wire:click="buscarDocumento"
                                            variant="outline" label="Buscar" size="xs"
                                            class="self-end min-h-[30px] px-2" />
                                    @endif
                                </flux:input.group>
                            </div>
                        </div>

                        <!-- Nombre -->
                        <flux:input label="Nombre Completo" wire:model="name" disabled
                            placeholder="Ingrese el nombre completo" size="xs" />

                        <!-- Teléfono -->
                        <flux:input
                            mask="999999999"
                            label="Teléfono"
                            wire:model="phone"
                            placeholder="Ej: 999 999 999"
                            size="xs"
                            inputmode="tel"
                            autocomplete="tel"
                        />

                        <!-- Fecha de Nacimiento -->
                        <flux:input label="Fecha de Nacimiento" type="date" wire:model="birth_date" disabled
                            size="xs" />

                        <!-- Dirección -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Dirección</label>
                            <textarea id="address" wire:model="address" rows="3"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('address') border-red-500 @enderror resize-none"
                                placeholder="Ingrese la dirección"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Información Comercial -->
                <div class="space-y-3">
                    <h2 class="text-sm sm:text-base font-medium text-gray-900 border-b border-gray-200 pb-1">
                        Información Comercial
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Tipo de Cliente -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de Cliente</label>
                            <select id="client_type" wire:model="client_type"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('client_type') border-red-500 @enderror">
                                @foreach ($clientTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fuente -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Fuente</label>
                            <select id="source" wire:model="source"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('source') border-red-500 @enderror">
                                @foreach ($sources as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                            <select id="status" wire:model="status"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('status') border-red-500 @enderror">
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Score -->
                        <flux:input label="Score (0-100)" type="number" wire:model="score" min="0"
                            max="100" size="xs" />
                    </div>

                    <!-- Notas -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Notas</label>
                        <textarea id="notes" wire:model="notes" rows="3"
                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('notes') border-red-500 @enderror resize-none"
                            placeholder="Ingrese cualquier información adicional sobre el cliente"></textarea>
                    </div>
                </div>

                <!-- Botones Compactos - Mobile Optimized -->
                <div class="flex flex-col sm:flex-row justify-end gap-2 pt-3 border-t border-gray-200">
                    <flux:button icon="x-mark" type="button" variant="outline" wire:click="resetForm"
                        size="xs" class="w-full sm:w-auto order-2 sm:order-1">
                        Limpiar
                    </flux:button>
                    <flux:button icon="plus" type="submit" color="primary" size="xs"
                        class="w-full sm:w-auto order-1 sm:order-2">
                        Datear Cliente
                    </flux:button>
                </div>
            </form>
        </div>

        <!-- Modal QR Compacto - Mobile Optimized -->
        <flux:modal wire:model="showQRModal" class="w-full max-w-xs sm:max-w-sm">
            <div class="p-3 sm:p-4 text-center">
                <h2 class="text-sm sm:text-base font-medium text-gray-900 mb-3 flex items-center justify-center gap-2">
                    <flux:icon name="qr-code" class="w-4 h-4 text-green-600" />
                    Mi Código QR Personal
                </h2>
                <div class="bg-white p-2 sm:p-3 rounded-lg border border-gray-200 flex justify-center items-center">
                    {!! $qrcode !!}
                </div>
                <p class="text-xs text-gray-500 mt-2 text-center">
                    Escanea este código QR para acceder rápidamente a tu perfil.
                </p>
                <flux:button icon="x-mark" type="button" variant="outline" class="mt-3 w-full"
                    wire:click="closeQRModal" size="xs">
                    Cerrar
                </flux:button>
            </div>
        </flux:modal>
    </div>
</div>
