<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Client;
use App\Services\Clients\ClientServiceCazador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    use ApiResponse;

    protected ClientServiceCazador $clientService;

    public function __construct(ClientServiceCazador $clientService)
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
            'city_id' => $client->city_id,
            'birth_date' => $client->birth_date?->format('Y-m-d'),
            'client_type' => $client->client_type,
            'source' => $client->source,
            'status' => $client->status,
            'create_type' => $client->create_type,
            'create_mode' => $client->create_mode,
            'score' => $client->score,
            'notes' => $client->notes,
            'created_at' => $client->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $client->updated_at->format('Y-m-d H:i:s'),
        ];

        if ($client->relationLoaded('city') && $client->city) {
            $data['city'] = ['id' => $client->city->id, 'name' => $client->city->name];
        } else {
            $data['city'] = null;
        }

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

            // Solo clientes asignados al cazador (assigned_advisor_id)
            $query = Client::with(array_merge(['city:id,name'], array_values($includes)))
                ->withCount(['opportunities', 'activities', 'tasks'])
                ->where('assigned_advisor_id', Auth::id());

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
                'city:id,name',
                'opportunities.project:id,name',
            ];

            $client = Client::with(array_merge($baseRelations, array_values($includes)))
                ->withCount(['opportunities', 'activities', 'tasks'])
                ->find($id);

            if (!$client) {
                return $this->notFoundResponse('Cliente');
            }

            // Verificar que el cliente esté asignado al cazador (assigned_advisor_id)
            if ($client->assigned_advisor_id !== Auth::id()) {
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
            // Preparar datos del formulario
            $formData = $request->only([
                'name',
                'phone',
                'document_type',
                'document_number',
                'address',
                'city_id',
                'birth_date',
                'client_type',
                'source',
                'status',
                'create_type',
                'create_mode',
                'score',
                'notes'
            ]);
            if (empty($formData['create_mode'])) {
                $formData['create_mode'] = empty($formData['document_number']) ? 'phone' : 'dni';
            }

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
            $duplicateOwner = $this->getDuplicateOwnerInfo(
                $formData['phone'] ?? null,
                $formData['document_number'] ?? null
            );
            if ($duplicateOwner) {
                return $this->errorResponse($this->buildDuplicateMessage($duplicateOwner), [
                    'errors' => $e->errors(),
                    'duplicate_owner' => $duplicateOwner,
                ], 422);
            }
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

            // Verificar que el cliente esté asignado al cazador (assigned_advisor_id)
            if ($client->assigned_advisor_id !== Auth::id()) {
                return $this->forbiddenResponse('No tienes permiso para actualizar este cliente');
            }

            // En el formulario solo se puede actualizar assigned_advisor_id; updated_by lo establece el servicio
            $formData = $request->only(['assigned_advisor_id']);

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

                $formData = collect($payload)->only([
                    'name',
                    'phone',
                    'document_type',
                    'document_number',
                    'address',
                    'city_id',
                    'birth_date',
                    'client_type',
                    'source',
                    'status',
                    'create_type',
                    'create_mode',
                    'score',
                    'notes'
                ])->toArray();
                if (empty($formData['create_mode'])) {
                    $formData['create_mode'] = empty($formData['document_number']) ? 'phone' : 'dni';
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

                    if ($client->assigned_advisor_id !== Auth::id()) {
                        $errors[] = [
                            'index' => $index,
                            'errors' => ['permission' => ['No tienes permiso para actualizar este cliente.']],
                        ];
                        continue;
                    }

                    try {
                        $this->clientService->updateClient($clientId, ['assigned_advisor_id' => $payload['assigned_advisor_id'] ?? $client->assigned_advisor_id]);
                        $updated[] = $this->formatClient($client->fresh());
                    } catch (ValidationException $e) {
                        $errors[] = [
                            'index' => $index,
                            'errors' => $e->errors(),
                        ];
                    }
                } else {
                    try {
                        $client = $this->clientService->createClient($formData);
                        $created[] = $this->formatClient($client);
                    } catch (ValidationException $e) {
                        $duplicateOwner = $this->getDuplicateOwnerInfo(
                            $formData['phone'] ?? null,
                            $formData['document_number'] ?? null
                        );
                        $errors[] = [
                            'index' => $index,
                            'errors' => $e->errors(),
                            'duplicate_owner' => $duplicateOwner,
                            'message' => $duplicateOwner ? $this->buildDuplicateMessage($duplicateOwner) : null,
                        ];
                    }
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
            ->where('assigned_advisor_id', Auth::id())
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
            ->where('assigned_advisor_id', Auth::id())
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

    private function getDuplicateOwnerInfo(?string $phone, ?string $documentNumber): ?array
    {
        $normalizedPhone = $phone ? preg_replace('/[^0-9]/', '', (string) $phone) : null;
        $normalizedDocument = $documentNumber ? trim((string) $documentNumber) : null;

        if ($normalizedDocument !== null && $normalizedDocument !== '') {
            if (preg_match('/^[0-9]+$/', $normalizedDocument)) {
                $normalizedDocument = preg_replace('/[^0-9]/', '', $normalizedDocument);
            } else {
                $normalizedDocument = strtoupper(preg_replace('/\s+/', '', $normalizedDocument));
            }
        }

        if (!$normalizedPhone && !$normalizedDocument) {
            return null;
        }

        $client = Client::query()
            ->where(function ($q) use ($normalizedPhone, $normalizedDocument) {
                if ($normalizedPhone) {
                    $q->orWhere('phone', $normalizedPhone);
                }
                if ($normalizedDocument) {
                    $q->orWhere('document_number', $normalizedDocument);
                }
            })
            ->with(['assignedAdvisor:id,name', 'createdBy:id,name'])
            ->first();

        if (!$client) {
            return null;
        }

        $owner = $client->createdBy ?: $client->assignedAdvisor;
        if (!$owner) {
            return null;
        }

        $field = $normalizedPhone && $client->phone === $normalizedPhone ? 'phone' : 'document_number';

        return [
            'name' => $owner->name,
            'user_id' => $owner->id,
            'client_id' => $client->id,
            'field' => $field,
        ];
    }

    private function buildDuplicateMessage(array $duplicateOwner): string
    {
        $label = ($duplicateOwner['field'] ?? '') === 'phone' ? 'Telefono' : 'DNI';
        $name = $duplicateOwner['name'] ?? 'Desconocido';

        return $label . ' registrado por "' . $name . '"';
    }
}
