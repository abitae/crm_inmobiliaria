<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Url;

class UserList extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public $search = '';

    // Modal unificado de usuario
    public $showUserModal = false;
    public $selectedUser = null;
    public $selectedRole = '';
    public $roles = [];
    public $leaders = [];
    
    // Datos del usuario para edición
    public $name = '';
    public $email = '';
    public $phone = '';
    public $lider_id = '';

    public function mount()
    {
        $this->roles = Role::orderBy('name')->get();
        $this->leaders = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'lider']);
        })->get();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openUserModal($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->name = $this->selectedUser->name;
        $this->email = $this->selectedUser->email;
        $this->phone = $this->selectedUser->phone;
        $this->lider_id = $this->selectedUser->lider_id;
        $this->selectedRole = $this->selectedUser->getRoleNames()->first() ?? '';
        $this->showUserModal = true;
    }

    public function closeUserModal()
    {
        $this->showUserModal = false;
        $this->selectedUser = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->lider_id = '';
        $this->selectedRole = '';
    }

    public function saveUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->selectedUser->id,
            'phone' => 'nullable|string|max:20',
            'lider_id' => 'nullable|exists:users,id',
            'selectedRole' => 'required|exists:roles,name',
        ]);

        if ($this->selectedUser) {
            // Actualizar datos del usuario
            $this->selectedUser->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'lider_id' => $this->lider_id ?: null,
            ]);

            // Actualizar rol del usuario
            $this->selectedUser->syncRoles([]);
            $this->selectedUser->assignRole($this->selectedRole);

            session()->flash('message', 'Usuario actualizado correctamente.');
            $this->closeUserModal();
        }
    }

    public function getUsersProperty()
    {
        return User::with(['roles', 'lider'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.settings.user-list', [
            'users' => $this->users
        ]);
    }
}
