<?php

namespace App\Livewire\Opportunities;

use App\Services\OpportunityService;
use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class OpportunityList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $stageFilter = '';
    public $advisorFilter = '';
    public $projectFilter = '';
    public $clientFilter = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showStageModal = false;
    public $showWinModal = false;
    public $showLoseModal = false;
    public $selectedOpportunity = null;
    public $editingOpportunity = null;

    // Form fields
    public $client_id = '';
    public $project_id = '';
    public $unit_id = '';
    public $advisor_id = '';
    public $stage = 'captado';
    public $status = 'activa';
    public $probability = 10;
    public $expected_value = 0;
    public $expected_close_date = '';
    public $close_value = 0;
    public $close_reason = '';
    public $lost_reason = '';
    public $notes = '';
    public $source = '';
    public $campaign = '';

    // Stage advancement
    public $newStage = '';
    public $stageNotes = '';

    // Win/Lose
    public $winValue = 0;
    public $winReason = '';
    public $loseReason = '';

    protected $opportunityService;
    public $clients = [];
    public $projects = [];
    public $units = [];
    public $advisors = [];

    protected $rules = [
        'client_id' => 'required|exists:clients,id',
        'project_id' => 'required|exists:projects,id',
        'unit_id' => 'nullable|exists:units,id',
        'advisor_id' => 'required|exists:users,id',
        'stage' => 'required|in:captado,calificado,contacto,propuesta,visita,negociacion,cierre',
        'status' => 'required|in:activa,ganada,perdida,cancelada',
        'probability' => 'required|integer|min:0|max:100',
        'expected_value' => 'required|numeric|min:0',
        'expected_close_date' => 'required|date|after:today',
        'notes' => 'nullable|string',
        'source' => 'nullable|string|max:255',
        'campaign' => 'nullable|string|max:255'
    ];

    public function boot(OpportunityService $opportunityService)
    {
        $this->opportunityService = $opportunityService;
    }

    public function mount()
    {
        $this->clients = Client::all();
        $this->projects = Project::active()->get();
        $this->advisors = User::getAdvisorsAndAdmins();
        $this->expected_close_date = now()->addDays(30)->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedStageFilter()
    {
        $this->resetPage();
    }

    public function updatedProjectFilter()
    {
        $this->resetPage();
        $this->loadUnitsForProject();
    }

    public function updatedClientFilter()
    {
        $this->resetPage();
    }

    public function updatedAdvisorFilter()
    {
        $this->resetPage();
    }

    public function updatedProjectId()
    {
        $this->loadUnitsForProject();
    }

    public function loadUnitsForProject()
    {
        if ($this->project_id) {
            $this->units = Unit::where('project_id', $this->project_id)
                ->whereIn('status', ['disponible', 'reservado'])
                ->get();
        } else {
            $this->units = [];
        }
        $this->unit_id = '';
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($opportunityId)
    {
        $this->editingOpportunity = $this->opportunityService->getOpportunityById($opportunityId);
        if ($this->editingOpportunity) {
            $this->fillFormFromOpportunity($this->editingOpportunity);
            $this->showEditModal = true;
        }
    }

    public function openDeleteModal($opportunityId)
    {
        $this->selectedOpportunity = $this->opportunityService->getOpportunityById($opportunityId);
        $this->showDeleteModal = true;
    }

    public function openStageModal($opportunityId)
    {
        $this->selectedOpportunity = $this->opportunityService->getOpportunityById($opportunityId);
        $this->newStage = $this->selectedOpportunity->stage;
        $this->showStageModal = true;
    }

    public function openWinModal($opportunityId)
    {
        $this->selectedOpportunity = $this->opportunityService->getOpportunityById($opportunityId);
        $this->winValue = $this->selectedOpportunity->expected_value;
        $this->showWinModal = true;
    }

    public function openLoseModal($opportunityId)
    {
        $this->selectedOpportunity = $this->opportunityService->getOpportunityById($opportunityId);
        $this->showLoseModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->showStageModal = false;
        $this->showWinModal = false;
        $this->showLoseModal = false;
        $this->resetForm();
        $this->editingOpportunity = null;
        $this->selectedOpportunity = null;
        $this->newStage = '';
        $this->stageNotes = '';
        $this->winValue = 0;
        $this->winReason = '';
        $this->loseReason = '';
    }

    public function resetForm()
    {
        $this->client_id = '';
        $this->project_id = '';
        $this->unit_id = '';
        $this->advisor_id = '';
        $this->stage = 'captado';
        $this->status = 'activa';
        $this->probability = 10;
        $this->expected_value = 0;
        $this->expected_close_date = now()->addDays(30)->format('Y-m-d');
        $this->close_value = 0;
        $this->close_reason = '';
        $this->lost_reason = '';
        $this->notes = '';
        $this->source = '';
        $this->campaign = '';
        $this->units = [];
    }

    public function fillFormFromOpportunity($opportunity)
    {
        $this->client_id = $opportunity->client_id;
        $this->project_id = $opportunity->project_id;
        $this->unit_id = $opportunity->unit_id;
        $this->advisor_id = $opportunity->advisor_id;
        $this->stage = $opportunity->stage;
        $this->status = $opportunity->status;
        $this->probability = $opportunity->probability;
        $this->expected_value = $opportunity->expected_value;
        $this->expected_close_date = $opportunity->expected_close_date ? $opportunity->expected_close_date->format('Y-m-d') : '';
        $this->close_value = $opportunity->close_value;
        $this->close_reason = $opportunity->close_reason;
        $this->lost_reason = $opportunity->lost_reason;
        $this->notes = $opportunity->notes;
        $this->source = $opportunity->source;
        $this->campaign = $opportunity->campaign;

        $this->loadUnitsForProject();
    }

    public function createOpportunity()
    {
        $this->validate();

        $data = [
            'client_id' => $this->client_id,
            'project_id' => $this->project_id,
            'unit_id' => $this->unit_id ?: null,
            'advisor_id' => $this->advisor_id,
            'stage' => $this->stage,
            'status' => $this->status,
            'probability' => $this->probability,
            'expected_value' => $this->expected_value,
            'expected_close_date' => $this->expected_close_date,
            'notes' => $this->notes,
            'source' => $this->source,
            'campaign' => $this->campaign,
        ];

        $this->opportunityService->createOpportunity($data);

        $this->closeModals();
        $this->dispatch('opportunity-created');
        session()->flash('message', 'Oportunidad creada exitosamente.');
    }

    public function updateOpportunity()
    {
        $this->validate();

        if (!$this->editingOpportunity) {
            return;
        }

        $data = [
            'client_id' => $this->client_id,
            'project_id' => $this->project_id,
            'unit_id' => $this->unit_id ?: null,
            'advisor_id' => $this->advisor_id,
            'stage' => $this->stage,
            'status' => $this->status,
            'probability' => $this->probability,
            'expected_value' => $this->expected_value,
            'expected_close_date' => $this->expected_close_date,
            'notes' => $this->notes,
            'source' => $this->source,
            'campaign' => $this->campaign,
        ];

        $this->opportunityService->updateOpportunity($this->editingOpportunity->id, $data);

        $this->closeModals();
        $this->dispatch('opportunity-updated');
        session()->flash('message', 'Oportunidad actualizada exitosamente.');
    }

    public function deleteOpportunity()
    {
        if (!$this->selectedOpportunity) {
            return;
        }

        $this->opportunityService->deleteOpportunity($this->selectedOpportunity->id);

        $this->closeModals();
        $this->dispatch('opportunity-deleted');
        session()->flash('message', 'Oportunidad eliminada exitosamente.');
    }

    public function advanceStage()
    {
        if (!$this->selectedOpportunity || !$this->newStage) {
            return;
        }

        $this->opportunityService->advanceStage($this->selectedOpportunity->id, $this->newStage);

        $this->closeModals();
        $this->dispatch('opportunity-stage-advanced');
        session()->flash('message', 'Etapa de la oportunidad avanzada exitosamente.');
    }

    public function markAsWon()
    {
        if (!$this->selectedOpportunity || !$this->winValue) {
            return;
        }

        $this->opportunityService->markAsWon($this->selectedOpportunity->id, $this->winValue, $this->winReason);

        $this->closeModals();
        $this->dispatch('opportunity-won');
        session()->flash('message', '¡Oportunidad marcada como ganada!');
    }

    public function markAsLost()
    {
        if (!$this->selectedOpportunity || !$this->loseReason) {
            return;
        }

        $this->opportunityService->markAsLost($this->selectedOpportunity->id, $this->loseReason);

        $this->closeModals();
        $this->dispatch('opportunity-lost');
        session()->flash('message', 'Oportunidad marcada como perdida.');
    }

    public function updateProbability($opportunityId, $newProbability)
    {
        $this->opportunityService->updateProbability($opportunityId, $newProbability);
        $this->dispatch('opportunity-probability-updated');
        session()->flash('message', 'Probabilidad actualizada.');
    }

    public function updateExpectedValue($opportunityId, $newValue)
    {
        $this->opportunityService->updateExpectedValue($opportunityId, $newValue);
        $this->dispatch('opportunity-value-updated');
        session()->flash('message', 'Valor esperado actualizado.');
    }

    public function render()
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFilter,
            'stage' => $this->stageFilter,
            'advisor_id' => $this->advisorFilter,
            'project_id' => $this->projectFilter,
            'client_id' => $this->clientFilter,
        ];

        $opportunities = $this->opportunityService->getAllOpportunities(15, $filters);

        return view('livewire.opportunities.opportunity-list', [
            'opportunities' => $opportunities
        ]);
    }
}
