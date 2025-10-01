<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Project;
use App\Models\Opportunity;
use App\Models\Task;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Obtener estadísticas generales del dashboard
     */
    public function getDashboardStats(array $filters = []): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startOfMonth = Carbon::parse($filters['start_date'])->startOfDay();
            $endOfMonth = Carbon::parse($filters['end_date'])->endOfDay();
        }

        // Estadísticas de clientes
        $totalClients = Client::count();
        $newClientsThisMonth = Client::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $activeClients = Client::active()->count();

        // Estadísticas de proyectos
        $totalProjects = Project::count();
        $activeProjects = Project::active()->count();
        $projectsWithAvailableUnits = Project::withAvailableUnits()->count();

        // Estadísticas de oportunidades
        $totalOpportunities = Opportunity::count();
        $activeOpportunities = Opportunity::active()->count();
        $wonOpportunities = Opportunity::won()->count();
        $overdueOpportunities = Opportunity::overdue()->count();

        // Estadísticas de tareas
        $totalTasks = Task::count();
        $pendingTasks = Task::where('status', 'pendiente')->count();
        $overdueTasks = Task::where('due_date', '<', $now)->where('status', 'pendiente')->count();

        // Estadísticas de actividades
        $totalActivities = Activity::count();
        $activitiesThisWeek = Activity::whereBetween('created_at', [
            $now->startOfWeek(),
            $now->endOfWeek()
        ])->count();

        // Valor total de oportunidades activas
        $totalOpportunityValue = Opportunity::active()->sum('expected_value');
        $weightedOpportunityValue = Opportunity::active()
            ->sum(DB::raw('expected_value * probability / 100'));

        // Tasa de conversión
        $conversionRate = $totalOpportunities > 0 ?
            round(($wonOpportunities / $totalOpportunities) * 100, 2) : 0;

        return [
            'clients' => [
                'total' => $totalClients,
                'new_this_month' => $newClientsThisMonth,
                'active' => $activeClients
            ],
            'projects' => [
                'total' => $totalProjects,
                'active' => $activeProjects,
                'with_available_units' => $projectsWithAvailableUnits
            ],
            'opportunities' => [
                'total' => $totalOpportunities,
                'active' => $activeOpportunities,
                'won' => $wonOpportunities,
                'overdue' => $overdueOpportunities,
                'total_value' => $totalOpportunityValue,
                'weighted_value' => $weightedOpportunityValue,
                'conversion_rate' => $conversionRate
            ],
            'tasks' => [
                'total' => $totalTasks,
                'pending' => $pendingTasks,
                'overdue' => $overdueTasks
            ],
            'activities' => [
                'total' => $totalActivities,
                'this_week' => $activitiesThisWeek
            ]
        ];
    }

    /**
     * Obtener gráfico de oportunidades por etapa
     */
    public function getOpportunitiesByStage(array $filters = []): array
    {
        $stages = ['captado', 'calificado', 'contacto', 'propuesta', 'visita', 'negociacion', 'cierre'];

        $data = [];
        foreach ($stages as $stage) {
            $query = Opportunity::byStage($stage);
            
            // Aplicar filtros si se proporcionan
            if (!empty($filters)) {
                if (isset($filters['date_range'])) {
                    $this->applyDateRangeFilter($query, $filters['date_range']);
                }
                
                if (isset($filters['advisor_id']) && !empty($filters['advisor_id'])) {
                    $query->where('advisor_id', $filters['advisor_id']);
                }
            }
            
            $count = $query->count();
            $data[] = [
                'stage' => ucfirst($stage),
                'count' => $count
            ];
        }

        return $data;
    }

    /**
     * Obtener gráfico de clientes por estado
     */
    public function getClientsByStatus(array $filters = []): array
    {
        $statuses = ['nuevo', 'contacto_inicial', 'en_seguimiento', 'cierre', 'perdido'];

        $data = [];
        foreach ($statuses as $status) {
            $query = Client::byStatus($status);
            
            // Aplicar filtros si se proporcionan
            if (!empty($filters)) {
                if (isset($filters['date_range'])) {
                    $this->applyDateRangeFilter($query, $filters['date_range']);
                }
                
                if (isset($filters['advisor_id']) && !empty($filters['advisor_id'])) {
                    $query->where('assigned_advisor_id', $filters['advisor_id']);
                }
            }
            
            $count = $query->count();
            $data[] = [
                'status' => ucfirst(str_replace('_', ' ', $status)),
                'count' => $count
            ];
        }

        return $data;
    }

    /**
     * Obtener gráfico de proyectos por tipo
     */
    public function getProjectsByType(array $filters = []): array
    {
        $types = ['lotes', 'casas', 'departamentos', 'oficinas', 'mixto'];

        $data = [];
        foreach ($types as $type) {
            $query = Project::byType($type);
            
            // Aplicar filtros si se proporcionan
            if (!empty($filters)) {
                if (isset($filters['date_range'])) {
                    $this->applyDateRangeFilter($query, $filters['date_range']);
                }
                
                if (isset($filters['advisor_id']) && !empty($filters['advisor_id'])) {
                    $query->where('assigned_advisor_id', $filters['advisor_id']);
                }
            }

            $count = $query->count();
            $data[] = [
                'type' => ucfirst($type),
                'count' => $count
            ];
        }

        return $data;
    }

    /**
     * Obtener gráfico de ventas por mes (últimos 12 meses)
     */
    public function getSalesByMonth(array $filters = []): array
    {
        $months = [];
        $sales = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $monthStart = $date->startOfMonth();
            $monthEnd = $date->endOfMonth();

            $monthlyQuery = Opportunity::where('status', 'pagado')
                ->whereBetween('actual_close_date', [$monthStart, $monthEnd]);

            if (!empty($filters['advisor_id'])) {
                $monthlyQuery->where('advisor_id', $filters['advisor_id']);
            }

            $monthlySales = $monthlyQuery->sum('close_value');

            $months[] = $monthName;
            $sales[] = $monthlySales;
        }

        return [
            'months' => $months,
            'sales' => $sales
        ];
    }

    /**
     * Obtener actividades recientes
     */
    public function getRecentActivities(int $limit = 10, array $filters = []): array
    {
        $query = Activity::with(['assignedTo', 'client', 'project', 'opportunity']);

        // Aplicar filtros si se proporcionan
        if (!empty($filters)) {
            if (isset($filters['date_range'])) {
                $this->applyDateRangeFilter($query, $filters['date_range']);
            }
            
            if (isset($filters['advisor_id']) && !empty($filters['advisor_id'])) {
                $query->where('assigned_to', $filters['advisor_id']);
            }
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Obtener tareas pendientes
     */
    public function getPendingTasks(int $limit = 10, array $filters = []): array
    {
        $query = Task::with(['assignedTo', 'client', 'project', 'opportunity'])
            ->where('status', 'pendiente');

        // Aplicar filtros si se proporcionan
        if (!empty($filters)) {
            if (isset($filters['date_range'])) {
                $this->applyDateRangeFilter($query, $filters['date_range']);
            }
            
            if (isset($filters['advisor_id']) && !empty($filters['advisor_id'])) {
                $query->where('assigned_to', $filters['advisor_id']);
            }
        }

        return $query->orderBy('due_date', 'asc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Obtener oportunidades que cierran pronto
     */
    public function getUpcomingClosings(int $limit = 10, array $filters = []): array
    {
        $nextWeek = Carbon::now()->addWeek();
        $query = Opportunity::with(['client', 'project', 'unit'])
            ->where('status', 'activa')
            ->where('expected_close_date', '<=', $nextWeek);

        if (!empty($filters['advisor_id'])) {
            $query->where('advisor_id', $filters['advisor_id']);
        }

        return $query->orderBy('expected_close_date', 'asc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Obtener métricas de rendimiento por asesor
     */
    public function getAdvisorPerformance(array $filters = []): array
    {
        // Solo líderes y sumar ventas de sus cazadores (hunters)
        $query = DB::table('users as leaders')
            ->leftJoin('users as hunters', 'hunters.lider_id', '=', 'leaders.id')
            ->leftJoin('opportunities', 'opportunities.advisor_id', '=', 'hunters.id')
            ->select(
                'leaders.id',
                'leaders.name',
                DB::raw('COUNT(DISTINCT opportunities.id) as total_opportunities'),
                DB::raw('COUNT(DISTINCT CASE WHEN opportunities.status = "pagado" THEN opportunities.id END) as won_opportunities'),
                DB::raw('SUM(CASE WHEN opportunities.status = "pagado" THEN opportunities.close_value ELSE 0 END) as total_sales')
            )
            ->whereNull('leaders.lider_id') // líderes no tienen líder asignado
            ->groupBy('leaders.id', 'leaders.name')
            ->orderBy('total_sales', 'desc');

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('opportunities.actual_close_date', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay(),
            ]);
        }

        if (!empty($filters['advisor_id'])) {
            $query->where('leaders.id', $filters['advisor_id']);
        }

        return $query->get()->toArray();
    }

    /**
     * Obtener métricas de conversión por fuente
     */
    public function getConversionBySource(): array
    {
        return DB::table('clients')
            ->leftJoin('opportunities', 'clients.id', '=', 'opportunities.client_id')
            ->select(
                'clients.source',
                DB::raw('COUNT(DISTINCT clients.id) as total_clients'),
                DB::raw('COUNT(DISTINCT opportunities.id) as total_opportunities'),
                DB::raw('COUNT(DISTINCT CASE WHEN opportunities.status = "pagado" THEN opportunities.id END) as won_opportunities'),
                DB::raw('ROUND((COUNT(DISTINCT CASE WHEN opportunities.status = "pagado" THEN opportunities.id END) / COUNT(DISTINCT clients.id)) * 100, 2) as conversion_rate')
            )
            ->groupBy('clients.source')
            ->orderBy('conversion_rate', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Obtener oportunidades cerradas por vendedor
     */
    public function getClosedOpportunitiesBySeller(array $filters = []): array
    {
        $query = DB::table('users')
            ->leftJoin('opportunities', 'users.id', '=', 'opportunities.advisor_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(CASE WHEN opportunities.status = "pagado" THEN opportunities.id END) as closed_opportunities'),
                DB::raw('SUM(CASE WHEN opportunities.status = "pagado" THEN opportunities.close_value ELSE 0 END) as total_sales'),
                DB::raw('AVG(CASE WHEN opportunities.status = "pagado" THEN opportunities.close_value ELSE NULL END) as average_sale')
            )
            ->where('opportunities.status', 'pagado')
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_sales', 'desc');

        // Aplicar filtros si se proporcionan
        if (!empty($filters)) {
            if (isset($filters['date_range'])) {
                $this->applyDateRangeFilter($query, $filters['date_range']);
            }
            
            if (isset($filters['advisor_id']) && !empty($filters['advisor_id'])) {
                $query->where('users.id', $filters['advisor_id']);
            }
        }

        return $query->get()->toArray();
    }

    /**
     * Aplicar filtro de rango de fechas a una consulta
     */
    private function applyDateRangeFilter($query, string $dateRange): void
    {
        $now = Carbon::now();
        
        // Verificar si la consulta tiene un JOIN con opportunities
        $hasOpportunitiesJoin = false;
        if (method_exists($query, 'getQuery')) {
            $queryBuilder = $query->getQuery();
            if (isset($queryBuilder->joins)) {
                foreach ($queryBuilder->joins as $join) {
                    if (str_contains($join->table, 'opportunities')) {
                        $hasOpportunitiesJoin = true;
                        break;
                    }
                }
            }
        }
        
        // Solo aplicar filtros de fecha si hay JOIN con opportunities
        if (!$hasOpportunitiesJoin) {
            return;
        }
        
        switch ($dateRange) {
            case 'today':
                $query->whereDate('opportunities.created_at', $now->toDateString());
                break;
            case 'this_week':
                $query->whereBetween('opportunities.created_at', [
                    $now->startOfWeek(),
                    $now->endOfWeek()
                ]);
                break;
            case 'this_month':
                $query->whereBetween('opportunities.created_at', [
                    $now->startOfMonth(),
                    $now->endOfMonth()
                ]);
                break;
            case 'this_quarter':
                $query->whereBetween('opportunities.created_at', [
                    $now->startOfQuarter(),
                    $now->endOfQuarter()
                ]);
                break;
            case 'this_year':
                $query->whereBetween('opportunities.created_at', [
                    $now->startOfYear(),
                    $now->endOfYear()
                ]);
                break;
            case 'last_month':
                $lastMonth = $now->subMonth();
                $query->whereBetween('opportunities.created_at', [
                    $lastMonth->startOfMonth(),
                    $lastMonth->endOfMonth()
                ]);
                break;
            case 'last_quarter':
                $lastQuarter = $now->subQuarter();
                $query->whereBetween('opportunities.created_at', [
                    $lastQuarter->startOfQuarter(),
                    $lastQuarter->endOfQuarter()
                ]);
                break;
            case 'last_year':
                $lastYear = $now->subYear();
                $query->whereBetween('opportunities.created_at', [
                    $lastYear->startOfYear(),
                    $lastYear->endOfYear()
                ]);
                break;
        }
    }
}
