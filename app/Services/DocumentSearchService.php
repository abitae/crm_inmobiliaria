<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DocumentSearchService
{
    private string $apiPassword = "facturalaya_cris_JPckC4FPGYHtMR5";
    private string $apiBaseUrl = "https://facturalahoy.com/api";

    /**
     * Buscar documento completo en API externa
     */
    public function searchComplete(string $documentType, string $documentNumber): array
    {
        try {
            $documentType = strtolower($documentType);
            $documentNumber = trim($documentNumber);

            // Validar formato
            if (!$this->validateDocumentFormat($documentType, $documentNumber)) {
                return $this->buildErrorResponse('Tipo de Documento Desconocido');
            }

            // Construir URL de la API
            $url = $this->buildApiUrl($documentType, $documentNumber);

            // Realizar petición a la API
            $response = $this->makeApiRequest($url);

            if (!$response['success']) {
                return $response;
            }

            // Procesar respuesta
            return $this->processApiResponse($response['data'], $documentType);

        } catch (\Exception $e) {
            Log::error('Error en DocumentSearchService::searchComplete', [
                'document_type' => $documentType,
                'document_number' => $documentNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->buildErrorResponse('Error al buscar los datos del cliente');
        }
    }

    /**
     * Verificar si un cliente ya existe por documento
     */
    public function clientExists(string $documentType, string $documentNumber): ?Client
    {
        try {
            return Client::with('assignedAdvisor')
                ->where('document_number', $documentNumber)
                ->where('document_type', $documentType)
                ->first();
        } catch (\Exception $e) {
            Log::error('Error al verificar existencia de cliente', [
                'document_type' => $documentType,
                'document_number' => $documentNumber,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Buscar y procesar datos del cliente
     */
    public function searchAndProcessClient(string $documentType, string $documentNumber): array
    {
        // Verificar si el cliente ya existe
        $existingClient = $this->clientExists($documentType, $documentNumber);
        
        if ($existingClient) {
            return [
                'found' => true,
                'exists_in_db' => true,
                'client' => $existingClient,
                'data' => null
            ];
        }

        // Buscar en API externa
        $apiResult = $this->searchComplete($documentType, $documentNumber);

        if ($apiResult['encontrado'] ?? false) {
            return [
                'found' => true,
                'exists_in_db' => false,
                'client' => null,
                'data' => $apiResult['data'] ?? null
            ];
        }

        return [
            'found' => false,
            'exists_in_db' => false,
            'client' => null,
            'data' => null,
            'error' => $apiResult['mensaje'] ?? 'Documento no encontrado'
        ];
    }

    /**
     * Extraer datos del cliente desde la respuesta de la API
     */
    public function extractClientData(object $apiData): array
    {
        $name = $apiData->nombre ?? '';
        
        // Buscar fecha de nacimiento en diferentes formatos
        $birthDate = null;
        if (isset($apiData->fecha_nacimiento)) {
            $birthDate = $apiData->fecha_nacimiento;
        } elseif (isset($apiData->fechaNacimiento)) {
            $birthDate = $apiData->fechaNacimiento;
        } elseif (isset($apiData->api->result->fechaNacimiento)) {
            $birthDate = $apiData->api->result->fechaNacimiento;
        }

        // Parsear fecha de nacimiento
        $parsedBirthDate = $this->parseBirthDate($birthDate);

        return [
            'name' => $name,
            'birth_date' => $parsedBirthDate,
        ];
    }

    /**
     * Validar formato de documento
     */
    private function validateDocumentFormat(string $documentType, string $documentNumber): bool
    {
        if ($documentType === 'dni' && strlen($documentNumber) === 8) {
            return true;
        }
        
        if ($documentType === 'ruc' && strlen($documentNumber) === 11) {
            return true;
        }

        return false;
    }

    /**
     * Construir URL de la API
     */
    private function buildApiUrl(string $documentType, string $documentNumber): string
    {
        $searchType = 'completa';
        
        if ($documentType === 'dni') {
            return "{$this->apiBaseUrl}/persona/{$documentNumber}/{$this->apiPassword}/{$searchType}";
        }
        
        return "{$this->apiBaseUrl}/empresa/{$documentNumber}/{$this->apiPassword}/{$searchType}";
    }

    /**
     * Realizar petición a la API
     */
    private function makeApiRequest(string $url): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Consulta Datos',
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => 400,
            CURLOPT_FAILONERROR => true,
        ]);

        $data = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            Log::warning('Error en petición cURL a API de documentos', [
                'url' => $url,
                'error' => $error
            ]);

            return [
                'success' => false,
                'error' => 'Error en Api de Búsqueda',
                'curl_error' => $error
            ];
        }

        return [
            'success' => true,
            'data' => $data
        ];
    }

    /**
     * Procesar respuesta de la API
     */
    private function processApiResponse(string $rawData, string $documentType): array
    {
        $data = json_decode($rawData);

        if (!isset($data->respuesta) || $data->respuesta === 'error') {
            return $this->buildErrorResponse('Error en la respuesta de la API', $rawData);
        }

        // Procesar ubigeo si está disponible
        $ubigeoData = $this->processUbigeo($data, $documentType);

        return [
            'respuesta' => 'ok',
            'encontrado' => true,
            'api' => true,
            'data' => $data,
            'texto_ubigeo' => $ubigeoData['texto_ubigeo'] ?? '',
            'codigo_ubigeo' => $ubigeoData['codigo_ubigeo'] ?? null,
        ];
    }

    /**
     * Procesar datos de ubigeo
     */
    private function processUbigeo(object $data, string $documentType): array
    {
        $textoUbigeo = '';
        $codigoUbigeo = null;

        // Buscar ubigeo por código
        if (isset($data->codigo_ubigeo) && !empty($data->codigo_ubigeo)) {
            $ubigeo = DB::table('ubigeo')->where('ubigeo2', $data->codigo_ubigeo)->first();
            if ($ubigeo) {
                $textoUbigeo = "{$ubigeo->dpto} - {$ubigeo->prov} - {$ubigeo->distrito}";
            }
        }

        // Para DNI, buscar por dirección
        if ($documentType === 'dni' && isset($data->api->result->depaDireccion)) {
            $ubigeo = DB::table('ubigeo')
                ->where('dpto', $data->api->result->depaDireccion)
                ->where('prov', $data->api->result->provDireccion)
                ->where('distrito', $data->api->result->distDireccion)
                ->first();

            if ($ubigeo) {
                $textoUbigeo = "{$ubigeo->departamento} - {$ubigeo->provincia} - {$ubigeo->distrito}";
                $codigoUbigeo = $ubigeo->codigo_ubigeo ?? null;
            }
        }

        return [
            'texto_ubigeo' => $textoUbigeo,
            'codigo_ubigeo' => $codigoUbigeo,
        ];
    }

    /**
     * Construir respuesta de error
     */
    private function buildErrorResponse(string $message, ?string $data = null): array
    {
        $response = [
            'respuesta' => 'error',
            'titulo' => 'Error',
            'encontrado' => false,
            'mensaje' => $message,
        ];

        if ($data !== null) {
            $response['data_resp'] = $data;
        }

        return $response;
    }

    /**
     * Parsear fecha de nacimiento desde diferentes formatos
     */
    private function parseBirthDate(?string $birthDate): ?string
    {
        if (empty($birthDate)) {
            return null;
        }

        try {
            // Intentar formato d/m/Y
            return Carbon::createFromFormat('d/m/Y', $birthDate)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                // Intentar parseo automático
                return Carbon::parse($birthDate)->format('Y-m-d');
            } catch (\Exception $e2) {
                Log::warning('No se pudo parsear fecha de nacimiento', [
                    'fecha' => $birthDate,
                    'error' => $e2->getMessage()
                ]);
                return null;
            }
        }
    }
}

