<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Activity;
use App\Services\ActivityService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClientActivityController extends Controller
{
    use ApiResponse;

    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    public function index(Client $client, Request $request)
    {
        if ($client->assigned_advisor_id !== Auth::id() && $client->created_by !== Auth::id()) {
            return $this->forbiddenResponse('No tienes permiso para este cliente');
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        $query = Activity::with(['advisor:id,name,email', 'assignedTo:id,name,email'])
            ->where('client_id', $client->id);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->get('activity_type'));
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->get('priority'));
        }
        if ($request->filled('start_date_from')) {
            $query->whereDate('start_date', '>=', $request->get('start_date_from'));
        }
        if ($request->filled('start_date_to')) {
            $query->whereDate('start_date', '<=', $request->get('start_date_to'));
        }
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }

        try {
            $activities = $query->orderBy('start_date', 'desc')->paginate($perPage);

            return $this->successResponse([
                'activities' => $activities->items(),
                'pagination' => $this->formatPagination($activities),
            ], 'Actividades obtenidas exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al listar actividades del cliente');
        }
    }

    public function store(Client $client, Request $request)
    {
        if ($client->assigned_advisor_id !== Auth::id() && $client->created_by !== Auth::id()) {
            return $this->forbiddenResponse('No tienes permiso para este cliente');
        }

        try {
            $data = $request->all();
            $data['client_id'] = $client->id;

            $activity = $this->activityService->createActivity($data, Auth::id());

            return $this->successResponse([
                'activity' => $activity,
            ], 'Actividad creada correctamente', 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al crear la actividad del cliente');
        }
    }

    public function update(Client $client, Activity $activity, Request $request)
    {
        if ($client->assigned_advisor_id !== Auth::id() && $client->created_by !== Auth::id()) {
            return $this->forbiddenResponse('No tienes permiso para este cliente');
        }

        if ($activity->client_id !== $client->id) {
            return $this->forbiddenResponse('La actividad no pertenece al cliente');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:programada,en_progreso,completada,cancelada',
            'result' => 'nullable|string',
            'notes' => 'nullable|string',
            'start_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $updateData = $request->only([
                'status',
                'result',
                'notes',
                'start_date',
                'assigned_to',
            ]);
            $updateData['updated_by'] = Auth::id();

            $activity->update($updateData);
            $activity->refresh();

            return $this->successResponse([
                'activity' => $activity,
            ], 'Actividad actualizada correctamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al actualizar la actividad del cliente');
        }
    }

    private function formatPagination($paginator): array
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
}
