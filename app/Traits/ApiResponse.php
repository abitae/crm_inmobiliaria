<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Respuesta exitosa estándar
     */
    protected function successResponse(
        $data = null,
        string $message = 'Operación exitosa',
        int $statusCode = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Respuesta de error estándar
     */
    protected function errorResponse(
        string $message = 'Error en la operación',
        $errors = null,
        int $statusCode = 400
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Respuesta de validación
     */
    protected function validationErrorResponse($errors, string $message = 'Error de validación'): JsonResponse
    {
        return $this->errorResponse($message, $errors, 422);
    }

    /**
     * Respuesta de no encontrado
     */
    protected function notFoundResponse(string $resource = 'Recurso'): JsonResponse
    {
        return $this->errorResponse("{$resource} no encontrado", null, 404);
    }

    /**
     * Respuesta de acceso denegado
     */
    protected function forbiddenResponse(string $message = 'Acceso denegado'): JsonResponse
    {
        return $this->errorResponse($message, null, 403);
    }

    /**
     * Respuesta de no autenticado
     */
    protected function unauthorizedResponse(string $message = 'No autenticado'): JsonResponse
    {
        return $this->errorResponse($message, null, 401);
    }

    /**
     * Respuesta de error del servidor
     */
    protected function serverErrorResponse(\Exception $e, string $message = 'Error en el servidor'): JsonResponse
    {
        $error = config('app.debug') ? $e->getMessage() : 'Error interno del servidor';
        return $this->errorResponse($message, ['error' => $error], 500);
    }
}

