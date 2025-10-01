<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
class TaskList extends Component
{
    use WithPagination;
    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $clientFilter = '';
    public $showDeleteModal = false;
    public $taskToDeleteId = null;

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

        $task = Task::find($this->taskToDeleteId);
        if (!$task) {
            $this->dispatch('show-error', message: 'La tarea no existe');
            $this->closeDeleteModal();
            return;
        }

        $task->delete();
        $this->dispatch('show-success', message: 'Tarea eliminada correctamente');
        $this->closeDeleteModal();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->taskToDeleteId = null;
    }

    public function render()
    {
        $tasks = Task::with(['client'])
            ->when($this->clientFilter !== '', function ($q) {
                $q->where('client_id', $this->clientFilter);
            })
            ->when($this->statusFilter !== '', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->priorityFilter !== '', function ($q) {
                $q->where('priority', $this->priorityFilter);
            })
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('due_date', 'asc')
            ->paginate(10);

        return view('livewire.tasks.task-list', [
            'tasks' => $tasks,
            'clients' => $this->clients,
        ]);
    }
}
