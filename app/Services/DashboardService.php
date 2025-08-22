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
    public function getDashboardStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->startOfMonth();
        $endOfMonth = $now->endOfMonth();

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
    public function getOpportunitiesByStage(): array
    {
        $stages = ['captado', 'calificado', 'contacto', 'propuesta', 'visita', 'negociacion', 'cierre'];

        $data = [];
        foreach ($stages as $stage) {
            $count = Opportunity::byStage($stage)->count();
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
    public function getClientsByStatus(): array
    {
        $statuses = ['nuevo', 'contacto_inicial', 'en_seguimiento', 'cierre', 'perdido'];

        $data = [];
        foreach ($statuses as $status) {
            $count = Client::byStatus($status)->count();
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
    public function getProjectsByType(): array
    {
        $types = ['lotes', 'casas', 'departamentos', 'oficinas', 'mixto'];

        $data = [];
        foreach ($types as $type) {
            $count = Project::byType($type)->count();
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
    public function getSalesByMonth(): array
    {
        $months = [];
        $sales = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $monthStart = $date->startOfMonth();
            $monthEnd = $date->endOfMonth();

            $monthlySales = Opportunity::where('status', 'ganada')
                ->whereBetween('actual_close_date', [$monthStart, $monthEnd])
                ->sum('close_value');

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
    public function getRecentActivities(int $limit = 10): array
    {
        return Activity::with(['assignedTo', 'client', 'project', 'opportunity'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Obtener tareas pendientes
     */
    public function getPendingTasks(int $limit = 10): array
    {
        return Task::with(['assignedTo', 'client', 'project', 'opportunity'])
            ->where('status', 'pendiente')
            ->orderBy('due_date', 'asc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Obtener oportunidades que cierran pronto
     */
    public function getUpcomingClosings(int $limit = 10): array
    {
        $nextWeek = Carbon::now()->addWeek();

        return Opportunity::with(['client', 'project', 'unit'])
            ->where('status', 'activa')
            ->where('expected_close_date', '<=', $nextWeek)
            ->orderBy('expected_close_date', 'asc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Obtener métricas de rendimiento por asesor
     */
    public function getAdvisorPerformance(): array
    {
        return DB::table('users')
            ->leftJoin('opportunities', 'users.id', '=', 'opportunities.advisor_id')
            ->leftJoin('clients', 'users.id', '=', 'clients.assigned_advisor_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(DISTINCT opportunities.id) as total_opportunities'),
                DB::raw('COUNT(DISTINCT CASE WHEN opportunities.status = "ganada" THEN opportunities.id END) as won_opportunities'),
                DB::raw('COUNT(DISTINCT clients.id) as assigned_clients'),
                DB::raw('SUM(CASE WHEN opportunities.status = "ganada" THEN opportunities.close_value ELSE 0 END) as total_sales')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_sales', 'desc')
            ->get()
            ->toArray();
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
                DB::raw('COUNT(DISTINCT CASE WHEN opportunities.status = "ganada" THEN opportunities.id END) as won_opportunities'),
                DB::raw('ROUND((COUNT(DISTINCT CASE WHEN opportunities.status = "ganada" THEN opportunities.id END) / COUNT(DISTINCT clients.id)) * 100, 2) as conversion_rate')
            )
            ->groupBy('clients.source')
            ->orderBy('conversion_rate', 'desc')
            ->get()
            ->toArray();
    }
}
