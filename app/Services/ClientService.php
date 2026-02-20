<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ClientService
{
    /**
     * Construir la consulta base de clientes.
     */
    private function baseClientsQuery(bool $onlyDatero = false, bool $forExport = false)
    {
        $query = Client::query();

        if (!$forExport) {
            $query->select([
                'id',
                'name',
                'phone',
                'document_type',
                'document_number',
                'birth_date',
                'client_type',
                'source',
                'status',
                'score',
                'assigned_advisor_id',
                'created_by',
                'city_id',
            ])->with([
                'assignedAdvisor:id,name',
                'createdBy:id,name',
                'city:id,name',
                'activities' => function ($q) {
                    $q->select('id', 'client_id', 'title', 'start_date')
                        ->latest('start_date')
                        ->limit(1);
                }
            ]);
        } else {
            $query->with([
                'assignedAdvisor:id,name',
                'createdBy:id,name',
            ]);
        }

        if ($onlyDatero) {
            $query->whereHas('createdBy', function ($q) {
                $q->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'datero');
                });
            });
        } else {
            $query->whereDoesntHave('createdBy', function ($q) {
                $q->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'datero');
                });
            });
        }

        // Cada usuario solo ve los clientes asignados a él (assigned_advisor_id)
        if (Auth::check()) {
            $query->where('assigned_advisor_id', Auth::id());
        }

        return $query;
    }

    /**
     * Obtener todos los clientes con paginación y filtros (excluyendo clientes creados por usuarios datero)
     */
    public function getAllClients(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        try {
            $query = $this->baseClientsQuery(false);
            $this->applyFilters($query, $filters);

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Error al obtener clientes: ' . $e->getMessage());
            throw new \Exception('Error al obtener la lista de clientes');
        }
    }

    /**
     * Obtener clientes creados por usuarios datero
     */
    public function getClientsByDateros(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        try {
            $query = $this->baseClientsQuery(true);

            $this->applyFilters($query, $filters);

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Error al obtener clientes de dateros: ' . $e->getMessage());
            throw new \Exception('Error al obtener la lista de clientes de dateros');
        }
    }

    /**
     * Obtener todos los clientes filtrados sin paginación (para exportación).
     */
    public function getClientsForExport(array $filters = []): Collection
    {
        try {
            $query = $this->baseClientsQuery(false, true);
            $this->applyFilters($query, $filters);

            return $query->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            Log::error('Error al obtener clientes para exportación: ' . $e->getMessage());
            throw new \Exception('Error al obtener clientes para exportación');
        }
    }

    /**
     * Aplicar filtros a la consulta
     */
    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (!empty($filters['source'])) {
            $query->bySource($filters['source']);
        }

        if (!empty($filters['advisor_id'])) {
            $query->byAdvisor($filters['advisor_id']);
        }

        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%");
            });
        }
    }

    /**
     * Obtener cliente por ID con relaciones básicas
     */
    public function getClientById(int $id): ?Client
    {
        try {
            if ($id <= 0) {
                throw new \Exception('ID de cliente inválido');
            }

            $query = Client::with([
                'assignedAdvisor',
                'createdBy',
                'opportunities.project',
                'activities',
                'tasks'
            ])->where('id', $id);

            if (Auth::check()) {
                $query->where('assigned_advisor_id', Auth::id());
            }

            return $query->first();
        } catch (\Exception $e) {
            Log::error("Error al obtener cliente ID {$id}: " . $e->getMessage());
            throw new \Exception('Error al obtener la información del cliente');
        }
    }

    /**
     * Crear nuevo cliente
     */
    public function createClient(array $formData, ?int $createdById = null): Client
    {
        try {
            $data = $this->prepareFormData($formData, $createdById);
            $createMode = $formData['create_mode'] ?? null;
            if (!$createMode) {
                $createMode = empty($data['document_number']) ? 'phone' : 'dni';
            }

            Log::info('Intentando crear cliente', [
                'create_mode' => $createMode,
                'phone' => $data['phone'] ?? null,
                'document_type' => $data['document_type'] ?? null,
                'document_number' => $data['document_number'] ?? null,
                'user_id' => $createdById ?? Auth::id(),
            ]);

            $this->validateClientData($data, null, $createMode);

            $client = Client::create($data);

            Log::info("Cliente creado exitosamente ID: {$client->id}");
            return $client;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al crear cliente: ' . $e->getMessage());
            throw new \Exception('Error al crear el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar cliente existente
     */
    public function updateClient(int $id, array $formData): bool
    {
        try {
            if ($id <= 0) {
                throw new \Exception('ID de cliente inválido');
            }

            $client = Client::find($id);
            if (!$client) {
                throw new \Exception('Cliente no encontrado');
            }

            if (Auth::check() && $client->assigned_advisor_id !== Auth::id()) {
                throw new \Exception('No tienes permiso para actualizar este cliente');
            }

            $data = $this->prepareFormData($formData, null, $client);

            $createMode = $formData['create_mode'] ?? null;
            if (!$createMode) {
                $createMode = empty($data['document_number']) ? 'phone' : 'dni';
            }
            $this->validateClientData($data, $id, $createMode);

            $updated = $client->update($data);

            if ($updated) {
                Log::info("Cliente actualizado exitosamente ID: {$id}");
                return true;
            }

            return false;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Error al actualizar cliente ID {$id}: " . $e->getMessage());
            throw new \Exception('Error al actualizar el cliente: ' . $e->getMessage());
        }
    }


    /**
     * Cambiar estado del cliente
     */
    public function changeStatus(int $clientId, string $newStatus): bool
    {
        try {
            if ($clientId <= 0) {
                throw new \Exception('ID de cliente inválido');
            }

            $validStatuses = ['nuevo', 'contacto_inicial', 'en_seguimiento', 'cierre', 'perdido'];
            if (!in_array($newStatus, $validStatuses)) {
                throw new \Exception('Estado de cliente inválido');
            }

            $client = Client::find($clientId);
            if (!$client) {
                throw new \Exception('Cliente no encontrado');
            }

            $client->update(['status' => $newStatus]);

            Log::info("Estado del cliente {$clientId} cambiado a: {$newStatus}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error al cambiar estado del cliente {$clientId}: " . $e->getMessage());
            throw new \Exception('Error al cambiar el estado del cliente: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar score del cliente
     */
    public function updateScore(int $clientId, int $newScore): bool
    {
        try {
            if ($clientId <= 0) {
                throw new \Exception('ID de cliente inválido');
            }

            if ($newScore < 0 || $newScore > 100) {
                throw new \Exception('Score debe estar entre 0 y 100');
            }

            $client = Client::find($clientId);
            if (!$client) {
                throw new \Exception('Cliente no encontrado');
            }

            $client->update(['score' => $newScore]);

            Log::info("Score del cliente {$clientId} actualizado a: {$newScore}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error al actualizar score del cliente {$clientId}: " . $e->getMessage());
            throw new \Exception('Error al actualizar el score del cliente: ' . $e->getMessage());
        }
    }

    /**
     * Verificar si un cliente ya existe por documento
     */
    public function clientExists(string $documentType, string $documentNumber): ?Client
    {
        $documentType = strtoupper(trim($documentType));
        $documentNumber = trim($documentNumber);
        if (in_array($documentType, ['DNI', 'RUC'], true)) {
            $documentNumber = preg_replace('/[^0-9]/', '', $documentNumber);
        }

        return Client::with('assignedAdvisor')
            ->where('document_number', $documentNumber)
            ->where('document_type', $documentType)
            ->first();
    }

    /**
     * Obtener estadísticas de clientes
     */
    public function getClientStats(): array
    {
        try {
            $totalClients = Client::count();
            $clientsByStatus = Client::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $clientsByType = Client::selectRaw('client_type, COUNT(*) as count')
                ->groupBy('client_type')
                ->pluck('count', 'client_type')
                ->toArray();

            $clientsBySource = Client::selectRaw('source, COUNT(*) as count')
                ->groupBy('source')
                ->pluck('count', 'source')
                ->toArray();

            return [
                'total' => $totalClients,
                'by_status' => $clientsByStatus,
                'by_type' => $clientsByType,
                'by_source' => $clientsBySource,
            ];
        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de clientes: ' . $e->getMessage());
            return [
                'total' => 0,
                'by_status' => [],
                'by_type' => [],
                'by_source' => [],
            ];
        }
    }

    /**
     * Obtener clientes recientes
     */
    public function getRecentClients(int $limit = 10): Collection
    {
        try {
            return Client::with(['assignedAdvisor', 'createdBy'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error al obtener clientes recientes: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Buscar clientes por término de búsqueda
     */
    public function searchClients(string $searchTerm, int $perPage = 15): LengthAwarePaginator
    {
        try {
            $query = Client::with(['assignedAdvisor', 'createdBy'])
                ->withCount(['opportunities', 'activities', 'tasks'])
                ->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('phone', 'like', "%{$searchTerm}%")
                        ->orWhere('document_number', 'like', "%{$searchTerm}%")
                        ->orWhere('address', 'like', "%{$searchTerm}%");
                });

            if (Auth::check()) {
                $query->where('assigned_advisor_id', Auth::id());
            }

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Error al buscar clientes: ' . $e->getMessage());
            throw new \Exception('Error al buscar clientes');
        }
    }

    /**
     * Obtener clientes por asesor
     */
    public function getClientsByAdvisor(int $advisorId, int $perPage = 15): LengthAwarePaginator
    {
        try {
            return Client::with(['assignedAdvisor', 'createdBy'])
                ->withCount(['opportunities', 'activities', 'tasks'])
                ->where('assigned_advisor_id', $advisorId)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        } catch (\Exception $e) {
            Log::error("Error al obtener clientes del asesor {$advisorId}: " . $e->getMessage());
            throw new \Exception('Error al obtener clientes del asesor');
        }
    }

    /**
     * Eliminar cliente
     */
    public function deleteClient(int $id): bool
    {
        try {
            if ($id <= 0) {
                throw new \Exception('ID de cliente inválido');
            }

            $client = Client::find($id);
            if (!$client) {
                throw new \Exception('Cliente no encontrado');
            }

            if (Auth::check() && $client->assigned_advisor_id !== Auth::id()) {
                throw new \Exception('No tienes permiso para eliminar este cliente');
            }

            $deleted = $client->delete();

            if ($deleted) {
                Log::info("Cliente eliminado exitosamente ID: {$id}");
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Error al eliminar cliente ID {$id}: " . $e->getMessage());
            throw new \Exception('Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Preparar datos del formulario para crear/actualizar cliente
     */
    public function prepareFormData(array $formData, ?int $createdById = null, ?Client $editingClient = null): array
    {
        $formData = $this->sanitizeFormData($formData);
        $createMode = $formData['create_mode'] ?? null;
        if (!$createMode) {
            $documentNumber = $formData['document_number'] ?? null;
            $createMode = empty($documentNumber) ? 'phone' : 'dni';
        }
        $data = [
            'name' => $formData['name'],
            'phone' => $formData['phone'],
            'document_type' => $formData['document_type'],
            'document_number' => $formData['document_number'],
            'address' => $formData['address'] ?? null,
            'city_id' => $formData['city_id'] ?? null,
            'birth_date' => $formData['birth_date'] ?? null,
            'client_type' => $formData['client_type'],
            'source' => $formData['source'],
            'status' => $formData['status'] ?? null,
            'score' => $formData['score'] ?? null,
            'notes' => $formData['notes'] ?? null,
            'assigned_advisor_id' => $formData['assigned_advisor_id'] ?? null,
            'create_mode' => $createMode,
        ];

        // Agregar campos de auditoría
        if (!$editingClient) {
            // Al crear: assigned_advisor_id, created_by y updated_by = usuario autenticado
            $userId = $createdById ?? Auth::id();
            if ($userId === null) {
                throw new \Exception('No se puede crear un cliente sin especificar el usuario creador (created_by)');
            }

            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('Usuario creador no encontrado');
            }

            $data['assigned_advisor_id'] = $userId;
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;
            $data['create_type'] = $user->isDatero() ? 'datero' : 'propio';

            if (!isset($formData['status'])) {
                $data['status'] = 'nuevo';
            }
            if (!isset($formData['score'])) {
                $data['score'] = 0;
            }
        } else {
            // Al actualizar: todos los campos editables; updated_by = usuario que modifica
            $data['updated_by'] = Auth::id();
        }

        return $data;
    }

    private function sanitizeFormData(array $formData): array
    {
        $data = $formData;

        if (isset($data['name'])) {
            $data['name'] = trim($data['name']);
        }
        if (isset($data['phone'])) {
            $data['phone'] = preg_replace('/[^0-9]/', '', (string) $data['phone']);
        }
        if (array_key_exists('create_mode', $data)) {
            $createMode = strtolower(trim((string) $data['create_mode']));
            $data['create_mode'] = $createMode === '' ? null : $createMode;
        }
        if (array_key_exists('document_type', $data)) {
            $documentType = trim((string) $data['document_type']);
            $data['document_type'] = $documentType === '' ? null : strtoupper($documentType);
        }
        if (array_key_exists('document_number', $data)) {
            $documentNumber = trim((string) $data['document_number']);
            if ($documentNumber === '') {
                $data['document_number'] = null;
                return $data;
            }
            $documentType = $data['document_type'] ?? null;
            if (in_array($documentType, ['DNI', 'RUC'], true)) {
                $documentNumber = preg_replace('/[^0-9]/', '', $documentNumber);
            } else {
                $documentNumber = strtoupper(preg_replace('/\s+/', '', $documentNumber));
            }
            $data['document_number'] = $documentNumber;
        }
        if (isset($data['address'])) {
            $data['address'] = trim((string) $data['address']);
        }
        if (isset($data['notes'])) {
            $data['notes'] = trim((string) $data['notes']);
        }

        return $data;
    }

    /**
     * Obtener reglas de validación centralizadas
     */
    public function getValidationRules(?int $clientId = null, ?string $createMode = null): array
    {
        $isPhoneMode = $createMode === 'phone';
        $documentTypeRules = $isPhoneMode
            ? ['nullable', 'in:DNI,RUC,CE,PASAPORTE']
            : ['required', 'in:DNI,RUC,CE,PASAPORTE'];

        $documentNumberRules = $isPhoneMode
            ? ['nullable', 'string', 'max:20']
            : ['required', 'string', 'max:20'];

        if ($isPhoneMode) {
            $uniqueRule = Rule::unique('clients', 'document_number')
                ->where(fn($query) => $query->where('document_number', '!=', '00000000'));
            if ($clientId) {
                $uniqueRule = $uniqueRule->ignore($clientId);
            }
            $documentNumberRules[] = $uniqueRule;
        } else {
            if ($clientId) {
                $documentNumberRules[] = Rule::unique('clients', 'document_number')->ignore($clientId);
            } else {
                $documentNumberRules[] = Rule::unique('clients', 'document_number');
            }
        }

        $rules = [
            'create_mode' => 'required|in:dni,phone',
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^9[0-9]{8}$/'],
            'document_type' => $documentTypeRules,
            'document_number' => $documentNumberRules,
            'address' => 'nullable|string|max:500',
            'city_id' => 'required|exists:cities,id',
            'birth_date' => 'required|date',
            'client_type' => 'required|in:inversor,comprador,empresa,constructor',
            'source' => 'required|in:redes_sociales,ferias,referidos,formulario_web,publicidad',
            'status' => 'required|in:nuevo,contacto_inicial,en_seguimiento,cierre,perdido',
            'score' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'assigned_advisor_id' => 'nullable|exists:users,id'
        ];

        // Validar documento único excepto para el cliente actual
        if ($clientId) {
            $rules['phone'] = ['required', 'string', 'regex:/^9[0-9]{8}$/', 'unique:clients,phone,' . $clientId];
        } else {
            $rules['phone'] = ['required', 'string', 'regex:/^9[0-9]{8}$/', 'unique:clients,phone'];
        }

        return $rules;
    }

    /**
     * Obtener mensajes de validación centralizados
     */
    public function getValidationMessages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.string' => 'El teléfono debe ser una cadena de texto.',
            'phone.regex' => 'El teléfono debe tener 9 dígitos y comenzar con el número 9 (ejemplo: 912345678).',
            'phone.unique' => 'El teléfono ya está en uso.',
            'create_mode.in' => 'El modo de creación seleccionado no es válido.',
            'document_type.required' => 'El tipo de documento es obligatorio.',
            'document_type.in' => 'El tipo de documento seleccionado no es válido.',
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.string' => 'El número de documento debe ser una cadena de texto.',
            'document_number.max' => 'El número de documento no puede exceder 20 caracteres.',
            'document_number.unique' => 'El número de documento ya está en uso.',
            'city_id.required' => 'La ciudad es obligatoria.',
            'city_id.exists' => 'La ciudad seleccionada no es válida.',
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'address.max' => 'La dirección no puede exceder 500 caracteres.',
            'birth_date.required' => 'La fecha de nacimiento es obligatoria.',
            'birth_date.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'client_type.required' => 'El tipo de cliente es obligatorio.',
            'client_type.in' => 'El tipo de cliente seleccionado no es válido.',
            'source.required' => 'El origen es obligatorio.',
            'source.in' => 'El origen seleccionado no es válido.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            'score.required' => 'La puntuación es obligatoria.',
            'score.integer' => 'La puntuación debe ser un número entero.',
            'score.min' => 'La puntuación debe ser al menos 0.',
            'score.max' => 'La puntuación no puede exceder 100.',
            'notes.string' => 'Las notas deben ser una cadena de texto.',
            'assigned_advisor_id.exists' => 'El asesor seleccionado no existe.',
        ];
    }

    /**
     * Obtener opciones para formularios
     */
    public function getFormOptions(): array
    {
        return [
            'document_types' => [
                'DNI' => 'DNI',
                'RUC' => 'RUC',
                'CE' => 'Carné de Extranjería',
                'PASAPORTE' => 'Pasaporte'
            ],
            'client_types' => [
                'inversor' => 'Inversor',
                'comprador' => 'Comprador',
                'empresa' => 'Empresa',
                'constructor' => 'Constructor'
            ],
            'sources' => [
                'redes_sociales' => 'Redes Sociales',
                'ferias' => 'Ferias',
                'referidos' => 'Referidos',
                'formulario_web' => 'Formulario Web',
                'publicidad' => 'Publicidad'
            ],
            'statuses' => [
                'nuevo' => 'Nuevo',
                'contacto_inicial' => 'Contacto Inicial',
                'en_seguimiento' => 'En Seguimiento',
                'cierre' => 'Cierre',
                'perdido' => 'Perdido'
            ]
        ];
    }

    /**
     * Validar datos del cliente
     */
    private function validateClientData(array $data, ?int $clientId = null, ?string $createMode = null): void
    {
        $rules = $this->getValidationRules($clientId, $createMode);
        $messages = $this->getValidationMessages();

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            Log::warning('Validación fallida al guardar cliente', [
                'create_mode' => $createMode,
                'phone' => $data['phone'] ?? null,
                'document_type' => $data['document_type'] ?? null,
                'document_number' => $data['document_number'] ?? null,
                'errors' => $validator->errors()->toArray(),
            ]);
            throw new ValidationException($validator);
        }
    }
}
