<?php

namespace App\Livewire\Opportunities;

use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Services\OpportunityService;
use Livewire\Component;

class OpportunityForm extends Component
{
    public $showModal = false;
    public $isEditing = false;
    public $opportunityId = null;

    protected $listeners = [
        'edit-opportunity' => 'openEditModal'
    ];

    // Campos del formulario
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

    // Datos para los selects
    public $clients = [];
    public $projects = [];
    public $units = [];
    public $advisors = [];

    protected $opportunityService;

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

    protected $messages = [
        'client_id.required' => 'El cliente es obligatorio.',
        'project_id.required' => 'El proyecto es obligatorio.',
        'advisor_id.required' => 'El asesor es obligatorio.',
        'stage.required' => 'La etapa es obligatoria.',
        'status.required' => 'El estado es obligatorio.',
        'probability.required' => 'La probabilidad es obligatoria.',
        'probability.integer' => 'La probabilidad debe ser un número entero.',
        'probability.min' => 'La probabilidad debe ser mayor o igual a 0.',
        'probability.max' => 'La probabilidad debe ser menor o igual a 100.',
        'expected_value.required' => 'El valor esperado es obligatorio.',
        'expected_value.numeric' => 'El valor esperado debe ser un número.',
        'expected_value.min' => 'El valor esperado debe ser mayor a 0.',
        'expected_close_date.required' => 'La fecha de cierre esperada es obligatoria.',
        'expected_close_date.date' => 'La fecha de cierre esperada debe ser una fecha válida.',
        'expected_close_date.after' => 'La fecha de cierre esperada debe ser posterior a hoy.',
    ];

    public function boot(OpportunityService $opportunityService)
    {
        $this->opportunityService = $opportunityService;
    }

    public function mount()
    {
        $this->loadFormData();
        $this->expected_close_date = now()->addDays(30)->format('Y-m-d');
    }

    public function loadFormData()
    {
        $this->clients = Client::orderBy('name')->get();
        $this->projects = Project::active()->orderBy('name')->get();
        $this->advisors = User::getAdvisorsAndAdmins();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal($opportunityId)
    {
        $opportunity = $this->opportunityService->getOpportunityById($opportunityId);
        if ($opportunity) {
            $this->fillFormFromOpportunity($opportunity);
            $this->opportunityId = $opportunityId;
            $this->isEditing = true;
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('opportunity-modal-closed');
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
        $this->opportunityId = null;
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

    public function updatedProjectId()
    {
        $this->loadUnitsForProject();
    }

    public function loadUnitsForProject()
    {
        if ($this->project_id) {
            $this->units = Unit::where('project_id', $this->project_id)
                ->whereIn('status', ['disponible', 'reservado'])
                ->orderBy('name')
                ->get();
        } else {
            $this->units = [];
        }
        $this->unit_id = '';
    }

    public function save()
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

        if ($this->isEditing) {
            $this->opportunityService->updateOpportunity($this->opportunityId, $data);
            $this->dispatch('opportunity-updated');
            session()->flash('message', 'Oportunidad actualizada exitosamente.');
        } else {
            $this->opportunityService->createOpportunity($data);
            $this->dispatch('opportunity-created');
            session()->flash('message', 'Oportunidad creada exitosamente.');
        }

        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.opportunities.opportunity-form');
    }
}
