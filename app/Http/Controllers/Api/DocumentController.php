<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Traits\SearchDocument;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    use ApiResponse, SearchDocument;

    /**
     * Buscar datos de persona o empresa por DNI/RUC
     * 
     * Este endpoint permite buscar información completa de personas (DNI) o empresas (RUC)
     * utilizando el servicio externo de Facturalahoy.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            // Validar y sanitizar los datos de entrada
            $validator = Validator::make($request->all(), [
                'document_type' => 'required|string|in:dni,ruc',
                'document_number' => 'required|string|regex:/^[0-9]+$/',
            ], [
                'document_type.required' => 'El tipo de documento es obligatorio.',
                'document_type.in' => 'El tipo de documento debe ser "dni" o "ruc".',
                'document_number.required' => 'El número de documento es obligatorio.',
                'document_number.regex' => 'El número de documento solo debe contener dígitos.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Sanitizar y normalizar datos
            $tipo = strtolower(trim($request->input('document_type')));
            $num_doc = preg_replace('/[^0-9]/', '', $request->input('document_number')); // Solo números

            // Validar formato según el tipo
            if ($tipo === 'dni') {
                if (strlen($num_doc) !== 8) {
                    return $this->errorResponse(
                        'El DNI debe tener exactamente 8 dígitos.',
                        ['document_number' => ['El DNI debe tener exactamente 8 dígitos.']],
                        422
                    );
                }
            } elseif ($tipo === 'ruc') {
                if (strlen($num_doc) !== 11) {
                    return $this->errorResponse(
                        'El RUC debe tener exactamente 11 dígitos.',
                        ['document_number' => ['El RUC debe tener exactamente 11 dígitos.']],
                        422
                    );
                }
            }

            // Verificar si el documento ya está registrado en la base de datos
            $documentTypeNormalized = strtoupper($tipo); // DNI o RUC
            $existingClient = Client::where('document_number', $num_doc)
                ->where('document_type', $documentTypeNormalized)
                ->with('assignedAdvisor:id,name,email')
                ->first();

            if ($existingClient) {
                // Cliente ya registrado
                $advisorInfo = null;
                if ($existingClient->assignedAdvisor) {
                    $advisorInfo = [
                        'id' => $existingClient->assignedAdvisor->id,
                        'name' => $existingClient->assignedAdvisor->name,
                        'email' => $existingClient->assignedAdvisor->email,
                    ];
                }

                Log::info('Intento de búsqueda de documento ya registrado', [
                    'document_type' => $tipo,
                    'document_number' => $num_doc,
                    'client_id' => $existingClient->id,
                    'assigned_advisor_id' => $existingClient->assigned_advisor_id,
                    'user_id' => auth()->id(),
                ]);

                return $this->errorResponse(
                    'Cliente registrado por el cazador responsable de ese cliente',
                    [
                        'client_registered' => true,
                        'client_id' => $existingClient->id,
                        'client_name' => $existingClient->name,
                        'assigned_advisor' => $advisorInfo,
                        'message' => $advisorInfo 
                            ? "El cliente ya está registrado. Cazador responsable: {$advisorInfo['name']}"
                            : 'El cliente ya está registrado en el sistema.'
                    ],
                    409 // Conflict - recurso ya existe
                );
            }

            // Realizar la búsqueda usando el trait
            $result = $this->searchComplete($tipo, $num_doc);

            // Si hay un error en la respuesta
            if (isset($result['respuesta']) && $result['respuesta'] === 'error') {
                $errorMessage = $result['mensaje'] ?? 'Error al buscar el documento';
                
                // Log del error para debugging
                Log::warning('Error en búsqueda de documento', [
                    'document_type' => $tipo,
                    'document_number' => $num_doc,
                    'error' => $errorMessage,
                    'user_id' => auth()->id(),
                ]);

                return $this->errorResponse(
                    $errorMessage,
                    ['details' => $result],
                    404
                );
            }

            // Si no se encontró el documento
            if (!isset($result['encontrado']) || !$result['encontrado']) {
                Log::info('Documento no encontrado', [
                    'document_type' => $tipo,
                    'document_number' => $num_doc,
                    'user_id' => auth()->id(),
                ]);

                return $this->errorResponse(
                    'No se encontró información para el documento proporcionado',
                    null,
                    404
                );
            }

            // Formatear la respuesta exitosa
            $data = $result['data'] ?? null;
            
            $response = [
                'found' => true,
                'document_type' => $tipo,
                'document_number' => $num_doc,
                'data' => $data,
            ];

            // Agregar información de ubigeo si está disponible
            if (isset($result['texto_ubigeo']) && !empty($result['texto_ubigeo'])) {
                $response['ubigeo'] = [
                    'text' => $result['texto_ubigeo'],
                    'code' => $result['codigo_ubigeo'] ?? null,
                ];
            }

            // Log de búsqueda exitosa
            Log::info('Búsqueda de documento exitosa', [
                'document_type' => $tipo,
                'document_number' => $num_doc,
                'user_id' => auth()->id(),
            ]);

            return $this->successResponse($response, 'Datos encontrados exitosamente');

        } catch (\Exception $e) {
            // Log del error
            Log::error('Error al procesar búsqueda de documento', [
                'document_type' => $request->input('document_type'),
                'document_number' => $request->input('document_number'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return $this->errorResponse(
                'Error al procesar la búsqueda. Por favor, intente nuevamente.',
                config('app.debug') ? ['error' => $e->getMessage()] : null,
                500
            );
        }
    }
}

