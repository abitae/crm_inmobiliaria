<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    use ApiResponse;

    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * Formatear cliente para respuesta API
     */
    protected function formatClient(Client $client): array
    {
        $data = [
            'id' => $client->id,
            'name' => $client->name,
            'phone' => $client->phone,
            'document_type' => $client->document_type,
            'document_number' => $client->document_number,
            'address' => $client->address,
            'birth_date' => $client->birth_date?->format('Y-m-d'),
            'client_type' => $client->client_type,
            'source' => $client->source,
            'status' => $client->status,
            'create_type' => $client->create_type,
            'score' => $client->score,
            'notes' => $client->notes,
            'created_at' => $client->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $client->updated_at->format('Y-m-d H:i:s'),
        ];

        // Solo incluir assigned_advisor si la relación está cargada
        if ($client->relationLoaded('assignedAdvisor') && $client->assignedAdvisor) {
            $data['assigned_advisor'] = [
                'id' => $client->assignedAdvisor->id,
                'name' => $client->assignedAdvisor->name,
                'email' => $client->assignedAdvisor->email,
            ];
        } else {
            $data['assigned_advisor'] = null;
        }

        return $data;
    }

    /**
     * Listar clientes del cazador autenticado
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = min((int) $request->get('per_page', 15), 100);
            $includes = $this->parseIncludes($request->get('include'), [
                'assignedAdvisor',
                'createdBy',
                'activities',
                'reservations',
                'tasks',
                'opportunities',
            ]);
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'type' => $request->get('type'),
                'source' => $request->get('source'),
                'create_type' => $request->get('create_type'),
            ];

            // Obtener clientes asignados al cazador o creados por él
            $query = Client::with(array_values($includes))
                ->withCount(['opportunities', 'activities', 'tasks'])
                ->where(function ($q) {
                    $q->where('assigned_advisor_id', Auth::id())
                      ->orWhere('created_by', Auth::id());
                });

            // Aplicar filtros
            if (!empty($filters['status'])) {
                $query->byStatus($filters['status']);
            }

            if (!empty($filters['type'])) {
                $query->byType($filters['type']);
            }

            if (!empty($filters['source'])) {
                $query->bySource($filters['source']);
            }

            if (!empty($filters['create_type'])) {
                $query->byCreateType($filters['create_type']);
            }

            if (!empty($filters['search'])) {
                $search = trim($filters['search']);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('document_number', 'like', "%{$search}%");
                });
            }

            // Paginar resultados
            $clients = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Formatear clientes
            $formattedClients = $clients->map(function ($client) {
                return $this->formatClient($client);
            });

            return $this->successResponse([
                'clients' => $formattedClients,
                'pagination' => $this->formatPagination($clients),
            ], 'Clientes obtenidos exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al listar clientes del cazador');
        }
    }

    /**
     * Obtener un cliente específico
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $includes = $this->parseIncludes(request()->get('include'), [
                'assignedAdvisor',
                'activities',
                'reservations',
                'tasks',
                'opportunities',
                'documents',
            ]);

            $baseRelations = [
                'assignedAdvisor:id,name,email',
                'opportunities.project:id,name',
            ];

            $client = Client::with(array_merge($baseRelations, array_values($includes)))
            ->withCount(['opportunities', 'activities', 'tasks'])
            ->find($id);

            if (!$client) {
                return $this->notFoundResponse('Cliente');
            }

            // Verificar que el cliente esté asignado al cazador o creado por él
            if ($client->assigned_advisor_id !== Auth::id() && $client->created_by !== Auth::id()) {
                return $this->forbiddenResponse('No tienes permiso para acceder a este cliente');
            }

            $clientData = $this->formatClient($client);
            $clientData['opportunities_count'] = $client->opportunities_count;
            $clientData['activities_count'] = $client->activities_count;
            $clientData['tasks_count'] = $client->tasks_count;

            return $this->successResponse(['client' => $clientData], 'Cliente obtenido exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener el cliente solicitado');
        }
    }

    /**
     * Crear un nuevo cliente
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Obtener reglas de validación
            $rules = $this->clientService->getValidationRules();
            $messages = $this->clientService->getValidationMessages();

            // Validar datos
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Preparar y sanitizar datos del formulario
            $formData = $request->only([
                'name',
                'phone',
                'document_type',
                'document_number',
                'address',
                'birth_date',
                'client_type',
                'source',
                'status',
                'create_type',
                'score',
                'notes'
            ]);

            // Sanitizar campos de texto
            if (isset($formData['name'])) {
                $formData['name'] = trim($formData['name']);
            }
            if (isset($formData['phone'])) {
                $formData['phone'] = preg_replace('/[^0-9+\-() ]/', '', $formData['phone']);
            }
            if (isset($formData['document_number'])) {
                $formData['document_number'] = preg_replace('/[^0-9]/', '', $formData['document_number']);
            }
            if (isset($formData['address'])) {
                $formData['address'] = trim($formData['address']);
            }
            if (isset($formData['notes'])) {
                $formData['notes'] = trim($formData['notes']);
            }

            // Establecer valores por defecto
            $formData['status'] = $formData['status'] ?? 'nuevo';
            $formData['score'] = isset($formData['score']) ? max(0, min(100, (int) $formData['score'])) : 0;
            // Asignar automáticamente al cazador autenticado
            $formData['assigned_advisor_id'] = Auth::id();

            // Crear el cliente usando el servicio
            $client = $this->clientService->createClient($formData);
            
            // Recargar con relaciones necesarias
            $client->load('assignedAdvisor:id,name,email');

            return $this->successResponse(
                ['client' => $this->formatClient($client)],
                'Cliente creado exitosamente',
                201
            );

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            Log::error('Error al crear cliente (Cazador)', [
                'user_id' => Auth::id(),
                'data' => $request->except(['password', 'password_confirmation']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error al crear el cliente en Cazador');
        }
    }

    /**
     * Actualizar un cliente existente
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Buscar cliente
            $client = Client::find($id);

            if (!$client) {
                return $this->notFoundResponse('Cliente');
            }

            // Verificar que el cliente esté asignado al cazador o creado por él
            if ($client->assigned_advisor_id !== Auth::id() && $client->created_by !== Auth::id()) {
                return $this->forbiddenResponse('No tienes permiso para actualizar este cliente');
            }

            // Obtener reglas de validación
            $rules = $this->clientService->getValidationRules($id);
            $messages = $this->clientService->getValidationMessages();

            // Validar datos
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Preparar datos del formulario
            $formData = $request->only([
                'name',
                'phone',
                'document_type',
                'document_number',
                'address',
                'birth_date',
                'client_type',
                'source',
                'status',
                'create_type',
                'score',
                'notes'
            ]);

            // Mantener el assigned_advisor_id del cazador autenticado (no permitir cambiar)
            // Solo si el cliente ya está asignado al cazador, mantener la asignación
            if ($client->assigned_advisor_id === Auth::id()) {
                $formData['assigned_advisor_id'] = Auth::id();
            }

            // Actualizar el cliente usando el servicio
            $updated = $this->clientService->updateClient($id, $formData);

            if (!$updated) {
                return $this->errorResponse('Error al actualizar el cliente', null, 500);
            }

            // Obtener el cliente actualizado
            $client = Client::with('assignedAdvisor:id,name,email')->find($id);

            return $this->successResponse(
                ['client' => $this->formatClient($client)],
                'Cliente actualizado exitosamente'
            );

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al actualizar el cliente en Cazador');
        }
    }

    /**
     * Crear o actualizar clientes en batch
     */
    public function batchStore(Request $request)
    {
        $items = $request->input('clients', []);

        if (!is_array($items) || count($items) === 0) {
            return $this->errorResponse('La lista de clientes es obligatoria', null, 422);
        }

        $created = [];
        $updated = [];
        $errors = [];

        foreach ($items as $index => $payload) {
            try {
                $clientId = isset($payload['id']) ? (int) $payload['id'] : null;
                $rules = $this->clientService->getValidationRules($clientId);
                $messages = $this->clientService->getValidationMessages();
                $validator = Validator::make($payload, $rules, $messages);

                if ($validator->fails()) {
                    $errors[] = [
                        'index' => $index,
                        'errors' => $validator->errors()->toArray(),
                    ];
                    continue;
                }

                $formData = collect($payload)->only([
                    'name',
                    'phone',
                    'document_type',
                    'document_number',
                    'address',
                    'birth_date',
                    'client_type',
                    'source',
                    'status',
                    'create_type',
                    'score',
                    'notes'
                ])->toArray();

                if (isset($formData['name'])) {
                    $formData['name'] = trim($formData['name']);
                }
                if (isset($formData['phone'])) {
                    $formData['phone'] = preg_replace('/[^0-9+\-() ]/', '', $formData['phone']);
                }
                if (isset($formData['document_number'])) {
                    $formData['document_number'] = preg_replace('/[^0-9]/', '', $formData['document_number']);
                }
                if (isset($formData['address'])) {
                    $formData['address'] = trim($formData['address']);
                }
                if (isset($formData['notes'])) {
                    $formData['notes'] = trim($formData['notes']);
                }

                if ($clientId) {
                    /** @var Client|null $client */
                    $client = Client::find($clientId);
                    if (!$client) {
                        $errors[] = [
                            'index' => $index,
                            'errors' => ['id' => ['Cliente no encontrado.']],
                        ];
                        continue;
                    }

                    if ($client->assigned_advisor_id !== Auth::id() && $client->created_by !== Auth::id()) {
                        $errors[] = [
                            'index' => $index,
                            'errors' => ['permission' => ['No tienes permiso para actualizar este cliente.']],
                        ];
                        continue;
                    }

                    $this->clientService->updateClient($clientId, $formData);
                    $updated[] = $this->formatClient($client->fresh());
                } else {
                    $formData['status'] = $formData['status'] ?? 'nuevo';
                    $formData['score'] = isset($formData['score'])
                        ? max(0, min(100, (int) $formData['score']))
                        : 0;
                    $formData['assigned_advisor_id'] = Auth::id();
                    $client = $this->clientService->createClient($formData);
                    $created[] = $this->formatClient($client);
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'errors' => ['exception' => [$e->getMessage()]],
                ];
            }
        }

        return $this->successResponse([
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
        ], 'Batch de clientes procesado');
    }

    /**
     * Obtener multiples clientes por IDs
     */
    public function batchShow(Request $request)
    {
        $idsParam = $request->query('ids', '');
        $ids = collect(explode(',', $idsParam))
            ->map(fn($id) => (int) trim($id))
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return $this->errorResponse('Se requieren IDs validos', null, 422);
        }

        $clients = Client::with(['assignedAdvisor'])
            ->whereIn('id', $ids)
            ->where(function ($q) {
                $q->where('assigned_advisor_id', Auth::id())
                    ->orWhere('created_by', Auth::id());
            })
            ->get();

        return $this->successResponse([
            'clients' => $clients->map(fn($client) => $this->formatClient($client)),
        ], 'Clientes obtenidos exitosamente');
    }

    /**
     * Sugerencias rapidas de clientes
     */
    public function suggestions(Request $request)
    {
        $query = trim((string) $request->get('q', ''));
        $limit = min((int) $request->get('limit', 10), 20);

        if (strlen($query) < 2) {
            return $this->successResponse(['suggestions' => []], 'Consulta muy corta');
        }

        $clients = Client::query()
            ->where(function ($q) {
                $q->where('assigned_advisor_id', Auth::id())
                    ->orWhere('created_by', Auth::id());
            })
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('document_number', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'phone', 'document_number']);

        return $this->successResponse([
            'suggestions' => $clients->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'phone' => $client->phone,
                    'document_number' => $client->document_number,
                ];
            }),
        ], 'Sugerencias obtenidas');
    }

    /**
     * Obtener opciones para formularios
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function options()
    {
        try {
            $options = $this->clientService->getFormOptions();

            return $this->successResponse($options, 'Opciones obtenidas exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener opciones de formulario');
        }
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

