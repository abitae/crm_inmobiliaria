<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Services\ClientService;
use App\Models\User;
use App\Traits\ClientFormTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente Livewire refactorizado para la lista de clientes
 * Demuestra el uso del ClientFormTrait y ClientService mejorado
 */
class ClientListRefactored extends Component
{
    use WithPagination, ClientFormTrait;
    
    // Filtros
    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $sourceFilter = '';
    public $advisorFilter = '';

    // Modales
    public $showFormModal = false;
    public $editingClient = null;

    protected $clientService;
    public $advisors = [];

    public function boot(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function mount()
    {
        $this->advisors = User::getAvailableAdvisors(Auth::user());
        $user = Auth::user();
        $this->advisorFilter = ($user->isAdmin() || $user->isLider()) ? '' : $user->id;
        $this->setDefaultValues();
    }

    // Métodos para resetear paginación cuando cambian los filtros
    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }
    public function updatedTypeFilter() { $this->resetPage(); }
    public function updatedSourceFilter() { $this->resetPage(); }
    public function updatedAdvisorFilter() { $this->resetPage(); }

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

    public function createClient()
    {
        $this->validate();

        $formData = $this->prepareFormData();
        $this->clientService->createClient($formData);

        $this->closeModals();
        $this->dispatch('show-success', message: 'Cliente creado exitosamente.');
    }

    public function updateClient()
    {
        $this->validate();

        if (!$this->editingClient) {
            return;
        }

        $formData = $this->prepareFormData();
        $this->clientService->updateClient($this->editingClient->id, $formData);

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

    public function deleteClient($clientId)
    {
        $this->clientService->deleteClient($clientId);
        $this->dispatch('show-success', message: 'Cliente eliminado exitosamente.');
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
