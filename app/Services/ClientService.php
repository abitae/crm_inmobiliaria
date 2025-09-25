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
use Illuminate\Validation\ValidationException;

class ClientService
{
    /**
     * Obtener todos los clientes con paginación y filtros
     */
    public function getAllClients(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Client::with(['assignedAdvisor', 'createdBy'])
                ->withCount(['opportunities', 'activities', 'tasks']);
            $this->applyFilters($query, $filters);

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Error al obtener clientes: ' . $e->getMessage());
            throw new \Exception('Error al obtener la lista de clientes');
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

            return Client::with([
                'assignedAdvisor',
                'createdBy',
                'opportunities.project',
                'activities',
                'tasks'
            ])->find($id);
        } catch (\Exception $e) {
            Log::error("Error al obtener cliente ID {$id}: " . $e->getMessage());
            throw new \Exception('Error al obtener la información del cliente');
        }
    }

    /**
     * Crear nuevo cliente
     */
    public function createClient(array $data): Client
    {
        try {
            $this->validateClientData($data);
            $data['created_by'] = $data['created_by'] ?? Auth::id();
            $data['updated_by'] = $data['updated_by'] ?? Auth::id();

            $client = Client::create($data);

            Log::info("Cliente creado exitosamente ID: {$client->id}");
            return $client;
        } catch (\Exception $e) {
            Log::error('Error al crear cliente: ' . $e->getMessage());
            throw new \Exception('Error al crear el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar cliente existente
     */
    public function updateClient(int $id, array $data): bool
    {
        try {
            if ($id <= 0) {
                throw new \Exception('ID de cliente inválido');
            }

            $client = Client::find($id);
            if (!$client) {
                throw new \Exception('Cliente no encontrado');
            }

            $this->validateClientData($data, $id);

            $data['updated_by'] = Auth::id();
            $updated = $client->update($data);

            if ($updated) {
                Log::info("Cliente actualizado exitosamente ID: {$id}");
                return true;
            }

            return false;
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
     * Validar datos del cliente
     */
    private function validateClientData(array $data, ?int $clientId = null): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'document_type' => 'required|in:DNI,RUC,CE,PASAPORTE',
            'document_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'client_type' => 'required|in:inversor,comprador,empresa,constructor',
            'source' => 'required|in:redes_sociales,ferias,referidos,formulario_web,publicidad',
            'status' => 'required|in:nuevo,contacto_inicial,en_seguimiento,cierre,perdido',
            'score' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'assigned_advisor_id' => 'nullable|exists:users,id'
        ];

        // Validar documento único excepto para el cliente actual
        if ($clientId) {
            $rules['document_number'] = 'required|string|max:20|unique:clients,document_number,' . $clientId;
        } else {
            $rules['document_number'] = 'required|string|max:20|unique:clients,document_number';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
