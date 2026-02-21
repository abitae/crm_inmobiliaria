<?php

namespace App\Livewire\Dateros;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('components.layouts.app')]
class DaterosList extends Component
{
    use Toast;
    use WithPagination;

    // Filtros
    public $search = '';
    public $statusFilter = 'all'; // 'all', 'active', 'inactive'
    public $vendedorFilter = '';

    // Propiedades para datos
    public $vendedores;

    /**
     * Inicializa el componente
     */
    public function mount()
    {
        $user = Auth::user();
        
        // Cargar vendedores disponibles según el rol de forma optimizada
        $this->vendedores = $this->loadVendedoresByRole($user);
    }

    /**
     * Carga los vendedores disponibles según el rol del usuario de forma optimizada
     * 
     * @param User $user Usuario autenticado
     * @return \Illuminate\Support\Collection
     */
    protected function loadVendedoresByRole(User $user): \Illuminate\Support\Collection
    {
        if ($user->isAdmin()) {
            return $this->loadVendedoresForAdmin($user);
        } elseif ($user->isLider()) {
            return $this->loadVendedoresForLider($user);
        }
        
        // Para vendedor u otros roles, retornar colección vacía
        return collect();
    }

    /**
     * Carga vendedores para admin (solo vendedores a su cargo: bajo sus líderes)
     *
     * @param User $admin Usuario administrador
     * @return \Illuminate\Support\Collection
     */
    protected function loadVendedoresForAdmin(User $admin): \Illuminate\Support\Collection
    {
        $lideresIds = User::where('lider_id', $admin->id)
            ->whereHas('roles', fn ($q) => $q->where('name', 'lider'))
            ->pluck('id')
            ->toArray();
        if (empty($lideresIds)) {
            return collect();
        }
        return User::whereIn('lider_id', $lideresIds)
            ->role('vendedor')
            ->select('users.id', 'users.name')
            ->whereHas('subordinados', function ($query) {
                $query->role('datero');
            })
            ->orderBy('users.name')
            ->get();
    }

    /**
     * Líder solo tiene dateros directos; no usa filtro por vendedor en esta lista.
     *
     * @param User $lider Usuario líder
     * @return \Illuminate\Support\Collection
     */
    protected function loadVendedoresForLider(User $lider): \Illuminate\Support\Collection
    {
        return collect();
    }

    /**
     * Resetea la paginación cuando cambian los filtros
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedVendedorFilter()
    {
        $this->resetPage();
    }

    /**
     * Limpia los filtros
     */
    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'vendedorFilter']);
        $this->resetPage();
    }

    /**
     * Obtiene los dateros según el rol del usuario
     */
    protected function getDateros()
    {
        $user = Auth::user();
        $query = User::role('datero');

        // Aplicar filtros según el rol del usuario (solo dateros a su cargo)
        if ($user->isAdmin()) {
            $dateroIds = $user->getDateroIdsUnderResponsibility();
            if (!empty($dateroIds)) {
                $query->whereIn('id', $dateroIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->isLider()) {
            // Líder solo ve sus dateros directos
            $dateroIds = $user->getDateroIdsUnderResponsibility();
            if (!empty($dateroIds)) {
                $query->whereIn('id', $dateroIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->isAdvisor()) {
            // Vendedor solo ve sus dateros
            $query->where('lider_id', $user->id);
        } else {
            // Para otros roles, retornar vacío
            $query->whereRaw('1 = 0');
        }

        // Aplicar filtro de vendedor si está seleccionado (solo admin tiene vendedores en el filtro)
        if ($this->vendedorFilter && $this->vendedorFilter !== '' && $user->isAdmin()) {
            $query->where('lider_id', $this->vendedorFilter);
        }

        // Aplicar búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Aplicar filtro de estado
        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        // Incluir relaciones
        $query->with(['lider', 'roles'])
              ->orderBy('name');

        return $query->paginate(15);
    }

    /**
     * Renderiza el componente
     */
    public function render()
    {
        return view('livewire.dateros.dateros-list', [
            'dateros' => $this->getDateros(),
            'vendedores' => $this->vendedores
        ]);
    }
}
