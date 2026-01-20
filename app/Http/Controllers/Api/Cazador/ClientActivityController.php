<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\ActivityService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ClientActivityController extends Controller
{
    use ApiResponse;

    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
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
            return $this->serverErrorResponse($e, 'Error al crear la actividad');
        }
    }
}
