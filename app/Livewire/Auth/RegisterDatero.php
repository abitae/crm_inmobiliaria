<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('components.layouts.auth.mobile')]
class RegisterDatero extends Component
{
    use Toast;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $banco = '';
    public string $cuenta_bancaria = '';
    public string $cci_bancaria = '';
    public function render()
    {
        return view('livewire.auth.register-datero');
    }
    public function register(): void
    {
        try {
            $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/', 'confirmed'],
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

        // PIN de 6 dígitos: se guarda en password y pin; pin siempre con Hash::make
        $validated['pin'] = Hash::make($validated['password']);
        unset($validated['password_confirmation']);
        $validated['lider_id'] = Auth::id();

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);
        $user->assignRole('datero');
        $this->redirect(route('dashboard', absolute: true));
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->reset([
                'name', 'email', 'phone', 'password', 'password_confirmation',
                'banco', 'cuenta_bancaria', 'cci_bancaria'
            ]);
            $this->error(__('Error'), $e->getMessage(), 'toast-top toast-center');
        }
    }
}
