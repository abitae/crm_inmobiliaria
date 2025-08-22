<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

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
        $this->stats = $this->dashboardService->getDashboardStats();
        $this->opportunitiesByStage = $this->dashboardService->getOpportunitiesByStage();
        $this->clientsByStatus = $this->dashboardService->getClientsByStatus();
        $this->projectsByType = $this->dashboardService->getProjectsByType();
        $this->salesByMonth = $this->dashboardService->getSalesByMonth();
        $this->recentActivities = $this->dashboardService->getRecentActivities();
        $this->pendingTasks = $this->dashboardService->getPendingTasks();
        $this->upcomingClosings = $this->dashboardService->getUpcomingClosings();
        $this->advisorPerformance = $this->dashboardService->getAdvisorPerformance();
        $this->conversionBySource = $this->dashboardService->getConversionBySource();
    }

    public function refreshDashboard()
    {
        $this->loadDashboardData();
        $this->dispatch('dashboard-refreshed');
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard');
    }
}
