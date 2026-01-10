<?php

namespace App\Http\Controllers\Api\Datero;

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
     * Verificar que el cliente pertenezca al datero autenticado
     */
    protected function ensureClientOwnership(Client $client): ?\Illuminate\Http\JsonResponse
    {
        if ($client->created_by !== Auth::id()) {
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

            // Obtener solo clientes creados por este datero
            $query = Client::with(['assignedAdvisor'])
                ->withCount(['opportunities', 'activities', 'tasks'])
                ->where('created_by', Auth::id());

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
                'notes',
                'assigned_advisor_id'
            ]);
            $formData['create_type'] = 'datero';
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

            // Establecer valores por defecto si no se proporcionan
            $formData['status'] = $formData['status'] ?? 'nuevo';
            $formData['score'] = isset($formData['score']) ? max(0, min(100, (int) $formData['score'])) : 0;

            // El servicio se encargará de establecer assigned_advisor_id, created_by y updated_by
            // basándose en el lider_id del datero autenticado
            // Crear el cliente usando el servicio
            $client = $this->clientService->createClient($formData, Auth::id());

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

            // Verificar propiedad del cliente
            if ($forbidden = $this->ensureClientOwnership($client)) {
                return $forbidden;
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
                'score',
                'notes',
                'assigned_advisor_id'
            ]);

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
            return $this->serverErrorResponse($e, 'Error al actualizar el cliente');
        }
    }

    /**
     * Obtener opciones para formularios (tipos, estados, etc.)
     * 
     * @return \Illuminate\Http\JsonResponse
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
}
