<?php

namespace App\Livewire\Opportunities;

use App\Services\OpportunityService;
use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\ValidationException;
use Exception;

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
    public $showDetailModal = false;
    public $selectedOpportunity = null;
    public $editingOpportunity = null;

    // Listeners para eventos de otros componentes
    protected $listeners = [
        'edit-opportunity' => 'openEditModal',
        'opportunity-created' => 'refreshOpportunities',
        'opportunity-updated' => 'refreshOpportunities',
        'opportunity-deleted' => 'refreshOpportunities',
    ];

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

    protected $messages = [
        'client_id.required' => 'El cliente es obligatorio.',
        'client_id.exists' => 'El cliente seleccionado no existe.',
        'project_id.required' => 'El proyecto es obligatorio.',
        'project_id.exists' => 'El proyecto seleccionado no existe.',
        'unit_id.exists' => 'La unidad seleccionada no existe.',
        'advisor_id.required' => 'El asesor es obligatorio.',
        'advisor_id.exists' => 'El asesor seleccionado no existe.',
        'stage.required' => 'La etapa es obligatoria.',
        'stage.in' => 'La etapa seleccionada no es válida.',
        'status.required' => 'El estado es obligatorio.',
        'status.in' => 'El estado seleccionado no es válido.',
        'probability.required' => 'La probabilidad es obligatoria.',
        'probability.integer' => 'La probabilidad debe ser un número entero.',
        'probability.min' => 'La probabilidad debe ser al menos 0%.',
        'probability.max' => 'La probabilidad no puede exceder 100%.',
        'expected_value.required' => 'El valor esperado es obligatorio.',
        'expected_value.numeric' => 'El valor esperado debe ser un número.',
        'expected_value.min' => 'El valor esperado debe ser mayor a 0.',
        'expected_close_date.required' => 'La fecha de cierre es obligatoria.',
        'expected_close_date.date' => 'La fecha de cierre debe ser una fecha válida.',
        'expected_close_date.after' => 'La fecha de cierre debe ser posterior a hoy.',
        'notes.string' => 'Las notas deben ser texto.',
        'source.max' => 'El origen no puede exceder 255 caracteres.',
        'campaign.max' => 'La campaña no puede exceder 255 caracteres.'
    ];

    // Validación en tiempo real
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        // Resetear página cuando cambian los filtros
        if (in_array($propertyName, ['search', 'statusFilter', 'stageFilter', 'advisorFilter', 'projectFilter', 'clientFilter'])) {
            $this->resetPage();
        }

        // Cargar unidades cuando cambia el proyecto
        if ($propertyName === 'project_id') {
            $this->loadUnitsForProject();
        }
    }

    public function boot(OpportunityService $opportunityService)
    {
        $this->opportunityService = $opportunityService;
    }

    public function mount()
    {
        $this->clients = Client::all();
        $this->projects = Project::all();
        $this->advisors = User::getAdvisorsAndAdmins();
        $this->expected_close_date = now()->addDays(30)->format('Y-m-d');
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->stageFilter = '';
        $this->advisorFilter = '';
        $this->projectFilter = '';
        $this->clientFilter = '';
        $this->resetPage();
        $this->dispatch('show-info', message: 'Filtros limpiados correctamente.');
    }

    public function loadUnitsForProject()
    {
        if ($this->project_id) {
            $this->units = Unit::where('project_id', $this->project_id)
                ->whereIn('status', ['disponible', 'reservado'])
                ->get();

            if (count($this->units) > 0) {
                $this->dispatch('show-info', message: 'Se cargaron ' . count($this->units) . ' unidades disponibles para este proyecto.');
            } else {
                $this->dispatch('show-info', message: 'No hay unidades disponibles para este proyecto en este momento.');
            }
        } else {
            $this->units = [];
        }
        $this->unit_id = '';
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
        $this->dispatch('show-info', message: 'Formulario de nueva oportunidad abierto. Completa todos los campos obligatorios.');
    }

    public function openEditModal($opportunityId)
    {
        $this->editingOpportunity = $this->opportunityService->getOpportunityById($opportunityId);
        if ($this->editingOpportunity) {
            $this->fillFormFromOpportunity($this->editingOpportunity);
            $this->showEditModal = true;
            $this->dispatch('show-info', message: 'Editando oportunidad para ' . $this->editingOpportunity->client->name . '.');
        }
    }

    public function openDeleteModal($opportunityId)
    {
        $this->selectedOpportunity = $this->opportunityService->getOpportunityById($opportunityId);
        $this->showDeleteModal = true;
    }

    public function openDetailModal($opportunityId)
    {
        $this->selectedOpportunity = $this->opportunityService->getOpportunityById($opportunityId);
        $this->showDetailModal = true;
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
        try {
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
            $this->dispatch('show-success', message: 'Oportunidad creada exitosamente.');
        } catch (ValidationException $e) {
            $this->dispatch('show-error', message: 'Por favor, corrige los errores en el formulario.');
        } catch (Exception $e) {
            $this->dispatch('show-error', message: 'Error al crear la oportunidad: ' . $e->getMessage());
        }
    }

    public function updateOpportunity()
    {
        try {
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
            $this->dispatch('show-success', message: 'Oportunidad actualizada exitosamente.');
        } catch (ValidationException $e) {
            $this->dispatch('show-error', message: 'Por favor, corrige los errores en el formulario.');
        } catch (Exception $e) {
            $this->dispatch('show-error', message: 'Error al actualizar la oportunidad: ' . $e->getMessage());
        }
    }

    public function deleteOpportunity()
    {
        try {
            if (!$this->selectedOpportunity) {
                return;
            }

            $this->opportunityService->deleteOpportunity($this->selectedOpportunity->id);

            $this->closeModals();
            $this->dispatch('opportunity-deleted');
            $this->dispatch('show-success', message: 'Oportunidad eliminada exitosamente.');
        } catch (Exception $e) {
            $this->dispatch('show-error', message: 'Error al eliminar la oportunidad: ' . $e->getMessage());
        }
    }

    public function advanceStage()
    {
        try {
            if (!$this->selectedOpportunity || !$this->newStage) {
                return;
            }

            $result = $this->opportunityService->advanceStage($this->selectedOpportunity->id, $this->newStage);

            if ($result) {
                $this->dispatch('show-success', message: 'Etapa de la oportunidad avanzada exitosamente.');
            } else {
                $this->dispatch('show-error', message: 'Error al cambiar la etapa: No se pudo avanzar a la etapa seleccionada.');
            }

            $this->closeModals();
        } catch (Exception $e) {
            $this->dispatch('show-error', message: 'Error al cambiar la etapa: ' . $e->getMessage());
        }
    }

    public function markAsWon()
    {
        try {
            if (!$this->selectedOpportunity || !$this->winValue) {
                return;
            }

            $result = $this->opportunityService->markAsWon($this->selectedOpportunity->id, $this->winValue, $this->winReason);
            if ($result) {
                $this->dispatch('show-success', message: 'Oportunidad marcada como ganada.');
            } else {
                $this->dispatch('show-error', message: 'Error al marcar como ganada: No se pudo marcar como ganada.');
            }

            $this->closeModals();
        } catch (Exception $e) {
            $this->dispatch('show-error', message: 'Error al marcar como ganada: ' . $e->getMessage());
        }
    }

    public function markAsLost()
    {
        try {
            if (!$this->selectedOpportunity || !$this->loseReason) {
                return;
            }
            $result = $this->opportunityService->markAsLost($this->selectedOpportunity->id, $this->loseReason);
            if ($result) {
                $this->dispatch('show-success', message: 'Oportunidad marcada como perdida.');
            } else {
                $this->dispatch('show-error', message: 'Error al marcar como perdida: No se pudo marcar como perdida.');
            }
            $this->closeModals();
        } catch (Exception $e) {
            $this->dispatch('show-error', message: 'Error al marcar como perdida: ' . $e->getMessage());
        }
    }

    public function updateProbability($opportunityId, $newProbability)
    {
        try {
            $this->opportunityService->updateProbability($opportunityId, $newProbability);
            $this->dispatch('opportunity-probability-updated');
            $this->dispatch('show-success', message: 'Probabilidad actualizada a ' . $newProbability . '%.');
        } catch (Exception $e) {
            $this->dispatch('show-error', message: 'Error al actualizar la probabilidad: ' . $e->getMessage());
        }
    }

    public function updateExpectedValue($opportunityId, $newValue)
    {
        try {
            $this->opportunityService->updateExpectedValue($opportunityId, $newValue);
            $this->dispatch('opportunity-value-updated');
            $this->dispatch('show-success', message: 'Valor esperado actualizado a S/ ' . number_format($newValue, 2) . '.');
        } catch (Exception $e) {
            $this->dispatch('show-error', message: 'Error al actualizar el valor esperado: ' . $e->getMessage());
        }
    }

    public function refreshOpportunities()
    {
        // Este método se llama cuando se actualiza la lista
        // No es necesario hacer nada especial ya que Livewire se actualiza automáticamente
    }

    public function exportOpportunities()
    {
        try {
            $filters = [
                'search' => $this->search,
                'status' => $this->statusFilter,
                'stage' => $this->stageFilter,
                'advisor_id' => $this->advisorFilter,
                'project_id' => $this->projectFilter,
                'client_id' => $this->clientFilter,
            ];

            $opportunities = $this->opportunityService->getAllOpportunities(1000, $filters);

            $filename = 'oportunidades_' . now()->format('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($opportunities) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'ID',
                    'Cliente',
                    'Email Cliente',
                    'Proyecto',
                    'Unidad',
                    'Etapa',
                    'Estado',
                    'Probabilidad (%)',
                    'Valor Esperado (S/)',
                    'Valor Cierre (S/)',
                    'Asesor',
                    'Fecha Cierre',
                    'Origen',
                    'Campaña',
                    'Notas',
                    'Fecha Creación'
                ]);

                foreach ($opportunities as $opportunity) {
                    fputcsv($file, [
                        $opportunity->id,
                        $opportunity->client->name ?? '',
                        $opportunity->client->email ?? '',
                        $opportunity->project->name ?? '',
                        $opportunity->unit->unit_number ?? '',
                        ucfirst($opportunity->stage),
                        ucfirst($opportunity->status),
                        $opportunity->probability,
                        $opportunity->expected_value,
                        $opportunity->close_value,
                        $opportunity->advisor->name ?? '',
                        $opportunity->expected_close_date ? $opportunity->expected_close_date->format('Y-m-d') : '',
                        $opportunity->source,
                        $opportunity->campaign,
                        $opportunity->notes,
                        $opportunity->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            $this->dispatch('show-success', message: 'Exportación iniciada. El archivo se descargará automáticamente.');
            return response()->stream($callback, 200, $headers);
        } catch (Exception $e) {
            $this->dispatch('show-error', message: 'Error al exportar las oportunidades: ' . $e->getMessage());
        }
    }

    public function getStageColor($stage)
    {
        return match ($stage) {
            'captado' => 'bg-gray-100 text-gray-800',
            'calificado' => 'bg-blue-100 text-blue-800',
            'contacto' => 'bg-yellow-100 text-yellow-800',
            'propuesta' => 'bg-orange-100 text-orange-800',
            'visita' => 'bg-purple-100 text-purple-800',
            'negociacion' => 'bg-indigo-100 text-indigo-800',
            'cierre' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusColor($status)
    {
        return match ($status) {
            'activa' => 'bg-blue-100 text-blue-800',
            'ganada' => 'bg-green-100 text-green-800',
            'perdida' => 'bg-red-100 text-red-800',
            'cancelada' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
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
        $stats = $this->opportunityService->getOpportunityStats();
        $projects = Project::active()->orderBy('name')->get();

        return view('livewire.opportunities.opportunity-list', [
            'opportunities' => $opportunities,
            'stats' => $stats,
            'projects' => $projects
        ]);
    }
}
