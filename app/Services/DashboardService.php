<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Project;
use App\Models\Opportunity;
use App\Models\Task;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Obtener estadísticas generales del dashboard según el rol del usuario
     */
    public function getDashboardStats(array $filters = [], ?User $user = null): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startOfMonth = Carbon::parse($filters['start_date'])->startOfDay();
            $endOfMonth = Carbon::parse($filters['end_date'])->endOfDay();
        }

        // Obtener IDs de usuarios según jerarquía
        $userIds = $this->getUserIdsByHierarchy($user);

        // Estadísticas de clientes
        $totalClients = Client::whereIn('assigned_advisor_id', $userIds)->count();
        $newClientsThisMonth = Client::whereIn('assigned_advisor_id', $userIds)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $activeClients = Client::whereIn('assigned_advisor_id', $userIds)->active()->count();

        // Estadísticas de proyectos (los proyectos no tienen assigned_advisor_id, usamos created_by)
        $totalProjects = Project::whereIn('created_by', $userIds)->count();
        $activeProjects = Project::whereIn('created_by', $userIds)->active()->count();
        $projectsWithAvailableUnits = Project::whereIn('created_by', $userIds)->withAvailableUnits()->count();

        // Estadísticas de oportunidades
        $totalOpportunities = Opportunity::whereIn('advisor_id', $userIds)->count();
        $activeOpportunities = Opportunity::whereIn('advisor_id', $userIds)->active()->count();
        $wonOpportunities = Opportunity::whereIn('advisor_id', $userIds)->won()->count();
        $overdueOpportunities = Opportunity::whereIn('advisor_id', $userIds)->overdue()->count();

        // Estadísticas de tareas
        $totalTasks = Task::whereIn('assigned_to', $userIds)->count();
        $pendingTasks = Task::whereIn('assigned_to', $userIds)->where('status', 'pendiente')->count();
        $overdueTasks = Task::whereIn('assigned_to', $userIds)
            ->where('due_date', '<', $now)->where('status', 'pendiente')->count();

        // Estadísticas de actividades
        $totalActivities = Activity::whereIn('assigned_to', $userIds)->count();
        $activitiesThisWeek = Activity::whereIn('assigned_to', $userIds)
            ->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()])->count();

        // Valor total de oportunidades activas
        $totalOpportunityValue = Opportunity::whereIn('advisor_id', $userIds)->active()->sum('expected_value');
        $weightedOpportunityValue = Opportunity::whereIn('advisor_id', $userIds)->active()
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
     * Obtener IDs de usuarios según la jerarquía del usuario autenticado
     */
    public function getUserIdsByHierarchy(?User $user): array
    {
        if (!$user) {
            return [];
        }

        if ($user->isAdmin()) {
            // Admin puede ver todos los datos
            return User::pluck('id')->toArray();
        }

        if ($user->isLider()) {
            // Líder puede ver sus datos, de sus vendedores y de los dateros de sus vendedores
            $userIds = [$user->id]; // Incluir al líder
            
            // Obtener vendedores a cargo
            $vendedoresIds = User::where('lider_id', $user->id)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'vendedor');
                })->pluck('id')->toArray();
            
            $userIds = array_merge($userIds, $vendedoresIds);
            
            // Obtener dateros de los vendedores
            $daterosIds = User::whereIn('lider_id', $vendedoresIds)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'datero');
                })->pluck('id')->toArray();
            
            $userIds = array_merge($userIds, $daterosIds);
            
            return array_unique($userIds);
        }

        if ($user->isAdvisor()) {
            // Vendedor puede ver sus datos y de sus dateros
            $userIds = [$user->id]; // Incluir al vendedor
            
            // Obtener dateros a cargo
            $daterosIds = User::where('lider_id', $user->id)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'datero');
                })->pluck('id')->toArray();
            
            $userIds = array_merge($userIds, $daterosIds);
            
            return array_unique($userIds);
        }

        if ($user->isDatero()) {
            // Datero solo puede ver sus propios datos
            return [$user->id];
        }

        return [$user->id];
    }

    /**
     * Obtener gráfico de oportunidades por etapa
     */
    public function getOpportunitiesByStage(array $filters = [], ?User $user = null): array
    {
        $stages = ['calificado', 'visita', 'cierre'];
        $userIds = $this->getUserIdsByHierarchy($user);

        $data = [];
        foreach ($stages as $stage) {
            $query = Opportunity::byStage($stage)->whereIn('advisor_id', $userIds);
            
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
    public function getClientsByStatus(array $filters = [], ?User $user = null): array
    {
        $statuses = ['nuevo', 'contacto_inicial', 'en_seguimiento', 'cierre', 'perdido'];
        $userIds = $this->getUserIdsByHierarchy($user);

        $data = [];
        foreach ($statuses as $status) {
            $query = Client::byStatus($status)->whereIn('assigned_advisor_id', $userIds);
            
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
    public function getProjectsByType(array $filters = [], ?User $user = null): array
    {
        $types = ['lotes', 'casas', 'departamentos', 'oficinas', 'mixto'];
        $userIds = $this->getUserIdsByHierarchy($user);

        $data = [];
        foreach ($types as $type) {
            $query = Project::byType($type)->whereIn('created_by', $userIds);
            
            // Aplicar filtros si se proporcionan
            if (!empty($filters)) {
                if (isset($filters['date_range'])) {
                    $this->applyDateRangeFilter($query, $filters['date_range']);
                }
                
                if (isset($filters['advisor_id']) && !empty($filters['advisor_id'])) {
                    $query->where('created_by', $filters['advisor_id']);
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
    public function getSalesByMonth(array $filters = [], ?User $user = null): array
    {
        $months = [];
        $sales = [];
        $userIds = $this->getUserIdsByHierarchy($user);

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $monthStart = $date->startOfMonth();
            $monthEnd = $date->endOfMonth();

            $monthlyQuery = Opportunity::where('status', 'pagado')
                ->whereIn('advisor_id', $userIds)
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
    public function getRecentActivities(int $limit = 10, array $filters = [], ?User $user = null): array
    {
        $userIds = $this->getUserIdsByHierarchy($user);
        $query = Activity::with(['assignedTo', 'client', 'project', 'opportunity'])
            ->whereIn('assigned_to', $userIds);

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
    public function getPendingTasks(int $limit = 10, array $filters = [], ?User $user = null): array
    {
        $userIds = $this->getUserIdsByHierarchy($user);
        $query = Task::with(['assignedTo', 'client', 'project', 'opportunity'])
            ->where('status', 'pendiente')
            ->whereIn('assigned_to', $userIds);

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
    public function getUpcomingClosings(int $limit = 10, array $filters = [], ?User $user = null): array
    {
        $nextWeek = Carbon::now()->addWeek();
        $userIds = $this->getUserIdsByHierarchy($user);
        $query = Opportunity::with(['client', 'project', 'unit'])
            ->where('status', 'activa')
            ->whereIn('advisor_id', $userIds)
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
    public function getAdvisorPerformance(array $filters = [], ?User $user = null): array
    {
        $userIds = $this->getUserIdsByHierarchy($user);
        
        // Obtener usuarios que pueden ser mostrados según jerarquía
        $query = DB::table('users')
            ->leftJoin('opportunities', 'users.id', '=', 'opportunities.advisor_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(DISTINCT opportunities.id) as total_opportunities'),
                DB::raw('COUNT(DISTINCT CASE WHEN opportunities.status = "pagado" THEN opportunities.id END) as won_opportunities'),
                DB::raw('SUM(CASE WHEN opportunities.status = "pagado" THEN opportunities.close_value ELSE 0 END) as total_sales')
            )
            ->whereIn('users.id', $userIds)
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_sales', 'desc');

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('opportunities.actual_close_date', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay(),
            ]);
        }

        if (!empty($filters['advisor_id'])) {
            $query->where('users.id', $filters['advisor_id']);
        }

        return $query->get()->toArray();
    }

    /**
     * Obtener métricas de conversión por fuente
     */
    public function getConversionBySource(array $filters = [], ?User $user = null): array
    {
        $userIds = $this->getUserIdsByHierarchy($user);
        
        return DB::table('clients')
            ->leftJoin('opportunities', 'clients.id', '=', 'opportunities.client_id')
            ->select(
                'clients.source',
                DB::raw('COUNT(DISTINCT clients.id) as total_clients'),
                DB::raw('COUNT(DISTINCT opportunities.id) as total_opportunities'),
                DB::raw('COUNT(DISTINCT CASE WHEN opportunities.status = "pagado" THEN opportunities.id END) as won_opportunities'),
                DB::raw('ROUND((COUNT(DISTINCT CASE WHEN opportunities.status = "pagado" THEN opportunities.id END) / COUNT(DISTINCT clients.id)) * 100, 2) as conversion_rate')
            )
            ->whereIn('clients.assigned_advisor_id', $userIds)
            ->groupBy('clients.source')
            ->orderBy('conversion_rate', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Obtener oportunidades cerradas por vendedor
     */
    public function getClosedOpportunitiesBySeller(array $filters = [], ?User $user = null): array
    {
        $userIds = $this->getUserIdsByHierarchy($user);
        $query = DB::table('users')
            ->leftJoin('opportunities', 'users.id', '=', 'opportunities.advisor_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(CASE WHEN opportunities.status = "pagado" THEN opportunities.id END) as closed_opportunities'),
                DB::raw('SUM(CASE WHEN opportunities.status = "pagado" THEN opportunities.close_value ELSE 0 END) as total_sales'),
                DB::raw('AVG(CASE WHEN opportunities.status = "pagado" THEN opportunities.close_value ELSE NULL END) as average_sale')
            )
            ->whereIn('users.id', $userIds)
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
     * Obtener rendimiento de líderes con ventas de su equipo
     */
    public function getLeaderPerformance(array $filters = [], ?User $user = null): array
    {
        $userIds = $this->getUserIdsByHierarchy($user);
        
        // Obtener solo líderes que están en la jerarquía del usuario
        $leaders = User::whereHas('roles', function($query) {
            $query->where('name', 'lider');
        })->whereIn('id', $userIds)->get();

        $leaderPerformance = [];

        foreach ($leaders as $leader) {
            // Obtener vendedores a cargo del líder
            $vendedores = User::where('lider_id', $leader->id)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'vendedor');
                })->get();

            $vendedorIds = $vendedores->pluck('id')->toArray();
            
            // Incluir al líder mismo en las ventas
            $allTeamIds = array_merge([$leader->id], $vendedorIds);

            // Calcular ventas del equipo completo
            $teamSales = DB::table('opportunities')
                ->whereIn('advisor_id', $allTeamIds)
                ->where('status', 'pagado');

            // Aplicar filtros de fecha si se proporcionan
            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $teamSales->whereBetween('actual_close_date', [
                    Carbon::parse($filters['start_date'])->startOfDay(),
                    Carbon::parse($filters['end_date'])->endOfDay(),
                ]);
            }

            $totalSales = $teamSales->sum('close_value');
            $closedOpportunities = $teamSales->count();

            // Calcular ventas solo del líder
            $leaderOnlySales = DB::table('opportunities')
                ->where('advisor_id', $leader->id)
                ->where('status', 'pagado');

            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $leaderOnlySales->whereBetween('actual_close_date', [
                    Carbon::parse($filters['start_date'])->startOfDay(),
                    Carbon::parse($filters['end_date'])->endOfDay(),
                ]);
            }

            $leaderSales = $leaderOnlySales->sum('close_value');
            $teamSales = $totalSales - $leaderSales;

            $leaderPerformance[] = [
                'id' => $leader->id,
                'name' => $leader->name,
                'total_sales' => $totalSales,
                'leader_sales' => $leaderSales,
                'team_sales' => $teamSales,
                'closed_opportunities' => $closedOpportunities,
                'team_members' => count($vendedorIds),
                'average_sale' => $closedOpportunities > 0 ? $totalSales / $closedOpportunities : 0
            ];
        }

        // Ordenar por ventas totales descendente
        usort($leaderPerformance, function($a, $b) {
            return $b['total_sales'] <=> $a['total_sales'];
        });

        return $leaderPerformance;
    }

    /**
     * Exportar datos del dashboard
     */
    public function exportDashboardData(array $filters = [], ?User $user = null): array
    {
        $userIds = $this->getUserIdsByHierarchy($user);
        
        return [
            'stats' => $this->getDashboardStats($filters, $user),
            'opportunities_by_stage' => $this->getOpportunitiesByStage($filters, $user),
            'clients_by_status' => $this->getClientsByStatus($filters, $user),
            'projects_by_type' => $this->getProjectsByType($filters, $user),
            'sales_by_month' => $this->getSalesByMonth($filters, $user),
            'recent_activities' => $this->getRecentActivities(50, $filters, $user),
            'pending_tasks' => $this->getPendingTasks(50, $filters, $user),
            'upcoming_closings' => $this->getUpcomingClosings(50, $filters, $user),
            'advisor_performance' => $this->getAdvisorPerformance($filters, $user),
            'conversion_by_source' => $this->getConversionBySource($filters),
            'closed_opportunities_by_seller' => $this->getClosedOpportunitiesBySeller($filters, $user),
            'exported_at' => now()->toDateTimeString(),
            'user_role' => $user ? $user->getRoleName() : 'guest',
            'user_name' => $user ? $user->name : 'Invitado'
        ];
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
