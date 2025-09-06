<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?int $lider_id = null;

    /**
     * Handle an incoming registration request.
     */
    public $leaders = [];

    public function mount()
    {
        $this->leaders = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['lider', 'admin']);
        })->get();
        
    }

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'lider_id' => ['nullable', 'exists:users,id'],
        ],[
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'email.required' => 'El email es requerido',
            'email.string' => 'El email debe ser una cadena de texto',
            'email.email' => 'El email debe ser una dirección de email válida',
            'email.max' => 'El email debe tener menos de 255 caracteres',
            'email.unique' => 'El email ya está registrado',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser una cadena de texto',
            'password.confirmed' => 'La contraseña y la confirmación de la contraseña no coinciden',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.max' => 'La contraseña debe tener menos de 255 caracteres',
            'password_confirmation.required' => 'La confirmación de la contraseña es requerida',
            'password_confirmation.string' => 'La confirmación de la contraseña debe ser una cadena de texto',
            'password_confirmation.confirmed' => 'La confirmación de la contraseña y la contraseña no coinciden',
            'password_confirmation.min' => 'La confirmación de la contraseña debe tener al menos 8 caracteres',
            'password_confirmation.max' => 'La confirmación de la contraseña debe tener menos de 255 caracteres',
            'phone.unique' => 'El teléfono ya está registrado',
            'phone.required' => 'El teléfono es requerido',
            'phone.string' => 'El teléfono debe ser una cadena de texto',
            'phone.max' => 'El teléfono debe tener menos de 255 caracteres',
            'lider_id.exists' => 'El líder no existe',
            'lider_id.nullable' => 'El líder es opcional',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);
        $user->assignRole('vendedor');
        $this->redirect(route('welcome', absolute: true));
    }
}
