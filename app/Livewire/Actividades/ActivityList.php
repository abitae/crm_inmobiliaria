<?php

namespace App\Livewire\Actividades;

use App\Models\Activity;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityList extends Component
{
    use WithPagination;

    public $clientFilter = '';
    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $showDeleteModal = false;
    public $activityToDeleteId = null;

    protected $queryString = [
        'clientFilter' => ['except' => ''],
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updating($name, $value)
    {
        if (in_array($name, ['clientFilter', 'search', 'statusFilter', 'typeFilter'])) {
            $this->resetPage();
        }
    }

    public function getClientsProperty()
    {
        return Client::orderBy('name')->get(['id', 'name']);
    }

    public function confirmDelete(int $activityId): void
    {
        $this->activityToDeleteId = $activityId;
        $this->showDeleteModal = true;
    }

    public function deleteActivity(): void
    {
        if (!$this->activityToDeleteId) {
            $this->dispatch('show-info', message: 'No hay actividad seleccionada');
            return;
        }

        $activity = Activity::find($this->activityToDeleteId);
        if (!$activity) {
            $this->dispatch('show-error', message: 'La actividad no existe');
            $this->closeDeleteModal();
            return;
        }

        $activity->delete();
        $this->dispatch('show-success', message: 'Actividad eliminada correctamente');
        $this->closeDeleteModal();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->activityToDeleteId = null;
    }

    public function render()
    {
        $user = Auth::user();
        
        $activities = Activity::with(['client', 'project', 'opportunity'])
            ->when($this->shouldFilterByRole($user), function ($q) use ($user) {
                $this->applyRoleFilter($q, $user);
            })
            ->when($this->clientFilter !== '', function ($q) {
                $q->where('client_id', $this->clientFilter);
            })
            ->when($this->statusFilter !== '', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter !== '', function ($q) {
                $q->where('activity_type', $this->typeFilter);
            })
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhereHas('client', function ($qClient) {
                            $qClient->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->orderByDesc('start_date')
            ->paginate(10);

        return view('livewire.actividades.activity-list', [
            'activities' => $activities,
            'clients' => $this->clients,
        ]);
    }

    /**
     * Determina si se debe aplicar filtro por rol
     */
    protected function shouldFilterByRole(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // Solo aplicar filtro si NO es admin
        return !$user->isAdmin();
    }

    /**
     * Aplica filtros según el rol del usuario
     */
    protected function applyRoleFilter($query, User $user): void
    {
        if ($user->isLider()) {
            // Líder: ver actividades de su equipo
            $teamUserIds = $this->getTeamUserIds($user);
            $query->where(function ($q) use ($teamUserIds) {
                $q->whereIn('assigned_to', $teamUserIds)
                  ->orWhereIn('advisor_id', $teamUserIds);
            });
        } elseif ($user->isAdvisor()) {
            // Vendedor: ver solo sus propias actividades
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('advisor_id', $user->id);
            });
        } else {
            // Otros roles: ver solo sus propias actividades
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('advisor_id', $user->id);
            });
        }
    }

    /**
     * Obtiene los IDs de usuarios del equipo (para líderes)
     */
    protected function getTeamUserIds(User $leader): array
    {
        $userIds = [$leader->id]; // Incluir al líder
        
        // Obtener vendedores a cargo
        $vendedoresIds = User::where('lider_id', $leader->id)
            ->whereHas('roles', function($query) {
                $query->where('name', 'vendedor');
            })
            ->pluck('id')
            ->toArray();
        
        $userIds = array_merge($userIds, $vendedoresIds);
        
        return array_unique($userIds);
    }
}
