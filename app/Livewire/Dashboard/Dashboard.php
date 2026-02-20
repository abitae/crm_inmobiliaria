<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    use WithPagination;

    // Datos del dashboard - agrupados por categoría
    public array $stats = [];
    public array $chartData = [
        'opportunitiesByStage' => [],
        'clientsByStatus' => [],
        'closedOpportunitiesBySeller' => [],
        'advisorPerformance' => [],
        'leaderPerformance' => [],
    ];
    public array $listData = [
        'recentActivities' => [],
        'pendingTasks' => [],
        'upcomingClosings' => [],
    ];
    public array $analyticsData = [
        'projectsByType' => [],
        'salesByMonth' => [],
        'conversionBySource' => [],
    ];

    // Filtros del dashboard
    public string $dateRange = 'this_month';
    public string $startDate = '';
    public string $endDate = '';

    protected DashboardService $dashboardService;

    // Listeners optimizados
    protected $listeners = [
        'opportunity-created' => 'refreshData',
        'opportunity-updated' => 'refreshData',
        'opportunity-deleted' => 'refreshData',
        'opportunity-probability-updated' => 'refreshData',
        'opportunity-value-updated' => 'refreshData',
    ];

    public function boot(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function mount(): void
    {
        $this->setDefaultDates();
        $this->loadDashboardData();
    }

    /**
     * Cargar todos los datos del dashboard de forma optimizada
     */
    public function loadDashboardData(): void
    {
        $this->dispatch('dashboard-loading', true);
        
        $filters = $this->getFilters();
        $user = Auth::user();

        try {
            // Cargar datos en paralelo usando métodos optimizados
            $this->loadStats($filters, $user);
            $this->loadChartData($filters, $user);
            $this->loadListData($filters, $user);
            $this->loadAnalyticsData($filters, $user);
            
            // Optimizar datos de vendedores
            $this->optimizeSellerData();
            
            // Notificar actualización de gráficos
            $this->dispatchChartUpdate();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar los datos del dashboard: ' . $e->getMessage());
        } finally {
            $this->dispatch('dashboard-loading', false);
        }
    }

    /**
     * Método de conveniencia para refrescar datos
     */
    public function refreshData(): void
    {
        $this->loadDashboardData();
    }

    /**
     * Cargar estadísticas principales
     */
    private function loadStats(array $filters, $user): void
    {
        $this->stats = $this->dashboardService->getDashboardStats($filters, $user);
    }

    /**
     * Cargar datos para gráficos
     */
    private function loadChartData(array $filters, $user): void
    {
        $this->chartData = [
            'opportunitiesByStage' => $this->dashboardService->getOpportunitiesByStage($filters, $user),
            'clientsByStatus' => $this->dashboardService->getClientsByStatus($filters, $user),
            'closedOpportunitiesBySeller' => $this->dashboardService->getClosedOpportunitiesBySeller($filters, $user),
            'advisorPerformance' => $this->dashboardService->getAdvisorPerformance($filters, $user),
            'leaderPerformance' => $this->dashboardService->getLeaderPerformance($filters, $user),
        ];
    }

    /**
     * Cargar datos para listas
     */
    private function loadListData(array $filters, $user): void
    {
        $this->listData = [
            'recentActivities' => $this->dashboardService->getRecentActivities(10, $filters, $user),
            'pendingTasks' => $this->dashboardService->getPendingTasks(10, $filters, $user),
            'upcomingClosings' => $this->dashboardService->getUpcomingClosings(10, $filters, $user),
        ];
    }

    /**
     * Cargar datos analíticos
     */
    private function loadAnalyticsData(array $filters, $user): void
    {
        $this->analyticsData = [
            'projectsByType' => $this->dashboardService->getProjectsByType($filters, $user),
            'salesByMonth' => $this->dashboardService->getSalesByMonth($filters, $user),
            'conversionBySource' => $this->dashboardService->getConversionBySource($filters, $user),
        ];
    }

    /**
     * Optimizar datos de vendedores (top 10)
     */
    private function optimizeSellerData(): void
    {
        if (is_array($this->chartData['closedOpportunitiesBySeller'])) {
            usort($this->chartData['closedOpportunitiesBySeller'], function ($a, $b) {
                return ($b->total_sales ?? 0) <=> ($a->total_sales ?? 0);
            });
            $this->chartData['closedOpportunitiesBySeller'] = array_slice(
                $this->chartData['closedOpportunitiesBySeller'], 
                0, 
                10
            );
        }
    }

    /**
     * Obtener filtros actuales
     */
    private function getFilters(): array
    {
        return [
            'date_range' => $this->dateRange,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ];
    }

    /**
     * Notificar actualización de gráficos
     */
    private function dispatchChartUpdate(): void
    {
        $this->dispatch('dashboard-data-updated', $this->chartData);
    }

    /**
     * Establecer fechas por defecto
     */
    private function setDefaultDates(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    /**
     * Actualizar rango de fechas predefinido
     */
    public function updatedDateRange(): void
    {
        $this->setDatesFromRange();
        $this->loadDashboardData();
    }

    /**
     * Actualizar fecha de inicio
     */
    public function updatedStartDate(): void
    {
        $this->syncDateRangeFromDates();
        $this->validateAndCorrectFilters();
        $this->loadDashboardData();
    }

    /**
     * Actualizar fecha de fin
     */
    public function updatedEndDate(): void
    {
        $this->syncDateRangeFromDates();
        $this->validateAndCorrectFilters();
        $this->loadDashboardData();
    }

    /**
     * Establecer fechas basadas en el rango seleccionado
     */
    private function setDatesFromRange(): void
    {
        $this->startDate = match ($this->dateRange) {
            'this_week' => now()->startOfWeek()->toDateString(),
            'this_year' => now()->startOfYear()->toDateString(),
            'this_month' => now()->startOfMonth()->toDateString(),
            default => now()->startOfMonth()->toDateString(),
        };
        $this->endDate = now()->toDateString();
    }

    /**
     * Sincronizar rango de fechas basado en las fechas actuales
     */
    private function syncDateRangeFromDates(): void
    {
        $today = now()->toDateString();
        $dateRanges = [
            'this_week' => now()->startOfWeek()->toDateString(),
            'this_month' => now()->startOfMonth()->toDateString(),
            'this_year' => now()->startOfYear()->toDateString(),
        ];

        foreach ($dateRanges as $range => $startDate) {
            if ($this->endDate === $today && $this->startDate === $startDate) {
                $this->dateRange = $range;
                return;
            }
        }
    }

    /**
     * Validar y corregir los filtros de fecha
     */
    private function validateAndCorrectFilters(): void
    {
        // Validar fechas vacías
        if (empty($this->startDate) || empty($this->endDate)) {
            $this->resetToDefaultDates();
            $this->flashError('Las fechas de inicio y fin son requeridas. Se han restablecido las fechas por defecto.');
            return;
        }

        // Validar orden de fechas
        if ($this->startDate > $this->endDate) {
            [$this->startDate, $this->endDate] = [$this->endDate, $this->startDate];
            $this->flashError('La fecha de inicio no puede ser mayor a la fecha de fin. Se han intercambiado las fechas.');
            return;
        }

        // Validar fecha futura
        $today = now()->toDateString();
        if ($this->endDate > $today) {
            $this->endDate = $today;
            $this->flashError('La fecha de fin no puede ser mayor a la fecha actual. Se ha ajustado a hoy.');
            return;
        }

        // Validar rango máximo (2 años)
        $this->validateDateRange();
    }

    /**
     * Validar que el rango de fechas no exceda 2 años
     */
    private function validateDateRange(): void
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        
        if ($start->diffInDays($end) > 730) { // 2 años
            $this->endDate = $start->addDays(730)->toDateString();
            $this->flashError('El rango de fechas no puede ser mayor a 2 años. Se ha ajustado el rango.');
        }
    }

    /**
     * Mostrar mensaje de error
     */
    private function flashError(string $message): void
    {
        session()->flash('error', $message);
    }

    /**
     * Resetear fechas a valores por defecto
     */
    private function resetToDefaultDates(): void
    {
        $this->setDefaultDates();
        $this->dateRange = 'this_month';
    }



    /**
     * Exportar datos del dashboard
     */
    public function exportDashboardData(): void
    {
        try {
            $filters = $this->getFilters();
            $user = Auth::user();
            $exportData = $this->dashboardService->exportDashboardData($filters, $user);

            $this->dispatch('dashboard-exported', $exportData);
            session()->flash('message', 'Datos del dashboard exportados exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al exportar los datos: ' . $e->getMessage());
        }
    }

    /**
     * Obtener descripción del rol del usuario
     */
    public function getUserRoleDescription(): string
    {
        $user = Auth::user();
        
        return match (true) {
            $user->isAdmin() => 'Administrador - Acceso completo',
            $user->isLider() => 'Líder de Equipo - Datos de tu equipo',
            $user->isAdvisor() => 'Vendedor - Tus datos y dateros',
            $user->isDatero() => 'Datero - Solo tus datos',
            default => 'Usuario',
        };
    }

    /**
     * Renderizar la vista del dashboard
     */
    public function render()
    {
        return view('livewire.dashboard.dashboard', [
            'stats' => $this->stats,
            'chartData' => $this->chartData,
            'listData' => $this->listData,
            'analyticsData' => $this->analyticsData,
        ]);
    }
}

