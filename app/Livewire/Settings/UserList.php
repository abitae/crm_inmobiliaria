<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserList extends Component
{
    use WithPagination;

    // Propiedades de filtrado y búsqueda
    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = 'all';

    #[Url(as: 'role')]
    public string $roleFilter = 'all';

    #[Url(as: 'leader')]
    public string $leaderFilter = 'all';

    // Propiedades del modal
    public bool $showUserModal = false;
    public ?User $selectedUser = null;
    public bool $isCreating = false;
    public bool $showQRModal = false;
    // Propiedades para confirmación
    public bool $isConfirming = false;
    public string $confirmAction = '';
    public string $confirmMessage = '';
    public ?int $confirmUserId = null;

    // Propiedades para modal de activación/desactivación
    public bool $showDeactivateModal = false;
    public ?User $userToModify = null;
    public string $actionType = ''; // 'activate' o 'deactivate'

    // Propiedades para formulario de usuario
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public ?int $lider_id = null;
    public string $selectedRole = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $banco = '';
    public string $cuenta_bancaria = '';
    public string $cci_bancaria = '';

    // Propiedades para datos
    public $roles;
    public $leaders;
    public string $qrcode;
    protected ?string $cachedQRCode = null;
    /**
     * Inicializa el componente con datos necesarios
     */
    public function mount(): void
    {
        $this->roles = Role::orderBy('name')->get();
        $this->leaders = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'lider']);
        })->select('id', 'name', 'email')->get();
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
        $this->selectedUser = User::with('roles')->find($userId);
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
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'lider_id' => 'nullable|exists:users,id',
            'selectedRole' => 'required|exists:roles,name',
            'banco' => 'nullable|string|max:255',
            'cuenta_bancaria' => 'nullable|string|max:255',
            'cci_bancaria' => 'nullable|string|max:255',
        ];

        if ($this->isCreating) {
            $rules['email'] = 'required|email|max:255|unique:users,email';
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $this->selectedUser->id;
        }

        $this->validate($rules);

        if ($this->isCreating) {
            $this->createNewUser();
        } else {
            $this->updateExistingUser();
        }

        $this->closeUserModal();
    }

    /**
     * Crea un nuevo usuario
     */
    private function createNewUser(): void
    {
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'lider_id' => $this->lider_id,
            'password' => bcrypt($this->password),
            'is_active' => true,
            'banco' => $this->banco,
            'cuenta_bancaria' => $this->cuenta_bancaria,
            'cci_bancaria' => $this->cci_bancaria,
        ]);

        $user->setRole($this->selectedRole);
        $this->dispatch('user-created', message: 'Usuario creado correctamente.');
    }

    /**
     * Actualiza un usuario existente
     */
    private function updateExistingUser(): void
    {
        $this->selectedUser->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'lider_id' => $this->lider_id,
            'banco' => $this->banco,
            'cuenta_bancaria' => $this->cuenta_bancaria,
            'cci_bancaria' => $this->cci_bancaria,
        ]);

        $this->selectedUser->setRole($this->selectedRole);

        $this->dispatch('user-updated', message: 'Usuario actualizado correctamente.');
    }

    /**
     * Confirma la activación de un usuario
     */
    public function confirmActivate(int $userId): void
    {
        $user = User::with('roles')->find($userId);

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
        $user = User::with('roles')->find($userId);

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
     * Valida que el usuario existe y no está en el estado opuesto
     */
    private function validateUserStatus(int $userId, bool $shouldBeActive): ?User
    {
        $user = User::find($userId);

        if (!$user) {
            $this->dispatch('show-error', message: 'Usuario no encontrado.');
            return null;
        }

        if ($shouldBeActive && $user->isActive()) {
            $this->dispatch('show-error', message: 'El usuario ya está activo.');
            return null;
        }

        if (!$shouldBeActive && $user->isInactive()) {
            $this->dispatch('show-error', message: 'El usuario ya está inactivo.');
            return null;
        }

        if (!$shouldBeActive && Auth::check() && $user->id === Auth::id()) {
            $this->dispatch('show-error', message: 'No puedes desactivar tu propia cuenta.');
            return null;
        }

        return $user;
    }

    /**
     * Activa un usuario
     */
    public function activateUser($userId): void
    {
        $user = $this->validateUserStatus($userId, true);
        if (!$user) return;

        try {
            $user->activate();
            $this->dispatch('user-activated', message: "Usuario {$user->name} activado correctamente.");
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'Error al activar el usuario.');
        }
    }

    /**
     * Desactiva un usuario
     */
    public function deactivateUser($userId): void
    {
        $user = $this->validateUserStatus($userId, false);
        if (!$user) return;

        try {
            $user->deactivate();
            $this->dispatch('user-deactivated', message: "Usuario {$user->name} desactivado correctamente.");
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'Error al desactivar el usuario.');
        }
    }

    /**
     * Obtiene la lista paginada de usuarios
     */
    public function getUsersProperty()
    {
        return User::with(['roles', 'lider'])
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm)
                        ->orWhere('phone', 'like', $searchTerm);
                });
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($this->statusFilter === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->when($this->roleFilter !== 'all', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', $this->roleFilter);
                });
            })
            ->when($this->leaderFilter !== 'all', function ($query) {
                if ($this->leaderFilter === 'no_leader') {
                    $query->whereNull('lider_id');
                } else {
                    $query->where('lider_id', $this->leaderFilter);
                }
            })
            ->orderBy('name')
            ->paginate(10);
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
        $this->selectedUser = User::find($userId);
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
            $url = url('clients/registro-datero/' . $this->selectedUser->id);
            $this->cachedQRCode = QrCode::size(150)
                ->color(0, 0, 255)
                ->margin(2)
                ->backgroundColor(0, 255, 0)
                ->generate($url);
        }
        return $this->cachedQRCode;
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
