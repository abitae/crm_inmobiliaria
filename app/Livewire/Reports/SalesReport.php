<?php

namespace App\Livewire\Reports;

use App\Services\DashboardService;
use App\Models\Opportunity;
use App\Models\Client;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class SalesReport extends Component
{
    use WithPagination;

    public $dateRange = 'this_month';
    public $startDate = '';
    public $endDate = '';
    public $reportType = 'sales_summary';
    public $advisorFilter = '';
    public $projectFilter = '';
    public $clientTypeFilter = '';

    // Filtros alineados con Activities
    public $clientFilter = '';
    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';

    public $salesData = [];
    public $chartData = [];
    public $salesChartData = [];
    public $topPerformers = [];
    public $conversionMetrics = [];
    public $isLoading = false;

    // Ordenación de resumen por asesor
    public $advisorSortField = 'total_value';
    public $advisorSortDirection = 'desc';

    protected $dashboardService;

    protected $queryString = [
        'clientFilter' => ['except' => ''],
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];


    public function boot(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    private function applyCommonFilters($query)
    {
        return $query
            ->when($this->advisorFilter !== '', function ($q) {
                $q->where('advisor_id', $this->advisorFilter);
            })
            ->when($this->projectFilter !== '', function ($q) {
                $q->where('project_id', $this->projectFilter);
            })
            ->when($this->clientFilter !== '', function ($q) {
                $q->where('client_id', $this->clientFilter);
            })
            ->when($this->statusFilter !== '', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter !== '', function ($q) {
                $q->where('stage', $this->typeFilter);
            })
            ->when($this->search !== '', function ($q) {
                $search = $this->search;
                $q->where(function ($qq) use ($search) {
                    $qq->orWhereHas('client', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('project', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    });
                });
            });
    }

    public function mount()
    {
        $this->setDateRange();
        $this->generateReport();
        
        // Asegurar que los gráficos se generen al montar
        if ($this->endDate) {
            $endDate = Carbon::parse($this->endDate);
            $this->generateSalesChartDataByMonths($endDate);
        }
        
    }

    public function updating($name, $value)
    {
        if (in_array($name, ['clientFilter', 'search', 'statusFilter', 'typeFilter'])) {
            $this->resetPage();
        }
    }

    public function getClientsProperty()
    {
        return Client::orderBy('name')->get(['id', 'name']);
    }

    public function sortAdvisorBy(string $field): void
    {
        if ($this->advisorSortField === $field) {
            $this->advisorSortDirection = $this->advisorSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->advisorSortField = $field;
            $this->advisorSortDirection = 'asc';
        }
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
                if (!$this->endDate) {
                    $this->endDate = $now->format('Y-m-d');
                }
                break;
        }

        // Garantizar que la Fecha Hasta no exceda la fecha actual
        $today = $now->format('Y-m-d');
        if (!$this->endDate || $this->endDate > $today) {
            $this->endDate = $today;
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
            // Actualizar gráficos con las nuevas fechas
            $endDate = Carbon::parse($this->endDate);
            $this->generateSalesChartDataByMonths($endDate);
            $this->dispatch('sales-report-update-charts', salesChartData: $this->salesChartData);
        }
    }

    public function updatedEndDate()
    {
        if ($this->startDate && $this->endDate) {
            $this->generateReport();
            // Actualizar gráficos con las nuevas fechas
            $endDate = Carbon::parse($this->endDate);
            $this->generateSalesChartDataByMonths($endDate);
            $this->dispatch('sales-report-update-charts', salesChartData: $this->salesChartData);
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

    public function updatedClientFilter()
    {
        $this->generateReport();
    }

    public function updatedSearch()
    {
        $this->generateReport();
    }

    public function updatedStatusFilter()
    {
        $this->generateReport();
    }

    public function updatedTypeFilter()
    {
        $this->generateReport();
    }


    public function generateReport()
    {
        if (!$this->startDate || !$this->endDate) {
            return;
        }

        $this->isLoading = true;
        
        try {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

            // Generar todos los datos en paralelo usando caché
            $this->generateAllData($startDate, $endDate);
            
            // Actualizar datasets de gráficos comunes
            $this->generateSalesChartDataByMonths($endDate);
            $this->dispatch('sales-report-update-charts', salesChartData: $this->salesChartData);
        } finally {
            $this->isLoading = false;
        }
    }

    protected function generateAllData($startDate, $endDate)
    {
        $cacheKey = 'sales_report_' . md5(serialize([
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
            'filters' => [
                'advisor' => $this->advisorFilter,
                'project' => $this->projectFilter,
                'client' => $this->clientFilter,
                'status' => $this->statusFilter,
                'stage' => $this->typeFilter,
                'search' => $this->search,
            ]
        ]));

        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            // Consulta optimizada con agregaciones en una sola query
            $baseQuery = $this->applyCommonFilters(
                Opportunity::whereBetween('actual_close_date', [$startDate, $endDate])
            );

            // Métricas principales con una sola consulta
            $metrics = $baseQuery->clone()
                ->selectRaw('
                    COUNT(*) as total_opportunities,
                    SUM(CASE WHEN status = "ganada" THEN 1 ELSE 0 END) as won_opportunities,
                    SUM(CASE WHEN status = "ganada" THEN close_value ELSE 0 END) as total_sales,
                    AVG(CASE WHEN status = "ganada" THEN close_value ELSE NULL END) as avg_deal_size,
                    COUNT(DISTINCT client_id) as total_clients
                ')
                ->first();

            $this->salesData = [
                'total_sales' => $metrics->total_sales ?? 0,
                'total_opportunities' => $metrics->won_opportunities ?? 0,
                'average_deal_size' => $metrics->avg_deal_size ?? 0,
                'total_clients' => $metrics->total_clients ?? 0,
            ];

            // Métricas de conversión
            $this->conversionMetrics = [
                'total_opportunities' => $metrics->total_opportunities ?? 0,
                'won_opportunities' => $metrics->won_opportunities ?? 0,
                'win_rate' => $metrics->total_opportunities > 0 
                    ? round(($metrics->won_opportunities / $metrics->total_opportunities) * 100, 2) 
                    : 0,
            ];

            return true;
        });
    }

    public function generateSalesSummary($startDate, $endDate)
    {
        $query = $this->applyCommonFilters(
            Opportunity::where('status', 'ganada')
                ->whereBetween('actual_close_date', [$startDate, $endDate])
        );

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
        $query = $this->applyCommonFilters(
            Opportunity::where('status', 'ganada')
                ->whereBetween('actual_close_date', [$startDate, $endDate])
        );

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
        $query = $this->applyCommonFilters(
            Opportunity::where('status', 'ganada')
                ->whereBetween('actual_close_date', [$startDate, $endDate])
        );

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
        $query = $this->applyCommonFilters(
            Opportunity::whereBetween('created_at', [$startDate, $endDate])
        );

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
        $salesByDate = $this->applyCommonFilters(
            Opportunity::where('status', 'ganada')
            ->whereBetween('actual_close_date', [$startDate, $endDate])
        )
            ->selectRaw('DATE(actual_close_date) as date, SUM(close_value) as total_sales, COUNT(*) as deals')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $this->chartData = [
            'labels' => $salesByDate->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            })->values(),
            'sales' => $salesByDate->pluck('total_sales')->values(),
            'deals' => $salesByDate->pluck('deals')->values(),
        ];
    }

    protected function generateSalesChartDataByMonths(Carbon $endDate)
    {
        $months = 6; // Fijo a 6 meses
        $start = (clone $endDate)->copy()->startOfMonth()->subMonths($months - 1);
        $end = (clone $endDate)->copy()->endOfMonth();

        $cacheKey = 'sales_chart_' . md5(serialize([
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'filters' => $this->getFiltersForCache()
        ]));

        $this->salesChartData = Cache::remember($cacheKey, 300, function () use ($start, $end) {
            $rows = $this->applyCommonFilters(
                Opportunity::where('status', 'ganada')
                    ->whereBetween('actual_close_date', [$start, $end])
            )
                ->selectRaw('DATE_FORMAT(actual_close_date, "%Y-%m") as ym, SUM(close_value) as total_sales, COUNT(*) as deals')
                ->groupBy('ym')
                ->orderBy('ym')
                ->get()
                ->keyBy('ym');

            $labels = [];
            $sales = [];
            $deals = [];

            $cursor = $start->copy();
            while ($cursor <= $end) {
                $ym = $cursor->format('Y-m');
                $labels[] = $cursor->locale(app()->getLocale())->isoFormat('MMM YYYY');
                $sales[] = (float) optional($rows->get($ym))->total_sales ?: 0;
                $deals[] = (int) optional($rows->get($ym))->deals ?: 0;
                $cursor->addMonth();
            }

            return [
                'labels' => $labels,
                'sales' => $sales,
                'deals' => $deals,
            ];
        });
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


    protected function getFiltersForCache()
    {
        return [
            'advisor' => $this->advisorFilter,
            'project' => $this->projectFilter,
            'client' => $this->clientFilter,
            'status' => $this->statusFilter,
            'stage' => $this->typeFilter,
            'search' => $this->search,
        ];
    }

    public function exportReport()
    {
        // Implementar exportación a Excel/CSV
        session()->flash('message', 'Reporte exportado exitosamente.');
    }

    protected function getSalesDetails()
    {
        if (!$this->startDate || !$this->endDate) {
            return Opportunity::whereRaw('1=0')->paginate(15);
        }

        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        return $this->applyCommonFilters(
            Opportunity::with(['client', 'project', 'unit', 'advisor'])
                ->whereBetween('actual_close_date', [$startDate, $endDate])
        )
            ->select(['*'])
            ->selectRaw('actual_close_date as sale_date, close_value as sale_value, 0 as commission_value')
            ->orderByDesc('actual_close_date')
            ->paginate(15);
    }

    protected function getAdvisorSummary()
    {
        if (!$this->startDate || !$this->endDate) {
            return collect();
        }

        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        $baseTotal = Opportunity::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($this->advisorFilter !== '', function ($q) {
                $q->where('advisor_id', $this->advisorFilter);
            })
            ->when($this->projectFilter !== '', function ($q) {
                $q->where('project_id', $this->projectFilter);
            })
            ->when($this->clientFilter !== '', function ($q) {
                $q->where('client_id', $this->clientFilter);
            })
            ->when($this->statusFilter !== '', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter !== '', function ($q) {
                $q->where('stage', $this->typeFilter);
            })
            ->when($this->search !== '', function ($q) {
                $search = $this->search;
                $q->where(function ($qq) use ($search) {
                    $qq->orWhereHas('client', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('project', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->selectRaw('advisor_id, COUNT(*) as total_opps')
            ->groupBy('advisor_id')
            ->get()
            ->keyBy('advisor_id');

        $won = Opportunity::query()
            ->where('status', 'pagado')
            ->whereBetween('actual_close_date', [$startDate, $endDate])
            ->when($this->advisorFilter !== '', function ($q) {
                $q->where('advisor_id', $this->advisorFilter);
            })
            ->when($this->projectFilter !== '', function ($q) {
                $q->where('project_id', $this->projectFilter);
            })
            ->when($this->clientFilter !== '', function ($q) {
                $q->where('client_id', $this->clientFilter);
            })
            ->when($this->typeFilter !== '', function ($q) {
                $q->where('stage', $this->typeFilter);
            })
            ->when($this->search !== '', function ($q) {
                $search = $this->search;
                $q->where(function ($qq) use ($search) {
                    $qq->orWhereHas('client', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('project', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->with('commissions')
            ->selectRaw('advisor_id, COUNT(*) as sales_count, SUM(close_value) as total_value')
            ->groupBy('advisor_id')
            ->get()
            ->keyBy('advisor_id');

        $advisorIds = $baseTotal->keys()->merge($won->keys())->unique()->values();
        if ($advisorIds->isEmpty()) {
            return collect();
        }

        // Obtener comisiones por asesor
        $commissions = \App\Models\Commission::query()
            ->whereIn('advisor_id', $advisorIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('advisor_id, SUM(total_commission) as total_commission')
            ->groupBy('advisor_id')
            ->get()
            ->keyBy('advisor_id');

        $users = \App\Models\User::whereIn('id', $advisorIds)->get()->keyBy('id');

        $summary = $advisorIds->map(function ($advisorId) use ($users, $baseTotal, $won, $commissions) {
            $user = $users->get($advisorId);
            $totalOpps = (int) optional($baseTotal->get($advisorId))->total_opps;
            $salesCount = (int) optional($won->get($advisorId))->sales_count;
            $totalValue = (float) optional($won->get($advisorId))->total_value;
            $totalCommission = (float) optional($commissions->get($advisorId))->total_commission;
            $conversion = $totalOpps > 0 ? round(($salesCount / $totalOpps) * 100, 2) : 0;

            return (object) [
                'id' => $advisorId,
                'name' => $user->name ?? 'N/A',
                'email' => $user->email ?? null,
                'sales_count' => $salesCount,
                'total_value' => $totalValue,
                'total_commission' => $totalCommission,
                'conversion_rate' => $conversion,
            ];
        });

        $field = $this->advisorSortField;
        $direction = strtolower($this->advisorSortDirection) === 'asc' ? 'asc' : 'desc';

        return $direction === 'asc'
            ? $summary->sortBy($field)->values()
            : $summary->sortByDesc($field)->values();
    }

    public function render()
    {
        $advisors = \App\Models\User::getAvailableAdvisors(Auth::user());
        $projects = \App\Models\Project::active()->get();
        $clientTypes = ['inversor', 'comprador', 'empresa', 'constructor'];
        $clients = $this->clients;
        $salesDetails = $this->getSalesDetails();
        $advisorSummary = $this->getAdvisorSummary();

        return view('livewire.reports.sales-report', [
            'advisors' => $advisors,
            'projects' => $projects,
            'clientTypes' => $clientTypes,
            'clients' => $clients,
            'salesDetails' => $salesDetails,
            'advisorSummary' => $advisorSummary,
            'salesChartData' => $this->salesChartData,
        ]);
    }
}
