<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Exports\ClientsExport;
use App\Services\Clients\ClientServiceWebDatero;
use App\Services\ActivityService;
use App\Services\TaskService;
use App\Services\ReservationService;
use App\Models\User;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ClientListDatero extends Component
{
    use Toast;
    use WithPagination;

    // Filtros
    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $sourceFilter = '';
    public $cityFilter = '';
    public $vendedorFilter = '';
    public $searchMinLength = 2;

    // Modales
    public $showActivityModal = false;
    public $showTaskModal = false;
    public $showReservationModal = false;
    public $selectedClientId = null;
    public $activityPage = 1;
    public $taskPage = 1;

    // Campos de actividad
    public $activity_title = '';
    public $activity_type = 'llamada';
    public $activity_status = 'programada';
    public $activity_priority = 'media';
    public $activity_start_date = '';
    public $activity_assigned_to = '';
    public $activity_notes = '';

    // Campos de tarea
    public $task_title = '';
    public $task_type = 'seguimiento';
    public $task_status = 'pendiente';
    public $task_priority = 'media';
    public $task_due_date = '';
    public $task_assigned_to = '';
    public $task_notes = '';

    // Campos de reserva
    public $reservationClientId = null;
    public $reservationClientName = '';
    public $reservation_project_id = '';
    public $reservation_unit_id = '';
    public $reservation_amount = 0;
    public $reservation_projects = [];
    public $reservation_units = [];

    protected $clientService;
    protected $activityService;
    protected $taskService;
    protected $reservationService;
    /** @var \Illuminate\Support\Collection Vendedores (y "Dateros directos" para líder) a cargo, igual que DaterosList */
    public $vendedores = [];
    /** @var \Illuminate\Support\Collection Dateros a cargo, para asignar en formularios/actividades/tareas */
    public $advisors = [];
    public $cities = [];

    public function boot(
        ClientServiceWebDatero $clientService,
        ActivityService $activityService,
        TaskService $taskService,
        ReservationService $reservationService
    ) {
        $this->clientService = $clientService;
        $this->activityService = $activityService;
        $this->taskService = $taskService;
        $this->reservationService = $reservationService;
    }

    public function mount()
    {
        $user = Auth::user();

        $dateros = User::where('lider_id', $user->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'datero'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->vendedores = $dateros->values();
        $this->cities = City::orderBy('name')->get(['id', 'name']);

        $this->vendedorFilter = '';
        $this->reservation_projects = $this->reservationService->getActiveProjects();
    }

    // Métodos para resetear paginación cuando cambian los filtros
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }
    public function updatedTypeFilter()
    {
        $this->resetPage();
    }
    public function updatedSourceFilter()
    {
        $this->resetPage();
    }
    public function updatedCityFilter()
    {
        $this->resetPage();
    }
    public function updatedVendedorFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'typeFilter', 'sourceFilter', 'cityFilter', 'vendedorFilter']);
        $this->vendedorFilter = '';
        $this->resetPage();
    }

    public function closeActionModals(): void
    {
        $this->reset(['showActivityModal', 'showTaskModal', 'selectedClientId']);
        $this->resetPage('activityPage');
        $this->resetPage('taskPage');
        $this->resetActivityForm();
        $this->resetTaskForm();
        $this->resetErrorBag();
    }

    public function closeReservationModal(): void
    {
        $this->reset(['showReservationModal', 'reservationClientId', 'reservationClientName']);
        $this->resetReservationForm();
        $this->resetErrorBag();
    }

    private function resetReservationForm(): void
    {
        $this->reset([
            'reservation_project_id',
            'reservation_unit_id',
            'reservation_amount',
            'reservation_units',
        ]);
        $this->reservation_amount = 0;
    }

    private function resetActivityForm(): void
    {
        $this->reset([
            'activity_title',
            'activity_type',
            'activity_status',
            'activity_priority',
            'activity_start_date',
            'activity_assigned_to',
            'activity_notes',
        ]);
        $this->activity_type = 'llamada';
        $this->activity_status = 'programada';
        $this->activity_priority = 'media';
        $this->activity_start_date = Carbon::now()->format('Y-m-d\TH:i');
    }

    private function resetTaskForm(): void
    {
        $this->reset([
            'task_title',
            'task_type',
            'task_status',
            'task_priority',
            'task_due_date',
            'task_assigned_to',
            'task_notes',
        ]);
        $this->task_type = 'seguimiento';
        $this->task_status = 'pendiente';
        $this->task_priority = 'media';
    }

    public function openActivityModal(int $clientId): void
    {
        $this->resetErrorBag();
        $this->resetActivityForm();
        $this->selectedClientId = $clientId;
        $this->activity_assigned_to = $this->getClientAssignedAdvisorId($clientId) ?? '';
        $this->resetPage('activityPage');
        $this->showActivityModal = true;
    }

    public function openTaskModal(int $clientId): void
    {
        $this->resetErrorBag();
        $this->resetTaskForm();
        $this->selectedClientId = $clientId;
        $this->task_assigned_to = $this->getClientAssignedAdvisorId($clientId) ?? '';
        $this->resetPage('taskPage');
        $this->showTaskModal = true;
    }

    public function openReservationModal(int $clientId): void
    {
        $this->resetErrorBag();
        $this->resetReservationForm();
        $this->reservationClientId = $clientId;
        $client = Client::select('name')->find($clientId);
        $this->reservationClientName = $client ? $client->name : '';
        $this->reservation_units = [];
        $this->showReservationModal = true;
    }

    public function updatedReservationProjectId(): void
    {
        if ($this->reservation_project_id) {
            $this->reservation_units = $this->reservationService->getAvailableUnitsForProject($this->reservation_project_id);
        } else {
            $this->reservation_units = [];
        }

        $this->reservation_unit_id = '';
    }

    public function createActivity(): void
    {
        if (!$this->selectedClientId) {
            $this->warning('No se encontro cliente para la actividad.');
            return;
        }

        $this->validate($this->getActivityRules(), $this->getActivityMessages());

        $data = $this->getActivityData();
        $data['client_id'] = $this->selectedClientId;

        try {
            $this->activityService->createActivity($data, Auth::id());
            $this->showActivityModal = false;
            $this->closeActionModals();
            $this->resetPage();
            $this->success('Actividad creada correctamente.');
        } catch (ValidationException $e) {
            $this->error('Error de validacion al crear la actividad.');
        } catch (\Exception $e) {
            $this->error('Error al crear la actividad: ' . $e->getMessage());
        }
    }

    public function createTask(): void
    {
        if (!$this->selectedClientId) {
            $this->warning('No se encontro cliente para la tarea.');
            return;
        }

        $this->validate($this->getTaskRules(), $this->getTaskMessages());

        $data = $this->getTaskData();
        $data['client_id'] = $this->selectedClientId;

        try {
            $this->taskService->createTask($data, Auth::id());
            $this->showTaskModal = false;
            $this->closeActionModals();
            $this->resetPage();
            $this->success('Tarea creada correctamente.');
        } catch (ValidationException $e) {
            $this->error('Error de validacion al crear la tarea.');
        } catch (\Exception $e) {
            $this->error('Error al crear la tarea: ' . $e->getMessage());
        }
    }

    public function createReservationFromClient(): void
    {
        if (!$this->reservationClientId) {
            $this->warning('No se encontro cliente para la reserva.');
            return;
        }

        $this->validate($this->getReservationRules(), $this->getReservationMessages());

        try {
            $this->reservationService->createReservation([
                'client_id' => $this->reservationClientId,
                'project_id' => $this->reservation_project_id,
                'unit_id' => $this->reservation_unit_id,
                'advisor_id' => Auth::id(),
                'reservation_amount' => $this->reservation_amount,
            ], Auth::id());

            $this->closeReservationModal();
            $this->resetPage();
            $this->success('Reserva creada exitosamente en estado activa.');
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());
        } catch (\Exception $e) {
            $this->error('Error al crear la reserva: ' . $e->getMessage());
        }
    }

    private function getActivityData(): array
    {
        return [
            'title' => $this->activity_title,
            'activity_type' => $this->activity_type,
            'status' => $this->activity_status,
            'priority' => $this->activity_priority,
            'start_date' => $this->activity_start_date,
            'assigned_to' => $this->activity_assigned_to ?: null,
            'notes' => $this->activity_notes,
        ];
    }

    private function getTaskData(): array
    {
        return [
            'title' => $this->task_title,
            'task_type' => $this->task_type,
            'status' => $this->task_status,
            'priority' => $this->task_priority,
            'due_date' => $this->task_due_date ?: null,
            'assigned_to' => $this->task_assigned_to ?: null,
            'notes' => $this->task_notes,
        ];
    }

    private function getActivityRules(): array
    {
        return [
            'activity_title' => 'required|string|max:255',
            'activity_type' => 'required|in:llamada,reunion,visita,seguimiento,tarea',
            'activity_status' => 'required|in:programada,en_progreso,completada,cancelada',
            'activity_priority' => 'required|in:baja,media,alta,urgente',
            'activity_start_date' => 'required|date',
            'activity_assigned_to' => 'nullable|exists:users,id',
            'activity_notes' => 'nullable|string',
        ];
    }

    private function getActivityMessages(): array
    {
        return [
            'activity_title.required' => 'El titulo es obligatorio.',
            'activity_type.required' => 'El tipo es obligatorio.',
            'activity_status.required' => 'El estado es obligatorio.',
            'activity_priority.required' => 'La prioridad es obligatoria.',
            'activity_start_date.required' => 'La fecha es obligatoria.',
        ];
    }

    private function getTaskRules(): array
    {
        return [
            'task_title' => 'required|string|max:255',
            'task_type' => 'required|in:seguimiento,visita,llamada,documento,otros',
            'task_status' => 'required|in:pendiente,en_progreso,completada,cancelada',
            'task_priority' => 'required|in:baja,media,alta,urgente',
            'task_due_date' => 'nullable|date',
            'task_assigned_to' => 'nullable|exists:users,id',
            'task_notes' => 'nullable|string',
        ];
    }

    private function getTaskMessages(): array
    {
        return [
            'task_title.required' => 'El titulo es obligatorio.',
            'task_type.required' => 'El tipo es obligatorio.',
            'task_status.required' => 'El estado es obligatorio.',
            'task_priority.required' => 'La prioridad es obligatoria.',
        ];
    }

    private function getReservationRules(): array
    {
        return [
            'reservation_project_id' => 'required|exists:projects,id',
            'reservation_unit_id' => 'required|exists:units,id',
            'reservation_amount' => 'required|numeric|min:0',
        ];
    }

    private function getReservationMessages(): array
    {
        return [
            'reservation_project_id.required' => 'El proyecto es obligatorio.',
            'reservation_unit_id.required' => 'La unidad es obligatoria.',
            'reservation_amount.required' => 'El monto es obligatorio.',
        ];
    }

    private function getClientAssignedAdvisorId(int $clientId): ?int
    {
        $client = Client::select('assigned_advisor_id')->find($clientId);
        if (!$client) {
            return null;
        }

        return $client->assigned_advisor_id;
    }

    private function getClientActivitiesPaginator()
    {
        if (!$this->showActivityModal || !$this->selectedClientId) {
            return collect();
        }

        return $this->activityService->getClientActivitiesPaginated($this->selectedClientId, 5, 'activityPage');
    }

    private function getClientTasksPaginator()
    {
        if (!$this->showTaskModal || !$this->selectedClientId) {
            return collect();
        }

        return $this->taskService->getClientTasksPaginated($this->selectedClientId, 5, 'taskPage');
    }


    public function render()
    {
        $filters = $this->buildFilters();

        // Usar el nuevo método para obtener solo clientes de dateros
        $clients = $this->clientService->getClientsByDateros(15, $filters);
        return view('livewire.clients.client-list-datero', [
            'clients' => $clients,
            'clientActivities' => $this->getClientActivitiesPaginator(),
            'clientTasks' => $this->getClientTasksPaginator(),
        ]);
    }

    public function exportClients()
    {
        try {
            $filters = $this->buildFilters();
            $clients = $this->clientService->getClientsByDaterosForExport($filters);

            $filename = 'clientes_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            $this->success('Exportacion iniciada. El archivo se descargara automaticamente.');
            return Excel::download(new ClientsExport($clients), $filename);
        } catch (\Exception $e) {
            Log::error('Error al exportar clientes', [
                'filters' => $this->buildFilters(),
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Error al exportar clientes: ' . $e->getMessage());
        }
    }

    private function buildFilters(): array
    {
        $search = $this->normalizeSearch($this->search);
        $length = function_exists('mb_strlen') ? mb_strlen($search) : strlen($search);
        $searchIsReady = $search !== '' && $length >= $this->searchMinLength;

        $filters = [
            'search' => $searchIsReady ? $search : '',
            'status' => $this->statusFilter,
            'type' => $this->typeFilter,
            'source' => $this->sourceFilter,
            'city_id' => $this->cityFilter ?: '',
            'datero_id' => $this->vendedorFilter ?: '',
        ];
        return $filters;
    }

    private function normalizeSearch(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        $value = preg_replace('/\s+/', ' ', $value);

        if (function_exists('mb_strtolower')) {
            return mb_strtolower($value);
        }

        return strtolower($value);
    }
}
