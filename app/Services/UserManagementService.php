<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserManagementService
{
    /**
     * Obtiene usuarios paginados con filtros específicos por roles
     */
    public function getUsersWithRoles(array $allowedRoles, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = User::with(['roles', 'lider'])
            ->whereHas('roles', function ($query) use ($allowedRoles) {
                $query->whereIn('name', $allowedRoles);
            });

        // Aplicar filtros de búsqueda
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm)
                    ->orWhere('dni', 'like', $searchTerm);
            });
        }

        // Aplicar filtro de estado
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('is_active', $filters['status'] === 'active');
        }

        // Aplicar filtro de rol
        if (!empty($filters['role']) && $filters['role'] !== 'all') {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        // Aplicar filtro de líder
        if (!empty($filters['leader']) && $filters['leader'] !== 'all') {
            if ($filters['leader'] === 'no_leader') {
                $query->whereNull('lider_id');
            } else {
                $query->where('lider_id', $filters['leader']);
            }
        }

        return $query->orderBy('name')->paginate(10);
    }

    /**
     * Obtiene roles disponibles
     */
    public function getRoles(array $allowedRoles = []): \Illuminate\Database\Eloquent\Collection
    {
        if (empty($allowedRoles)) {
            return Role::orderBy('name')->get();
        }

        return Role::whereIn('name', $allowedRoles)->orderBy('name')->get();
    }

    /**
     * Obtiene líderes disponibles
     */
    public function getLeaders(): \Illuminate\Database\Eloquent\Collection
    {
        return User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'lider']);
        })->select('id', 'name', 'email')->get();
    }

    /**
     * Valida y obtiene un usuario por ID
     */
    public function findUser(int $userId): ?User
    {
        return User::with('roles')->find($userId);
    }

    /**
     * Valida el estado del usuario para activación/desactivación
     */
    public function validateUserStatus(int $userId, bool $shouldBeActive): array
    {
        $user = $this->findUser($userId);

        if (!$user) {
            return ['success' => false, 'message' => 'Usuario no encontrado.'];
        }

        if ($shouldBeActive && $user->isActive()) {
            return ['success' => false, 'message' => 'El usuario ya está activo.'];
        }

        if (!$shouldBeActive && $user->isInactive()) {
            return ['success' => false, 'message' => 'El usuario ya está inactivo.'];
        }

        if (!$shouldBeActive && Auth::check() && $user->id === Auth::id()) {
            return ['success' => false, 'message' => 'No puedes desactivar tu propia cuenta.'];
        }

        return ['success' => true, 'user' => $user];
    }

    /**
     * Activa un usuario
     */
    public function activateUser(int $userId): array
    {
        $validation = $this->validateUserStatus($userId, true);
        
        if (!$validation['success']) {
            return $validation;
        }

        try {
            $validation['user']->activate();
            return [
                'success' => true, 
                'message' => "Usuario {$validation['user']->name} activado correctamente."
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error al activar el usuario.'];
        }
    }

    /**
     * Desactiva un usuario
     */
    public function desactivateUser(int $userId): array
    {
        $validation = $this->validateUserStatus($userId, false);
        
        if (!$validation['success']) {
            return $validation;
        }

        try {
            $validation['user']->deactivate();
            return [
                'success' => true, 
                'message' => "Usuario {$validation['user']->name} desactivado correctamente."
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error al desactivar el usuario.'];
        }
    }

    /**
     * Crea un nuevo usuario
     */
    public function createUser(array $userData): array
    {
        try {
            $password = $userData['password'];
            $pin = $userData['pin'] ?? $password; // Si no se proporciona pin, usar password
            
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'] ?: null,
                'dni' => $userData['dni'] ?? null,
                'lider_id' => $userData['lider_id'],
                'city_id' => $userData['city_id'] ?? null,
                'password' => bcrypt($password),
                'pin' => $pin, // El cast 'hashed' del modelo hasheará automáticamente
                'is_active' => true,
                'banco' => $userData['banco'] ?? '',
                'cuenta_bancaria' => $userData['cuenta_bancaria'] ?? '',
                'cci_bancaria' => $userData['cci_bancaria'] ?? '',
            ]);

            $user->setRole($userData['selectedRole']);

            return [
                'success' => true, 
                'message' => 'Usuario creado correctamente.',
                'user' => $user
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error al crear el usuario: ' . $e->getMessage()];
        }
    }

    /**
     * Actualiza un usuario existente
     */
    public function updateUser(User $user, array $userData): array
    {
        try {
            $updateData = [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'] ?: null,
                'lider_id' => $userData['lider_id'],
                'city_id' => $userData['city_id'] ?? null,
                'banco' => $userData['banco'] ?? '',
                'cuenta_bancaria' => $userData['cuenta_bancaria'] ?? '',
                'cci_bancaria' => $userData['cci_bancaria'] ?? '',
            ];

            // Actualizar DNI si se proporciona
            if (isset($userData['dni'])) {
                $updateData['dni'] = $userData['dni'];
            }

            // Si se proporciona password, actualizar también password y PIN
            if (isset($userData['password']) && !empty($userData['password'])) {
                $password = $userData['password'];
                $updateData['password'] = bcrypt($password);
                // Sincronizar PIN con password (el cast 'hashed' hasheará automáticamente)
                $updateData['pin'] = $password;
            }

            $user->update($updateData);

            $user->setRole($userData['selectedRole']);

            return [
                'success' => true, 
                'message' => 'Usuario actualizado correctamente.',
                'user' => $user
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error al actualizar el usuario: ' . $e->getMessage()];
        }
    }

    /**
     * Valida los datos del formulario de usuario
     */
    public function validateUserForm(array $data, bool $isCreating = false, ?User $existingUser = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'lider_id' => 'nullable|exists:users,id',
            'city_id' => 'nullable|exists:cities,id',
            'selectedRole' => 'required|exists:roles,name',
            'banco' => 'nullable|string|max:255',
            'cuenta_bancaria' => 'nullable|string|max:255',
            'cci_bancaria' => 'nullable|string|max:255',
        ];

        // Validación de DNI - debe tener exactamente 8 dígitos numéricos
        if ($isCreating) {
            $rules['dni'] = 'required|string|size:8|regex:/^[0-9]{8}$/|unique:users,dni';
        } else {
            $rules['dni'] = 'required|string|size:8|regex:/^[0-9]{8}$/|unique:users,dni,' . $existingUser->id;
        }

        if ($isCreating) {
            $rules['email'] = 'required|email|max:255|unique:users,email';
            // Para dateros, la contraseña debe ser de 6 dígitos numéricos
            $rules['password'] = 'required|string|size:6|regex:/^[0-9]{6}$/|confirmed';
        } else {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $existingUser->id;
            // Si se proporciona password al actualizar, debe ser de 6 dígitos
            if (isset($data['password']) && !empty($data['password'])) {
                $rules['password'] = 'required|string|size:6|regex:/^[0-9]{6}$/|confirmed';
            }
        }

        // Mensajes de validación personalizados
        $messages = [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.size' => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.regex' => 'El DNI debe contener solo números.',
            'dni.unique' => 'Este DNI ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.size' => 'La contraseña debe tener exactamente 6 dígitos.',
            'password.regex' => 'La contraseña debe contener solo números.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'selectedRole.required' => 'Debe seleccionar un rol.',
            'selectedRole.exists' => 'El rol seleccionado no es válido.',
            'city_id.exists' => 'La ciudad seleccionada no es válida.',
        ];

        $validator = validator($data, $rules, $messages);

        if ($validator->fails()) {
            return [
                'success' => false, 
                'message' => 'Error de validación.',
                'errors' => $validator->errors()
            ];
        }

        return ['success' => true];
    }

    /**
     * Genera código QR para un usuario
     */
    public function generateQRCode(User $user): string
    {
        $url = url('clients/registro-datero/' . $user->id);
        return QrCode::size(150)
            ->color(0, 0, 0)
            ->margin(2)
            ->backgroundColor(255, 255, 255)
            ->generate($url);
    }

    /**
     * Cambia la contraseña de un usuario (para administradores)
     */
    public function changePassword(int $userId, string $newPassword, string $newPasswordConfirmation): array
    {
        $user = $this->findUser($userId);

        if (!$user) {
            return ['success' => false, 'message' => 'Usuario no encontrado.'];
        }

        // Validar que la nueva contraseña y su confirmación coincidan
        if ($newPassword !== $newPasswordConfirmation) {
            return ['success' => false, 'message' => 'Las contraseñas no coinciden.'];
        }

        // Validar que sea exactamente 6 dígitos numéricos
        if (strlen($newPassword) !== 6 || !preg_match('/^[0-9]{6}$/', $newPassword)) {
            return ['success' => false, 'message' => 'La contraseña debe tener exactamente 6 dígitos numéricos.'];
        }

        try {
            // Actualizar password y PIN con el mismo valor
            // El PIN se hasheará automáticamente por el cast 'hashed' del modelo
            $user->update([
                'password' => bcrypt($newPassword),
                'pin' => $newPassword // Sincronizar PIN con password (se hasheará automáticamente)
            ]);

            return [
                'success' => true,
                'message' => "Contraseña y PIN de {$user->name} actualizados correctamente."
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error al cambiar la contraseña: ' . $e->getMessage()];
        }
    }
}
