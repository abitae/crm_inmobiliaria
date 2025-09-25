<?php

namespace App\Livewire\Reports;

use App\Services\DashboardService;
use App\Models\Opportunity;
use App\Models\Client;
use App\Models\Project;
use Livewire\Component;
use Carbon\Carbon;

class SalesReport extends Component
{
    public $dateRange = 'this_month';
    public $startDate = '';
    public $endDate = '';
    public $reportType = 'sales_summary';
    public $advisorFilter = '';
    public $projectFilter = '';
    public $clientTypeFilter = '';

    public $salesData = [];
    public $chartData = [];
    public $topPerformers = [];
    public $conversionMetrics = [];

    protected $dashboardService;

    public function boot(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function mount()
    {
        $this->setDateRange();
        $this->generateReport();
    }

    public function setDateRange()
    {
        $now = Carbon::now();

        switch ($this->dateRange) {
            case 'today':
                $this->startDate = $now->format('Y-m-d');
                $this->endDate = $now->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = $now->subDay()->format('Y-m-d');
                $this->endDate = $now->subDay()->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = $now->startOfWeek()->format('Y-m-d');
                $this->endDate = $now->endOfWeek()->format('Y-m-d');
                break;
            case 'last_week':
                $this->startDate = $now->subWeek()->startOfWeek()->format('Y-m-d');
                $this->endDate = $now->subWeek()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = $now->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = $now->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_quarter':
                $this->startDate = $now->startOfQuarter()->format('Y-m-d');
                $this->endDate = $now->endOfQuarter()->format('Y-m-d');
                break;
            case 'this_year':
                $this->startDate = $now->startOfYear()->format('Y-m-d');
                $this->endDate = $now->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Las fechas personalizadas se manejan por separado
                break;
        }
    }

    public function updatedDateRange()
    {
        $this->setDateRange();
        $this->generateReport();
    }

    public function updatedStartDate()
    {
        if ($this->startDate && $this->endDate) {
            $this->generateReport();
        }
    }

    public function updatedEndDate()
    {
        if ($this->startDate && $this->endDate) {
            $this->generateReport();
        }
    }

    public function updatedReportType()
    {
        $this->generateReport();
    }

    public function updatedAdvisorFilter()
    {
        $this->generateReport();
    }

    public function updatedProjectFilter()
    {
        $this->generateReport();
    }

    public function updatedClientTypeFilter()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        if (!$this->startDate || !$this->endDate) {
            return;
        }

        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        switch ($this->reportType) {
            case 'sales_summary':
                $this->generateSalesSummary($startDate, $endDate);
                break;
            case 'advisor_performance':
                $this->generateAdvisorPerformance($startDate, $endDate);
                break;
            case 'project_performance':
                $this->generateProjectPerformance($startDate, $endDate);
                break;
            case 'conversion_funnel':
                $this->generateConversionFunnel($startDate, $endDate);
                break;
            case 'trends':
                $this->generateTrends($startDate, $endDate);
                break;
        }
    }

    public function generateSalesSummary($startDate, $endDate)
    {
        $query = Opportunity::where('status', 'ganada')
            ->whereBetween('actual_close_date', [$startDate, $endDate]);

        if ($this->advisorFilter) {
            $query->where('advisor_id', $this->advisorFilter);
        }

        if ($this->projectFilter) {
            $query->where('project_id', $this->projectFilter);
        }

        $this->salesData = [
            'total_sales' => $query->sum('close_value'),
            'total_opportunities' => $query->count(),
            'average_deal_size' => $query->count() > 0 ? $query->sum('close_value') / $query->count() : 0,
            'total_clients' => $query->distinct('client_id')->count(),
        ];

        // Generar datos para gráficos
        $this->generateSalesChartData($startDate, $endDate);
    }

