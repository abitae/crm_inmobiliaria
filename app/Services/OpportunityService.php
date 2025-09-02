<?php

namespace App\Services;

use App\Models\Opportunity;
use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OpportunityService
{
    /**
     * Obtener todas las oportunidades con paginación optimizada
     */
    public function getAllOpportunities(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        // Construir query base con eager loading selectivo
        $query = Opportunity::query()
            ->with([
                'client:id,name,email,phone,status',
                'project:id,name,status,project_type,stage',
                'unit:id,unit_number,unit_type,status,final_price',
                'advisor:id,name,email'
            ])
            ->withCount([
                'activities',
                'tasks',
                'interactions'
            ]);

        // Aplicar filtros de manera optimizada
        $this->applyFilters($query, $filters);

        // Ordenamiento optimizado con índice compuesto
        $query->orderBy('expected_close_date', 'asc')
            ->orderBy('id', 'desc');
        return $query->paginate($perPage);
    }

    /**
     * Aplicar filtros de manera optimizada
     */
    private function applyFilters($query, array $filters): void
    {
        // Filtro de búsqueda optimizado con índices
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = trim($filters['search']);
            if (strlen($search) >= 2) { // Solo buscar si hay al menos 2 caracteres
                $query->where(function ($q) use ($search) {
                    $q->whereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->select('id')
                            ->where('name', 'like', "{$search}%") // Usar prefijo para mejor rendimiento
                            ->orWhere('email', 'like', "{$search}%");
                    })
                        ->orWhereHas('project', function ($projectQuery) use ($search) {
                            $projectQuery->select('id')
                                ->where('name', 'like', "{$search}%");
                        });
                });
            }
        }

        // Filtros simples con índices
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['stage']) && !empty($filters['stage'])) {
            $query->byStage($filters['stage']);
        }

        if (isset($filters['advisor_id']) && is_numeric($filters['advisor_id'])) {
            $query->byAdvisor($filters['advisor_id']);
        }

        if (isset($filters['project_id']) && is_numeric($filters['project_id'])) {
            $query->byProject($filters['project_id']);
        }

        if (isset($filters['client_id']) && is_numeric($filters['client_id'])) {
            $query->byClient($filters['client_id']);
        }

        // Filtros de fecha optimizados
        if (isset($filters['overdue']) && $filters['overdue']) {
            $query->overdue();
        }

        if (isset($filters['closing_this_month']) && $filters['closing_this_month']) {
            $query->closingThisMonth();
        }

        if (isset($filters['high_probability']) && $filters['high_probability']) {
            $query->highProbability();
        }

        // Filtros de fecha personalizados
        if (isset($filters['date_from'])) {
            $query->where('expected_close_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('expected_close_date', '<=', $filters['date_to']);
        }

        // Filtro de probabilidad
        if (isset($filters['min_probability']) && is_numeric($filters['min_probability'])) {
            $query->where('probability', '>=', $filters['min_probability']);
        }

        if (isset($filters['max_probability']) && is_numeric($filters['max_probability'])) {
            $query->where('probability', '<=', $filters['max_probability']);
        }

        // Filtro de valor esperado
        if (isset($filters['min_value']) && is_numeric($filters['min_value'])) {
            $query->where('expected_value', '>=', $filters['min_value']);
        }

        if (isset($filters['max_value']) && is_numeric($filters['max_value'])) {
            $query->where('expected_value', '<=', $filters['max_value']);
        }

        // Filtros adicionales para mejor segmentación
        if (isset($filters['source']) && !empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if (isset($filters['campaign']) && !empty($filters['campaign'])) {
            $query->where('campaign', $filters['campaign']);
        }

        // Filtro de rango de fechas de creación
        if (isset($filters['created_from'])) {
            $query->where('created_at', '>=', $filters['created_from']);
        }

        if (isset($filters['created_to'])) {
            $query->where('created_at', '<=', $filters['created_to']);
        }

        // Filtro de asesores múltiples
        if (isset($filters['advisor_ids']) && is_array($filters['advisor_ids'])) {
            $query->whereIn('advisor_id', $filters['advisor_ids']);
        }

        // Filtro de proyectos múltiples
        if (isset($filters['project_ids']) && is_array($filters['project_ids'])) {
            $query->whereIn('project_id', $filters['project_ids']);
        }

        // Filtro de clientes múltiples
        if (isset($filters['client_ids']) && is_array($filters['client_ids'])) {
            $query->whereIn('client_id', $filters['client_ids']);
        }
    }

    /**
     * Generar clave de caché única para los filtros
     */
    private function generateCacheKey(array $filters, int $perPage): string
    {
        $filterString = json_encode(array_filter($filters));
        return "opportunities_list_" . md5($filterString . $perPage . request()->get('page', 1));
    }

    /**
     * Obtener oportunidad por ID
     */
    public function getOpportunityById(int $id): ?Opportunity
    {
        return Opportunity::with([
            'client',
            'project',
            'unit',
            'advisor',
            'createdBy',
            'updatedBy',
            'activities',
            'tasks',
            'interactions',
            'documents'
        ])->find($id);
    }

    /**
     * Crear nueva oportunidad
     */
    public function createOpportunity(array $data): Opportunity
    {
        $data['created_by'] = request()->user() ? request()->user()->id : null;
        $data['updated_by'] = request()->user() ? request()->user()->id : null;

        return Opportunity::create($data);
    }

    /**
     * Actualizar oportunidad
     */
    public function updateOpportunity(int $id, array $data): bool
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return false;
        }

        $data['updated_by'] = request()->user() ? request()->user()->id : null;
        return $opportunity->update($data);
    }

    /**
     * Eliminar oportunidad (soft delete)
     */
    public function deleteOpportunity(int $id): bool
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return false;
        }

        return $opportunity->delete();
    }

    /**
     * Avanzar etapa de la oportunidad
     */
    public function advanceStage(int $id, string $newStage): bool
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return false;
        } else {
            $opportunity->stage = $newStage;
            $opportunity->save();
        }
        return true;
    }

    /**
     * Marcar oportunidad como ganada
     */
    public function markAsWon(int $id, float $closeValue, string $closeReason = null): bool
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return false;
        } else {
            $opportunity->status = 'ganada';
            $opportunity->close_value = $closeValue;
            $opportunity->close_reason = $closeReason;
            $opportunity->actual_close_date = Carbon::now();
            $opportunity->save();
        }

        return true;
    }

    /**
     * Marcar oportunidad como perdida
     */
    public function markAsLost(int $id, string $lostReason): bool
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return false;
        } else {
            $opportunity->status = 'perdida';
            $opportunity->lost_reason = $lostReason;
            $opportunity->actual_close_date = Carbon::now();
            $opportunity->save();
        }

        return true;
    }

    /**
     * Cancelar oportunidad
     */
    public function cancelOpportunity(int $id, string $reason = null): bool
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return false;
        }

        return $opportunity->cancel($reason);
    }

    /**
     * Actualizar probabilidad
     */
    public function updateProbability(int $id, int $newProbability): bool
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return false;
        }

        $opportunity->updateProbability($newProbability);
        return true;
    }

    /**
     * Actualizar valor esperado
     */
    public function updateExpectedValue(int $id, float $newValue): bool
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return false;
        }

        $opportunity->updateExpectedValue($newValue);
        return true;
    }

    /**
     * Actualizar fecha de cierre esperada
     */
    public function updateExpectedCloseDate(int $id, \DateTime $newDate): bool
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return false;
        }

        $opportunity->updateExpectedCloseDate($newDate);
        return true;
    }

    /**
     * Asignar asesor
     */
    public function assignAdvisor(int $id, int $advisorId): bool
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return false;
        } else {
            $opportunity->advisor_id = $advisorId;
            $opportunity->save();
        }
        return true;
    }

    /**
     * Obtener estadísticas de oportunidades
     */
    public function getOpportunityStats(): array
    {
        $totalOpportunities = Opportunity::count();
        $activeOpportunities = Opportunity::active()->count();
        $wonOpportunities = Opportunity::won()->count();
        $lostOpportunities = Opportunity::lost()->count();
        $overdueOpportunities = Opportunity::overdue()->count();
        $closingThisMonth = Opportunity::closingThisMonth()->count();

        $totalValue = Opportunity::active()->sum('expected_value');
        $wonValue = Opportunity::won()->sum('close_value');
        $weightedValue = Opportunity::active()->sum(\Illuminate\Support\Facades\DB::raw('expected_value * probability / 100'));

        return [
            'total' => $totalOpportunities,
            'active' => $activeOpportunities,
            'won' => $wonOpportunities,
            'lost' => $lostOpportunities,
            'overdue' => $overdueOpportunities,
            'closing_this_month' => $closingThisMonth,
            'total_value' => $totalValue,
            'won_value' => $wonValue,
            'weighted_value' => $weightedValue,
            'win_rate' => $totalOpportunities > 0 ? round(($wonOpportunities / $totalOpportunities) * 100, 2) : 0
        ];
    }

    /**
     * Obtener oportunidades por asesor
     */
    public function getOpportunitiesByAdvisor(int $advisorId): Collection
    {
        return Opportunity::with(['client', 'project', 'unit'])
            ->byAdvisor($advisorId)
            ->orderBy('expected_close_date', 'asc')
            ->get();
    }

    /**
     * Obtener oportunidades por proyecto
     */
    public function getOpportunitiesByProject(int $projectId): Collection
    {
        return Opportunity::with(['client', 'unit', 'advisor'])
            ->byProject($projectId)
            ->orderBy('expected_close_date', 'asc')
            ->get();
    }

    /**
     * Obtener oportunidades por cliente
     */
    public function getOpportunitiesByClient(int $clientId): Collection
    {
        return Opportunity::with(['project', 'unit', 'advisor'])
            ->byClient($clientId)
            ->orderBy('expected_close_date', 'asc')
            ->get();
    }

    /**
     * Obtener oportunidades vencidas
     */
    public function getOverdueOpportunities(): Collection
    {
        return Opportunity::with(['client', 'project', 'unit', 'advisor'])
            ->overdue()
            ->orderBy('expected_close_date', 'asc')
            ->get();
    }

    /**
     * Obtener oportunidades que cierran este mes
     */
    public function getOpportunitiesClosingThisMonth(): Collection
    {
        return Opportunity::with(['client', 'project', 'unit', 'advisor'])
            ->closingThisMonth()
            ->orderBy('expected_close_date', 'asc')
            ->get();
    }

    /**
     * Determinar duración del caché basada en la complejidad de los filtros
     */
    private function getCacheDuration(array $filters): \DateTime
    {
        // Caché más largo para consultas simples
        if (empty($filters) || count($filters) <= 1) {
            return now()->addMinutes(30);
        }

        // Caché medio para consultas moderadas
        if (count($filters) <= 3) {
            return now()->addMinutes(15);
        }

        // Caché corto para consultas complejas
        return now()->addMinutes(5);
    }

    /**
     * Limpiar caché de oportunidades
     */
    public function clearOpportunitiesCache(): void
    {
        $pattern = 'opportunities_list_*';
        $keys = cache()->get($pattern) ?: [];

        foreach ($keys as $key) {
            cache()->forget($key);
        }
    }

    /**
     * Limpiar caché específico de una consulta
     */
    public function clearSpecificCache(array $filters, int $perPage = 15): void
    {
        $cacheKey = $this->generateCacheKey($filters, $perPage);
        cache()->forget($cacheKey);
    }

    /**
     * Obtener estadísticas de rendimiento del caché
     */
    public function getCacheStats(): array
    {
        $pattern = 'opportunities_list_*';
        $keys = cache()->get($pattern) ?: [];

        return [
            'total_cached_queries' => count($keys),
            'cache_hit_rate' => $this->calculateCacheHitRate(),
            'memory_usage' => $this->getMemoryUsage(),
        ];
    }

    /**
     * Calcular tasa de aciertos del caché
     */
    private function calculateCacheHitRate(): float
    {
        $hits = cache()->get('opportunities_cache_hits', 0);
        $misses = cache()->get('opportunities_cache_misses', 0);
        $total = $hits + $misses;

        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    /**
     * Obtener uso de memoria del caché
     */
    private function getMemoryUsage(): string
    {
        $pattern = 'opportunities_list_*';
        $keys = cache()->get($pattern) ?: [];
        $totalSize = 0;

        foreach ($keys as $key) {
            $value = cache()->get($key);
            if ($value) {
                $totalSize += strlen(serialize($value));
            }
        }

        return $this->formatBytes($totalSize);
    }

    /**
     * Formatear bytes en formato legible
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
