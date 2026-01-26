<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    /**
     * Formatear proyecto para respuesta API (incluye campos financieros)
     */
    protected function formatProject(Project $project): array
    {
        $data = [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'project_type' => $project->project_type,
            'is_published' => $project->is_published,
            'lote_type' => $project->lote_type,
            'stage' => $project->stage,
            'legal_status' => $project->legal_status,
            'estado_legal' => $project->estado_legal,
            'tipo_proyecto' => $project->tipo_proyecto,
            'tipo_financiamiento' => $project->tipo_financiamiento,
            'banco' => $project->banco,
            'tipo_cuenta' => $project->tipo_cuenta,
            'cuenta_bancaria' => $project->cuenta_bancaria,
            'address' => $project->address,
            'district' => $project->district,
            'province' => $project->province,
            'region' => $project->region,
            'country' => $project->country,
            'ubicacion' => $project->ubicacion,
            'full_address' => $project->full_address,
            'coordinates' => $project->coordinates,
            'total_units' => $project->total_units,
            'available_units' => $project->available_units,
            'reserved_units' => $project->reserved_units,
            'sold_units' => $project->sold_units,
            'blocked_units' => $project->blocked_units,
            'progress_percentage' => $project->progress_percentage,
            'start_date' => $project->start_date?->format('Y-m-d'),
            'end_date' => $project->end_date?->format('Y-m-d'),
            'delivery_date' => $project->delivery_date?->format('Y-m-d'),
            'status' => $project->status,
            'path_image_portada' => $project->path_image_portada,
            'path_video_portada' => $project->path_video_portada,
            'path_images' => $project->path_images ?? [],
            'path_videos' => $project->path_videos ?? [],
            'path_documents' => $project->path_documents ?? [],
            'created_at' => $project->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $project->updated_at->format('Y-m-d H:i:s'),
        ];

        // Incluir asesores asignados si la relación está cargada
        if ($project->relationLoaded('advisors')) {
            $data['advisors'] = $project->advisors->map(function ($advisor) {
                return [
                    'id' => $advisor->id,
                    'name' => $advisor->name,
                    'email' => $advisor->email,
                    'is_primary' => $advisor->pivot->is_primary ?? false,
                ];
            });
        }

        return $data;
    }

    /**
     * Formatear unidad para respuesta API
     */
    protected function formatUnit(Unit $unit): array
    {
        return [
            'id' => $unit->id,
            'project_id' => $unit->project_id,
            'unit_manzana' => $unit->unit_manzana,
            'unit_number' => $unit->unit_number,
            'unit_type' => $unit->unit_type,
            'floor' => $unit->floor,
            'tower' => $unit->tower,
            'block' => $unit->block,
            'area' => (float) $unit->area,
            'bedrooms' => $unit->bedrooms,
            'bathrooms' => $unit->bathrooms,
            'parking_spaces' => $unit->parking_spaces,
            'storage_rooms' => $unit->storage_rooms,
            'balcony_area' => (float) $unit->balcony_area,
            'terrace_area' => (float) $unit->terrace_area,
            'garden_area' => (float) $unit->garden_area,
            'total_area' => (float) $unit->total_area,
            'status' => $unit->status,
            'base_price' => (float) $unit->base_price,
            'total_price' => (float) $unit->total_price,
            'discount_percentage' => (float) $unit->discount_percentage,
            'discount_amount' => (float) $unit->discount_amount,
            'final_price' => (float) $unit->final_price,
            'price_per_square_meter' => (float) $unit->price_per_square_meter,
            'commission_percentage' => (float) $unit->commission_percentage,
            'commission_amount' => (float) $unit->commission_amount,
            'blocked_until' => $unit->blocked_until?->format('Y-m-d H:i:s'),
            'blocked_reason' => $unit->blocked_reason,
            'is_blocked' => $unit->is_blocked,
            'is_available' => $unit->isAvailable(),
            'full_identifier' => $unit->full_identifier,
            'notes' => $unit->notes,
            'created_at' => $unit->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $unit->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Listar todos los proyectos (acceso completo para cazadores)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Validar y obtener parámetros de paginación
            $perPage = min((int) $request->get('per_page', 15), 100);
            $includes = $this->parseIncludes($request->get('include'), [
                'advisors',
                'reservations',
            ]);
            
            // Validar filtros básicos
            $filters = [
                'search' => $request->get('search'),
                'project_type' => $request->get('project_type'),
                'lote_type' => $request->get('lote_type'),
                'stage' => $request->get('stage'),
                'legal_status' => $request->get('legal_status'),
                'status' => $request->get('status'),
                'district' => $request->get('district'),
                'province' => $request->get('province'),
                'region' => $request->get('region'),
                'has_available_units' => filter_var($request->get('has_available_units', false), FILTER_VALIDATE_BOOLEAN),
            ];

            $relations = ['advisors:id,name,email'];
            if (!empty($includes)) {
                $relations = array_unique(array_merge($relations, $includes));
            }

            // Obtener todos los proyectos (no solo publicados) con eager loading optimizado
            $query = Project::with($relations);

            // Aplicar filtros
            if (!empty($filters['project_type'])) {
                $query->byType($filters['project_type']);
            }

            if (!empty($filters['lote_type'])) {
                $query->where('lote_type', $filters['lote_type']);
            }

            if (!empty($filters['stage'])) {
                $query->byStage($filters['stage']);
            }

            if (!empty($filters['legal_status'])) {
                $query->where('legal_status', $filters['legal_status']);
            }

            if (!empty($filters['status'])) {
                $query->byStatus($filters['status']);
            }

            if (!empty($filters['district']) || !empty($filters['province']) || !empty($filters['region'])) {
                $query->byLocation(
                    $filters['district'],
                    $filters['province'],
                    $filters['region']
                );
            }

            if ($filters['has_available_units']) {
                $query->withAvailableUnits();
            }

            if (!empty($filters['search'])) {
                $search = trim($filters['search']);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('province', 'like', "%{$search}%");
                });
            }

            // Paginar resultados
            $projects = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Formatear proyectos
            $formattedProjects = $projects->map(function ($project) {
                return $this->formatProject($project);
            });

            return $this->successResponse([
                'projects' => $formattedProjects,
                'pagination' => $this->formatPagination($projects),
            ], 'Proyectos obtenidos exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al listar proyectos del cazador');
        }
    }

    /**
     * Obtener un proyecto específico completo
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            // Validar ID
            if (!is_numeric($id)) {
                return $this->errorResponse('ID de proyecto inválido', null, 400);
            }

            // Obtener parámetros de paginación para unidades
            $unitsPerPage = min((int) $request->get('units_per_page', 15), 100);
            $includeUnits = $request->get('include_units', true);
            $includes = $this->parseIncludes($request->get('include'), [
                'advisors',
                'reservations',
            ]);

            // Buscar proyecto con relaciones optimizadas
            $relations = ['advisors:id,name,email'];
            if (!empty($includes)) {
                $relations = array_unique(array_merge($relations, $includes));
            }

            $project = Project::with($relations)->find($id);

            if (!$project) {
                return $this->notFoundResponse('Proyecto');
            }

            $projectData = $this->formatProject($project);
            
            // Paginar unidades disponibles si se solicitan
            if ($includeUnits) {
                $unitsQuery = Unit::where('project_id', $id)
                    ->available()
                    ->orderBy('unit_manzana', 'asc')
                    ->orderBy('unit_number', 'asc');
                
                $units = $unitsQuery->paginate($unitsPerPage, ['*'], 'units_page');
                
                $projectData['units'] = $units->map(function ($unit) {
                    return $this->formatUnit($unit);
                });
                
                $projectData['units_pagination'] = $this->formatPagination($units);
            }

            return $this->successResponse(['project' => $projectData], 'Proyecto obtenido exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener detalle del proyecto');
        }
    }

    /**
     * Obtener unidades de un proyecto
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function units(Request $request, $id)
    {
        try {
            // Validar ID
            if (!is_numeric($id)) {
                return $this->errorResponse('ID de proyecto inválido', null, 400);
            }

            // Verificar que el proyecto existe (solo campos necesarios)
            $project = Project::select('id', 'name')->find($id);

            if (!$project) {
                return $this->notFoundResponse('Proyecto');
            }

            // Obtener parámetros de paginación
            $perPage = min((int) $request->get('per_page', 15), 100);

            // Obtener solo unidades disponibles del proyecto
            // Ordenar primero por manzana y luego por número de unidad
            $units = Unit::where('project_id', $id)
                ->available()
                ->orderBy('unit_manzana', 'asc')
                ->orderBy('unit_number', 'asc')
                ->paginate($perPage);

            // Formatear unidades
            $formattedUnits = $units->map(function ($unit) {
                return $this->formatUnit($unit);
            });

            return $this->successResponse([
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                ],
                'units' => $formattedUnits,
                'pagination' => $this->formatPagination($units),
            ], 'Unidades obtenidas exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al listar unidades del proyecto');
        }
    }

    /**
     * Sugerencias rapidas de proyectos
     */
    public function suggestions(Request $request)
    {
        $query = trim((string) $request->get('q', ''));
        $limit = min((int) $request->get('limit', 10), 20);

        if (strlen($query) < 2) {
            return $this->successResponse(['suggestions' => []], 'Consulta muy corta');
        }

        $projects = Project::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'status']);

        return $this->successResponse([
            'suggestions' => $projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'status' => $project->status,
                ];
            }),
        ], 'Sugerencias obtenidas');
    }

    protected function formatPagination($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }

    protected function parseIncludes(?string $includeParam, array $allowed): array
    {
        if (!$includeParam) {
            return [];
        }

        return collect(explode(',', $includeParam))
            ->map(fn($item) => trim($item))
            ->filter(fn($item) => $item !== '' && in_array($item, $allowed, true))
            ->unique()
            ->values()
            ->all();
    }
}

