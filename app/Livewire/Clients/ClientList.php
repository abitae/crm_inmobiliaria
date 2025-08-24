<?php

namespace App\Livewire\Clients;

use App\Services\ClientService;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ClientList extends Component
{
    use WithPagination;

    // Filtros
    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $sourceFilter = '';
    public $advisorFilter = '';

    // Modales
    public $showFormModal = false;
    public $showDeleteModal = false;
    public $selectedClient = null;
    public $editingClient = null;

    // Campos del formulario
    public $name = '';
    public $email = '';
    public $phone = '';
    public $document_type = '';
    public $document_number = '';
    public $address = '';
    public $district = '';
    public $province = '';
    public $region = '';
    public $country = '';
    public $client_type = '';
    public $source = '';
    public $status = '';
    public $score = 0;
    public $notes = '';
    public $assigned_advisor_id = '';

    protected $clientService;
    public $advisors = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'document_type' => 'required|in:DNI,RUC,CE,PASAPORTE',
        'document_number' => 'required|string|max:20',
        'address' => 'nullable|string|max:500',
        'district' => 'nullable|string|max:255',
        'province' => 'nullable|string|max:255',
        'region' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'client_type' => 'required|in:inversor,comprador,empresa,constructor',
        'source' => 'required|in:redes_sociales,ferias,referidos,formulario_web,publicidad',
        'status' => 'required|in:nuevo,contacto_inicial,en_seguimiento,cierre,perdido',
        'score' => 'required|integer|min:0|max:100',
        'notes' => 'nullable|string',
        'assigned_advisor_id' => 'nullable|exists:users,id'
    ];

    public function boot(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function mount()
    {
        $this->advisors = User::getAdvisorsAndAdmins();
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

    public function openDeleteModal($clientId)
    {
        $this->selectedClient = $this->clientService->getClientById($clientId);
        $this->showDeleteModal = true;
    }

    public function closeModals()
    {
        $this->reset(['showFormModal', 'showDeleteModal', 'editingClient', 'selectedClient']);
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'email',
            'phone',
            'document_type',
            'document_number',
            'address',
            'district',
            'province',
            'region',
            'country',
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
        $this->email = $client->email;
        $this->phone = $client->phone;
        $this->document_type = $client->document_type;
        $this->document_number = $client->document_number;
        $this->address = $client->address;
        $this->district = $client->district;
        $this->province = $client->province;
        $this->region = $client->region;
        $this->country = $client->country;
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
        $this->dispatch('client-created');
        session()->flash('message', 'Cliente creado exitosamente.');
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
        $this->dispatch('client-updated');
        session()->flash('message', 'Cliente actualizado exitosamente.');
    }

    public function deleteClient()
    {
        if (!$this->selectedClient) {
            return;
        }

        $this->clientService->deleteClient($this->selectedClient->id);

        $this->closeModals();
        $this->dispatch('client-deleted');
        session()->flash('message', 'Cliente eliminado exitosamente.');
    }

    public function changeStatus($clientId, $newStatus)
    {
        $this->clientService->changeStatus($clientId, $newStatus);
        $this->dispatch('client-status-changed');
        session()->flash('message', 'Estado del cliente actualizado.');
    }

    public function updateScore($clientId, $newScore)
    {
        $this->clientService->updateScore($clientId, $newScore);
        $this->dispatch('client-score-updated');
        session()->flash('message', 'Score del cliente actualizado.');
    }

    private function getFormData(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'document_type' => $this->document_type,
            'document_number' => $this->document_number,
            'address' => $this->address,
            'district' => $this->district,
            'province' => $this->province,
            'region' => $this->region,
            'country' => $this->country,
            'client_type' => $this->client_type,
            'source' => $this->source,
            'status' => $this->status,
            'score' => $this->score,
            'notes' => $this->notes,
            'assigned_advisor_id' => $this->assigned_advisor_id ?: null,
        ];
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
