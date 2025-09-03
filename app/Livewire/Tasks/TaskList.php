<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\Client;
use App\Models\Project;
use App\Models\Opportunity;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class TaskList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $assignedToFilter = '';
    public $dueDateFilter = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedTask = null;
    public $editingTask = null;

    // Form fields
    public $title = '';
    public $description = '';
    public $status = 'pendiente';
    public $priority = 'media';
    public $due_date = '';
    public $assigned_to = '';
    public $client_id = '';
    public $project_id = '';
    public $opportunity_id = '';
    public $notes = '';

    public $clients = [];
    public $projects = [];
    public $opportunities = [];
    public $users = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:pendiente,en_progreso,completada,cancelada',
        'priority' => 'required|in:baja,media,alta,urgente',
        'due_date' => 'required|date|after_or_equal:today',
        'assigned_to' => 'required|exists:users,id',
        'client_id' => 'nullable|exists:clients,id',
        'project_id' => 'nullable|exists:projects,id',
        'opportunity_id' => 'nullable|exists:opportunities,id',
        'notes' => 'nullable|string'
    ];

    public function mount()
    {
        $this->clients = Client::all();
        $this->projects = Project::active()->get();
        $this->opportunities = Opportunity::active()->get();
        $this->users = User::getAvailableAdvisors();
        $this->due_date = now()->addDays(1)->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatedAssignedToFilter()
    {
        $this->resetPage();
    }

    public function updatedDueDateFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($taskId)
    {
        $this->editingTask = Task::find($taskId);
        if ($this->editingTask) {
            $this->fillFormFromTask($this->editingTask);
            $this->showEditModal = true;
        }
    }

    public function openDeleteModal($taskId)
    {
        $this->selectedTask = Task::find($taskId);
        $this->showDeleteModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
        $this->editingTask = null;
        $this->selectedTask = null;
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->status = 'pendiente';
        $this->priority = 'media';
        $this->due_date = now()->addDays(1)->format('Y-m-d');
        $this->assigned_to = '';
        $this->client_id = '';
        $this->project_id = '';
        $this->opportunity_id = '';
        $this->notes = '';
    }

    public function fillFormFromTask($task)
    {
        $this->title = $task->title;
        $this->description = $task->description;
        $this->status = $task->status;
        $this->priority = $task->priority;
        $this->due_date = $task->due_date ? $task->due_date->format('Y-m-d') : '';
        $this->assigned_to = $task->assigned_to;
        $this->client_id = $task->client_id;
        $this->project_id = $task->project_id;
        $this->opportunity_id = $task->opportunity_id;
        $this->notes = $task->notes;
    }

    public function createTask()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            'assigned_to' => $this->assigned_to,
            'client_id' => $this->client_id ?: null,
            'project_id' => $this->project_id ?: null,
            'opportunity_id' => $this->opportunity_id ?: null,
            'notes' => $this->notes,
            'created_by' => \Illuminate\Support\Facades\Auth::id(),
        ];

        Task::create($data);

        $this->closeModals();
        $this->dispatch('task-created');
        session()->flash('message', 'Tarea creada exitosamente.');
    }

    public function updateTask()
    {
        $this->validate();

        if (!$this->editingTask) {
            return;
        }

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            'assigned_to' => $this->assigned_to,
            'client_id' => $this->client_id ?: null,
            'project_id' => $this->project_id ?: null,
            'opportunity_id' => $this->opportunity_id ?: null,
            'notes' => $this->notes,
            'updated_by' => \Illuminate\Support\Facades\Auth::id(),
        ];

        $this->editingTask->update($data);

        $this->closeModals();
        $this->dispatch('task-updated');
        session()->flash('message', 'Tarea actualizada exitosamente.');
    }

    public function deleteTask()
    {
        if (!$this->selectedTask) {
            return;
        }

        $this->selectedTask->delete();

        $this->closeModals();
        $this->dispatch('task-deleted');
        session()->flash('message', 'Tarea eliminada exitosamente.');
    }

    public function changeStatus($taskId, $newStatus)
    {
        $task = Task::find($taskId);
        if ($task) {
            $task->update(['status' => $newStatus]);
            $this->dispatch('task-status-changed');
            session()->flash('message', 'Estado de la tarea actualizado.');
        }
    }

    public function changePriority($taskId, $newPriority)
    {
        $task = Task::find($taskId);
        if ($task) {
            $task->update(['priority' => $newPriority]);
            $this->dispatch('task-priority-changed');
            session()->flash('message', 'Prioridad de la tarea actualizada.');
        }
    }

    public function render()
    {
        $query = Task::with(['assignedTo', 'client', 'project', 'opportunity', 'createdBy'])
            ->orderBy('due_date', 'asc');

        // Aplicar filtros
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        if ($this->assignedToFilter) {
            $query->where('assigned_to', $this->assignedToFilter);
        }

        if ($this->dueDateFilter) {
            switch ($this->dueDateFilter) {
                case 'today':
                    $query->whereDate('due_date', today());
                    break;
                case 'tomorrow':
                    $query->whereDate('due_date', now()->addDay());
                    break;
                case 'this_week':
                    $query->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'overdue':
                    $query->where('due_date', '<', now())->where('status', 'pendiente');
                    break;
            }
        }

        $tasks = $query->paginate(15);

        return view('livewire.tasks.task-list', [
            'tasks' => $tasks
        ]);
    }
}
