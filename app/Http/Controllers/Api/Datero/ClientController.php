<?php

namespace App\Http\Controllers\Api\Datero;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Verificar que el cliente esté asignado al datero autenticado (assigned_advisor_id)
     */
    protected function ensureClientOwnership(Client $client): ?\Illuminate\Http\JsonResponse
    {
        if ($client->assigned_advisor_id !== Auth::id()) {
            return $this->forbiddenResponse('No tienes permiso para acceder a este cliente');
        }
        return null;
    }

    /**
     * Listar clientes del datero autenticado
     * 
     * Permite listar y filtrar los clientes creados por el datero autenticado.
     * Soporta paginación, búsqueda por texto y filtros por estado, tipo y origen.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Validar y sanitizar parámetros de paginación
            $perPage = min(max((int) $request->get('per_page', 15), 1), 100);
            $page = max((int) $request->get('page', 1), 1);

            // Sanitizar filtros
            $filters = [
                'search' => trim($request->get('search', '')),
                'dni' => trim($request->get('dni', '')), // Búsqueda específica por DNI
                'status' => $request->get('status'),
                'type' => $request->get('type'),
                'source' => $request->get('source'),
            ];

            // Solo clientes asignados a este datero (assigned_advisor_id)
            $query = Client::with(['assignedAdvisor', 'city:id,name'])
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

            // Búsqueda específica por DNI (prioritaria)
            if (!empty($filters['dni'])) {
                $dni = preg_replace('/[^0-9]/', '', $filters['dni']);
                if (!empty($dni)) {
                    $query->where('document_number', 'like', "%{$dni}%");
                }
            } elseif (!empty($filters['search'])) {
                // Búsqueda general (nombre, teléfono, DNI)
                $search = trim($filters['search']);
                // Sanitizar búsqueda para prevenir SQL injection
                $search = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $search);

                if (!empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('document_number', 'like', "%{$search}%");
                    });
                }
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
                'pagination' => [
                    'current_page' => $clients->currentPage(),
                    'per_page' => $clients->perPage(),
                    'total' => $clients->total(),
                    'last_page' => $clients->lastPage(),
                    'from' => $clients->firstItem(),
                    'to' => $clients->lastItem(),
                ]
            ], 'Clientes obtenidos exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al obtener clientes (Datero)', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->serverErrorResponse($e, 'Error al obtener los clientes');
        }
    }

    /**
     * Obtener un cliente específico del datero
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $client = Client::with([
                'assignedAdvisor:id,name,email',
                'city:id,name',
                'opportunities.project:id,name',
            ])
                ->withCount(['opportunities', 'activities', 'tasks'])
                ->find($id);

            if (!$client) {
                return $this->notFoundResponse('Cliente');
            }

            // Verificar propiedad del cliente
            if ($forbidden = $this->ensureClientOwnership($client)) {
                return $forbidden;
            }

            $clientData = $this->formatClient($client);
            $clientData['opportunities_count'] = $client->opportunities_count;
            $clientData['activities_count'] = $client->activities_count;
            $clientData['tasks_count'] = $client->tasks_count;

            return $this->successResponse(['client' => $clientData], 'Cliente obtenido exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener el cliente');
        }
    }

    /**
     * Crear un nuevo cliente
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Crear un nuevo cliente (misma lógica y patrón que API Cazador; incluye city_id)
     */
    public function store(Request $request)
    {
        try {
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
                'notes',
            ]);
            if (empty($formData['create_mode'])) {
                $formData['create_mode'] = empty($formData['document_number']) ? 'phone' : 'dni';
            }

            $client = $this->clientService->createClient($formData, Auth::id());
            $client->load(['assignedAdvisor:id,name,email', 'city:id,name']);

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
            Log::error('Error al crear cliente (Datero)', [
                'user_id' => Auth::id(),
                'data' => $request->except(['password', 'password_confirmation']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->serverErrorResponse($e, 'Error al crear el cliente');
        }
    }

    /**
     * Actualizar un cliente existente (misma lógica y patrón que API Cazador; incluye city_id)
     */
    public function update(Request $request, $id)
    {
        try {
            $client = Client::find($id);

            if (!$client) {
                return $this->notFoundResponse('Cliente');
            }

            if ($forbidden = $this->ensureClientOwnership($client)) {
                return $forbidden;
            }

            // En el formulario solo se puede actualizar assigned_advisor_id; updated_by lo establece el servicio
            $formData = $request->only(['assigned_advisor_id']);

            $updated = $this->clientService->updateClient($id, $formData);

            if (!$updated) {
                return $this->errorResponse('Error al actualizar el cliente', null, 500);
            }

            $client = Client::with(['assignedAdvisor:id,name,email', 'city:id,name'])->find($id);

            return $this->successResponse(
                ['client' => $this->formatClient($client)],
                'Cliente actualizado exitosamente'
            );
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al actualizar el cliente');
        }
    }

    /**
     * Obtener opciones para formularios (tipos, estados, ciudades, etc.)
     */
    public function options()
    {
        try {
            $options = $this->clientService->getFormOptions();

            return $this->successResponse($options, 'Opciones obtenidas exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener las opciones');
        }
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
