<?php

namespace App\Livewire\Clients;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Services\Clients\ClientServiceWebDatero;
use App\Models\Client;
use App\Models\User;
use App\Traits\SearchDocument;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

#[Layout('components.layouts.auth.mobile')]
class ClientRegistroDatero extends Component
{
    use SearchDocument, Toast;
    // Constantes para configuraciones
    private const DEFAULT_SCORE = 50;
    private const QR_SIZE = 150;
    private const DATE_FORMATS = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d'];

    // Propiedades del formulario
    public string $name = '';
    public string $phone = '';
    public string $document_type = 'DNI';
    public string $document_number = '';
    public string $create_mode = 'dni';
    public ?string $address = null;
    public ?int $city_id = null;
    public ?string $birth_date = null;
    public ?string $ocupacion = null;
    public string $client_type = 'comprador';
    public string $source = 'formulario_web';
    public string $create_type = 'propio';
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
    public $cities = [];

    protected function rules(): array
    {
        $rules = $this->clientService->getValidationRules(null, $this->create_mode);
        if ($this->create_mode === 'dni') {
            $rules['document_type'] = 'required|in:DNI';
            $rules['document_number'] .= '|size:8';
        }
        $rules['ocupacion'] = 'nullable|string|max:255';

        return $rules;
    }

    protected function messages(): array
    {
        $messages = $this->clientService->getValidationMessages();
        $messages['document_number.size'] = 'El número de documento debe tener exactamente 8 dígitos.';

        return $messages;
    }
    public function boot(ClientServiceWebDatero $clientService)
    {
        $this->clientService = $clientService;
    }

    public function mount($id)
    {
        /** @var \App\Models\User|null $user */
        $user = User::find($id);
        if (!$user || !$user->isDatero()) {
            abort(404);
        } else {
            // El servicio establecerá assigned_advisor_id automáticamente basándose en lider_id
            // Inicializar campos de auditoría con el id del datero
            $this->created_by = $user->id;
            $this->updated_by = $user->id;
        }
        $this->cities = City::orderBy('name')->get(['id', 'name']);
    }

    public function updatedCreateMode(): void
    {
        if ($this->create_mode === 'phone') {
            $this->document_type = '';
            $this->document_number = '';
        }

        if ($this->create_mode === 'dni' && !$this->document_type) {
            $this->document_type = 'DNI';
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

            // Construir notas incluyendo ocupación si existe
            $notes = $this->notes;
            if ($this->ocupacion) {
                $ocupacionText = "Ocupación: {$this->ocupacion}";
                $notes = $notes ? "{$notes}\n{$ocupacionText}" : $ocupacionText;
            }

            $data = [
                'name' => $this->name,
                'phone' => $this->phone,
                'document_type' => $this->document_type,
                'document_number' => $this->document_number,
                'address' => $this->address,
                'city_id' => $this->city_id,
                'birth_date' => $birthDate,
                'client_type' => $this->client_type,
                'source' => $this->source,
                'status' => $this->status,
                'score' => $this->score,
                'notes' => $notes,
                'create_mode' => $this->create_mode,
            ];

            // El servicio establecerá assigned_advisor_id, created_by y updated_by automáticamente
            // basándose en el lider_id del datero
            $client = $this->clientService->createClient($data, $this->created_by);

            $this->resetForm();
            $this->success(__('Éxito'), "Cliente '{$client->name}' registrado exitosamente.", 'toast-top toast-center');
        } catch (\Exception $e) {
            $this->resetForm();
            $this->error(__('Error'), $e->getMessage(), 'toast-top toast-center');
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
            'city_id',
            'birth_date',
            'ocupacion',
            'client_type',
            'source',
            'status',
            'score',
            'notes',
        ]);

        // Resetear a valores por defecto (mantener created_by/updated_by del datero en mount)
        $this->document_type = 'DNI';
        $this->create_mode = 'dni';
        $this->client_type = 'comprador';
        $this->source = 'formulario_web';
        $this->status = 'nuevo';
        $this->score = 50;
    }

    public function closeMessages()
    {
        $this->showSuccessMessage = false;
        $this->showErrorMessage = false;
    }

    public function buscarDocumento()
    {
        if ($this->create_mode !== 'dni') {
            return;
        }
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
            $advisorName = $client->assignedAdvisor ? $client->assignedAdvisor->name : 'Sin asignar';
            $this->handleError('Cliente ya existe en la base de datos, asesor asignado: ' . $advisorName);
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
     * Parsear la fecha de nacimiento de la API
     */
    private function parseApiBirthDate(?string $fecha_nacimiento): ?string
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
     * Manejar errores de manera consistente (toast Mary al centro)
     */
    private function handleError(string $message): void
    {
        $this->showErrorMessage = true;
        $this->errorMessage = $message;
        $this->showSuccessMessage = false;
        $this->error(__('Error'), $message, 'toast-top toast-center');
    }
    public function render()
    {
        $url = url('clients/registro-datero/' . $this->assigned_advisor_id);
        $qrcode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)
            ->color(0, 0, 0)
            ->margin(2)
            ->backgroundColor(255, 255, 255)
            ->generate($url);
        return view('livewire.clients.client-registro-datero', compact('qrcode'));
    }
}
