<div class="flex flex-col p-6 border-2 border-gray-500 gap-6 rounded-xl">
    <x-auth-header :title="__('REGISTRO CAZADOR')" :description="__('Registrate como cazador para gestionar tus clientes')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
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
        <flux:input size="xs" wire:model="banco" :label="__('Banco')" type="text" required autocomplete="banco"
            placeholder="Banco" />

        <!-- Cuenta bancaria -->
        <flux:input size="xs" wire:model="cuenta_bancaria" :label="__('Cuenta bancaria')" type="text" required autocomplete="cuenta_bancaria"
            placeholder="Cuenta bancaria" />

        <!-- CCI bancaria -->
        <flux:input size="xs" wire:model="cci_bancaria" :label="__('CCI bancaria')" type="text" required autocomplete="cci_bancaria"
            placeholder="CCI bancaria" />

        <!-- Password -->
        <flux:input size="xs" wire:model="password" :label="__('Contraseña')" type="password" required autocomplete="new-password"
            :placeholder="__('Contraseña')" viewable />

        <!-- Confirm Password -->
        <flux:input size="xs" wire:model="password_confirmation" :label="__('Confirmar contraseña')" type="password" required
            autocomplete="new-password" :placeholder="__('Confirm password')" viewable />

        <!-- Líder -->
        <flux:select size="xs" wire:model="lider_id" :label="__('Líder')">
            @foreach ($leaders as $leader)
                <option value="{{ $leader->id }}">{{ $leader->name }}</option>
            @endforeach
        </flux:select>

        <div class="flex items-center justify-end">
            <flux:button size="xs" type="submit" variant="primary" class="w-full">
                {{ __('Crear cuenta') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Ya tienes una cuenta?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Iniciar sesión') }}</flux:link>
    </div>
</div>