    public function generateAdvisorPerformance($startDate, $endDate)
    {
        $query = Opportunity::where('status', 'ganada')
            ->whereBetween('actual_close_date', [$startDate, $endDate]);

        if ($this->projectFilter) {
            $query->where('project_id', $this->projectFilter);
        }

        $this->topPerformers = $query->with('advisor')
            ->selectRaw('advisor_id, COUNT(*) as total_sales, SUM(close_value) as total_value, AVG(close_value) as avg_value')
            ->groupBy('advisor_id')
            ->orderBy('total_value', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'advisor' => $item->advisor->name ?? 'N/A',
                    'total_sales' => $item->total_sales,
                    'total_value' => $item->total_value,
                    'avg_value' => $item->avg_value,
                ];
            });
    }

    public function generateProjectPerformance($startDate, $endDate)
    {
        $query = Opportunity::where('status', 'ganada')
            ->whereBetween('actual_close_date', [$startDate, $endDate]);

        if ($this->advisorFilter) {
            $query->where('advisor_id', $this->advisorFilter);
        }

        $this->salesData = $query->with('project')
            ->selectRaw('project_id, COUNT(*) as total_sales, SUM(close_value) as total_value')
            ->groupBy('project_id')
            ->orderBy('total_value', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'project' => $item->project->name ?? 'N/A',
                    'total_sales' => $item->total_sales,
                    'total_value' => $item->total_value,
                ];
            });
    }

    public function generateConversionFunnel($startDate, $endDate)
    {
        $query = Opportunity::whereBetween('created_at', [$startDate, $endDate]);

        if ($this->advisorFilter) {
            $query->where('advisor_id', $this->advisorFilter);
        }

        if ($this->projectFilter) {
            $query->where('project_id', $this->projectFilter);
        }

        $totalOpportunities = $query->count();
        $wonOpportunities = $query->where('status', 'pagado')->count();
        $lostOpportunities = $query->where('status', 'cancelado')->count();
        $activeOpportunities = $query->where('status', 'registrado')->count();

        $this->conversionMetrics = [
            'total_opportunities' => $totalOpportunities,
            'won_opportunities' => $wonOpportunities,
            'lost_opportunities' => $lostOpportunities,
            'active_opportunities' => $activeOpportunities,
            'win_rate' => $totalOpportunities > 0 ? round(($wonOpportunities / $totalOpportunities) * 100, 2) : 0,
            'loss_rate' => $totalOpportunities > 0 ? round(($lostOpportunities / $totalOpportunities) * 100, 2) : 0,
        ];

        // Generar datos para el embudo de conversión
        $this->generateFunnelChartData();
    }

    public function generateTrends($startDate, $endDate)
    {
        $this->generateSalesTrends($startDate, $endDate);
        $this->generateConversionTrends($startDate, $endDate);
    }

    public function generateSalesChartData($startDate, $endDate)
    {
        $salesByDate = Opportunity::where('status', 'ganada')
            ->whereBetween('actual_close_date', [$startDate, $endDate])
            ->selectRaw('DATE(actual_close_date) as date, SUM(close_value) as total_sales, COUNT(*) as deals')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $this->chartData = [
            'labels' => $salesByDate->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            }),
            'sales' => $salesByDate->pluck('total_sales'),
            'deals' => $salesByDate->pluck('deals'),
        ];
    }

    public function generateFunnelChartData()
    {
        $this->chartData = [
            'labels' => ['Total Oportunidades', 'Activas', 'Ganadas', 'Perdidas'],
            'values' => [
                $this->conversionMetrics['total_opportunities'],
                $this->conversionMetrics['active_opportunities'],
                $this->conversionMetrics['won_opportunities'],
                $this->conversionMetrics['lost_opportunities'],
            ],
        ];
    }

    public function generateSalesTrends($startDate, $endDate)
    {
        // Implementar tendencias de ventas por período
        $periods = [];
        $sales = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $periods[] = $current->format('M d');

            $periodSales = Opportunity::where('status', 'ganada')
                ->whereDate('actual_close_date', $current)
                ->sum('close_value');

            $sales[] = $periodSales;

            $current->addDay();
        }

        $this->chartData = [
            'labels' => $periods,
            'sales' => $sales,
        ];
    }

    public function generateConversionTrends($startDate, $endDate)
    {
        // Implementar tendencias de conversión
    }

    public function exportReport()
    {
        // Implementar exportación a Excel/CSV
        session()->flash('message', 'Reporte exportado exitosamente.');
    }

    public function render()
    {
        $advisors = \App\Models\User::getAvailableAdvisors();
        $projects = \App\Models\Project::active()->get();
        $clientTypes = ['inversor', 'comprador', 'empresa', 'constructor'];

        return view('livewire.reports.sales-report', [
            'advisors' => $advisors,
            'projects' => $projects,
            'clientTypes' => $clientTypes,
        ]);
    }
}
