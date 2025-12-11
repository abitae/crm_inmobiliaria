<?php

namespace App\Traits;

use App\Services\ClientService;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait ClientFormTrait
{
    // Constantes comunes
    protected const DEFAULT_SCORE = 50;
    protected const DATE_FORMATS = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d'];

    // Propiedades del formulario (comunes a todos los componentes)
    public string $name = '';
    public string $phone = '';
    public string $document_type = 'DNI';
    public string $document_number = '';
    public ?string $address = null;
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
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|size:9',
            'document_type' => 'required|in:DNI,RUC,CE,PASAPORTE',
            'document_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'required|date',
            'client_type' => 'required|in:inversor,comprador,empresa,constructor',
            'source' => 'required|in:redes_sociales,ferias,referidos,formulario_web,publicidad',
            'status' => 'required|in:nuevo,contacto_inicial,en_seguimiento,cierre,perdido',
            'score' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'assigned_advisor_id' => 'nullable|exists:users,id'
        ];
    }

    /**
     * Mensajes de validación centralizados
     */
    protected function getValidationMessages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.size' => 'El teléfono debe tener exactamente 9 dígitos.',
            'document_type.required' => 'El tipo de documento es obligatorio.',
            'document_type.in' => 'El tipo de documento seleccionado no es válido.',
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.max' => 'El número de documento no puede exceder 20 caracteres.',
            'address.max' => 'La dirección no debe exceder los 500 caracteres.',
            'birth_date.required' => 'La fecha de nacimiento es obligatoria.',
            'birth_date.date' => 'La fecha de nacimiento no es válida.',
            'client_type.required' => 'El tipo de cliente es obligatorio.',
            'client_type.in' => 'El tipo de cliente seleccionado no es válido.',
            'source.required' => 'La fuente es obligatoria.',
            'source.in' => 'La fuente seleccionada no es válida.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            'score.required' => 'El puntaje es obligatorio.',
            'score.integer' => 'El puntaje debe ser un número entero.',
            'score.min' => 'El puntaje no puede ser menor a 0.',
            'score.max' => 'El puntaje no puede ser mayor a 100.',
            'notes.string' => 'Las notas deben ser texto.',
            'assigned_advisor_id.exists' => 'El asesor seleccionado no existe.',
        ];
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
            'birth_date' => $this->parseBirthDate(),
            'client_type' => $this->client_type,
            'source' => $this->source,
            'status' => $this->status,
            'score' => $this->score,
            'notes' => $this->notes,
            'assigned_advisor_id' => $this->assigned_advisor_id,
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
        $this->address = $client->address;
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
     * Manejar errores de manera consistente
     */
    protected function handleError(string $message): void
    {
        $this->showErrorMessage = true;
        $this->errorMessage = $message;
        $this->showSuccessMessage = false;
    }

    /**
     * Manejar éxito de manera consistente
     */
    protected function handleSuccess(string $message): void
    {
        $this->showSuccessMessage = true;
        $this->successMessage = $message;
        $this->showErrorMessage = false;
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
