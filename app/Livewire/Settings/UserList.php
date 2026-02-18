<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\City;
use App\Services\UserManagementService;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Mary\Traits\Toast;

class UserList extends Component
{
    use Toast;
    use WithPagination;

    // Propiedades de filtrado y búsqueda
    #[Url(as: 'search')]
    public $search = '';

    #[Url(as: 'status')]
    public $statusFilter = 'all';

    #[Url(as: 'role')]
    public $roleFilter = 'all';

    #[Url(as: 'leader')]
    public $leaderFilter = 'all';

    // Propiedades del modal
    public $showUserModal = false;
    public $selectedUser = null;
    public $isCreating = false;
    public $showQRModal = false;
    public $showPasswordModal = false;
    public $userForPasswordChange = null;
    // Propiedades para confirmación
    public $isConfirming = false;
    public $confirmAction = '';
    public $confirmMessage = '';
    public $confirmUserId = null;

    // Propiedades para modal de activación/desactivación
    public $showDeactivateModal = false;
    public $userToModify = null;
    public $actionType = ''; // 'activate' o 'deactivate'

    // Propiedades para formulario de usuario
    public $name = '';
    public $email = '';
    public $phone = '';
    public $dni = '';
    public $lider_id = null;
    public $selectedRole = '';
    public $password = '';
    public $password_confirmation = '';
    public $new_password = '';
    public $new_password_confirmation = '';
    public $banco = '';
    public $cuenta_bancaria = '';
    public $cci_bancaria = '';
    public $city_id = null;

    // Propiedades para datos
    public $roles;
    public $leaders;
    public $cities;
    public $qrcode;
    protected $cachedQRCode = null;

    // Roles permitidos para este componente
    protected $allowedRoles = ['admin', 'lider', 'vendedor'];

    /**
     * Obtiene el servicio de gestión de usuarios
     */
    protected function getUserService(): UserManagementService
    {
        return app(UserManagementService::class);
    }

    /**
     * Inicializa el componente con datos necesarios
     */
    public function mount(): void
    {
        $this->roles = $this->getUserService()->getRoles($this->allowedRoles);
        $this->leaders = $this->getUserService()->getLeaders();
        $this->cities = City::orderBy('name')->get(['id', 'name']);
    }

    /**
     * Resetea la paginación cuando cambian los filtros
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedLeaderFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Abre el modal para crear un nuevo usuario
     */
    public function createUser(): void
    {
        $this->resetForm();
        $this->isCreating = true;
        $this->showUserModal = true;
    }

