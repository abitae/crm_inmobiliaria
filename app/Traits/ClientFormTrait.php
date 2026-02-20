<?php

namespace App\Traits;

use App\Services\ClientService;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

trait ClientFormTrait
{
    use Toast;
    // Constantes comunes
    protected const DEFAULT_SCORE = 50;
    protected const DATE_FORMATS = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d'];

    // Propiedades del formulario (comunes a todos los componentes)
    public string $name = '';
    public string $phone = '';
    public string $document_type = 'DNI';
    public string $document_number = '';
    public string $create_mode = 'dni';
    public ?string $address = null;
    public ?int $city_id = null;
    public ?string $birth_date = null;
    public string $client_type = 'comprador';
    public string $source = 'formulario_web';
    public string $status = 'nuevo';
    public int $score = self::DEFAULT_SCORE;
    public ?string $notes = null;
    public ?int $assigned_advisor_id = null;

    // Propiedades para el estado del formulario
    public bool $showSuccessMessage = false;
    public string $successMessage = '';
    public bool $showErrorMessage = false;
    public string $errorMessage = '';

    // Opciones para los selects (centralizadas)
    public array $documentTypes = [
        'DNI' => 'DNI',
        'RUC' => 'RUC',
        'CE' => 'Carné de Extranjería',
        'PASAPORTE' => 'Pasaporte'
    ];

    public array $clientTypes = [
        'inversor' => 'Inversor',
        'comprador' => 'Comprador',
        'empresa' => 'Empresa',
        'constructor' => 'Constructor'
    ];

    public array $sources = [
        'redes_sociales' => 'Redes Sociales',
        'ferias' => 'Ferias',
        'referidos' => 'Referidos',
        'formulario_web' => 'Formulario Web',
        'publicidad' => 'Publicidad'
    ];

    public array $statuses = [
        'nuevo' => 'Nuevo',
        'contacto_inicial' => 'Contacto Inicial',
        'en_seguimiento' => 'En Seguimiento',
        'cierre' => 'Cierre',
        'perdido' => 'Perdido'
    ];

    /**
     * Reglas de validación centralizadas
     */
    protected function getValidationRules(): array
    {
        $clientId = null;
        if (property_exists($this, 'editingClient') && $this->editingClient) {
            $clientId = $this->editingClient->id;
        }

        return app(ClientService::class)->getValidationRules($clientId);
    }

    /**
     * Mensajes de validación centralizados
     */
    protected function getValidationMessages(): array
    {
        return app(ClientService::class)->getValidationMessages();
    }

