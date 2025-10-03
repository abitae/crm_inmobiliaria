<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Services\ClientService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\SearchDocument;
class ClientList extends Component
{
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
    public $client_type = 'Comprador';
    public $source = 'Redes Sociales';
    public $status = 'nuevo';
    public $score = 0;
    public $notes = '';
    public $assigned_advisor_id = '';

    protected $clientService;
    public $advisors = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'document_type' => 'required|in:DNI,RUC,CE,PASAPORTE',
        'document_number' => 'required|string|max:20',
        'address' => 'nullable|string|max:500',
        'birth_date' => 'nullable|date',
        'client_type' => 'required|in:inversor,comprador,empresa,constructor',
        'source' => 'required|in:redes_sociales,ferias,referidos,formulario_web,publicidad',
        'status' => 'required|in:nuevo,contacto_inicial,en_seguimiento,cierre,perdido',
        'score' => 'required|integer|min:0|max:100',
        'notes' => 'nullable|string',
        'assigned_advisor_id' => 'nullable|exists:users,id'
    ];
    protected $messages = [
        'document_number.unique' => 'El número de documento ya está en uso.',
        'birth_date.date' => 'La fecha de nacimiento debe ser una fecha válida.',
        'client_type.required' => 'El tipo de cliente es obligatorio.',
        'client_type.in' => 'El tipo de cliente seleccionado no es válido.',
        'source.required' => 'El origen es obligatorio.',
        'source.in' => 'El origen seleccionado no es válido.',
        'status.required' => 'El estado es obligatorio.',
        'status.in' => 'El estado seleccionado no es válido.',
        'score.required' => 'La puntuación es obligatoria.',
        'score.integer' => 'La puntuación debe ser un número entero.',
        'score.min' => 'La puntuación debe ser al menos 0.',
        'score.max' => 'La puntuación no puede exceder 100.',
        'notes.string' => 'Las notas deben ser una cadena de texto.',
        'assigned_advisor_id.exists' => 'El asesor seleccionado no existe.',
        'document_type.required' => 'El tipo de documento es obligatorio.',
        'document_type.in' => 'El tipo de documento seleccionado no es válido.',
        'document_number.required' => 'El número de documento es obligatorio.',
        'document_number.string' => 'El número de documento debe ser una cadena de texto.',
        'document_number.max' => 'El número de documento no puede exceder 20 caracteres.',
        'address.string' => 'La dirección debe ser una cadena de texto.',
        'address.max' => 'La dirección no puede exceder 500 caracteres.',
        'name.required' => 'El nombre es obligatorio.',
        'name.string' => 'El nombre debe ser una cadena de texto.',
        'name.max' => 'El nombre no puede exceder 255 caracteres.',
        'phone.string' => 'El teléfono debe ser una cadena de texto.',
        'phone.max' => 'El teléfono no puede exceder 20 caracteres.',
        'assigned_advisor_id.required' => 'El asesor es obligatorio.',
    ];

    public function boot(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function mount()
    {
        $this->advisors = User::getAvailableAdvisors(Auth::user());
        $user = Auth::user();
        $this->advisorFilter = ($user->isAdmin() || $user->isLider()) ? '' : $user->id;
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
        $this->resetPage();
    }

    public function openCreateModal($clientId = null)
    {
        if ($clientId) {
            $this->editingClient = $this->clientService->getClientById($clientId);
            if ($this->editingClient) {
                $this->fillFormFromClient($this->editingClient);
            }
        } else {
            $this->resetForm();
            $this->editingClient = null;
        }
        $this->showFormModal = true;
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
        $this->validate();

        $data = $this->getFormData();
        $this->clientService->createClient($data);

        $this->closeModals();
        $this->dispatch('show-success', message: 'Cliente creado exitosamente.');
    }

    public function updateClient()
    {
        $this->validate();

        if (!$this->editingClient) {
            return;
        }

        $data = $this->getFormData();
        $this->clientService->updateClient($this->editingClient->id, $data);

        $this->closeModals();
        $this->dispatch('show-success', message: 'Cliente actualizado exitosamente.');
    }


    public function changeStatus($clientId, $newStatus)
    {
        $this->clientService->changeStatus($clientId, $newStatus);
        $this->dispatch('show-success', message: 'Estado del cliente actualizado.');
    }

    public function updateScore($clientId, $newScore)
    {
        $this->clientService->updateScore($clientId, $newScore);
        $this->dispatch('show-success', message: 'Score del cliente actualizado.');
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
            'assigned_advisor_id' => $this->assigned_advisor_id ?: null,
        ];
    }
    public function searchClient()
    {
        $tipo = strtolower($this->document_type);
        $num_doc = $this->document_number;
        
        // Verificar si el cliente ya existe
        if ($this->clientExists($tipo, $num_doc)) {
            return;
        }
        
        if ($tipo === 'dni' && strlen($num_doc) === 8) {
            $this->searchClientData($tipo, $num_doc);
        } else {
            $this->dispatch('show-error', message: 'Ingrese un número de documento válido');
        }
    }
    private function clientExists(string $tipo, string $num_doc): bool
    {
        $client = Client::where('document_number', $num_doc)
            ->where('document_type', $tipo)
            ->first();
            
        if ($client) {
            $this->dispatch('show-error', message: 'Cliente ya existe en la base de datos, asesor asignado: ' . $client->assignedAdvisor->name);
            return true;
        }
        
        return false;
    }
    private function searchClientData(string $tipo, string $num_doc): void
    {
        $result = $this->searchComplete($tipo, $num_doc);
        
        if ($result['encontrado']) {
            $this->fillClientData($result['data']);
            $this->dispatch('show-success', message: 'Cliente encontrado: ' . $this->name);
        } else {
            $this->dispatch('show-error', message: 'No encontrado');
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
