<div class="flex flex-col p-6 border-2 border-gray-500 gap-6 rounded-xl">
    <x-auth-header :title="__('Login')" :description="__('Inicia sesion para continuar')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input size="xs" wire:model="email" :label="__('Email')" type="email" required autofocus autocomplete="email"
            placeholder="email@example.com" />

        <!-- PIN (6 dígitos) - se usa como contraseña -->
        <div class="relative">
            <flux:input size="xs" wire:model="password" :label="__('PIN')" type="password" required
                autocomplete="current-password" placeholder="000000" maxlength="6" inputmode="numeric" viewable />
        </div>

        <!-- Remember Me -->
        <flux:checkbox size="xs" wire:model="remember" :label="__('Recuérdame')" />

        <div class="flex items-center justify-end">
            <flux:button size="xs" variant="primary" type="submit" class="w-full">{{ __('Iniciar sesión') }}</flux:button>
        </div>
    </form>


</div>
