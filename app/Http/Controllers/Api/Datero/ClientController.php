<?php

namespace App\Http\Controllers\Api\Datero;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Obtener parámetros de paginación y filtros
            $perPage = min((int) $request->get('per_page', 15), 100);
            $filters = [
                'search' => $request->get('search'),
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

            // Establecer valores por defecto si no se proporcionan
            $formData['status'] = $formData['status'] ?? 'nuevo';
            $formData['score'] = $formData['score'] ?? 0;

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

