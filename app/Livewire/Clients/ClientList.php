<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Services\ClientService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\SearchDocument;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Mary\Traits\Toast;

class ClientList extends Component
{
    use Toast;
    use WithPagination;
    use SearchDocument;
    // Filtros
    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $sourceFilter = '';
    public $advisorFilter = '';

    // Modales
    public $showFormModal = false;
    public $editingClient = null;

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

    protected $clientService;
    public $advisors = [];

    public function getRules(): array
    {
        $clientId = $this->editingClient ? $this->editingClient->id : null;
        return $this->clientService->getValidationRules($clientId);
    }
    public function getMessages(): array
    {
        return $this->clientService->getValidationMessages();
    }

    public function boot(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function mount()
    {
        $this->advisors = User::getAvailableAdvisors(Auth::user());
        $user = Auth::user();
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
                Log::info('Abriendo modal para editar cliente', [
                    'client_id' => $clientId,
                    'user_id' => Auth::id()
                ]);
                
                $this->editingClient = $this->clientService->getClientById($clientId);
                if ($this->editingClient) {
                    $this->fillFormFromClient($this->editingClient);
                } else {
                    Log::warning('Cliente no encontrado al intentar editar', [
                        'client_id' => $clientId,
                        'user_id' => Auth::id()
                    ]);
                    $this->error('Cliente no encontrado.');
                    return;
                }
            } else {
                Log::info('Abriendo modal para crear nuevo cliente', [
                    'user_id' => Auth::id()
                ]);
                $this->resetForm();
                $this->editingClient = null;
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


    public function closeModals()
    {
        $this->reset(['showFormModal', 'editingClient']);
        $this->resetForm();
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
    public function buscarDocumento()
    {
        try {
            $tipo = strtolower($this->document_type);
            $num_doc = trim($this->document_number);
            
            if (empty($num_doc)) {
                $this->error('Ingrese un número de documento');
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
                return;
            }
            
            Log::info('Buscando documento en API externa', [
                'document_type' => $tipo,
                'document_number' => $num_doc,
                'user_id' => Auth::id()
            ]);
            
            $this->info('Buscando documento en la base de datos...');
            
            // Verificar si el cliente ya existe
            if ($this->clientExists($tipo, $num_doc)) {
                return;
            }
            
            // Buscar en API externa
            $this->searchClientData($tipo, $num_doc);
        } catch (\Exception $e) {
            Log::error('Error al buscar documento', [
                'document_type' => $this->document_type,
                'document_number' => $this->document_number,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Error al buscar el documento: ' . $e->getMessage());
        }
    }
    private function clientExists(string $tipo, string $num_doc): bool
    {
        try {
            // El servicio ya carga la relación assignedAdvisor
            $client = $this->clientService->clientExists($tipo, $num_doc);
                
            if ($client) {
                $advisorName = $client->assignedAdvisor ? $client->assignedAdvisor->name : 'Sin asignar';
                
                Log::info('Cliente ya existe en la base de datos', [
                    'client_id' => $client->id,
                    'document_type' => $tipo,
                    'document_number' => $num_doc,
                    'assigned_advisor' => $advisorName,
                    'user_id' => Auth::id()
                ]);
                
                $this->warning('Cliente ya existe en la base de datos, asesor asignado: ' . $advisorName);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Error al verificar existencia de cliente', [
                'document_type' => $tipo,
                'document_number' => $num_doc,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    private function searchClientData(string $tipo, string $num_doc): void
    {
        try {
            $result = $this->searchComplete($tipo, $num_doc);
            
            if ($result['encontrado'] ?? false) {
                $this->fillClientData($result['data']);
                
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
            Log::error('Error al buscar datos del cliente en API', [
                'document_type' => $tipo,
                'document_number' => $num_doc,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Error al buscar los datos del cliente. Por favor, complete los datos manualmente.');
        }
    }
    private function fillClientData(object $data): void
    {
        $this->document_type = 'DNI';
        $this->name = $data->nombre ?? '';
        
        // Verificar fecha_nacimiento en diferentes formatos posibles
        $fechaNacimiento = null;
        if (isset($data->fecha_nacimiento)) {
            $fechaNacimiento = $data->fecha_nacimiento;
        } elseif (isset($data->fechaNacimiento)) {
            $fechaNacimiento = $data->fechaNacimiento;
        } elseif (isset($data->api->result->fechaNacimiento)) {
            $fechaNacimiento = $data->api->result->fechaNacimiento;
        }
        
        $this->birth_date = $fechaNacimiento ? $this->parseApiBirthDate($fechaNacimiento) : null;
    }
    private function parseApiBirthDate(?string $fecha_nacimiento): ?string
    {
        if (empty($fecha_nacimiento)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $fecha_nacimiento)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($fecha_nacimiento)->format('Y-m-d');
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
    public function render()
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFilter,
            'type' => $this->typeFilter,
            'source' => $this->sourceFilter,
            'advisor_id' => $this->advisorFilter,
        ];

        $clients = $this->clientService->getAllClients(15, $filters);

        return view('livewire.clients.client-list', [
            'clients' => $clients
        ]);
    }
}
