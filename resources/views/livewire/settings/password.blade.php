<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Actualizar PIN')" :subheading="__('Tu contraseña es el PIN de 6 dígitos. Cámbialo aquí.')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            <flux:input
                wire:model="current_password"
                :label="__('PIN actual')"
                type="password"
                required
                autocomplete="current-password"
                placeholder="000000"
                maxlength="6"
                inputmode="numeric"
            />
            <flux:input
                wire:model="password"
                :label="__('Nuevo PIN')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="000000"
                maxlength="6"
                inputmode="numeric"
            />
            <flux:input
                wire:model="password_confirmation"
                :label="__('Confirmar PIN')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="000000"
                maxlength="6"
                inputmode="numeric"
            />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
