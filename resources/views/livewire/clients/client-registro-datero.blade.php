<div class="flex flex-col p-3 sm:p-4 border-2 border-gray-300 gap-3 sm:gap-4 rounded-xl bg-white shadow-sm">
    <x-auth-header
        title="{{ __('Registro Cliente') }}"
        :description="__('Completa el formulario para registrar un nuevo cliente.')"
    />

    <div class="flex flex-wrap justify-center gap-1.5 sm:gap-2">
        <flux:button size="xs" icon="qr-code" wire:click="verQR" variant="outline">
            {{ __('QR') }}
        </flux:button>
    </div>

    <form wire:submit.prevent="save" class="flex flex-col gap-3 sm:gap-4">
        <div class="flex flex-col gap-2 sm:gap-3">
            <flux:subheading class="text-sm">{{ __('Información Personal') }}</flux:subheading>

            <div class="flex flex-col gap-2 sm:gap-3">
                <div>
                    <flux:radio.group wire:model.live="create_mode" label="{{ __('Modo de alta') }}">
                        <flux:radio value="dni" label="{{ __('Por DNI') }}" />
                        <flux:radio value="phone" label="{{ __('Por teléfono') }}" />
                    </flux:radio.group>
                    <p class="mt-0.5 text-[11px] sm:text-xs text-gray-500">
                        @if ($create_mode === 'dni')
                            {{ __('Por DNI: use el botón Buscar para rellenar nombre y fecha de nacimiento.') }}
                        @else
                            {{ __('Por teléfono: ingrese directamente el nombre y el resto de datos.') }}
                        @endif
                    </p>
                </div>

                @if ($create_mode === 'dni')
                    <flux:input.group class="flex w-full items-end gap-1.5 sm:gap-2">
                        <flux:select wire:model.live="document_type" label="{{ __('Tipo') }}" size="xs" class="w-full">
                            @foreach ($documentTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                        <flux:input mask="99999999" class="flex-1" label="{{ __('Documento') }}"
                            placeholder="{{ __('Número de documento') }}" wire:model.live="document_number"
                            size="xs" inputmode="numeric" autocomplete="off" />
                        @if ($document_type == 'DNI')
                            <flux:button icon="magnifying-glass" wire:click="buscarDocumento" variant="outline"
                                size="xs" class="self-end">{{ __('Buscar') }}</flux:button>
                        @endif
                    </flux:input.group>
                @endif

                <flux:input size="xs" label="{{ __('Nombre completo (Cliente)') }}" wire:model.live="name"
                    placeholder="{{ __('Nombre completo del cliente') }}" required />

                <flux:input size="xs" mask="999999999" label="{{ __('Teléfono') }}" wire:model="phone"
                    placeholder="Ej: 999999999" inputmode="tel" autocomplete="tel" />

                <flux:input size="xs" label="{{ __('Ocupación') }}" wire:model="ocupacion"
                    placeholder="{{ __('Ingrese la ocupación') }}" />

                <flux:input size="xs" label="{{ __('Fecha de Nacimiento') }}" type="date"
                    wire:model.live="birth_date" />

                <div>
                    <flux:label class="text-xs">{{ __('Dirección') }}</flux:label>
                    <flux:textarea wire:model="address" rows="2" placeholder="{{ __('Ingrese la dirección') }}"
                        size="xs" class="mt-0.5" />
                </div>

                <flux:select size="xs" label="{{ __('Ciudad') }}" wire:model="city_id" class="w-full">
                    <option value="">{{ __('Sin ciudad') }}</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <div class="flex flex-col gap-2 sm:gap-3">
            <flux:subheading class="text-sm">{{ __('Información Comercial') }}</flux:subheading>

            <div class="grid grid-cols-1 gap-2 sm:gap-3 sm:grid-cols-2">
                <div>
                    <flux:label class="mb-0.5 text-xs">{{ __('Tipo de Cliente') }}</flux:label>
                    <flux:select wire:model="client_type" size="xs" class="w-full">
                        @foreach ($clientTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:label class="mb-0.5 text-xs">{{ __('Fuente') }}</flux:label>
                    <flux:select wire:model="source" size="xs" class="w-full">
                        @foreach ($sources as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:label class="mb-0.5 text-xs">{{ __('Estado') }}</flux:label>
                    <flux:select wire:model="status" size="xs" class="w-full">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <flux:input size="xs" label="{{ __('Score (0-100)') }}" type="number" wire:model="score"
                    min="0" max="100" />
            </div>

            <div>
                <flux:label class="mb-0.5 text-xs">{{ __('Notas') }}</flux:label>
                <flux:textarea wire:model="notes" rows="2"
                    placeholder="{{ __('Información adicional sobre el cliente') }}" size="xs"
                    class="mt-0.5" />
            </div>
        </div>

        <div class="flex flex-col gap-1.5 sm:flex-row sm:justify-end sm:gap-2 pt-1">
            <flux:button size="xs" icon="list-bullet" href="{{ route('clients.index-datero') }}" variant="outline"
                wire:navigate class="w-full sm:w-auto">
                {{ __('Lista') }}
            </flux:button>
            <flux:button icon="x-mark" type="button" variant="outline" wire:click="resetForm" size="xs"
                class="w-full sm:w-auto">
                {{ __('Limpiar') }}
            </flux:button>
            <flux:button icon="plus" type="submit" variant="primary" size="xs" class="w-full sm:w-auto">
                {{ __('Datear Cliente') }}
            </flux:button>
        </div>
    </form>

    <flux:modal wire:model="showQRModal" class="w-full max-w-sm">
        <div class="p-3 sm:p-4 text-center">
            <h2 class="mb-2 sm:mb-3 flex items-center justify-center gap-2 text-sm sm:text-base font-medium">
                <flux:icon name="qr-code" class="h-4 w-4" />
                {{ __('Mi Código QR Personal') }}
            </h2>
            <div class="flex items-center justify-center rounded-lg border border-gray-200 bg-gray-100 p-3">
                {!! $qrcode !!}
            </div>
            <p class="mt-2 text-center text-xs text-gray-500">
                {{ __('Escanea este código QR para acceder rápidamente a tu perfil.') }}
            </p>
            <flux:button icon="x-mark" type="button" variant="outline" class="mt-3 w-full" wire:click="closeQRModal" size="xs">
                {{ __('Cerrar') }}
            </flux:button>
        </div>
    </flux:modal>
</div>
