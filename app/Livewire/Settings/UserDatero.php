<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Services\UserManagementService;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class UserDatero extends Component
{

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
    public $lider_id = null;
    public $selectedRole = '';
    public $password = '';
    public $password_confirmation = '';
    public $banco = '';
    public $cuenta_bancaria = '';
    public $cci_bancaria = '';

    // Propiedades para datos
    public $roles;
    public $leaders;
    public $qrcode;
    protected $cachedQRCode = null;
    
    // Roles permitidos para este componente
    protected $allowedRoles = ['datero'];
    
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
        $this->selectedUser = $this->getUserService()->findUser($userId);
        if (!$this->selectedUser) {
            $this->dispatch('show-error', message: 'Usuario no encontrado.');
            return;
        }

        $this->isCreating = false;
        $this->fillFormFromUser();
        $this->showUserModal = true;
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
            if ($this->actionType === 'activate') {
                $this->userToModify->activate();
                $this->dispatch('user-activated', message: "Usuario {$this->userToModify->name} activado correctamente.");
            } else {
                $this->userToModify->deactivate();
                $this->dispatch('user-deactivated', message: "Usuario {$this->userToModify->name} desactivado correctamente.");
            }
            $this->closeDeactivateModal();
        } catch (\Exception $e) {
            $action = $this->actionType === 'activate' ? 'activar' : 'desactivar';
            $this->dispatch('show-error', message: "Error al {$action} el usuario.");
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
            'lider_id',
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
        $this->lider_id = $this->selectedUser->lider_id;
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
        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'lider_id' => $this->lider_id,
            'selectedRole' => $this->selectedRole,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'banco' => $this->banco,
            'cuenta_bancaria' => $this->cuenta_bancaria,
            'cci_bancaria' => $this->cci_bancaria,
        ];

        $validation = $this->getUserService()->validateUserForm($userData, $this->isCreating, $this->selectedUser);
        
        if (!$validation['success']) {
            $this->dispatch('show-error', message: $validation['message']);
            return;
        }

        if ($this->isCreating) {
            $result = $this->getUserService()->createUser($userData);
        } else {
            $result = $this->getUserService()->updateUser($this->selectedUser, $userData);
        }

        if ($result['success']) {
            $this->dispatch('user-created', message: $result['message']);
        } else {
            $this->dispatch('show-error', message: $result['message']);
        }

        $this->closeUserModal();
    }


    /**
     * Confirma la activación de un usuario
     */
    public function confirmActivate(int $userId): void
    {
        $user = $this->getUserService()->findUser($userId);

        if (!$user) {
            $this->dispatch('show-error', message: 'Usuario no encontrado.');
            return;
        }

        if ($user->isActive()) {
            $this->dispatch('show-error', message: 'El usuario ya está activo.');
            return;
        }

        $this->showDeactivateModal = true;
        $this->userToModify = $user;
        $this->actionType = 'activate';
    }

    /**
     * Confirma la desactivación de un usuario
     */
    public function confirmDeactivate(int $userId): void
    {
        $user = $this->getUserService()->findUser($userId);

        if (!$user) {
            $this->dispatch('show-error', message: 'Usuario no encontrado.');
            return;
        }

        if ($user->isInactive()) {
            $this->dispatch('show-error', message: 'El usuario ya está inactivo.');
            return;
        }

        if (Auth::check() && $user->id === Auth::id()) {
            $this->dispatch('show-error', message: 'No puedes desactivar tu propia cuenta.');
            return;
        }

        $this->showDeactivateModal = true;
        $this->userToModify = $user;
        $this->actionType = 'deactivate';
    }

    /**
     * Activa un usuario
     */
    public function activateUser($userId): void
    {
        $result = $this->getUserService()->activateUser($userId);
        
        if ($result['success']) {
            $this->dispatch('user-activated', message: $result['message']);
        } else {
            $this->dispatch('show-error', message: $result['message']);
        }
    }

    /**
     * Desactiva un usuario
     */
    public function deactivateUser($userId): void
    {
        $result = $this->getUserService()->desactivateUser($userId);
        
        if ($result['success']) {
            $this->dispatch('user-deactivated', message: $result['message']);
        } else {
            $this->dispatch('show-error', message: $result['message']);
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
        $this->selectedUser = $this->getUserService()->findUser($userId);
        if (!$this->selectedUser) {
            $this->dispatch('show-error', message: 'Usuario no encontrado.');
            return;
        }
        
        $this->qrcode = $this->getQRCode();
        $this->showQRModal = true;
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
     * Renderiza el componente
     */
    public function render()
    {
        return view('livewire.settings.user-datero', [
            'users' => $this->users,
            'roles' => $this->roles,
            'leaders' => $this->leaders,
        ]);
    }
}
