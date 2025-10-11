<?php

namespace App\Livewire\Opportunities;

use App\Services\OpportunityService;
use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Models\Activity;
use App\Models\Task;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OpportunityList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $stageFilter = '';
    public $advisorFilter = '';
    public $projectFilter = '';
    public $clientFilter = '';
    public $showFormModal = false;
    public $showDeleteModal = false;
    public $showDetailModal = false;
    public $showActivityModal = false;
    public $showTaskModal = false;
    public $selectedOpportunity = null;
    public $isEditing = false;

    // Listeners para eventos de otros componentes
    protected $listeners = [
        'edit-opportunity' => 'openFormModal',
        'opportunity-created' => 'refreshOpportunities',
        'opportunity-updated' => 'refreshOpportunities',
        'opportunity-deleted' => 'refreshOpportunities',
        'opportunity-probability-updated' => 'refreshOpportunities',
        'opportunity-value-updated' => 'refreshOpportunities',
    ];

    // Form fields
    public $client_id = '';
    public $project_id = '';
    public $unit_id = '';
    public $advisor_id = '';
    public $stage = 'calificado';
    public $status = 'registrado';
    public $probability = 10;
    public $expected_value = 0;
    public $expected_close_date = '';
    public $close_value = 0;
    public $close_reason = '';
    public $lost_reason = '';
    public $notes = '';
    public $source = '';
    public $campaign = '';

    // Activity form fields
    public $activity_title = '';
    public $activity_description = '';
    public $activity_type = 'llamada';
    public $activity_priority = 'media';
    public $activity_start_date = '';
    public $activity_end_date = '';
    public $activity_duration = 30;
    public $activity_location = '';
    public $activity_notes = '';

    // Task form fields
    public $task_title = '';
    public $task_description = '';
    public $task_priority = 'media';
    public $task_due_date = '';
    public $task_notes = '';

    protected $opportunityService;
    public $clients = [];
    public $projects = [];
    public $units = [];
    public $advisors = [];

    // Las reglas de validación ahora están en métodos específicos

    // Los mensajes de validación ahora están en métodos específicos

    // Métodos de validación específicos
    protected $rules_opportunity = [
        'client_id' => 'required|exists:clients,id',
        'project_id' => 'required|exists:projects,id',
        'unit_id' => 'nullable|exists:units,id',
        'advisor_id' => 'required|exists:users,id',
        'stage' => 'required|in:calificado,visita,cierre',
        'status' => 'required|in:registrado,reservado,cuotas,pagado,transferido,cancelado',
        'probability' => 'required|integer|min:0|max:100',
        'expected_value' => 'required|numeric|min:0',
        'expected_close_date' => 'required|date|after_or_equal:today',
        'close_value' => 'nullable|numeric|min:0',
        'close_reason' => 'nullable|string|max:255',
        'lost_reason' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'source' => 'nullable|string|max:255',
        'campaign' => 'nullable|string|max:255'
    ];

    protected $messages_opportunity = [
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
        'status.in' => 'El estado debe ser: registrado, reservado, cuotas, pagado, transferido o cancelado.',
        'probability.required' => 'La probabilidad es obligatoria.',
        'probability.integer' => 'La probabilidad debe ser un número entero.',
        'probability.min' => 'La probabilidad debe ser al menos 0%.',
        'probability.max' => 'La probabilidad no puede exceder 100%.',
        'expected_value.required' => 'El valor esperado es obligatorio.',
        'expected_value.numeric' => 'El valor esperado debe ser un número.',
        'expected_value.min' => 'El valor esperado debe ser mayor a 0.',
        'expected_close_date.required' => 'La fecha de cierre es obligatoria.',
        'expected_close_date.date' => 'La fecha de cierre debe ser una fecha válida.',
        'expected_close_date.after_or_equal' => 'La fecha de cierre debe ser hoy o posterior.',
        'notes.string' => 'Las notas deben ser texto.',
        'source.max' => 'El origen no puede exceder 255 caracteres.',
        'campaign.max' => 'La campaña no puede exceder 255 caracteres.'
    ];
    protected $rules_activity = [
        'activity_title' => 'required|string|max:255',
        'activity_description' => 'nullable|string',
        'activity_type' => 'required|in:llamada,reunion,visita,seguimiento,tarea',
        'activity_priority' => 'required|in:baja,media,alta,urgente',
        'activity_start_date' => 'required|date|after_or_equal:today',
        'activity_end_date' => 'nullable|date|after:activity_start_date',
        'activity_duration' => 'required|integer|min:1|max:1440',
        'activity_location' => 'nullable|string|max:255',
        'activity_notes' => 'nullable|string'
    ];

    protected $messages_activity = [
        'activity_title.required' => 'El título de la actividad es obligatorio.',
        'activity_title.max' => 'El título no puede exceder 255 caracteres.',
        'activity_type.required' => 'El tipo de actividad es obligatorio.',
        'activity_type.in' => 'El tipo de actividad seleccionado no es válido.',
        'activity_priority.required' => 'La prioridad es obligatoria.',
        'activity_priority.in' => 'La prioridad seleccionada no es válida.',
        'activity_start_date.required' => 'La fecha de inicio es obligatoria.',
        'activity_start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
        'activity_start_date.after_or_equal' => 'La fecha de inicio debe ser hoy o posterior.',
        'activity_end_date.date' => 'La fecha de fin debe ser una fecha válida.',
        'activity_end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        'activity_duration.required' => 'La duración es obligatoria.',
        'activity_duration.integer' => 'La duración debe ser un número entero.',
        'activity_duration.min' => 'La duración debe ser al menos 1 minuto.',
        'activity_duration.max' => 'La duración no puede exceder 1440 minutos (24 horas).',
        'activity_location.max' => 'La ubicación no puede exceder 255 caracteres.'
    ];
    protected $rules_task = [
        'task_title' => 'required|string|max:255',
        'task_description' => 'nullable|string',
        'task_priority' => 'required|in:baja,media,alta,urgente',
        'task_due_date' => 'required|date|after_or_equal:today',
        'task_notes' => 'nullable|string'
    ];

    protected $messages_task =  [
        'task_title.required' => 'El título de la tarea es obligatorio.',
        'task_title.max' => 'El título no puede exceder 255 caracteres.',
        'task_priority.required' => 'La prioridad es obligatoria.',
        'task_priority.in' => 'La prioridad seleccionada no es válida.',
        'task_due_date.required' => 'La fecha de vencimiento es obligatoria.',
        'task_due_date.date' => 'La fecha de vencimiento debe ser una fecha válida.',
        'task_due_date.after_or_equal' => 'La fecha de vencimiento debe ser hoy o posterior.'
    ];

    public function boot(OpportunityService $opportunityService)
    {
        $this->opportunityService = $opportunityService;
    }

    public function mount()
    {
        $this->loadInitialData();
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

    public function updatedProjectId()
    {
        $this->loadUnitsForProject();
        $this->unit_id = ''; // Resetear unidad cuando se cambia el proyecto
    }

    public function updatedUnitId()
    {
        // Validar que la unidad seleccionada pertenezca al proyecto actual
        if ($this->unit_id && $this->project_id) {
            $unit = Unit::where('id', $this->unit_id)
                ->where('project_id', $this->project_id)
                ->first();
            
            if (!$unit) {
                $this->addError('unit_id', 'La unidad seleccionada no pertenece al proyecto actual.');
                $this->unit_id = '';
            }
        }
    }

    public function loadUnitsForProject()
    {
        try {
            if ($this->project_id) {
                $this->units = Unit::where('project_id', $this->project_id)
                    ->whereIn('status', ['disponible', 'reservado'])
                    ->orderBy('unit_number')
                    ->get();
            } else {
                $this->units = collect();
            }
        } catch (Exception $e) {
            Log::error('Error al cargar unidades para proyecto', [
                'project_id' => $this->project_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->units = collect();
        }
    }

    public function openFormModal($opportunityId = null)
    {
        $this->resetForm();

        if ($opportunityId) {
            $this->selectedOpportunity = $this->opportunityService->getOpportunityById($opportunityId);
            if ($this->selectedOpportunity) {
                $this->fillFormFromOpportunity($this->selectedOpportunity);
                $this->isEditing = true;
            }
        } else {
            $this->isEditing = false;
        }

        $this->showFormModal = true;
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

    public function agregarActividad($opportunityId)
    {
        $this->selectedOpportunity = $this->opportunityService->getOpportunityById($opportunityId);
        $this->resetForm();
        $this->showActivityModal = true;
    }

    public function agregarTarea($opportunityId)
    {
        $this->selectedOpportunity = $this->opportunityService->getOpportunityById($opportunityId);
        $this->resetForm();
        $this->showTaskModal = true;
    }

    public function closeModals()
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->showDetailModal = false;
        $this->showActivityModal = false;
        $this->showTaskModal = false;
        $this->resetForm();
        $this->selectedOpportunity = null;
        $this->isEditing = false;
        
        // Limpiar errores de validación
        $this->resetErrorBag();
    }

    public function resetForm()
    {
        $this->client_id = '';
        $this->project_id = '';
        $this->unit_id = '';
        $this->advisor_id = '';
        $this->stage = 'calificado';
        $this->status = 'registrado';
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

        // Reset activity form
        $this->activity_title = '';
        $this->activity_description = '';
        $this->activity_type = 'llamada';
        $this->activity_priority = 'media';
        $this->activity_start_date = now()->format('Y-m-d\TH:i');
        $this->activity_end_date = '';
        $this->activity_duration = 30;
        $this->activity_location = '';
        $this->activity_notes = '';

        // Reset task form
        $this->task_title = '';
        $this->task_description = '';
        $this->task_priority = 'media';
        $this->task_due_date = now()->addDays(1)->format('Y-m-d');
        $this->task_notes = '';
    }

    public function fillFormFromOpportunity($opportunity)
    {
        // Primero asignar el project_id para cargar las unidades correctas
        $this->project_id = $opportunity->project_id;
        
        // Cargar las unidades del proyecto
        $this->loadUnitsForProject();
        
        // Luego asignar todos los demás campos
        $this->client_id = $opportunity->client_id;
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
        
        // Asignar unit_id solo si la unidad existe y está disponible
        if ($opportunity->unit_id) {
            $unitExists = false;
            foreach ($this->units as $unit) {
                if ($unit->id == $opportunity->unit_id) {
                    $unitExists = true;
                    break;
                }
            }
            
            if ($unitExists) {
                $this->unit_id = $opportunity->unit_id;
            } else {
                // Si la unidad no está disponible, mostrar un mensaje informativo
                $this->addError('unit_id', 'La unidad original no está disponible. Por favor selecciona otra unidad.');
                $this->unit_id = '';
            }
        }
    }

    public function saveOpportunity()
    {
        try {
            // Log de datos antes de validar para debugging
            Log::info('Intentando guardar oportunidad', [
                'is_editing' => $this->isEditing,
                'form_data' => $this->getFormData(),
                'user_id' => Auth::id()
            ]);

            $this->validate($this->rules_opportunity, $this->messages_opportunity);

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
                'close_value' => $this->close_value,
                'close_reason' => $this->close_reason,
                'lost_reason' => $this->lost_reason,
                'notes' => $this->notes,
                'source' => $this->source,
                'campaign' => $this->campaign,
            ];

            if ($this->isEditing && $this->selectedOpportunity) {
                $result = $this->opportunityService->updateOpportunity($this->selectedOpportunity->id, $data);
                
                if ($result) {
                    Log::info('Oportunidad actualizada exitosamente', [
                        'opportunity_id' => $this->selectedOpportunity->id,
                        'user_id' => Auth::id(),
                        'form_data' => $data
                    ]);
                    $this->dispatch('opportunity-updated');
                    $this->closeModals();
                    $this->dispatch('show-success', message: 'Oportunidad actualizada exitosamente.');
                } else {
                    throw new Exception('No se pudo actualizar la oportunidad');
                }
            } else {
                $opportunity = $this->opportunityService->createOpportunity($data);
                
                if ($opportunity) {
                    Log::info('Oportunidad creada exitosamente', [
                        'opportunity_id' => $opportunity->id ?? 'unknown',
                        'user_id' => Auth::id(),
                        'form_data' => $data
                    ]);
                    $this->dispatch('opportunity-created');
                    $this->closeModals();
                    $this->dispatch('show-success', message: 'Oportunidad creada exitosamente.');
                } else {
                    throw new Exception('No se pudo crear la oportunidad');
                }
            }
        } catch (ValidationException $e) {
            Log::warning('Error de validación al guardar oportunidad', [
                'is_editing' => $this->isEditing,
                'opportunity_id' => $this->selectedOpportunity->id ?? null,
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
                'form_data' => $this->getFormData()
            ]);
            $this->dispatch('show-error', message: 'Por favor corrige los errores en el formulario.');
        } catch (Exception $e) {
            $action = $this->isEditing ? 'actualizar' : 'crear';
            Log::error("Error al {$action} oportunidad", [
                'is_editing' => $this->isEditing,
                'opportunity_id' => $this->selectedOpportunity->id ?? null,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'form_data' => $this->getFormData()
            ]);
            $this->dispatch('show-error', message: "Error al {$action} la oportunidad: " . $e->getMessage());
        }
    }

    public function deleteOpportunity()
    {
        try {
            if (!$this->selectedOpportunity) {
                return;
            }

            $this->opportunityService->deleteOpportunity($this->selectedOpportunity->id);

            Log::info('Oportunidad eliminada exitosamente', [
                'opportunity_id' => $this->selectedOpportunity->id,
                'user_id' => Auth::id(),
                'opportunity_data' => [
                    'client_name' => $this->selectedOpportunity->client->name ?? 'N/A',
                    'project_name' => $this->selectedOpportunity->project->name ?? 'N/A',
                    'stage' => $this->selectedOpportunity->stage,
                    'status' => $this->selectedOpportunity->status
                ]
            ]);

            $this->closeModals();
            $this->dispatch('opportunity-deleted');
            $this->dispatch('show-success', message: 'Oportunidad eliminada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al eliminar oportunidad', [
                'opportunity_id' => $this->selectedOpportunity->id ?? null,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Los errores se muestran en el campo correspondiente
        }
    }

    public function saveActivity()
    {
        try {
            $this->validate($this->rules_activity, $this->messages_activity);

            if (!$this->selectedOpportunity) {
                $this->addError('activity_title', 'No se ha seleccionado una oportunidad.');
                return;
            }

            // Calcular fecha de fin si no se proporciona
            $startDate = \Carbon\Carbon::parse($this->activity_start_date);
            $endDate = $this->activity_end_date ?
                \Carbon\Carbon::parse($this->activity_end_date) :
                $startDate->copy()->addMinutes($this->activity_duration);

            $activity = Activity::create([
                'title' => $this->activity_title,
                'description' => $this->activity_description,
                'activity_type' => $this->activity_type,
                'status' => 'programada',
                'priority' => $this->activity_priority,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration' => $this->activity_duration,
                'location' => $this->activity_location,
                'client_id' => $this->selectedOpportunity->client_id,
                'project_id' => $this->selectedOpportunity->project_id,
                'unit_id' => $this->selectedOpportunity->unit_id,
                'opportunity_id' => $this->selectedOpportunity->id,
                'advisor_id' => $this->selectedOpportunity->advisor_id,
                'assigned_to' => Auth::id(),
                'notes' => $this->activity_notes,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);

            Log::info('Actividad creada exitosamente', [
                'activity_id' => $activity->id,
                'opportunity_id' => $this->selectedOpportunity->id,
                'user_id' => Auth::id(),
                'activity_data' => $this->getActivityData()
            ]);

            $this->closeModals();
            $this->dispatch('show-success', message: 'Actividad creada exitosamente.');
        } catch (ValidationException $e) {
            Log::warning('Error de validación al crear actividad', [
                'opportunity_id' => $this->selectedOpportunity->id ?? null,
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
                'activity_data' => $this->getActivityData()
            ]);
            // Los errores de validación se muestran en los campos correspondientes
        } catch (Exception $e) {
            Log::error('Error al crear actividad', [
                'opportunity_id' => $this->selectedOpportunity->id ?? null,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'activity_data' => $this->getActivityData()
            ]);
            // Los errores se muestran en el campo correspondiente
        }
    }

    public function saveTask()
    {
        try {
            $this->validate($this->rules_task, $this->messages_task);

            if (!$this->selectedOpportunity) {
                $this->addError('task_title', 'No se ha seleccionado una oportunidad.');
                return;
            }

            $task = Task::create([
                'title' => $this->task_title,
                'description' => $this->task_description,
                'priority' => $this->task_priority,
                'status' => 'pendiente',
                'due_date' => \Carbon\Carbon::parse($this->task_due_date),
                'client_id' => $this->selectedOpportunity->client_id,
                'project_id' => $this->selectedOpportunity->project_id,
                'unit_id' => $this->selectedOpportunity->unit_id,
                'opportunity_id' => $this->selectedOpportunity->id,
                'assigned_to' => Auth::id(),
                'notes' => $this->task_notes,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);

            Log::info('Tarea creada exitosamente', [
                'task_id' => $task->id,
                'opportunity_id' => $this->selectedOpportunity->id,
                'user_id' => Auth::id(),
                'task_data' => $this->getTaskData()
            ]);

            $this->closeModals();
            $this->dispatch('show-success', message: 'Tarea creada exitosamente.');
        } catch (ValidationException $e) {
            Log::warning('Error de validación al crear tarea', [
                'opportunity_id' => $this->selectedOpportunity->id ?? null,
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
                'task_data' => $this->getTaskData()
            ]);
            // Los errores de validación se muestran en los campos correspondientes
        } catch (Exception $e) {
            Log::error('Error al crear tarea', [
                'opportunity_id' => $this->selectedOpportunity->id ?? null,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'task_data' => $this->getTaskData()
            ]);
            // Los errores se muestran en el campo correspondiente
        }
    }

    public function updateProbability($opportunityId, $newProbability)
    {
        try {
            $this->opportunityService->updateProbability($opportunityId, $newProbability);
            $this->dispatch('opportunity-probability-updated');
            $this->dispatch('show-success', message: 'Probabilidad actualizada a ' . $newProbability . '%.');
        } catch (Exception $e) {
            Log::error('Error al actualizar probabilidad de oportunidad', [
                'opportunity_id' => $opportunityId,
                'new_probability' => $newProbability,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Los errores se muestran en el campo correspondiente
        }
    }

    public function updateExpectedValue($opportunityId, $newValue)
    {
        try {
            $this->opportunityService->updateExpectedValue($opportunityId, $newValue);
            $this->dispatch('opportunity-value-updated');
            $this->dispatch('show-success', message: 'Valor esperado actualizado a S/ ' . number_format($newValue, 2) . '.');
        } catch (Exception $e) {
            Log::error('Error al actualizar valor esperado de oportunidad', [
                'opportunity_id' => $opportunityId,
                'new_value' => $newValue,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Los errores se muestran en el campo correspondiente
        }
    }


    public function refreshData()
    {
        $this->loadInitialData();
        $this->dispatch('show-success', message: 'Datos actualizados correctamente.');
    }

    public function refreshOpportunities()
    {
        // Este método se llama desde los listeners para refrescar la lista
        // No necesita hacer nada específico ya que Livewire automáticamente
        // re-renderiza el componente cuando se disparan los eventos
        $this->resetPage();
    }

    private function loadInitialData()
    {
        $this->clients = Client::select('id', 'name', 'phone', 'document_number', 'client_type')
            ->whereIn('status', ['nuevo', 'contacto_inicial', 'en_seguimiento', 'cierre'])
            ->orderBy('name')
            ->get();

        $this->projects = Project::select('id', 'name', 'status', 'project_type')
            ->where('status', 'activo')
            ->orderBy('name')
            ->get();

        $this->advisors = User::getAvailableAdvisors(Auth::user());
    }

    public function duplicateOpportunity($opportunityId)
    {
        try {
            $originalOpportunity = $this->opportunityService->getOpportunityById($opportunityId);

            if (!$originalOpportunity) {
                $this->dispatch('show-error', message: 'Oportunidad no encontrada.');
                return;
            }

            // Llenar el formulario con los datos de la oportunidad original
            $this->fillFormFromOpportunity($originalOpportunity);

            // Cambiar algunos campos para la duplicación
            $this->stage = 'calificado';
            $this->status = 'registrado';
            $this->probability = 10;
            $this->expected_close_date = now()->addDays(30)->format('Y-m-d');
            $this->close_value = 0;
            $this->close_reason = '';
            $this->lost_reason = '';

            $this->isEditing = false;
            $this->showFormModal = true;
            $clientName = $originalOpportunity->client ? $originalOpportunity->client->name : 'Cliente desconocido';
            $this->dispatch('show-info', message: 'Formulario preparado para duplicar oportunidad de ' . $clientName . '.');
        } catch (Exception $e) {
            Log::error('Error al duplicar oportunidad', [
                'opportunity_id' => $opportunityId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Los errores se muestran en el campo correspondiente
        }
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

            // Obtener todas las oportunidades sin paginación para la exportación
            $opportunities = $this->opportunityService->getAllOpportunities(10000, $filters);

            $filename = 'oportunidades_' . now()->format('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            $callback = function () use ($opportunities) {
                $file = fopen('php://output', 'w');

                // Agregar BOM para UTF-8
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                fputcsv($file, [
                    'ID',
                    'Cliente',
                    'Teléfono Cliente',
                    'Documento Cliente',
                    'Tipo Cliente',
                    'Proyecto',
                    'Unidad',
                    'Tipo Unidad',
                    'Etapa',
                    'Estado',
                    'Probabilidad (%)',
                    'Valor Esperado (S/)',
                    'Valor Cierre (S/)',
                    'Asesor',
                    'Email Asesor',
                    'Fecha Cierre Esperada',
                    'Fecha Cierre Real',
                    'Origen',
                    'Campaña',
                    'Razón Cierre',
                    'Razón Pérdida',
                    'Notas',
                    'Fecha Creación',
                    'Última Actualización'
                ]);

                foreach ($opportunities as $opportunity) {
                    fputcsv($file, [
                        $opportunity->id,
                        $opportunity->client->name ?? '',
                        $opportunity->client->phone ?? '',
                        $opportunity->client->document_number ?? '',
                        $opportunity->client->client_type ?? '',
                        $opportunity->project->name ?? '',
                        $opportunity->unit->unit_number ?? '',
                        $opportunity->unit->unit_type ?? '',
                        ucfirst($opportunity->stage),
                        ucfirst($opportunity->status),
                        $opportunity->probability,
                        number_format($opportunity->expected_value, 2),
                        number_format($opportunity->close_value, 2),
                        $opportunity->advisor->name ?? '',
                        $opportunity->expected_close_date ? $opportunity->expected_close_date->format('Y-m-d') : '',
                        $opportunity->actual_close_date ? $opportunity->actual_close_date->format('Y-m-d') : '',
                        $opportunity->source ?? '',
                        $opportunity->campaign ?? '',
                        $opportunity->close_reason ?? '',
                        $opportunity->lost_reason ?? '',
                        $opportunity->notes ?? '',
                        $opportunity->created_at->format('Y-m-d H:i:s'),
                        $opportunity->updated_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            $this->dispatch('show-success', message: 'Exportación iniciada. El archivo se descargará automáticamente.');
            return response()->stream($callback, 200, $headers);
        } catch (Exception $e) {
            Log::error('Error al exportar oportunidades', [
                'filters' => $filters,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Los errores se muestran en el campo correspondiente
        }
    }

    public function getStageColor($stage)
    {
        return match ($stage) {
            'calificado' => 'bg-blue-100 text-blue-800',
            'visita' => 'bg-purple-100 text-purple-800',
            'cierre' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusColor($status)
    {
        return match ($status) {
            'registrado' => 'bg-blue-100 text-blue-800',
            'reservado' => 'bg-yellow-100 text-yellow-800',
            'cuotas' => 'bg-orange-100 text-orange-800',
            'pagado' => 'bg-green-100 text-green-800',
            'transferido' => 'bg-purple-100 text-purple-800',
            'cancelado' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStageIcon($stage)
    {
        return match ($stage) {
            'calificado' => 'user-check',
            'visita' => 'home',
            'cierre' => 'check-circle',
            default => 'question-mark-circle'
        };
    }

    public function getStatusIcon($status)
    {
        return match ($status) {
            'registrado' => 'document-plus',
            'reservado' => 'bookmark',
            'cuotas' => 'credit-card',
            'pagado' => 'check-circle',
            'transferido' => 'arrow-right-circle',
            'cancelado' => 'x-circle',
            default => 'question-mark-circle'
        };
    }

    public function getProbabilityColor($probability)
    {
        if ($probability >= 80) {
            return 'text-green-600 font-semibold';
        } elseif ($probability >= 60) {
            return 'text-yellow-600 font-medium';
        } elseif ($probability >= 40) {
            return 'text-orange-600 font-medium';
        } else {
            return 'text-red-600 font-medium';
        }
    }

    public function getValueColor($value)
    {
        if ($value >= 100000) {
            return 'text-green-600 font-semibold';
        } elseif ($value >= 50000) {
            return 'text-blue-600 font-medium';
        } elseif ($value >= 20000) {
            return 'text-yellow-600 font-medium';
        } else {
            return 'text-gray-600 font-medium';
        }
    }

    public function isOverdue($expectedCloseDate)
    {
        if (!$expectedCloseDate) {
            return false;
        }

        return \Carbon\Carbon::parse($expectedCloseDate)->isPast();
    }

    public function getDaysUntilClose($expectedCloseDate)
    {
        if (!$expectedCloseDate) {
            return null;
        }

        $closeDate = \Carbon\Carbon::parse($expectedCloseDate);
        $now = \Carbon\Carbon::now();

        return $now->diffInDays($closeDate, false);
    }

    public function render()
    {
        $filters = $this->getFilters();

        try {
            $opportunities = $this->opportunityService->getAllOpportunities(15, $filters);
            $projects = $this->projects; // Ya cargados en mount()

            return view('livewire.opportunities.opportunity-list', [
                'opportunities' => $opportunities,
                'projects' => $projects,
                'advisors' => $this->advisors,
                'units' => $this->units
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar oportunidades en render', [
                'filters' => $filters,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Los errores se muestran en el campo correspondiente

            return view('livewire.opportunities.opportunity-list', [
                'opportunities' => $this->getEmptyPaginator(),
                'projects' => collect(),
                'advisors' => collect(),
                'units' => collect()
            ]);
        }
    }

    private function getFilters()
    {
        return [
            'search' => $this->search,
            'status' => $this->statusFilter,
            'stage' => $this->stageFilter,
            'advisor_id' => $this->advisorFilter,
            'project_id' => $this->projectFilter,
            'client_id' => $this->clientFilter,
        ];
    }

    private function getEmptyPaginator()
    {
        return new \Illuminate\Pagination\LengthAwarePaginator(
            collect(),
            0,
            15,
            1,
            ['path' => request()->url(), 'pageName' => 'page']
        );
    }

    private function getFormData()
    {
        return [
            'client_id' => $this->client_id,
            'project_id' => $this->project_id,
            'unit_id' => $this->unit_id,
            'advisor_id' => $this->advisor_id,
            'stage' => $this->stage,
            'status' => $this->status,
            'probability' => $this->probability,
            'expected_value' => $this->expected_value,
            'expected_close_date' => $this->expected_close_date,
            'close_value' => $this->close_value,
            'close_reason' => $this->close_reason,
            'lost_reason' => $this->lost_reason,
            'notes' => $this->notes,
            'source' => $this->source,
            'campaign' => $this->campaign,
            'is_editing' => $this->isEditing,
            'selected_opportunity_id' => $this->selectedOpportunity->id ?? null
        ];
    }

    private function getActivityData()
    {
        return [
            'title' => $this->activity_title,
            'description' => $this->activity_description,
            'activity_type' => $this->activity_type,
            'priority' => $this->activity_priority,
            'start_date' => $this->activity_start_date,
            'end_date' => $this->activity_end_date,
            'duration' => $this->activity_duration,
            'location' => $this->activity_location,
            'notes' => $this->activity_notes
        ];
    }

    private function getTaskData()
    {
        return [
            'title' => $this->task_title,
            'description' => $this->task_description,
            'priority' => $this->task_priority,
            'due_date' => $this->task_due_date,
            'notes' => $this->task_notes
        ];
    }

    public function viewActivity($activityId)
    {
        try {
            $activity = Activity::findOrFail($activityId);
            $this->dispatch('show-info', message: 'Actividad: ' . $activity->title);
        } catch (Exception $e) {
            Log::error('Error al ver actividad', [
                'activity_id' => $activityId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            $this->dispatch('show-error', message: 'Error al cargar la actividad.');
        }
    }

    public function deleteActivity($activityId)
    {
        try {
            $activity = Activity::findOrFail($activityId);
            $activity->delete();
            
            Log::info('Actividad eliminada exitosamente', [
                'activity_id' => $activityId,
                'user_id' => Auth::id()
            ]);
            
            $this->dispatch('show-success', message: 'Actividad eliminada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al eliminar actividad', [
                'activity_id' => $activityId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            $this->dispatch('show-error', message: 'Error al eliminar la actividad.');
        }
    }
}
