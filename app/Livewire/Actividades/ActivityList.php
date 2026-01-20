<?php

namespace App\Livewire\Actividades;

use App\Models\Client;
use App\Services\ActivityService;
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
    protected $activityService;

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

    public function boot(ActivityService $activityService)
    {
        $this->activityService = $activityService;
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

        $deleted = $this->activityService->deleteActivity($this->activityToDeleteId);
        if (!$deleted) {
            $this->dispatch('show-error', message: 'La actividad no existe');
            $this->closeDeleteModal();
            return;
        }

        $this->dispatch('show-success', message: 'Actividad eliminada correctamente');
        $this->closeDeleteModal();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->activityToDeleteId = null;
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'clientFilter', 'statusFilter', 'typeFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        $activities = $this->activityService->getActivitiesPaginated([
            'client_id' => $this->clientFilter,
            'status' => $this->statusFilter,
            'type' => $this->typeFilter,
            'search' => $this->search,
        ], $user, 10);

        return view('livewire.actividades.activity-list', [
            'activities' => $activities,
            'clients' => $this->clients,
        ]);
    }
}
