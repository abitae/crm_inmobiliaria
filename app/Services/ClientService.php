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
                ->withCount(['opportunities', 'interactions', 'tasks']);

            // Aplicar filtros
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
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%");
            });
        }
    }

    /**
     * Obtener cliente por ID con todas las relaciones
     */
    public function getClientById(int $id): ?Client
    {
        try {
            if ($id <= 0) {
                throw new ValidationException('ID de cliente inválido');
            }

            return Client::with([
                'assignedAdvisor',
                'createdBy',
                'updatedBy',
                'opportunities.project',
                'opportunities.unit',
                'interactions',
                'tasks',
                'activities',
                'documents',
                'reservations.project',
                'reservations.unit',
                'projects',
                'units'
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
            DB::beginTransaction();

            $this->validateClientData($data);

            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $client = Client::create($data);

            DB::commit();

            Log::info("Cliente creado exitosamente ID: {$client->id}");
            return $client;
        } catch (\Exception $e) {
            DB::rollBack();
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
            DB::beginTransaction();

            if ($id <= 0) {
                throw new ValidationException('ID de cliente inválido');
            }

            $client = Client::find($id);
            if (!$client) {
                throw new \Exception('Cliente no encontrado');
            }

            $this->validateClientData($data, $id);

            $data['updated_by'] = Auth::id();
            $updated = $client->update($data);

            if ($updated) {
                DB::commit();
                Log::info("Cliente actualizado exitosamente ID: {$id}");
                return true;
            }

            DB::rollBack();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar cliente ID {$id}: " . $e->getMessage());
            throw new \Exception('Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar cliente (soft delete)
     */
    public function deleteClient(int $id): bool
    {
        try {
            if ($id <= 0) {
                throw new ValidationException('ID de cliente inválido');
            }

            $client = Client::find($id);
            if (!$client) {
                throw new \Exception('Cliente no encontrado');
            }

            // Verificar si el cliente tiene oportunidades activas
            if ($client->opportunities()->where('status', 'activa')->exists()) {
                throw new \Exception('No se puede eliminar un cliente con oportunidades activas');
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
     * Asignar asesor a cliente
     */
    public function assignAdvisor(int $clientId, int $advisorId): bool
    {
        try {
            if ($clientId <= 0 || $advisorId <= 0) {
                throw new ValidationException('IDs de cliente o asesor inválidos');
            }

            $client = Client::find($clientId);
            if (!$client) {
                throw new \Exception('Cliente no encontrado');
            }

            $advisor = User::find($advisorId);
            if (!$advisor) {
                throw new \Exception('Asesor no encontrado');
            }

            $client->assignAdvisor($advisorId);

            Log::info("Asesor {$advisorId} asignado al cliente {$clientId}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error al asignar asesor {$advisorId} al cliente {$clientId}: " . $e->getMessage());
            throw new \Exception('Error al asignar el asesor: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del cliente
     */
    public function changeStatus(int $clientId, string $newStatus): bool
    {
        try {
            if ($clientId <= 0) {
                throw new ValidationException('ID de cliente inválido');
            }

            $validStatuses = ['active', 'inactive', 'prospect', 'en_seguimiento', 'cierre', 'perdido'];
            if (!in_array($newStatus, $validStatuses)) {
                throw new ValidationException('Estado de cliente inválido');
            }

            $client = Client::find($clientId);
            if (!$client) {
                throw new \Exception('Cliente no encontrado');
            }

            $client->changeStatus($newStatus);

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
                throw new ValidationException('ID de cliente inválido');
            }

            if ($newScore < 0 || $newScore > 100) {
                throw new ValidationException('Score debe estar entre 0 y 100');
            }

            $client = Client::find($clientId);
            if (!$client) {
                throw new \Exception('Cliente no encontrado');
            }

            $client->updateScore($newScore);

            Log::info("Score del cliente {$clientId} actualizado a: {$newScore}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error al actualizar score del cliente {$clientId}: " . $e->getMessage());
            throw new \Exception('Error al actualizar el score del cliente: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas de clientes
     */
    public function getClientStats(): array
    {
        try {
            $totalClients = Client::count();
            $activeClients = Client::active()->count();
            $newClients = Client::byStatus('nuevo')->count();
            $inFollowUp = Client::byStatus('en_seguimiento')->count();
            $closing = Client::byStatus('cierre')->count();
            $lost = Client::byStatus('perdido')->count();

            return [
                'total' => $totalClients,
                'active' => $activeClients,
                'new' => $newClients,
                'in_follow_up' => $inFollowUp,
                'closing' => $closing,
                'lost' => $lost,
                'conversion_rate' => $totalClients > 0 ? round(($closing / $totalClients) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de clientes: ' . $e->getMessage());
            throw new \Exception('Error al obtener las estadísticas de clientes');
        }
    }

    /**
     * Obtener clientes por asesor
     */
    public function getClientsByAdvisor(int $advisorId): Collection
    {
        try {
            if ($advisorId <= 0) {
                throw new ValidationException('ID de asesor inválido');
            }

            return Client::with(['assignedAdvisor'])
                ->byAdvisor($advisorId)
                ->withCount(['opportunities', 'interactions', 'tasks'])
                ->get();
        } catch (\Exception $e) {
            Log::error("Error al obtener clientes del asesor {$advisorId}: " . $e->getMessage());
            throw new \Exception('Error al obtener los clientes del asesor');
        }
    }

    /**
     * Obtener clientes con oportunidades activas
     */
    public function getClientsWithActiveOpportunities(): Collection
    {
        try {
            return Client::whereHas('opportunities', function ($query) {
                $query->where('status', 'activa');
            })->with(['opportunities.project'])->get();
        } catch (\Exception $e) {
            Log::error('Error al obtener clientes con oportunidades activas: ' . $e->getMessage());
            throw new \Exception('Error al obtener clientes con oportunidades activas');
        }
    }

    /**
     * Buscar clientes por término
     */
    public function searchClients(string $term): Collection
    {
        try {
            $term = trim($term);
            if (strlen($term) < 2) {
                throw new ValidationException('El término de búsqueda debe tener al menos 2 caracteres');
            }

            return Client::where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('document_number', 'like', "%{$term}%")
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error al buscar clientes: ' . $e->getMessage());
            throw new \Exception('Error al buscar clientes: ' . $e->getMessage());
        }
    }

    /**
     * Validar datos del cliente
     */
    private function validateClientData(array $data, ?int $clientId = null): void
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'document_number' => 'nullable|string|max:20',
            'status' => 'nullable|string|in:active,inactive,prospect,en_seguimiento,cierre,perdido',
            'source' => 'nullable|string|max:100',
            'score' => 'nullable|integer|min:0|max:100',
        ];

        // Validar email único excepto para el cliente actual
        if ($clientId) {
            $rules['email'] = 'required|email|max:255|unique:clients,email,' . $clientId;
        } else {
            $rules['email'] = 'required|email|max:255|unique:clients,email';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
