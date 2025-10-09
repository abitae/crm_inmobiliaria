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
    public $closedOpportunitiesBySeller = [];

    // Filtros del dashboard
    public $dateRange = 'this_month';
    public $advisorFilter = '';
    public $startDate = '';
    public $endDate = '';

    protected $dashboardService;

    // Listeners para eventos de otros componentes
    protected $listeners = [
        'opportunity-created' => 'refreshDashboard',
        'opportunity-updated' => 'refreshDashboard',
        'opportunity-deleted' => 'refreshDashboard',
        'opportunity-probability-updated' => 'refreshDashboard',
        'opportunity-value-updated' => 'refreshDashboard',
    ];

    public function boot(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // indicar carga
        $this->dispatch('dashboard-loading', true);
        $filters = [
            'date_range' => $this->dateRange,
            'advisor_id' => $this->advisorFilter,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ];

        $this->stats = $this->dashboardService->getDashboardStats($filters);
        $this->opportunitiesByStage = $this->dashboardService->getOpportunitiesByStage($filters);
        $this->clientsByStatus = $this->dashboardService->getClientsByStatus($filters);
        $this->projectsByType = $this->dashboardService->getProjectsByType($filters);
        $this->salesByMonth = $this->dashboardService->getSalesByMonth($filters);
        $this->recentActivities = $this->dashboardService->getRecentActivities(10, $filters);
        $this->pendingTasks = $this->dashboardService->getPendingTasks(10, $filters);
        $this->upcomingClosings = $this->dashboardService->getUpcomingClosings(10, $filters);
        $this->advisorPerformance = $this->dashboardService->getAdvisorPerformance($filters);
        $this->conversionBySource = $this->dashboardService->getConversionBySource($filters);
        $this->closedOpportunitiesBySeller = $this->dashboardService->getClosedOpportunitiesBySeller($filters);
        // Top 10 vendedores por monto (total_sales) descendente
        if (is_array($this->closedOpportunitiesBySeller)) {
            usort($this->closedOpportunitiesBySeller, function ($a, $b) {
                $aTotal = (float)($a->total_sales ?? 0);
                $bTotal = (float)($b->total_sales ?? 0);
                if ($aTotal === $bTotal) {
                    return 0;
                }
                return $aTotal < $bTotal ? 1 : -1; // descendente
            });
            $this->closedOpportunitiesBySeller = array_slice($this->closedOpportunitiesBySeller, 0, 10);
        }

        // Notificar al frontend que los datos han cambiado para actualizar gráficos sin recargar
        $this->dispatch('dashboard-data-updated', [
            'opportunitiesByStage' => $this->opportunitiesByStage,
            'clientsByStatus' => $this->clientsByStatus,
            'closedOpportunitiesBySeller' => $this->closedOpportunitiesBySeller,
            'advisorPerformance' => $this->advisorPerformance,
        ]);

        // fin de carga
        $this->dispatch('dashboard-loading', false);
    }

    public function updatedDateRange()
    {
        // Sincronizar fechas con el rango seleccionado; fin siempre es hoy
        switch ($this->dateRange) {
            case 'this_week':
                $this->startDate = now()->startOfWeek()->toDateString();
                break;
            case 'this_year':
                $this->startDate = now()->startOfYear()->toDateString();
                break;
            case 'this_month':
            default:
                $this->startDate = now()->startOfMonth()->toDateString();
                break;
        }
        $this->endDate = now()->toDateString();
        $this->loadDashboardData();
    }

    public function updatedAdvisorFilter()
    {
        $this->loadDashboardData();
    }

    public function updatedStartDate()
    {
        $this->syncDateRangeFromDates();
        // debounce básico: re-disparar tras pequeña espera en frontend
        $this->dispatch('dashboard-schedule-refresh');
    }

    public function updatedEndDate()
    {
        $this->syncDateRangeFromDates();
        $this->dispatch('dashboard-schedule-refresh');
    }

    private function syncDateRangeFromDates(): void
    {
        $today = now()->toDateString();
        $startOfWeek = now()->startOfWeek()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $startOfYear = now()->startOfYear()->toDateString();

        if ($this->endDate === $today && $this->startDate === $startOfWeek) {
            $this->dateRange = 'this_week';
            return;
        }

        if ($this->endDate === $today && $this->startDate === $startOfMonth) {
            $this->dateRange = 'this_month';
            return;
        }

        if ($this->endDate === $today && $this->startDate === $startOfYear) {
            $this->dateRange = 'this_year';
            return;
        }
        // Si no coincide con ninguno, no se cambia el rango actual
    }

    public function clearFilters()
    {
        $this->reset(['dateRange', 'advisorFilter']);
        $this->dateRange = 'this_month';
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
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
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
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