    /**
     * Preparar datos del formulario para guardar
     */
    protected function prepareFormData(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'document_type' => $this->document_type,
            'document_number' => $this->document_number,
            'address' => $this->address,
            'city_id' => $this->city_id,
            'birth_date' => $this->parseBirthDate(),
            'client_type' => $this->client_type,
            'source' => $this->source,
            'status' => $this->status,
            'score' => $this->score,
            'notes' => $this->notes,
            'assigned_advisor_id' => $this->assigned_advisor_id,
            'create_mode' => $this->create_mode,
        ];
    }

    /**
     * Resetear formulario a valores por defecto
     */
    public function resetForm(): void
    {
        $this->reset([
            'name',
            'phone',
            'document_type',
            'document_number',
            'address',
            'city_id',
            'birth_date',
            'client_type',
            'source',
            'status',
            'score',
            'notes'
        ]);

        $this->setDefaultValues();
    }

    /**
     * Establecer valores por defecto
     */
    protected function setDefaultValues(): void
    {
        $this->document_type = 'DNI';
        $this->create_mode = 'dni';
        $this->client_type = 'comprador';
        $this->source = 'formulario_web';
        $this->status = 'nuevo';
        $this->score = self::DEFAULT_SCORE;
        $this->assigned_advisor_id = Auth::id();
    }

    /**
     * Llenar formulario desde un cliente existente
     */
    public function fillFormFromClient(Client $client): void
    {
        $this->name = $client->name;
        $this->phone = $client->phone;
        $this->document_type = $client->document_type;
        $this->document_number = $client->document_number;
        $this->create_mode = $client->document_number ? 'dni' : 'phone';
        $this->address = $client->address;
        $this->city_id = $client->city_id;
        $this->birth_date = $client->birth_date ? $client->birth_date->format('Y-m-d') : null;
        $this->client_type = $client->client_type;
        $this->source = $client->source;
        $this->status = $client->status;
        $this->score = $client->score;
        $this->notes = $client->notes;
        $this->assigned_advisor_id = $client->assigned_advisor_id;
    }

    /**
     * Parsear fecha de nacimiento a formato Y-m-d
     */
    protected function parseBirthDate(): ?string
    {
        if (!$this->birth_date) {
            return null;
        }

        foreach (self::DATE_FORMATS as $format) {
            try {
                return Carbon::createFromFormat($format, $this->birth_date)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return Carbon::parse($this->birth_date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parsear fecha de nacimiento desde API
     */
    protected function parseApiBirthDate(?string $fecha_nacimiento): ?string
    {
        if (empty($fecha_nacimiento)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $fecha_nacimiento)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($fecha_nacimiento)->format('Y-m-d');
            } catch (\Exception $e2) {
                return null;
            }
        }
    }

    /**
     * Cerrar mensajes de éxito y error
     */
    public function closeMessages(): void
    {
        $this->showSuccessMessage = false;
        $this->showErrorMessage = false;
    }

    /**
     * Manejar errores de manera consistente (toast Mary al centro)
     */
    protected function handleError(string $message): void
    {
        $this->showErrorMessage = true;
        $this->errorMessage = $message;
        $this->showSuccessMessage = false;
        $this->error(__('Error'), $message, 'toast-top toast-center');
    }

    /**
     * Manejar éxito de manera consistente (toast Mary al centro)
     */
    protected function handleSuccess(string $message): void
    {
        $this->showSuccessMessage = true;
        $this->successMessage = $message;
        $this->showErrorMessage = false;
        $this->success(__('Éxito'), $message, 'toast-top toast-center');
    }

    /**
     * Verificar si el cliente ya existe usando el servicio
     */
    protected function clientExists(ClientService $clientService, string $tipo, string $num_doc): bool
    {
        $client = $clientService->clientExists($tipo, $num_doc);
            
        if ($client) {
            $advisorName = $client->assignedAdvisor ? $client->assignedAdvisor->name : 'Sin asignar';
            $this->handleError('Cliente ya existe en la base de datos, asesor asignado: ' . $advisorName);
            return true;
        }
        
        return false;
    }

    /**
     * Buscar datos del cliente en API externa
     */
    protected function searchClientData(string $tipo, string $num_doc): void
    {
        $result = $this->searchComplete($tipo, $num_doc);
        
        if ($result['encontrado']) {
            $this->fillClientDataFromApi($result['data']);
            $this->handleSuccess('Cliente encontrado: ' . $this->name);
        } else {
            $this->handleError('No encontrado');
        }
    }

    /**
     * Llenar datos del cliente desde API
     */
    protected function fillClientDataFromApi(object $data): void
    {
        $this->document_type = 'DNI';
        $this->name = $data->nombre ?? '';
        
        // Verificar fecha_nacimiento en diferentes formatos posibles
        $fechaNacimiento = null;
        if (isset($data->fecha_nacimiento)) {
            $fechaNacimiento = $data->fecha_nacimiento;
        } elseif (isset($data->fechaNacimiento)) {
            $fechaNacimiento = $data->fechaNacimiento;
        } elseif (isset($data->api->result->fechaNacimiento)) {
            $fechaNacimiento = $data->api->result->fechaNacimiento;
        }
        
        $this->birth_date = $fechaNacimiento ? $this->parseApiBirthDate($fechaNacimiento) : null;
    }

    /**
     * Buscar documento (método común)
     */
    public function buscarDocumento(): void
    {
        $tipo = strtolower($this->document_type);
        $num_doc = $this->document_number;
        
        // Verificar si el cliente ya existe
        if ($this->clientExists(app(ClientService::class), $tipo, $num_doc)) {
            return;
        }
        
        if ($tipo === 'dni' && strlen($num_doc) === 8) {
            $this->searchClientData($tipo, $num_doc);
        } else {
            $this->handleError('Ingrese un número de documento válido');
        }
    }
}
