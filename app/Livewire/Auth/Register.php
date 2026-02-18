<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
    public string $banco = '';
    public string $cuenta_bancaria = '';
    public string $cci_bancaria = '';

    /**
     * Handle an incoming registration request.
     */
    public $leaders = [];

    public function mount()
    {
        $this->leaders = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['lider', 'admin']);
        })->get();
        $this->lider_id = $this->leaders->first()->id;
    }

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/', 'confirmed'],
            'lider_id' => ['nullable', 'exists:users,id'],
            'banco' => ['nullable', 'string', 'max:255'],
            'cuenta_bancaria' => ['nullable', 'string', 'max:255'],
            'cci_bancaria' => ['nullable', 'string', 'max:255'],
        ],[
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'email.required' => 'El email es requerido',
            'email.string' => 'El email debe ser una cadena de texto',
            'email.email' => 'El email debe ser una dirección de email válida',
            'email.max' => 'El email debe tener menos de 255 caracteres',
            'email.unique' => 'El email ya está registrado',
            'password.required' => 'El PIN es requerido',
            'password.string' => 'El PIN debe ser una cadena de texto',
            'password.size' => 'El PIN debe tener exactamente 6 dígitos',
            'password.regex' => 'El PIN debe contener solo números',
            'password.confirmed' => 'El PIN y la confirmación no coinciden',
            'password_confirmation.required' => 'La confirmación del PIN es requerida',
            'password_confirmation.string' => 'La confirmación del PIN debe ser una cadena de texto',
            'password_confirmation.confirmed' => 'La confirmación del PIN no coincide',
            'password_confirmation.size' => 'El PIN debe tener exactamente 6 dígitos',
            'phone.unique' => 'El teléfono ya está registrado',
            'phone.required' => 'El teléfono es requerido',
            'phone.string' => 'El teléfono debe ser una cadena de texto',
            'phone.max' => 'El teléfono debe tener menos de 255 caracteres',
            'lider_id.exists' => 'El líder no existe',
            'lider_id.nullable' => 'El líder es opcional',
            'banco.required' => 'El banco es requerido',
            'banco.string' => 'El banco debe ser una cadena de texto',
            'banco.max' => 'El banco debe tener menos de 255 caracteres',
            'cuenta_bancaria.required' => 'La cuenta bancaria es requerida',
            'cuenta_bancaria.string' => 'La cuenta bancaria debe ser una cadena de texto',
            'cuenta_bancaria.max' => 'La cuenta bancaria debe tener menos de 255 caracteres',
            'cci_bancaria.required' => 'La CCI bancaria es requerida',
            'cci_bancaria.string' => 'La CCI bancaria debe ser una cadena de texto',
            'cci_bancaria.max' => 'La CCI bancaria debe tener menos de 255 caracteres',
        ]);

        // PIN de 6 dígitos: se guarda en password y pin (el modelo hashea con el cast)
        $pin = $validated['password'];
        $validated['pin'] = $pin;
        unset($validated['password_confirmation']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);
        $user->assignRole('vendedor');
        $this->redirect(route('dashboard', absolute: true));
    }
}
