<?php

namespace App\Livewire\Clients;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Client;
use App\Services\ClientService;
use App\Traits\SearchDocument;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Componente Livewire para el registro masivo de clientes
 * 
 * @package App\Livewire\Clients
 */
#[Layout('components.layouts.auth')]
class ClientRegistroMasivo extends Component
{
    use SearchDocument;
    
    // Constantes para configuraciones
    private const DEFAULT_SCORE = 50;
    private const QR_SIZE = 150;
    private const DATE_FORMATS = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d'];
    
    // Propiedades del formulario
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

    // El asesor asignado será automáticamente el usuario autenticado
    public ?int $assigned_advisor_id = null;

    // Propiedades para el estado del formulario
    public bool $showSuccessMessage = false;
    public string $successMessage = '';
    public bool $showErrorMessage = false;
    public string $errorMessage = '';

    // Modal para ver el QR
    public bool $showQRModal = false;

    // Opciones para los selects
    public array $documentTypes = [
        'DNI' => 'DNI',
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

    protected ClientService $clientService;
    protected ?string $cachedQRCode = null;

    // Reglas de validación
    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'required|string|size:9',
        'document_type' => 'required|in:DNI',
        'document_number' => 'required|string|size:8',
        'address' => 'nullable|string|max:500',
        'birth_date' => 'nullable|date',
        'client_type' => 'required|in:inversor,comprador,empresa,constructor',
        'source' => 'required|in:redes_sociales,ferias,referidos,formulario_web,publicidad',
        'status' => 'required|in:nuevo,contacto_inicial,en_seguimiento,cierre,perdido',
        'score' => 'required|integer|min:0|max:100',
        'notes' => 'nullable|string',
    ];

    // Mensajes de validación personalizados
    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.max' => 'El nombre no debe exceder los 255 caracteres.',
        'phone.required' => 'El teléfono es obligatorio.',
        'phone.size' => 'El teléfono debe tener exactamente 9 dígitos.',
        'document_type.required' => 'El tipo de documento es obligatorio.',
        'document_type.in' => 'El tipo de documento seleccionado no es válido.',
        'document_number.required' => 'El número de documento es obligatorio.',
        'document_number.size' => 'El número de documento debe tener exactamente 8 dígitos.',
        'address.max' => 'La dirección no debe exceder los 500 caracteres.',
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
    ];

    /**
     * Inicializar el servicio de cliente
     */
    public function boot(ClientService $clientService): void
    {
        $this->clientService = $clientService;
    }

    /**
     * Montar el componente con el ID del asesor
     */
    public function mount(?int $id = null): void
    {
        $this->assigned_advisor_id = $id ?? Auth::id();
    }

    /**
     * Guardar el cliente en la base de datos
     */
    public function save(): void
    {
        $this->validate();

        try {
            $data = $this->prepareClientData();
            $client = $this->clientService->createClient($data);

            $this->resetForm();
            $this->showSuccessMessage = true;
            $this->successMessage = "Cliente '{$client->name}' registrado exitosamente.";
            $this->showErrorMessage = false;
        } catch (\Exception $e) {
            $this->handleError($e->getMessage());
        }
    }

    /**
     * Resetear el formulario a valores por defecto
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
     * Cerrar los mensajes de éxito y error
     */
    public function closeMessages(): void
    {
        $this->showSuccessMessage = false;
        $this->showErrorMessage = false;
    }

    /**
     * Buscar información del cliente por documento
     */
    public function buscarDocumento(): void
    {
        $tipo = strtolower($this->document_type);
        $num_doc = $this->document_number;
        
        // Verificar si el cliente ya existe
        if ($this->clientExists($tipo, $num_doc)) {
            return;
        }
        
        if ($tipo === 'dni' && strlen($num_doc) === 8) {
            $this->searchClientData($tipo, $num_doc);
        } else {
            $this->handleError('Ingrese un número de documento válido');
        }
    }

    /**
     * Mostrar el modal con el código QR
     */
    public function verQR(): void
    {
        $this->showQRModal = true;
    }

    /**
     * Cerrar el modal del código QR
     */
    public function closeQRModal(): void
    {
        $this->showQRModal = false;
    }
    /**
     * Renderizar el componente
     */
    public function render()
    {
        $qrcode = $this->getQRCode();
        return view('livewire.clients.client-registro-masivo', compact('qrcode'));
    }

    /**
     * Preparar los datos del cliente para guardar
     */
    private function prepareClientData(): array
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
     * Parsear la fecha de nacimiento a formato Y-m-d
     */
    private function parseBirthDate(): ?string
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
     * Verificar si el cliente ya existe en la base de datos
     */
    private function clientExists(string $tipo, string $num_doc): bool
    {
        $client = Client::where('document_number', $num_doc)
            ->where('document_type', $tipo)
            ->first();
            
        if ($client) {
            $this->handleError('Cliente ya existe en la base de datos, asesor asignado: ' . $client->assignedAdvisor->name);
            return true;
        }
        
        return false;
    }

    /**
     * Buscar datos del cliente en la API externa
     */
    private function searchClientData(string $tipo, string $num_doc): void
    {
        $result = $this->searchComplete($tipo, $num_doc);
        
        if ($result['encontrado']) {
            $this->fillClientData($result['data']);
            $this->showSuccessMessage = true;
            $this->successMessage = 'Cliente encontrado: ' . $this->name;
        } else {
            $this->handleError('No encontrado');
        }
    }

    /**
     * Llenar los datos del cliente desde la API
     */
    private function fillClientData(object $data): void
    {
        $this->document_type = 'DNI';
        $this->name = $data->nombre;
        $this->birth_date = $this->parseApiBirthDate($data->fecha_nacimiento);
    }

    /**
     * Parsear la fecha de nacimiento de la API
     */
    private function parseApiBirthDate(string $fecha_nacimiento): string
    {
        try {
            return Carbon::createFromFormat('d/m/Y', $fecha_nacimiento)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($fecha_nacimiento)->format('Y-m-d');
            } catch (\Exception $e2) {
                return '';
            }
        }
    }

    /**
     * Establecer valores por defecto
     */
    private function setDefaultValues(): void
    {
        $this->document_type = 'DNI';
        $this->client_type = 'comprador';
        $this->source = 'formulario_web';
        $this->status = 'nuevo';
        $this->score = self::DEFAULT_SCORE;
        $this->assigned_advisor_id = Auth::id();
    }

    /**
     * Manejar errores de manera consistente
     */
    private function handleError(string $message): void
    {
        $this->showErrorMessage = true;
        $this->errorMessage = $message;
        $this->showSuccessMessage = false;
    }

    /**
     * Obtener el código QR (con caché)
     */
    private function getQRCode(): string
    {
        if ($this->cachedQRCode === null) {
            $url = url('clients/registro-masivo/' . Auth::id());
            $this->cachedQRCode = QrCode::size(self::QR_SIZE)
                ->color(0, 0, 255)
                ->margin(2)
                ->backgroundColor(0, 255, 0)
                ->generate($url);
        }
        
        return $this->cachedQRCode;
    }
}
