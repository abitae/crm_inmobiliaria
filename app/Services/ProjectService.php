<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ProjectService
{
    /**
     * Obtener todos los proyectos con paginación
     */
    public function getAllProjects(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Project::with(['createdBy', 'units', 'prices'])
            ->withCount(['units', 'opportunities', 'clients']);

        // Aplicar filtros
        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (isset($filters['stage'])) {
            $query->byStage($filters['stage']);
        }

        if (isset($filters['location'])) {
            $query->byLocation(
                $filters['location']['district'] ?? null,
                $filters['location']['province'] ?? null,
                $filters['location']['region'] ?? null
            );
        }

        if (isset($filters['with_available_units']) && $filters['with_available_units']) {
            $query->withAvailableUnits();
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Obtener proyecto por ID
     */
    public function getProjectById(int $id): ?Project
    {
        return Project::with([
            'createdBy',
            'updatedBy',
            'units',
            'prices',
            'opportunities.client',
            'opportunities.unit',
            'clients',
            'advisors',
            'documents',
            'activities',
            'commissions'
        ])->find($id);
    }

    /**
     * Crear nuevo proyecto
     */
    public function createProject(array $data): Project
    {
        $data['created_by'] = Auth::user()->id;
        $data['updated_by'] = Auth::user()->id;

        return Project::create($data);
    }

    /**
     * Actualizar proyecto
     */
    public function updateProject(int $id, array $data): bool
    {
        $project = Project::find($id);
        if (!$project) {
            return false;
        }

        $data['updated_by'] = Auth::user()->id;
        return $project->update($data);
    }

    /**
     * Eliminar proyecto (soft delete)
     */
    public function deleteProject(int $id): bool
    {
        $project = Project::find($id);
        if (!$project) {
            return false;
        }

        return $project->delete();
    }

    /**
     * Asignar asesor a proyecto
     */
    public function assignAdvisor(int $projectId, int $advisorId, bool $isPrimary = false, string $notes = null): bool
    {
        $project = Project::find($projectId);
        if (!$project) {
            return false;
        }

        $project->assignAdvisor($advisorId, $isPrimary, $notes);
        return true;
    }

    /**
     * Remover asesor del proyecto
     */
    public function removeAdvisor(int $projectId, int $advisorId): bool
    {
        $project = Project::find($projectId);
        if (!$project) {
            return false;
        }

        $project->removeAdvisor($advisorId);
        return true;
    }

    /**
     * Actualizar conteo de unidades
     */
    public function updateUnitCounts(int $projectId): bool
    {
        $project = Project::find($projectId);
        if (!$project) {
            return false;
        }

        $project->updateUnitCounts();
        return true;
    }

    /**
     * Obtener estadísticas de proyectos
     */
    public function getProjectStats(): array
    {
        $totalProjects = Project::count();
        $activeProjects = Project::active()->count();
        $preventaProjects = Project::byStage('preventa')->count();
        $lanzamientoProjects = Project::byStage('lanzamiento')->count();
        $ventaActivaProjects = Project::byStage('venta_activa')->count();
        $cierreProjects = Project::byStage('cierre')->count();

        return [
            'total' => $totalProjects,
            'active' => $activeProjects,
            'preventa' => $preventaProjects,
            'lanzamiento' => $lanzamientoProjects,
            'venta_activa' => $ventaActivaProjects,
            'cierre' => $cierreProjects
        ];
    }

    /**
     * Obtener proyectos por tipo
     */
    public function getProjectsByType(string $type): Collection
    {
        return Project::with(['units', 'prices'])
            ->byType($type)
            ->active()
            ->get();
    }

    /**
     * Obtener proyectos por ubicación
     */
    public function getProjectsByLocation(string $district = null, string $province = null, string $region = null): Collection
    {
        return Project::with(['units', 'prices'])
            ->byLocation($district, $province, $region)
            ->active()
            ->get();
    }

    /**
     * Buscar proyectos por término
     */
    public function searchProjects(string $term): Collection
    {
        return Project::where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%")
            ->orWhere('address', 'like', "%{$term}%")
            ->limit(10)
            ->get();
    }

    /**
     * Obtener proyectos con unidades disponibles
     */
    public function getProjectsWithAvailableUnits(): Collection
    {
        return Project::with(['units' => function ($query) {
            $query->where('status', 'disponible');
        }, 'prices'])
            ->withAvailableUnits()
            ->active()
            ->get();
    }
}
