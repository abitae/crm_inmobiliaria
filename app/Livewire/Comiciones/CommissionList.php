<?php

namespace App\Livewire\Comiciones;

use App\Models\Commission;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Models\Opportunity;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CommissionList extends Component
{
    use WithPagination;

    // Filtros
    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $advisorFilter = '';
    public $projectFilter = '';

    // Modales
    public $showFormModal = false;
    public $showDetailModal = false;
    public $showApproveModal = false;
    public $showPayModal = false;
    public $editingCommission = null;

    // Campos del formulario
    public $advisor_id = '';
    public $project_id = '';
    public $unit_id = '';
    public $opportunity_id = '';
    public $commission_type = 'venta';
    public $base_amount = 0;
    public $commission_percentage = 0;
    public $bonus_amount = 0;
    public $status = 'pendiente';
    public $payment_method = '';
    public $payment_reference = '';
    public $notes = '';

    // Campos para aprobación y pago
    public $approval_notes = '';
    public $payment_date = '';

    public $projects = [];
    public $units = [];
    public $advisors = [];
    public $opportunities = [];

    protected $rules = [
        'advisor_id' => 'required|exists:users,id',
        'project_id' => 'required|exists:projects,id',
        'unit_id' => 'nullable|exists:units,id',
        'opportunity_id' => 'nullable|exists:opportunities,id',
        'commission_type' => 'required|in:venta,reserva,seguimiento,bono',
        'base_amount' => 'required|numeric|min:0',
        'commission_percentage' => 'required|numeric|min:0|max:100',
        'bonus_amount' => 'nullable|numeric|min:0',
        'status' => 'required|in:pendiente,aprobada,pagada,cancelada',
        'payment_method' => 'nullable|string|max:255',
        'payment_reference' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        // Obtener asesores disponibles excluyendo dateros
        $availableAdvisors = User::getAvailableAdvisors(Auth::user());
        $this->advisors = $availableAdvisors->filter(function($advisor) {
            return !$advisor->isDatero();
        });
        
        $this->projects = Project::active()->get();
        $user = Auth::user();
        $this->advisorFilter = ($user->isAdmin() || $user->isLider()) ? '' : $user->id;
    }

    public function updatedProjectId()
    {
        if ($this->project_id) {
            $this->units = Unit::where('project_id', $this->project_id)->get();
            $this->opportunities = Opportunity::where('project_id', $this->project_id)
                ->where('status', 'pagado')
                ->get();
        } else {
            $this->units = [];
            $this->opportunities = [];
        }
        $this->unit_id = '';
        $this->opportunity_id = '';
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

    public function updatedAdvisorFilter()
    {
        $this->resetPage();
    }

    public function updatedProjectFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'statusFilter',
            'typeFilter',
            'advisorFilter',
            'projectFilter'
        ]);
        $this->resetPage();
    }

    public function openCreateModal($commissionId = null)
    {
        if ($commissionId) {
            $this->editingCommission = Commission::with(['advisor', 'project', 'unit', 'opportunity'])->find($commissionId);
            if ($this->editingCommission) {
                $this->fillFormFromCommission($this->editingCommission);
            }
        } else {
            $this->resetForm();
            $this->editingCommission = null;
        }
        $this->showFormModal = true;
    }

    public function openDetailModal($commissionId)
    {
        $this->editingCommission = Commission::with([
            'advisor',
            'project',
            'unit',
            'opportunity',
            'approvedBy',
            'paidBy',
            'createdBy',
            'updatedBy'
        ])->find($commissionId);
        $this->showDetailModal = true;
    }

    public function openApproveModal($commissionId)
    {
        $this->editingCommission = Commission::find($commissionId);
        $this->approval_notes = '';
        $this->showApproveModal = true;
    }

    public function openPayModal($commissionId)
    {
        $this->editingCommission = Commission::find($commissionId);
        $this->payment_date = now()->format('Y-m-d');
        $this->payment_method = '';
        $this->payment_reference = '';
        $this->showPayModal = true;
    }

    public function closeModals()
    {
        $this->reset([
            'showFormModal',
            'showDetailModal',
            'showApproveModal',
            'showPayModal',
            'editingCommission',
            'approval_notes',
            'payment_date',
            'payment_method',
            'payment_reference'
        ]);
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'advisor_id',
            'project_id',
            'unit_id',
            'opportunity_id',
            'commission_type',
            'base_amount',
            'commission_percentage',
            'bonus_amount',
            'status',
            'payment_method',
            'payment_reference',
            'notes'
        ]);
        $this->status = 'pendiente';
        $this->commission_type = 'venta';
        $this->units = [];
        $this->opportunities = [];
    }

    public function fillFormFromCommission($commission)
    {
        $this->advisor_id = $commission->advisor_id;
        $this->project_id = $commission->project_id;
        $this->unit_id = $commission->unit_id;
        $this->opportunity_id = $commission->opportunity_id;
        $this->commission_type = $commission->commission_type;
        $this->base_amount = $commission->base_amount;
        $this->commission_percentage = $commission->commission_percentage;
        $this->bonus_amount = $commission->bonus_amount;
        $this->status = $commission->status;
        $this->payment_method = $commission->payment_method ?? '';
        $this->payment_reference = $commission->payment_reference ?? '';
        $this->notes = $commission->notes ?? '';

        // Cargar unidades y oportunidades del proyecto
        if ($this->project_id) {
            $this->units = Unit::where('project_id', $this->project_id)->get();
            $this->opportunities = Opportunity::where('project_id', $this->project_id)
                ->where('status', 'pagado')
                ->get();
        }
    }

    public function createCommission()
    {
        $this->validate();

        $commission = Commission::create([
            'advisor_id' => $this->advisor_id,
            'project_id' => $this->project_id,
            'unit_id' => $this->unit_id,
            'opportunity_id' => $this->opportunity_id,
            'commission_type' => $this->commission_type,
            'base_amount' => $this->base_amount,
            'commission_percentage' => $this->commission_percentage,
            'bonus_amount' => $this->bonus_amount ?? 0,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'notes' => $this->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Calcular comisión
        $commission->calculateCommission();

        $this->closeModals();
        $this->dispatch('show-success', message: 'Comisión creada exitosamente.');
    }

    public function updateCommission()
    {
        $this->validate();

        if (!$this->editingCommission) {
            return;
        }

        $this->editingCommission->update([
            'advisor_id' => $this->advisor_id,
            'project_id' => $this->project_id,
            'unit_id' => $this->unit_id,
            'opportunity_id' => $this->opportunity_id,
            'commission_type' => $this->commission_type,
            'base_amount' => $this->base_amount,
            'commission_percentage' => $this->commission_percentage,
            'bonus_amount' => $this->bonus_amount ?? 0,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'notes' => $this->notes,
            'updated_by' => Auth::id(),
        ]);

        // Recalcular comisión
        $this->editingCommission->calculateCommission();

        $this->closeModals();
        $this->dispatch('show-success', message: 'Comisión actualizada exitosamente.');
    }

    public function approveCommission()
    {
        if (!$this->editingCommission) {
            return;
        }

        if ($this->editingCommission->approve(Auth::id())) {
            $this->closeModals();
            $this->dispatch('show-success', message: 'Comisión aprobada exitosamente.');
        } else {
            $this->dispatch('show-error', message: 'No se pudo aprobar la comisión.');
        }
    }

    public function payCommission()
    {
        $this->validate([
            'payment_method' => 'required|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
        ]);

        if (!$this->editingCommission) {
            return;
        }

        if ($this->editingCommission->pay(Auth::id(), $this->payment_method, $this->payment_reference)) {
            $this->closeModals();
            $this->dispatch('show-success', message: 'Comisión pagada exitosamente.');
        } else {
            $this->dispatch('show-error', message: 'No se pudo pagar la comisión.');
        }
    }

    public function cancelCommission($commissionId)
    {
        $commission = Commission::find($commissionId);
        if ($commission && $commission->cancel()) {
            $this->dispatch('show-success', message: 'Comisión cancelada exitosamente.');
        } else {
            $this->dispatch('show-error', message: 'No se pudo cancelar la comisión.');
        }
    }

    public function render()
    {
        $query = Commission::with(['advisor', 'project', 'unit', 'opportunity'])
            ->whereDoesntHave('advisor', function($q) {
                // Excluir comisiones de usuarios con rol datero
                $q->whereHas('roles', function($roleQuery) {
                    $roleQuery->where('name', 'datero');
                });
            })
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('advisor', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('project', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        if ($this->statusFilter) {
            $query->byStatus($this->statusFilter);
        }

        if ($this->typeFilter) {
            $query->byType($this->typeFilter);
        }

        if ($this->advisorFilter) {
            $query->byAdvisor($this->advisorFilter);
        }

        if ($this->projectFilter) {
            $query->byProject($this->projectFilter);
        }

        $commissions = $query->paginate(15);

        return view('livewire.comiciones.commission-list', [
            'commissions' => $commissions
        ]);
    }
}
