<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Url;

class RoleList extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public $search = '';

    // Modal de permisos
    public $showPermissionsModal = false;
    public $selectedRole = null;
    public $selectedPermissions = [];
    public $permissions = [];

    public function mount()
    {
        $this->permissions = Permission::orderBy('name')->get();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openPermissionsModal($roleId)
    {
        $this->selectedRole = Role::find($roleId);
        $this->selectedPermissions = $this->selectedRole->permissions->pluck('id')->toArray();
        $this->showPermissionsModal = true;
    }

    public function closePermissionsModal()
    {
        $this->showPermissionsModal = false;
        $this->selectedRole = null;
        $this->selectedPermissions = [];
    }

    public function savePermissions()
    {
        if ($this->selectedRole) {
            $this->selectedRole->syncPermissions($this->selectedPermissions);
            session()->flash('message', 'Permisos actualizados correctamente.');
            $this->closePermissionsModal();
        }
    }

    public function getRolesProperty()
    {
        return Role::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
        ->orderBy('name')
        ->paginate(10);
    }

    public function render()
    {
        return view('livewire.settings.role-list', [
            'roles' => $this->roles
        ]);
    }
}
