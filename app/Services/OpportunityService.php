<?php

namespace App\Services;

use App\Models\Opportunity;
use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OpportunityService
{
    /**
     * Obtener todas las oportunidades con paginación
     */
    public function getAllOpportunities(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Opportunity::with(['client', 'project', 'unit', 'advisor'])
            ->withCount(['activities', 'tasks', 'interactions']);

        // Aplicar filtros
        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['stage'])) {
            $query->byStage($filters['stage']);
        }

        if (isset($filters['advisor_id'])) {
            $query->byAdvisor($filters['advisor_id']);
        }

        if (isset($filters['project_id'])) {
            $query->byProject($filters['project_id']);
        }

        if (isset($filters['client_id'])) {
            $query->byClient($filters['client_id']);
        }

        if (isset($filters['overdue']) && $filters['overdue']) {
            $query->overdue();
        }

        if (isset($filters['closing_this_month']) && $filters['closing_this_month']) {
            $query->closingThisMonth();
        }

        return $query->orderBy('expected_close_date', 'asc')->paginate($perPage);
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
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

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

        $data['updated_by'] = auth()->id();
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
        }

        return $opportunity->advanceStage($newStage);
    }

    /**
     * Marcar oportunidad como ganada
     */
    public function markAsWon(int $id, float $closeValue, string $closeReason = null): bool
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return false;
        }

        return $opportunity->markAsWon($closeValue, $closeReason);
    }

    /**
     * Marcar oportunidad como perdida
     */
    public function markAsLost(int $id, string $lostReason): bool
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return false;
        }

        return $opportunity->markAsLost($lostReason);
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
        }

        $opportunity->assignAdvisor($advisorId);
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
        $weightedValue = Opportunity::active()->sum(DB::raw('expected_value * probability / 100'));

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
}
