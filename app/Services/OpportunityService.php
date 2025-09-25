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
                'client:id,name,phone,document_number,client_type,status',
                'project:id,name,status,project_type,stage',
                'unit:id,unit_number,unit_type,status,final_price',
                'advisor:id,name'
            ])
            ->withCount([
                'activities',
                'tasks'
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
                            ->orWhere('phone', 'like', "{$search}%");
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
        }

        $opportunity->stage = $newStage;
        return $opportunity->save();
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

        $opportunity->status = 'pagado';
        $opportunity->close_value = $closeValue;
        $opportunity->close_reason = $closeReason;
        $opportunity->actual_close_date = Carbon::now();
        return $opportunity->save();
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

        $opportunity->status = 'cancelado';
        $opportunity->lost_reason = $lostReason;
        $opportunity->actual_close_date = Carbon::now();
        return $opportunity->save();
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

        $opportunity->probability = $newProbability;
        return $opportunity->save();
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

        $opportunity->expected_value = $newValue;
        return $opportunity->save();
    }



}
