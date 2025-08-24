<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectService
{
    /**
     * Obtener todos los proyectos con paginación
     * 
     * @param int $perPage Número de proyectos por página (por defecto: 15)
     * @param array $filters Filtros disponibles:
     *   - status: Estado del proyecto (activo, inactivo, suspendido, finalizado)
     *   - type: Tipo de proyecto (lotes, casas, departamentos, oficinas, mixto)
     *   - stage: Etapa del proyecto (preventa, lanzamiento, venta_activa, cierre)
     *   - location: Array con district, province, region
     *   - with_available_units: Boolean para filtrar solo proyectos con unidades disponibles
     *   - search: Término de búsqueda en nombre, descripción o dirección
     *   - order_by: Campo para ordenar (name, created_at, updated_at, start_date, end_date, total_units, available_units)
     *   - order_direction: Dirección del ordenamiento (asc, desc)
     * 
     * @return LengthAwarePaginator
     */
    public function getAllProjects(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Project::with(['createdBy', 'units', 'prices'])
                ->withCount(['units', 'opportunities', 'clients']);

            // Aplicar filtros
            if (isset($filters['status']) && !empty($filters['status'])) {
                $query->byStatus($filters['status']);
            }

            if (isset($filters['type']) && !empty($filters['type'])) {
                $query->byType($filters['type']);
            }

            if (isset($filters['stage']) && !empty($filters['stage'])) {
                $query->byStage($filters['stage']);
            }

            if (isset($filters['location']) && is_array($filters['location'])) {
                $query->byLocation(
                    $filters['location']['district'] ?? null,
                    $filters['location']['province'] ?? null,
                    $filters['location']['region'] ?? null
                );
            }

            if (isset($filters['with_available_units']) && $filters['with_available_units']) {
                $query->withAvailableUnits();
            }

            if (isset($filters['search']) && !empty($filters['search'])) {
                $search = trim($filters['search']);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            }

            // Ordenamiento personalizable
            $orderBy = $filters['order_by'] ?? 'created_at';
            $orderDirection = $filters['order_direction'] ?? 'desc';

            // Validar campos de ordenamiento permitidos
            $allowedOrderFields = ['name', 'created_at', 'updated_at', 'start_date', 'end_date', 'total_units', 'available_units'];
            if (!in_array($orderBy, $allowedOrderFields)) {
                $orderBy = 'created_at';
            }

            // Validar dirección de ordenamiento
            if (!in_array($orderDirection, ['asc', 'desc'])) {
                $orderDirection = 'desc';
            }

            return $query->orderBy($orderBy, $orderDirection)->paginate($perPage);
        } catch (\Exception $e) {
            // Log del error para debugging
            Log::error('Error en getAllProjects: ' . $e->getMessage(), [
                'filters' => $filters,
                'trace' => $e->getTraceAsString()
            ]);

            // Retornar paginación vacía en caso de error
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                $perPage,
                1
            );
        }
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

    /**
     * Obtener proyectos con filtros específicos y validación
     * 
     * @param array $filters Filtros validados
     * @param int $perPage Número de proyectos por página
     * @return LengthAwarePaginator
     */
    public function getProjectsWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        // Validar y limpiar filtros
        $validatedFilters = $this->validateAndCleanFilters($filters);

        return $this->getAllProjects($perPage, $validatedFilters);
    }

    /**
     * Validar y limpiar filtros de proyectos
     * 
     * @param array $filters Filtros a validar
     * @return array Filtros validados y limpios
     */
    private function validateAndCleanFilters(array $filters): array
    {
        $validated = [];

        // Validar status
        if (isset($filters['status']) && in_array($filters['status'], ['activo', 'inactivo', 'suspendido', 'finalizado'])) {
            $validated['status'] = $filters['status'];
        }

        // Validar type
        if (isset($filters['type']) && in_array($filters['type'], ['lotes', 'casas', 'departamentos', 'oficinas', 'mixto'])) {
            $validated['type'] = $filters['type'];
        }

        // Validar stage
        if (isset($filters['stage']) && in_array($filters['stage'], ['preventa', 'lanzamiento', 'venta_activa', 'cierre'])) {
            $validated['stage'] = $filters['stage'];
        }

        // Validar location
        if (isset($filters['location']) && is_array($filters['location'])) {
            $location = [];
            if (isset($filters['location']['district']) && !empty($filters['location']['district'])) {
                $location['district'] = trim($filters['location']['district']);
            }
            if (isset($filters['location']['province']) && !empty($filters['location']['province'])) {
                $location['province'] = trim($filters['location']['province']);
            }
            if (isset($filters['location']['region']) && !empty($filters['location']['region'])) {
                $location['region'] = trim($filters['location']['region']);
            }
            if (!empty($location)) {
                $validated['location'] = $location;
            }
        }

        // Validar with_available_units
        if (isset($filters['with_available_units'])) {
            $validated['with_available_units'] = (bool) $filters['with_available_units'];
        }

        // Validar search
        if (isset($filters['search']) && !empty(trim($filters['search']))) {
            $validated['search'] = trim($filters['search']);
        }

        // Validar order_by
        $allowedOrderFields = ['name', 'created_at', 'updated_at', 'start_date', 'end_date', 'total_units', 'available_units'];
        if (isset($filters['order_by']) && in_array($filters['order_by'], $allowedOrderFields)) {
            $validated['order_by'] = $filters['order_by'];
        }

        // Validar order_direction
        if (isset($filters['order_direction']) && in_array($filters['order_direction'], ['asc', 'desc'])) {
            $validated['order_direction'] = $filters['order_direction'];
        }

        return $validated;
    }
}
