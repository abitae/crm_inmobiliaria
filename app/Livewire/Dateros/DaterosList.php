<?php

namespace App\Livewire\Dateros;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

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
            return $this->loadVendedoresForAdmin();
        } elseif ($user->isLider()) {
            return $this->loadVendedoresForLider($user);
        }
        
        // Para vendedor u otros roles, retornar colección vacía
        return collect();
    }

    /**
     * Carga vendedores para admin (todos los vendedores que tienen dateros)
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function loadVendedoresForAdmin(): \Illuminate\Support\Collection
    {
        // Usar role() de Spatie que está optimizado y whereHas para subordinados con rol datero
        return User::role('vendedor')
            ->select('users.id', 'users.name')
            ->whereHas('subordinados', function($query) {
                // Usar role() directamente en lugar de whereHas('roles') anidado
                $query->role('datero');
            })
            ->orderBy('users.name')
            ->get();
    }

    /**
     * Carga vendedores para líder (sus vendedores + opción de dateros directos si aplica)
     * 
     * @param User $lider Usuario líder
     * @return \Illuminate\Support\Collection
     */
    protected function loadVendedoresForLider(User $lider): \Illuminate\Support\Collection
    {
        // Obtener vendedores del líder que tienen dateros en una sola consulta optimizada
        // Usar role() directamente que está optimizado por Spatie Permission
        $vendedores = User::where('lider_id', $lider->id)
            ->role('vendedor')
            ->select('users.id', 'users.name')
            ->whereHas('subordinados', function($query) {
                // Usar role() directamente en lugar de whereHas('roles') anidado
                $query->role('datero');
            })
            ->orderBy('users.name')
            ->get();

        // Verificar si el líder tiene dateros directos usando una consulta optimizada con exists()
        // Esto es más eficiente que count() o get() ya que solo verifica existencia
        $tieneDaterosDirectos = User::where('lider_id', $lider->id)
            ->role('datero')
            ->exists();

        // Si tiene dateros directos, agregar la opción al inicio de la lista
        if ($tieneDaterosDirectos) {
            $vendedores->prepend((object)[
                'id' => $lider->id,
                'name' => 'Dateros directos'
            ]);
        }

        return $vendedores;
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

        // Aplicar filtros según el rol del usuario
        if ($user->isAdmin()) {
            // Admin ve todos los dateros
            // No se aplica ningún filtro adicional
        } elseif ($user->isLider()) {
            // Lider ve dateros directos + dateros de sus vendedores
            $vendedoresIds = User::where('lider_id', $user->id)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'vendedor');
                })
                ->pluck('id')
                ->toArray();
            
            // Dateros directos del líder
            $daterosDirectosIds = User::where('lider_id', $user->id)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'datero');
                })
                ->pluck('id')
                ->toArray();
            
            // Dateros de los vendedores
            $daterosVendedoresIds = User::whereIn('lider_id', $vendedoresIds)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'datero');
                })
                ->pluck('id')
                ->toArray();
            
            $todosDaterosIds = array_merge($daterosDirectosIds, $daterosVendedoresIds);
            
            if (!empty($todosDaterosIds)) {
                $query->whereIn('id', $todosDaterosIds);
            } else {
                // Si no hay dateros, retornar query vacío
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->isAdvisor()) {
            // Vendedor solo ve sus dateros
            $query->where('lider_id', $user->id);
        } else {
            // Para otros roles, retornar vacío
            $query->whereRaw('1 = 0');
        }

        // Aplicar filtro de vendedor si está seleccionado
        if ($this->vendedorFilter && $this->vendedorFilter !== '') {
            if ($user->isLider() && $this->vendedorFilter == $user->id) {
                // Si es "Dateros directos", filtrar por lider_id del líder
                $query->where('lider_id', $user->id);
            } else {
                // Filtrar por el vendedor seleccionado
                $query->where('lider_id', $this->vendedorFilter);
            }
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
