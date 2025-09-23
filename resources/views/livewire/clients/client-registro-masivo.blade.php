<div class="max-w-4xl mx-auto p-4 sm:p-6">
    <div class="bg-white rounded-lg shadow-lg">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 sm:p-6 rounded-t-lg">
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold">Registro de Clientes</h1>
                    <p class="text-blue-100 mt-1 sm:mt-2 text-sm sm:text-base">Complete el formulario para registrar un
                        nuevo cliente en el sistema
                    </p>
                    <flux:button size="xs" icon="list-bullet" href="{{ route('clients.index') }}">
                        Lista de Clientes
                    </flux:button>
                    @php
                        $qrcode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->generate('www.nigmacode.com');
                    @endphp
                    {{ $qrcode }}
                </div>
            </div>
        </div>

        <!-- Mensajes de éxito y error -->
        @if ($showSuccessMessage)
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 sm:p-4 mx-2 sm:mx-6 mt-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <div class="flex items-center">
                        <flux:icon name="check-circle" class="w-5 h-5 text-green-500 mr-3" />
                        <div>
                            <h4 class="text-sm font-medium text-green-800">Éxito</h4>
                            <p class="text-sm text-green-600 mt-1">{{ $successMessage }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeMessages"
                        class="text-green-500 hover:text-green-700 mt-2 sm:mt-0">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if ($showErrorMessage)
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 sm:p-4 mx-2 sm:mx-6 mt-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <div class="flex items-center">
                        <flux:icon name="exclamation-triangle" class="w-5 h-5 text-red-500 mr-3" />
                        <div>
                            <h4 class="text-sm font-medium text-red-800">Error</h4>
                            <p class="text-sm text-red-600 mt-1">{{ $errorMessage }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeMessages"
                        class="text-green-500 hover:text-green-700 mt-2 sm:mt-0">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Formulario -->
        <div class="p-4 sm:p-6">
            <form wire:submit.prevent="save" class="space-y-8">
                <!-- Información Personal -->
                <div class="space-y-6">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900">Información Personal</h2>

                    <div class="flex flex-col gap-4">
                        <flux:input.group>
                            <flux:select class="w-full" wire:model.live="document_type">
                                @foreach ($documentTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:input class="w-full" placeholder="Ingrese el número de documento" wire:model="document_number"/>
                            @if ($document_type=='DNI')
                                <flux:button icon="plus" wire:click="buscarDocumento"></flux:button>
                            @endif
                        </flux:input.group>
                        
                        <!-- Nombre -->
                        <div>
                            <flux:input label="Nombre Completo" wire:model="name"
                                placeholder="Ingrese el nombre completo" class="w-full" />
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <flux:input label="Teléfono" wire:model="phone" placeholder="Ej: +51 999 999 999" class="w-full" />
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div>
                            <flux:input label="Fecha de Nacimiento" type="date" wire:model="birth_date" class="w-full" />
                        </div>

                        <!-- Dirección -->
                        <div>
                            <textarea id="address" wire:model="address" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror"
                                placeholder="Ingrese la dirección completa"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Información Comercial -->
                <div class="space-y-6">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900">Información Comercial</h2>

                    <div class="flex flex-col gap-4">
                        <!-- Tipo de Cliente -->
                        <div>
                            <select label="Tipo de Cliente" id="client_type" wire:model="client_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('client_type') border-red-500 @enderror">
                                @foreach ($clientTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fuente -->
                        <div>
                            <select label="Fuente" id="source" wire:model="source"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('source') border-red-500 @enderror">
                                @foreach ($sources as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estado -->
                        <div>
                            <select label="Estado" id="status" wire:model="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Score -->
                        <div>
                            <flux:input label="Score (0-100)" type="number" wire:model="score" min="0"
                                max="100" class="w-full" />
                        </div>

                        <!-- Notas -->
                        <div>
                            <textarea label="Notas Adicionales" id="notes" wire:model="notes" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"
                                placeholder="Ingrese cualquier información adicional sobre el cliente"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4 pt-4 sm:pt-6">
                    <flux:button icon="x-mark" type="button" variant="outline" wire:click="resetForm">
                        Limpiar Formulario
                    </flux:button>

                    <flux:button icon="plus" type="submit" color="primary">
                        Registrar Cliente
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
