<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\TaskService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ClientTaskController extends Controller
{
    use ApiResponse;

    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function store(Client $client, Request $request)
    {
        if ($client->assigned_advisor_id !== Auth::id() && $client->created_by !== Auth::id()) {
            return $this->forbiddenResponse('No tienes permiso para este cliente');
        }

        try {
            $data = $request->all();
            $data['client_id'] = $client->id;

            $task = $this->taskService->createTask($data, Auth::id());

            return $this->successResponse([
                'task' => $task,
            ], 'Tarea creada correctamente', 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al crear la tarea');
        }
    }
}
