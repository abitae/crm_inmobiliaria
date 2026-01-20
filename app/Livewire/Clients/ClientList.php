<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Exports\ClientsExport;
use App\Services\ClientService;
use App\Services\ActivityService;
use App\Services\TaskService;
use App\Services\DocumentSearchService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Mary\Traits\Toast;

class ClientList extends Component
{
    use Toast;
    use WithPagination;
    // Filtros
    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $sourceFilter = '';
    public $advisorFilter = '';
    public $searchMinLength = 2;

    // Modales
    public $showFormModal = false;
    public $editingClient = null;
    public $showActivityModal = false;
    public $showTaskModal = false;
    public $selectedClientId = null;
    public $activityPage = 1;
    public $taskPage = 1;

    // Campos del formulario
    public $name = '';
    public $phone = '';
    public $document_type = 'DNI';
    public $document_number = '';
    public $address = '';
    public $birth_date = '';
    public $client_type = 'comprador';
    public $source = 'redes_sociales';
    public $status = 'nuevo';
    public $score = 0;
    public $notes = '';
    public $assigned_advisor_id = '';

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

    protected $clientService;
    protected $documentSearchService;
    protected $activityService;
    protected $taskService;
    public $advisors = [];
    public $searchingDocument = false;

    public function getRules(): array
    {
        $clientId = $this->editingClient ? $this->editingClient->id : null;
        return $this->clientService->getValidationRules($clientId);
    }
    public function getMessages(): array
    {
        return $this->clientService->getValidationMessages();
    }

    public function boot(
        ClientService $clientService,
        DocumentSearchService $documentSearchService,
        ActivityService $activityService,
        TaskService $taskService
    )
    {
        $this->clientService = $clientService;
        $this->documentSearchService = $documentSearchService;
        $this->activityService = $activityService;
        $this->taskService = $taskService;
    }

