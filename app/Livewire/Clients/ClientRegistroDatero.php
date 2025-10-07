<?php

namespace App\Livewire\Clients;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Services\ClientService;
use App\Models\Client;
use App\Models\User;
use App\Traits\SearchDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.auth')]
class ClientRegistroDatero extends Component
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
    public $assigned_advisor_id = null;
    public $created_by = null;
    public $updated_by = null;
    // Propiedades para el estado del formulario
    public $showSuccessMessage = false;
    public $successMessage = '';
    public $showErrorMessage = false;
    public $errorMessage = '';

    // Modal para ver el QR
    public $showQRModal = false;

    // Opciones para los selects
    public $documentTypes = [
        'DNI' => 'DNI',
    ];

    public $clientTypes = [
        'inversor' => 'Inversor',
        'comprador' => 'Comprador',
        'empresa' => 'Empresa',
        'constructor' => 'Constructor'
    ];

    public $sources = [
        'redes_sociales' => 'Redes Sociales',
        'ferias' => 'Ferias',
        'referidos' => 'Referidos',
        'formulario_web' => 'Formulario Web',
        'publicidad' => 'Publicidad'
    ];

    public $statuses = [
        'nuevo' => 'Nuevo',
        'contacto_inicial' => 'Contacto Inicial',
        'en_seguimiento' => 'En Seguimiento',
        'cierre' => 'Cierre',
        'perdido' => 'Perdido'
    ];

    // Ya no necesitamos la propiedad advisors

    protected $clientService;

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
    public function boot(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function mount($id)
    {
        $user = User::find($id);
        if (!$user->isDatero()) {
            abort(404);
        }else{
        // Asignar automáticamente el usuario autenticado como asesor
        $this->assigned_advisor_id = $user->id;
        }
    }

    // Ya no necesitamos cargar asesores ya que se asigna automáticamente

    public function save()
    {
        $this->validate();

        try {
            // Convertir la fecha de nacimiento al formato correcto
            $birthDate = null;
            if ($this->birth_date) {
                // Intentar diferentes formatos de fecha
                $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d'];
                $birthDate = null;
                
                foreach ($formats as $format) {
                    try {
                        $birthDate = Carbon::createFromFormat($format, $this->birth_date)->format('Y-m-d');
                        break; // Si funciona, salir del bucle
                    } catch (\Exception $e) {
                        continue; // Intentar el siguiente formato
                    }
                }
                
                // Si ningún formato funcionó, intentar parse automático
                if (!$birthDate) {
                    try {
                        $birthDate = Carbon::parse($this->birth_date)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Si todo falla, usar null
                        $birthDate = null;
                    }
                }
            }

            $data = [
                'name' => $this->name,
                'phone' => $this->phone,
                'document_type' => $this->document_type,
                'document_number' => $this->document_number,
                'address' => $this->address,
                'birth_date' => $birthDate,
                'client_type' => $this->client_type,
                'source' => $this->source,
                'status' => $this->status,
                'score' => $this->score,
                'notes' => $this->notes,
                'assigned_advisor_id' => $this->assigned_advisor_id,
                'created_by' => $this->assigned_advisor_id,
                'updated_by' => $this->assigned_advisor_id,
            ];

            $client = $this->clientService->createClient($data);

            $this->resetForm();
            $this->showSuccessMessage = true;
            $this->successMessage = "Cliente '{$client->name}' registrado exitosamente.";
            $this->showErrorMessage = false;
        } catch (\Exception $e) {
            $this->showErrorMessage = true;
            $this->errorMessage = $e->getMessage();
            $this->showSuccessMessage = false;
        }
    }

    public function resetForm()
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
            'notes',
            'assigned_advisor_id',
            'created_by',
            'updated_by'
        ]);

        // Resetear a valores por defecto
        $this->document_type = 'DNI';
        $this->client_type = 'comprador';
        $this->source = 'formulario_web';
        $this->status = 'nuevo';
        $this->score = 50;
        $this->created_by = $this->assigned_advisor_id;
        $this->updated_by = $this->assigned_advisor_id;
        // Mantener el asesor asignado como el usuario autenticado
    }

    public function closeMessages()
    {
        $this->showSuccessMessage = false;
        $this->showErrorMessage = false;
    }

    public function buscarDocumento()
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

    public function verQR()
    {
        $this->showQRModal = true;
    }

    public function closeQRModal()
    {
        $this->showQRModal = false;
    }
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
    public function render()
    {
        $url = url('clients/registro-datero/'.$this->assigned_advisor_id);
        $qrcode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)
                            ->color(0, 0, 255)
                            ->margin(2)
                            ->backgroundColor(0, 255, 0)
                            ->generate($url);
        return view('livewire.clients.client-registro-datero',compact('qrcode'));
    }
}