    /**
     * Abre el modal para editar un usuario existente
     */
    public function openUserModal(int $userId): void
    {
        try {
            Log::info('Abriendo modal para editar usuario', [
                'user_id' => $userId,
                'current_user_id' => Auth::id()
            ]);

            $this->selectedUser = $this->getUserService()->findUser($userId);
            if (!$this->selectedUser) {
                Log::warning('Usuario no encontrado al intentar editar', [
                    'user_id' => $userId,
                    'current_user_id' => Auth::id()
                ]);
                $this->error('Usuario no encontrado.');
                return;
            }

            $this->isCreating = false;
            $this->fillFormFromUser();
            $this->showUserModal = true;
        } catch (\Exception $e) {
            Log::error('Error al abrir modal de usuario', [
                'user_id' => $userId,
                'current_user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            $this->error('Error al abrir el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Cierra el modal y resetea el formulario
     */
    public function closeUserModal(): void
    {
        $this->resetForm();
        $this->showUserModal = false;
    }

    /**
     * Muestra el modal de confirmación
     */
    private function showConfirmModal(string $action, int $userId, string $userName): void
    {
        $this->isConfirming = true;
        $this->confirmAction = $action;
        $this->confirmUserId = $userId;
        $this->confirmMessage = $action === 'activate'
            ? "¿Estás seguro de que deseas activar a {$userName}?"
            : "¿Estás seguro de que deseas desactivar a {$userName}?";
        $this->showUserModal = true;
    }

    /**
     * Cierra el modal de confirmación
     */
    public function closeConfirmModal(): void
    {
        $this->isConfirming = false;
        $this->confirmAction = '';
        $this->confirmMessage = '';
        $this->confirmUserId = null;
        $this->showUserModal = false;
    }

    /**
     * Ejecuta la acción confirmada
     */
    public function executeConfirmAction(): void
    {
        if (!$this->confirmUserId || !$this->confirmAction) {
            return;
        }

        if ($this->confirmAction === 'activate') {
            $this->activateUser($this->confirmUserId);
        } elseif ($this->confirmAction === 'deactivate') {
            $this->deactivateUser($this->confirmUserId);
        }

        $this->closeConfirmModal();
    }

    /**
     * Cierra el modal de activación/desactivación
     */
    public function closeDeactivateModal(): void
    {
        $this->showDeactivateModal = false;
        $this->userToModify = null;
        $this->actionType = '';
    }

    /**
     * Ejecuta la acción de activación o desactivación
     */
    public function executeDeactivate(): void
    {
        if (!$this->userToModify || !$this->actionType) {
            return;
        }

        try {
            Log::info('Intentando ' . $this->actionType . ' usuario', [
                'user_id' => $this->userToModify->id,
                'user_name' => $this->userToModify->name,
                'current_user_id' => Auth::id()
            ]);

            if ($this->actionType === 'activate') {
                $this->userToModify->activate();
                Log::info('Usuario activado exitosamente', [
                    'user_id' => $this->userToModify->id,
                    'current_user_id' => Auth::id()
                ]);
                $this->success("Usuario {$this->userToModify->name} activado correctamente.");
            } else {
                $this->userToModify->deactivate();
                Log::info('Usuario desactivado exitosamente', [
                    'user_id' => $this->userToModify->id,
                    'current_user_id' => Auth::id()
                ]);
                $this->success("Usuario {$this->userToModify->name} desactivado correctamente.");
            }
            $this->closeDeactivateModal();
            $this->resetPage(); // Refrescar la lista
        } catch (\Exception $e) {
            $action = $this->actionType === 'activate' ? 'activar' : 'desactivar';
            Log::error("Error al {$action} usuario", [
                'user_id' => $this->userToModify->id ?? null,
                'current_user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error("Error al {$action} el usuario: " . $e->getMessage());
        }
    }

    /**
     * Resetea todos los campos del formulario
     */
    private function resetForm(): void
    {
        $this->reset([
            'name',
            'email',
            'phone',
            'dni',
            'lider_id',
            'city_id',
            'selectedRole',
            'password',
            'password_confirmation',
            'banco',
            'cuenta_bancaria',
            'cci_bancaria'
        ]);
        $this->selectedUser = null;
        $this->isCreating = false;
    }

    /**
     * Llena el formulario con los datos del usuario seleccionado
     */
    private function fillFormFromUser(): void
    {
        $this->name = $this->selectedUser->name;
        $this->email = $this->selectedUser->email;
        $this->phone = $this->selectedUser->phone ?? '';
        $this->dni = $this->selectedUser->dni ?? '';
        $this->lider_id = $this->selectedUser->lider_id;
        $this->city_id = $this->selectedUser->city_id;
        $this->selectedRole = $this->selectedUser->roles->first()?->name ?? '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->banco = $this->selectedUser->banco ?? '';
        $this->cuenta_bancaria = $this->selectedUser->cuenta_bancaria ?? '';
        $this->cci_bancaria = $this->selectedUser->cci_bancaria ?? '';
    }

    /**
     * Guarda o actualiza un usuario
     */
    public function saveUser(): void
    {
        try {
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'dni' => $this->dni,
                'lider_id' => $this->lider_id,
            'city_id' => $this->city_id,
                'selectedRole' => $this->selectedRole,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'banco' => $this->banco,
                'cuenta_bancaria' => $this->cuenta_bancaria,
                'cci_bancaria' => $this->cci_bancaria,
            ];

            Log::info('Intentando ' . ($this->isCreating ? 'crear' : 'actualizar') . ' usuario', [
                'is_creating' => $this->isCreating,
                'user_id' => $this->selectedUser->id ?? null,
                'email' => $this->email,
                'current_user_id' => Auth::id()
            ]);

            $validation = $this->getUserService()->validateUserForm($userData, $this->isCreating, $this->selectedUser);

            if (!$validation['success']) {
                $errorMessage = $validation['message'];
                // Si hay errores específicos, agregarlos al mensaje
                if (isset($validation['errors']) && $validation['errors']->any()) {
                    $errorDetails = $validation['errors']->all();
                    $errorMessage .= ': ' . implode(', ', $errorDetails);
                }

                Log::warning('Error de validación al guardar usuario', [
                    'is_creating' => $this->isCreating,
                    'user_id' => $this->selectedUser->id ?? null,
                    'current_user_id' => Auth::id(),
                    'validation_message' => $errorMessage,
                    'validation_errors' => isset($validation['errors']) ? $validation['errors']->toArray() : []
                ]);
                $this->error($errorMessage);
                return;
            }

            if ($this->isCreating) {
                $result = $this->getUserService()->createUser($userData);
            } else {
                $result = $this->getUserService()->updateUser($this->selectedUser, $userData);
            }

            if ($result['success']) {
                Log::info('Usuario ' . ($this->isCreating ? 'creado' : 'actualizado') . ' exitosamente', [
                    'is_creating' => $this->isCreating,
                    'user_id' => $this->selectedUser->id ?? null,
                    'current_user_id' => Auth::id()
                ]);
                $this->success($result['message']);
                $this->closeUserModal();
                $this->resetPage(); // Refrescar la lista
            } else {
                Log::error('Error al guardar usuario', [
                    'is_creating' => $this->isCreating,
                    'user_id' => $this->selectedUser->id ?? null,
                    'current_user_id' => Auth::id(),
                    'error_message' => $result['message']
                ]);
                $this->error($result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Excepción al guardar usuario', [
                'is_creating' => $this->isCreating,
                'user_id' => $this->selectedUser->id ?? null,
                'current_user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Error al guardar el usuario: ' . $e->getMessage());
        }
    }


    /**
     * Confirma la activación de un usuario
     */
    public function confirmActivate(int $userId): void
    {
        try {
            Log::info('Confirmando activación de usuario', [
                'user_id' => $userId,
                'current_user_id' => Auth::id()
            ]);

            $user = $this->getUserService()->findUser($userId);

            if (!$user) {
                Log::warning('Usuario no encontrado al intentar activar', [
                    'user_id' => $userId,
                    'current_user_id' => Auth::id()
                ]);
                $this->error('Usuario no encontrado.');
                return;
            }

            if ($user->isActive()) {
                Log::info('Usuario ya está activo', [
                    'user_id' => $userId,
                    'current_user_id' => Auth::id()
                ]);
                $this->warning('El usuario ya está activo.');
                return;
            }

            $this->showDeactivateModal = true;
            $this->userToModify = $user;
            $this->actionType = 'activate';
        } catch (\Exception $e) {
            Log::error('Error al confirmar activación de usuario', [
                'user_id' => $userId,
                'current_user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            $this->error('Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Confirma la desactivación de un usuario
     */
    public function confirmDeactivate(int $userId): void
    {
        try {
            Log::info('Confirmando desactivación de usuario', [
                'user_id' => $userId,
                'current_user_id' => Auth::id()
            ]);

            $user = $this->getUserService()->findUser($userId);

            if (!$user) {
                Log::warning('Usuario no encontrado al intentar desactivar', [
                    'user_id' => $userId,
                    'current_user_id' => Auth::id()
                ]);
                $this->error('Usuario no encontrado.');
                return;
            }

            if ($user->isInactive()) {
                Log::info('Usuario ya está inactivo', [
                    'user_id' => $userId,
                    'current_user_id' => Auth::id()
                ]);
                $this->warning('El usuario ya está inactivo.');
                return;
            }

            if (Auth::check() && $user->id === Auth::id()) {
                Log::warning('Intento de desactivar cuenta propia', [
                    'user_id' => $userId,
                    'current_user_id' => Auth::id()
                ]);
                $this->error('No puedes desactivar tu propia cuenta.');
                return;
            }

            $this->showDeactivateModal = true;
            $this->userToModify = $user;
            $this->actionType = 'deactivate';
        } catch (\Exception $e) {
            Log::error('Error al confirmar desactivación de usuario', [
                'user_id' => $userId,
                'current_user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            $this->error('Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Activa un usuario
     */
    public function activateUser($userId): void
    {
        try {
            Log::info('Activando usuario', [
                'user_id' => $userId,
                'current_user_id' => Auth::id()
            ]);

            $result = $this->getUserService()->activateUser($userId);

            if ($result['success']) {
                Log::info('Usuario activado exitosamente', [
                    'user_id' => $userId,
                    'current_user_id' => Auth::id()
                ]);
                $this->success($result['message']);
                $this->resetPage(); // Refrescar la lista
            } else {
                Log::error('Error al activar usuario', [
                    'user_id' => $userId,
                    'current_user_id' => Auth::id(),
                    'error_message' => $result['message']
                ]);
                $this->error($result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Excepción al activar usuario', [
                'user_id' => $userId,
                'current_user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Error al activar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Desactiva un usuario
     */
    public function deactivateUser($userId): void
    {
        try {
            Log::info('Desactivando usuario', [
                'user_id' => $userId,
                'current_user_id' => Auth::id()
            ]);

            $result = $this->getUserService()->desactivateUser($userId);

            if ($result['success']) {
                Log::info('Usuario desactivado exitosamente', [
                    'user_id' => $userId,
                    'current_user_id' => Auth::id()
                ]);
                $this->success($result['message']);
                $this->resetPage(); // Refrescar la lista
            } else {
                Log::error('Error al desactivar usuario', [
                    'user_id' => $userId,
                    'current_user_id' => Auth::id(),
                    'error_message' => $result['message']
                ]);
                $this->error($result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Excepción al desactivar usuario', [
                'user_id' => $userId,
                'current_user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Error al desactivar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene la lista paginada de usuarios
     */
    public function getUsersProperty()
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFilter,
            'role' => $this->roleFilter,
            'leader' => $this->leaderFilter,
        ];

        return $this->getUserService()->getUsersWithRoles($this->allowedRoles, $filters);
    }

    /**
     * Limpia los filtros y resetea la búsqueda
     */
    public function clearFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'roleFilter', 'leaderFilter']);
        $this->resetPage();
    }
    public function verQR(int $userId): void
    {
        try {
            Log::info('Abriendo modal QR de usuario', [
                'user_id' => $userId,
                'current_user_id' => Auth::id()
            ]);

            $this->selectedUser = $this->getUserService()->findUser($userId);
            if (!$this->selectedUser) {
                Log::warning('Usuario no encontrado al intentar ver QR', [
                    'user_id' => $userId,
                    'current_user_id' => Auth::id()
                ]);
                $this->error('Usuario no encontrado.');
                return;
            }

            $this->qrcode = $this->getQRCode();
            $this->showQRModal = true;
        } catch (\Exception $e) {
            Log::error('Error al abrir modal QR de usuario', [
                'user_id' => $userId,
                'current_user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            $this->error('Error al generar el código QR: ' . $e->getMessage());
        }
    }

    public function closeQRModal(): void
    {
        $this->showQRModal = false;
        $this->selectedUser = null;
    }

    public function getQRCode(): string
    {
        if ($this->cachedQRCode === null) {
            $this->cachedQRCode = $this->getUserService()->generateQRCode($this->selectedUser);
        }
        return $this->cachedQRCode;
    }

    /**
     * Abre el modal para cambiar la contraseña de un usuario
     */
    public function openPasswordModal(int $userId): void
    {
        try {
            Log::info('Abriendo modal de cambio de contraseña', [
                'user_id' => $userId,
                'current_user_id' => Auth::id()
            ]);

            $this->userForPasswordChange = $this->getUserService()->findUser($userId);
            if (!$this->userForPasswordChange) {
                Log::warning('Usuario no encontrado al intentar cambiar contraseña', [
                    'user_id' => $userId,
                    'current_user_id' => Auth::id()
                ]);
                $this->error('Usuario no encontrado.');
                return;
            }

            $this->resetPasswordForm();
            $this->showPasswordModal = true;
        } catch (\Exception $e) {
            Log::error('Error al abrir modal de cambio de contraseña', [
                'user_id' => $userId,
                'current_user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            $this->error('Error al abrir el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Cierra el modal de cambio de contraseña
     */
    public function closePasswordModal(): void
    {
        $this->resetPasswordForm();
        $this->showPasswordModal = false;
        $this->userForPasswordChange = null;
    }

    /**
     * Resetea el formulario de cambio de contraseña
     */
    private function resetPasswordForm(): void
    {
        $this->reset([
            'new_password',
            'new_password_confirmation'
        ]);
    }

    /**
     * Cambia la contraseña de un usuario
     */
    public function changePassword(): void
    {
        try {
            if (!$this->userForPasswordChange) {
                $this->error('Usuario no encontrado.');
                return;
            }

            Log::info('Intentando cambiar contraseña de usuario', [
                'user_id' => $this->userForPasswordChange->id,
                'current_user_id' => Auth::id()
            ]);

            $result = $this->getUserService()->changePassword(
                $this->userForPasswordChange->id,
                $this->new_password,
                $this->new_password_confirmation
            );

            if ($result['success']) {
                Log::info('Contraseña cambiada exitosamente', [
                    'user_id' => $this->userForPasswordChange->id,
                    'current_user_id' => Auth::id()
                ]);
                $this->success($result['message']);
                $this->closePasswordModal();
            } else {
                Log::error('Error al cambiar contraseña', [
                    'user_id' => $this->userForPasswordChange->id,
                    'current_user_id' => Auth::id(),
                    'error_message' => $result['message']
                ]);
                $this->error($result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Excepción al cambiar contraseña', [
                'user_id' => $this->userForPasswordChange->id ?? null,
                'current_user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Error al cambiar la contraseña: ' . $e->getMessage());
        }
    }

    /**
     * Renderiza el componente
     */
    public function render()
    {
        return view('livewire.settings.user-list', [
            'users' => $this->users,
            'roles' => $this->roles,
            'leaders' => $this->leaders,
        ]);
    }
}
