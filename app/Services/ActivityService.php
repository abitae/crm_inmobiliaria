<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ActivityService
{
    public function getActivitiesPaginated(array $filters, ?User $user, int $perPage = 10): LengthAwarePaginator
    {
        $query = Activity::with(['client', 'project', 'opportunity']);

        if ($this->shouldFilterByRole($user)) {
            $this->applyRoleFilter($query, $user);
        }

        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('activity_type', $filters['type']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->whereHas('client', function ($qClient) use ($search) {
                $qClient->where('name', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('start_date')->paginate($perPage);
    }

    public function deleteActivity(int $activityId): bool
    {
        $deleted = Activity::whereKey($activityId)->delete();
        return $deleted > 0;
    }

    public function getClientActivities(int $clientId, int $limit = 10)
    {
        return Activity::where('client_id', $clientId)
            ->orderByDesc('start_date')
            ->limit($limit)
            ->get(['id', 'title', 'activity_type', 'status', 'start_date']);
    }

    public function getClientActivitiesPaginated(
        int $clientId,
        int $perPage = 5,
        string $pageName = 'activityPage'
    ) {
        return Activity::where('client_id', $clientId)
            ->orderByDesc('start_date')
            ->paginate($perPage, ['id', 'title', 'activity_type', 'status', 'start_date'], $pageName);
    }

    public function getCreateRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'activity_type' => 'required|in:llamada,reunion,visita,seguimiento,tarea',
            'status' => 'required|in:programada,en_progreso,completada,cancelada',
            'priority' => 'required|in:baja,media,alta,urgente',
            'start_date' => 'required|date',
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'unit_id' => 'nullable|exists:units,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'advisor_id' => 'nullable|exists:users,id',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:255',
            'reminder_before' => 'nullable|integer|min:1',
        ];
    }

    public function getCreateMessages(): array
    {
        return [
            'title.required' => 'El titulo es obligatorio.',
            'activity_type.required' => 'El tipo de actividad es obligatorio.',
            'activity_type.in' => 'El tipo de actividad no es valido.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado no es valido.',
            'priority.required' => 'La prioridad es obligatoria.',
            'priority.in' => 'La prioridad no es valida.',
            'start_date.required' => 'La fecha es obligatoria.',
            'start_date.date' => 'La fecha no es valida.',
            'client_id.exists' => 'El cliente no existe.',
            'assigned_to.exists' => 'El usuario asignado no existe.',
        ];
    }

    public function createActivity(array $data, int $userId): Activity
    {
        try {
            $data = $this->applyDefaults($data, $userId);
            $this->validateCreateData($data);

            return Activity::create($data);
        } catch (\Exception $e) {
            Log::error('Error al crear actividad', [
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
        $data['status'] = $data['status'] ?? 'programada';
        $data['priority'] = $data['priority'] ?? 'media';
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;
        $data['reminder_sent'] = $data['reminder_sent'] ?? false;

        return $data;
    }

    private function shouldFilterByRole(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return !$user->isAdmin();
    }

    private function applyRoleFilter($query, User $user): void
    {
        if ($user->isLider()) {
            $teamUserIds = $this->getTeamUserIds($user);
            $query->where(function ($q) use ($teamUserIds) {
                $q->whereIn('assigned_to', $teamUserIds)
                    ->orWhereIn('advisor_id', $teamUserIds);
            });
        } elseif ($user->isAdvisor()) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('advisor_id', $user->id);
            });
        } else {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('advisor_id', $user->id);
            });
        }
    }

    private function getTeamUserIds(User $leader): array
    {
        $userIds = [$leader->id];

        $vendedoresIds = User::where('lider_id', $leader->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'vendedor');
            })
            ->pluck('id')
            ->toArray();

        $userIds = array_merge($userIds, $vendedoresIds);

        return array_unique($userIds);
    }
}
