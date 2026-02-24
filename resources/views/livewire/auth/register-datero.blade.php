<div class="flex flex-col p-3 sm:p-4 border-2 border-gray-300 gap-3 sm:gap-4 rounded-xl bg-white shadow-sm">
    <x-auth-header :title="__('REGISTRO DATERO')" :description="__('Registra a tus dateros')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center text-sm" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-2 sm:gap-3">
        <!-- Name -->
        <flux:input size="xs" wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name"
            :placeholder="__('Nombres y Apellidos')" />

        <!-- Email Address -->
        <flux:input size="xs" wire:model="email" :label="__('Email')" type="email" required autocomplete="email"
            placeholder="email@gmail.com" />

        <!-- Phone -->
        <flux:input size="xs" wire:model="phone" :label="__('Teléfono')" type="text" required autocomplete="phone"
            placeholder="999999999" />

        <!-- Banco -->
        <flux:input size="xs" wire:model="banco" :label="__('Banco')" type="text" autocomplete="banco"
            placeholder="Banco" />

        <!-- Cuenta bancaria -->
        <flux:input size="xs" wire:model="cuenta_bancaria" :label="__('Cuenta bancaria')" type="text" autocomplete="cuenta_bancaria"
            placeholder="Cuenta bancaria" />

        <!-- CCI bancaria -->
        <flux:input size="xs" wire:model="cci_bancaria" :label="__('CCI bancaria')" type="text" autocomplete="cci_bancaria"
            placeholder="CCI bancaria" />

        <!-- PIN -->
        <flux:input size="xs" wire:model="password" :label="__('PIN (6 dígitos)')" type="password" required autocomplete="new-password"
            placeholder="6 dígitos" viewable />

        <!-- Confirmar PIN -->
        <flux:input size="xs" wire:model="password_confirmation" :label="__('Confirmar PIN')" type="password" required
            autocomplete="new-password" placeholder="6 dígitos" viewable />


        <div class="flex items-center justify-end pt-1">
            <flux:button type="submit" variant="primary" class="w-full" size="xs">
                {{ __('Crear cuenta') }}
            </flux:button>
        </div>
    </form>
</div>
