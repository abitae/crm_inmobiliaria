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
                    ->orWhere('phone', 'like', $searchTerm);
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
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'] ?: null,
                'lider_id' => $userData['lider_id'],
                'password' => bcrypt($userData['password']),
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
            return ['success' => false, 'message' => 'Error al crear el usuario.'];
        }
    }

    /**
     * Actualiza un usuario existente
     */
    public function updateUser(User $user, array $userData): array
    {
        try {
            $user->update([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'] ?: null,
                'lider_id' => $userData['lider_id'],
                'banco' => $userData['banco'] ?? '',
                'cuenta_bancaria' => $userData['cuenta_bancaria'] ?? '',
                'cci_bancaria' => $userData['cci_bancaria'] ?? '',
            ]);

            $user->setRole($userData['selectedRole']);

            return [
                'success' => true, 
                'message' => 'Usuario actualizado correctamente.',
                'user' => $user
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error al actualizar el usuario.'];
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
            'selectedRole' => 'required|exists:roles,name',
            'banco' => 'nullable|string|max:255',
            'cuenta_bancaria' => 'nullable|string|max:255',
            'cci_bancaria' => 'nullable|string|max:255',
        ];

        if ($isCreating) {
            $rules['email'] = 'required|email|max:255|unique:users,email';
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $existingUser->id;
        }

        $validator = validator($data, $rules);

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

        // Validar longitud mínima
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.'];
        }

        try {
            $user->update([
                'password' => bcrypt($newPassword)
            ]);

            return [
                'success' => true,
                'message' => "Contraseña de {$user->name} actualizada correctamente."
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error al cambiar la contraseña.'];
        }
    }
}
