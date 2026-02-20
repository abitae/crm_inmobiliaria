<?php

namespace App\Livewire\Tasks;

use App\Models\Client;
use App\Services\TaskService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class TaskList extends Component
{
    use WithPagination;
    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $clientFilter = '';
    public $showDeleteModal = false;
    public $taskToDeleteId = null;
    protected $taskService;

    protected $queryString = [
        'clientFilter' => ['except' => ''],
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updating($name, $value)
    {
        if (in_array($name, ['clientFilter', 'search', 'statusFilter', 'priorityFilter'])) {
            $this->resetPage();
        }
    }

    public function getClientsProperty()
    {
        return Client::orderBy('name')->get(['id', 'name']);
    }

    public function boot(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function confirmDelete(int $taskId): void
    {
        $this->taskToDeleteId = $taskId;
        $this->showDeleteModal = true;
    }

    public function deleteTask(): void
    {
        if (!$this->taskToDeleteId) {
            $this->dispatch('show-info', message: 'No hay tarea seleccionada');
            return;
        }

        $deleted = $this->taskService->deleteTask($this->taskToDeleteId);
        if (!$deleted) {
            $this->dispatch('show-error', message: 'La tarea no existe');
            $this->closeDeleteModal();
            return;
        }

        $this->dispatch('show-success', message: 'Tarea eliminada correctamente');
        $this->closeDeleteModal();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->taskToDeleteId = null;
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'clientFilter', 'statusFilter', 'priorityFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $tasks = $this->taskService->getTasksPaginated([
            'client_id' => $this->clientFilter,
            'status' => $this->statusFilter,
            'priority' => $this->priorityFilter,
            'search' => $this->search,
        ], 10);

        return view('livewire.tasks.task-list', [
            'tasks' => $tasks,
            'clients' => $this->clients,
        ]);
    }
}
