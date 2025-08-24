<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    // Datos del dashboard
    public $stats = [];
    public $opportunitiesByStage = [];
    public $clientsByStatus = [];
    public $projectsByType = [];
    public $salesByMonth = [];
    public $recentActivities = [];
    public $pendingTasks = [];
    public $upcomingClosings = [];
    public $advisorPerformance = [];
    public $conversionBySource = [];

    // Filtros del dashboard
    public $dateRange = 'this_month';
    public $advisorFilter = '';

    protected $dashboardService;

    public function boot(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $filters = [
            'date_range' => $this->dateRange,
            'advisor_id' => $this->advisorFilter,
        ];

        $this->stats = $this->dashboardService->getDashboardStats($filters);
        $this->opportunitiesByStage = $this->dashboardService->getOpportunitiesByStage($filters);
        $this->clientsByStatus = $this->dashboardService->getClientsByStatus($filters);
        $this->projectsByType = $this->dashboardService->getProjectsByType($filters);
        $this->salesByMonth = $this->dashboardService->getSalesByMonth($filters);
        $this->recentActivities = $this->dashboardService->getRecentActivities($filters);
        $this->pendingTasks = $this->dashboardService->getPendingTasks($filters);
        $this->upcomingClosings = $this->dashboardService->getUpcomingClosings($filters);
        $this->advisorPerformance = $this->dashboardService->getAdvisorPerformance($filters);
        $this->conversionBySource = $this->dashboardService->getConversionBySource($filters);
    }

    public function updatedDateRange()
    {
        $this->loadDashboardData();
    }

    public function updatedAdvisorFilter()
    {
        $this->loadDashboardData();
    }

    public function clearFilters()
    {
        $this->reset(['dateRange', 'advisorFilter']);
        $this->dateRange = 'this_month';
        $this->loadDashboardData();
    }

    public function refreshDashboard()
    {
        $this->loadDashboardData();
        $this->dispatch('dashboard-refreshed');
        session()->flash('message', 'Dashboard actualizado exitosamente.');
    }

    public function exportDashboardData()
    {
        $filters = [
            'date_range' => $this->dateRange,
            'advisor_id' => $this->advisorFilter,
        ];

        $exportData = $this->dashboardService->exportDashboardData($filters);

        $this->dispatch('dashboard-exported', $exportData);
        session()->flash('message', 'Datos del dashboard exportados exitosamente.');
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard');
    }
}
