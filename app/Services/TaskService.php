<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function getTasksPaginated(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Task::with(['client']);

        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->whereHas('client', function ($qClient) use ($search) {
                $qClient->where('name', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('due_date', 'asc')->paginate($perPage);
    }

    public function deleteTask(int $taskId): bool
    {
        $deleted = Task::whereKey($taskId)->delete();
        return $deleted > 0;
    }

    public function getClientTasks(int $clientId, int $limit = 10)
    {
        return Task::where('client_id', $clientId)
            ->orderByDesc('due_date')
            ->limit($limit)
            ->get(['id', 'title', 'priority', 'status', 'due_date']);
    }

    public function getClientTasksPaginated(
        int $clientId,
        int $perPage = 5,
        string $pageName = 'taskPage'
    ) {
        return Task::where('client_id', $clientId)
            ->orderByDesc('due_date')
            ->paginate($perPage, ['id', 'title', 'priority', 'status', 'due_date'], $pageName);
    }

    public function getCreateRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'task_type' => 'required|in:seguimiento,visita,llamada,documento,otros',
            'status' => 'required|in:pendiente,en_progreso,completada,cancelada',
            'priority' => 'required|in:baja,media,alta,urgente',
            'due_date' => 'nullable|date',
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'unit_id' => 'nullable|exists:units,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'tags' => 'nullable|array',
        ];
    }

    public function getCreateMessages(): array
    {
        return [
            'title.required' => 'El titulo es obligatorio.',
            'task_type.required' => 'El tipo de tarea es obligatorio.',
            'task_type.in' => 'El tipo de tarea no es valido.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado no es valido.',
            'priority.required' => 'La prioridad es obligatoria.',
            'priority.in' => 'La prioridad no es valida.',
            'client_id.exists' => 'El cliente no existe.',
            'assigned_to.exists' => 'El usuario asignado no existe.',
        ];
    }

    public function createTask(array $data, int $userId): Task
    {
        try {
            $data = $this->applyDefaults($data, $userId);
            $this->validateCreateData($data);

            return Task::create($data);
        } catch (\Exception $e) {
            Log::error('Error al crear tarea', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function validateCreateData(array $data): void
    {
        $validator = Validator::make($data, $this->getCreateRules(), $this->getCreateMessages());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function applyDefaults(array $data, int $userId): array
    {
        $data['status'] = $data['status'] ?? 'pendiente';
        $data['priority'] = $data['priority'] ?? 'media';
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;

        return $data;
    }
}
