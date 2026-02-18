<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Password extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/', 'confirmed'],
            ], [
                'password.required' => 'El PIN es obligatorio.',
                'password.size' => 'El PIN debe tener exactamente 6 dígitos.',
                'password.regex' => 'El PIN debe contener solo números.',
                'password.confirmed' => 'La confirmación del PIN no coincide.',
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        $user = Auth::user();
        if ($user) {
            $newPin = $validated['password'];
            $user->update([
                'password' => Hash::make($newPin),
                'pin' => $newPin, // el cast 'hashed' del modelo lo hasheará
            ]);
        }

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}