    public function mount()
    {
        $user = Auth::user();
        $cacheKey = 'available_advisors_' . $user->id;

        $this->advisors = Cache::remember($cacheKey, 300, function () use ($user) {
            return User::getAvailableAdvisors($user);
        });

        $this->advisorFilter = $user->id;
        $this->status = 'nuevo';
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
    public function updatedAdvisorFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'typeFilter', 'sourceFilter', 'advisorFilter']);
        $this->advisorFilter = Auth::user()->id;
        $this->resetPage();
    }

    public function openCreateModal($clientId = null)
    {
        try {
            if ($clientId) {
                $this->openEditModal($clientId);
            } else {
                $this->openNewModal();
            }
            $this->showFormModal = true;
        } catch (\Exception $e) {
            Log::error('Error al abrir modal de cliente', [
                'client_id' => $clientId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            $this->error('Error al abrir el formulario: ' . $e->getMessage());
        }
    }

    private function openEditModal($clientId): void
    {
        Log::info('Abriendo modal para editar cliente', [
            'client_id' => $clientId,
            'user_id' => Auth::id()
        ]);

        $this->editingClient = $this->clientService->getClientById($clientId);
        if (!$this->editingClient) {
            Log::warning('Cliente no encontrado al intentar editar', [
                'client_id' => $clientId,
                'user_id' => Auth::id()
            ]);
            $this->error('Cliente no encontrado.');
            return;
        }

        $this->fillFormFromClient($this->editingClient);
    }

    private function openNewModal(): void
    {
        Log::info('Abriendo modal para crear nuevo cliente', [
            'user_id' => Auth::id()
        ]);
        $this->resetForm();
        $this->editingClient = null;
    }


    public function closeModals()
    {
        $this->reset(['showFormModal', 'editingClient']);
        $this->resetForm();
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

    public function resetForm()
    {
        $this->reset([
            'name',
            'phone',
            'document_type',
            'document_number',
            'address',
            'birth_date',
            'client_type',
            'source',
            'status',
            'score',
            'notes',
            'assigned_advisor_id'
        ]);
        $this->document_type = 'DNI';
        $this->client_type = 'comprador';
        $this->source = 'redes_sociales';
        $this->status = 'nuevo';
        $this->score = 0;
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

    public function fillFormFromClient($client)
    {
        $this->name = $client->name;
        $this->phone = $client->phone;
        $this->document_type = $client->document_type;
        $this->document_number = $client->document_number;
        $this->address = $client->address;
        $this->birth_date = $client->birth_date ? $client->birth_date->format('Y-m-d') : '';
        $this->client_type = $client->client_type;
        $this->source = $client->source;
        $this->status = $client->status;
        $this->score = $client->score;
        $this->notes = $client->notes;
        $this->assigned_advisor_id = $client->assigned_advisor_id;
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

    public function createClient()
    {
        try {
            $this->validate($this->getRules(), $this->getMessages());

            $formData = $this->getFormData();

            Log::info('Intentando crear cliente', [
                'user_id' => Auth::id(),
                'document_number' => $formData['document_number'],
                'document_type' => $formData['document_type']
            ]);

            $client = $this->clientService->createClient($formData);

            Log::info('Cliente creado exitosamente', [
                'client_id' => $client->id,
                'user_id' => Auth::id()
            ]);

            $this->closeModals();
            $this->resetPage(); // Refrescar la lista
            $this->success('Cliente creado exitosamente.');
        } catch (ValidationException $e) {
            Log::warning('Error de validación al crear cliente', [
                'user_id' => Auth::id(),
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al crear cliente', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Error al crear el cliente: ' . $e->getMessage());
        }
    }

    public function updateClient()
    {
        try {
            if (!$this->editingClient) {
                Log::warning('Intento de actualizar cliente sin editingClient', [
                    'user_id' => Auth::id()
                ]);
                $this->warning('No se puede actualizar: cliente no seleccionado.');
                return;
            }

            $this->validate($this->getRules(), $this->getMessages());

            $formData = $this->getFormData();

            Log::info('Intentando actualizar cliente', [
                'client_id' => $this->editingClient->id,
                'user_id' => Auth::id(),
                'document_number' => $formData['document_number']
            ]);

            $this->clientService->updateClient($this->editingClient->id, $formData);

            Log::info('Cliente actualizado exitosamente', [
                'client_id' => $this->editingClient->id,
                'user_id' => Auth::id()
            ]);

            $this->closeModals();
            $this->resetPage(); // Refrescar la lista
            $this->success('Cliente actualizado exitosamente.');
        } catch (ValidationException $e) {
            Log::warning('Error de validación al actualizar cliente', [
                'client_id' => $this->editingClient->id ?? null,
                'user_id' => Auth::id(),
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al actualizar cliente', [
                'client_id' => $this->editingClient->id ?? null,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Error al actualizar el cliente: ' . $e->getMessage());
        }
    }


    public function changeStatus($clientId, $newStatus)
    {
        try {
            Log::info('Intentando cambiar estado de cliente', [
                'client_id' => $clientId,
                'new_status' => $newStatus,
                'user_id' => Auth::id()
            ]);

            $this->clientService->changeStatus($clientId, $newStatus);

            Log::info('Estado de cliente actualizado exitosamente', [
                'client_id' => $clientId,
                'new_status' => $newStatus,
                'user_id' => Auth::id()
            ]);

            $this->resetPage(); // Refrescar la lista
            $this->success('Estado del cliente actualizado.');
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de cliente', [
                'client_id' => $clientId,
                'new_status' => $newStatus,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    public function updateScore($clientId, $newScore)
    {
        try {
            Log::info('Intentando actualizar score de cliente', [
                'client_id' => $clientId,
                'new_score' => $newScore,
                'user_id' => Auth::id()
            ]);

            $this->clientService->updateScore($clientId, $newScore);

            Log::info('Score de cliente actualizado exitosamente', [
                'client_id' => $clientId,
                'new_score' => $newScore,
                'user_id' => Auth::id()
            ]);

            $this->resetPage(); // Refrescar la lista
            $this->success('Score del cliente actualizado.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar score de cliente', [
                'client_id' => $clientId,
                'new_score' => $newScore,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Error al actualizar el score: ' . $e->getMessage());
        }
    }

    private function getFormData(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'document_type' => $this->document_type,
            'document_number' => $this->document_number,
            'address' => $this->address,
            'birth_date' => $this->birth_date ?: null,
            'client_type' => $this->client_type,
            'source' => $this->source,
            'status' => $this->status,
            'score' => $this->score,
            'notes' => $this->notes,
            'assigned_advisor_id' => $this->assigned_advisor_id ?: Auth::user()->id,
        ];
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

    public function buscarDocumento()
    {
        $this->searchingDocument = true;

        try {
            $tipo = strtolower($this->document_type);
            $num_doc = trim($this->document_number);

            if (empty($num_doc)) {
                $this->error('Ingrese un número de documento');
                $this->searchingDocument = false;
                return;
            }

            // Validar formato antes de buscar
            if ($tipo !== 'dni' || strlen($num_doc) !== 8) {
                Log::warning('Formato de documento inválido para búsqueda', [
                    'document_type' => $tipo,
                    'document_number' => $num_doc,
                    'user_id' => Auth::id()
                ]);
                $this->error('Ingrese un número de DNI válido (8 dígitos)');
                $this->searchingDocument = false;
                return;
            }

            Log::info('Buscando documento en API externa', [
                'document_type' => $tipo,
                'document_number' => $num_doc,
                'user_id' => Auth::id()
            ]);

            $this->info('Buscando documento en la base de datos...');

            // Usar el servicio para buscar
            $result = $this->documentSearchService->searchAndProcessClient($tipo, $num_doc);

            if ($result['exists_in_db']) {
                $client = $result['client'];
                $advisorName = $client->assignedAdvisor ? $client->assignedAdvisor->name : 'Sin asignar';

                Log::info('Cliente ya existe en la base de datos', [
                    'client_id' => $client->id,
                    'document_type' => $tipo,
                    'document_number' => $num_doc,
                    'assigned_advisor' => $advisorName,
                    'user_id' => Auth::id()
                ]);

                $this->warning('Cliente ya existe en la base de datos, asesor asignado: ' . $advisorName);
                $this->searchingDocument = false;
                return;
            }

            if ($result['found'] && $result['data']) {
                $clientData = $this->documentSearchService->extractClientData($result['data']);
                $this->fillClientDataFromApi($clientData);

                Log::info('Cliente encontrado en API externa', [
                    'document_type' => $tipo,
                    'document_number' => $num_doc,
                    'name' => $this->name,
                    'user_id' => Auth::id()
                ]);

                $this->success('Cliente encontrado: ' . $this->name);
            } else {
                Log::warning('Cliente no encontrado en API externa', [
                    'document_type' => $tipo,
                    'document_number' => $num_doc,
                    'user_id' => Auth::id()
                ]);
                $this->error('Documento no encontrado en la base de datos. Por favor, complete los datos manualmente.');
            }
        } catch (\Exception $e) {
            Log::error('Error al buscar documento', [
                'document_type' => $this->document_type,
                'document_number' => $this->document_number,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Error al buscar el documento: ' . $e->getMessage());
        } finally {
            $this->searchingDocument = false;
        }
    }

    private function fillClientDataFromApi(array $clientData): void
    {
        $this->document_type = 'DNI';
        $this->name = $clientData['name'] ?? '';
        $this->birth_date = $clientData['birth_date'] ?? null;
    }

    public function clearSearchData(): void
    {
        $this->name = '';
        $this->birth_date = '';
    }
    public function render()
    {
        $filters = $this->buildFilters();

        $clients = $this->clientService->getAllClients(15, $filters);

        return view('livewire.clients.client-list', [
            'clients' => $clients,
            'clientActivities' => $this->getClientActivitiesPaginator(),
            'clientTasks' => $this->getClientTasksPaginator(),
        ]);
    }

    public function exportClients()
    {
        try {
            $filters = $this->buildFilters();
            $clients = $this->clientService->getClientsForExport($filters);

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

        return [
            'search' => $searchIsReady ? $search : '',
            'status' => $this->statusFilter,
            'type' => $this->typeFilter,
            'source' => $this->sourceFilter,
            'advisor_id' => $this->advisorFilter,
        ];
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
