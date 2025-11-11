<?php

namespace App\Livewire\Comiciones;

use App\Models\Commission;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CommissionListDatero extends Component
{
    use WithPagination;

    // Filtros
    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $projectFilter = '';

    // Modal de detalle
    public $showDetailModal = false;
    public $selectedCommission = null;

    public $projects = [];

    public function mount()
    {
        // Cargar proyectos activos para el filtro
        $this->projects = Project::active()->get();
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
            'projectFilter'
        ]);
        $this->resetPage();
    }

    public function openDetailModal($commissionId)
    {
        $this->selectedCommission = Commission::with([
            'advisor',
            'project',
            'unit',
            'opportunity',
            'approvedBy',
            'paidBy',
            'createdBy',
            'updatedBy'
        ])->find($commissionId);

        // Verificar que la comisión pertenezca al datero autenticado
        if ($this->selectedCommission && $this->selectedCommission->advisor_id !== Auth::id()) {
            $this->dispatch('show-error', message: 'No tienes permiso para ver esta comisión.');
            $this->closeDetailModal();
            return;
        }

        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->reset(['showDetailModal', 'selectedCommission']);
    }

    /**
     * Obtener estadísticas de comisiones del datero
     */
    public function getStatsProperty()
    {
        $userId = Auth::id();
        
        return [
            'total' => Commission::byAdvisor($userId)->count(),
            'pendiente' => Commission::byAdvisor($userId)->pending()->count(),
            'aprobada' => Commission::byAdvisor($userId)->approved()->count(),
            'pagada' => Commission::byAdvisor($userId)->paid()->count(),
            'total_pagado' => Commission::byAdvisor($userId)->paid()->sum('total_commission'),
            'total_pendiente' => Commission::byAdvisor($userId)->unpaid()->sum('total_commission'),
        ];
    }

    public function render()
    {
        $userId = Auth::id();
        
        $query = Commission::with(['project', 'unit', 'opportunity'])
            ->byAdvisor($userId)
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('project', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('opportunity', function($q) {
                    $q->whereHas('client', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
                });
            });
        }

        if ($this->statusFilter) {
            $query->byStatus($this->statusFilter);
        }

        if ($this->typeFilter) {
            $query->byType($this->typeFilter);
        }

        if ($this->projectFilter) {
            $query->byProject($this->projectFilter);
        }

        $commissions = $query->paginate(15);
        $stats = $this->stats;

        return view('livewire.comiciones.commission-list-datero', [
            'commissions' => $commissions,
            'stats' => $stats
        ]);
    }
}

