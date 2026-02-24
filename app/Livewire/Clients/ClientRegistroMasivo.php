<?php

namespace App\Livewire\Clients;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\Clients\ClientServiceWebCazador;
use App\Traits\SearchDocument;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Mary\Traits\Toast;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Componente Livewire para el registro masivo de clientes
 */
#[Layout('components.layouts.auth.mobile')]
class ClientRegistroMasivo extends Component
{
    use SearchDocument, Toast;

    private const DEFAULT_SCORE = 50;
    private const QR_SIZE = 150;
    private const DATE_FORMATS = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d'];

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

    public bool $showSuccessMessage = false;
    public string $successMessage = '';
    public bool $showErrorMessage = false;
    public string $errorMessage = '';

    public array $documentTypes = [
        'DNI' => 'DNI',
    ];
    public array $clientTypes = [
        'inversor' => 'Inversor',
        'comprador' => 'Comprador',
        'empresa' => 'Empresa',
        'constructor' => 'Constructor',
    ];
    public array $sources = [
        'redes_sociales' => 'Redes Sociales',
        'ferias' => 'Ferias',
        'referidos' => 'Referidos',
        'formulario_web' => 'Formulario Web',
        'publicidad' => 'Publicidad',
    ];
    public array $statuses = [
        'nuevo' => 'Nuevo',
        'contacto_inicial' => 'Contacto Inicial',
        'en_seguimiento' => 'En Seguimiento',
        'cierre' => 'Cierre',
        'perdido' => 'Perdido',
    ];

    protected ClientServiceWebCazador $clientService;
    protected ?string $cachedQRCode = null;

    public bool $showQRModal = false;
    public $cities = [];

    protected function rules(): array
    {
        $rules = $this->clientService->getValidationRules(null, $this->create_mode);

        if ($this->create_mode === 'dni') {
            $rules['document_type'] = ['required', 'in:DNI'];
            $rules['document_number'] = ['required', 'string', 'size:8', Rule::unique('clients', 'document_number')];
        }

        return $rules;
    }

    protected function messages()
    {
        $messages = $this->clientService->getValidationMessages();
        $messages['document_number.size'] = 'El número de documento debe tener exactamente 8 dígitos.';

        return $messages;
    }

    public function boot(ClientServiceWebCazador $clientService): void
    {
        $this->clientService = $clientService;
    }

    public function mount(?int $id = null): void
    {
        $this->assigned_advisor_id = $id ?? Auth::id();
        $this->setDefaultValues();
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

    public function save(): void
    {
        $this->validate();

        try {
            $data = $this->prepareFormData();
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $client = $this->clientService->createClient($data);

            $this->resetForm();
            $this->success(__('Éxito'), "Cliente '{$client->name}' registrado exitosamente.", 'toast-top toast-center');
        } catch (\Exception $e) {
            $this->resetForm();
            $this->error(__('Error'), $e->getMessage(), 'toast-top toast-center');
        }
    }

    public function verQR(): void
    {
        $this->showQRModal = true;
    }

    public function closeQRModal(): void
    {
        $this->showQRModal = false;
    }

    public function render()
    {
        $qrcode = $this->getQRCode();
        return view('livewire.clients.client-registro-masivo', compact('qrcode'));
    }

    private function getQRCode(): string
    {
        if ($this->cachedQRCode === null) {
            $url = url('clients/registro-masivo/' . Auth::id());
            $this->cachedQRCode = QrCode::size(self::QR_SIZE)
                ->color(0, 0, 0)
                ->margin(2)
                ->backgroundColor(255, 255, 255)
                ->generate($url);
        }

        return $this->cachedQRCode;
    }

    protected function setDefaultValues(): void
    {
        $this->document_type = 'DNI';
        $this->create_mode = 'dni';
        $this->client_type = 'comprador';
        $this->source = 'formulario_web';
        $this->status = 'nuevo';
        $this->score = self::DEFAULT_SCORE;
        $this->assigned_advisor_id = $this->assigned_advisor_id ?? Auth::id();
    }

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

    public function resetForm(): void
    {
        $this->reset([
            'name', 'phone', 'document_type', 'document_number', 'address', 'city_id', 'birth_date',
            'client_type', 'source', 'status', 'score', 'notes',
        ]);
        $this->setDefaultValues();
    }

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

    protected function fillClientDataFromApi(object $data): void
    {
        $this->document_type = 'DNI';
        $this->name = $data->nombre ?? '';
        $fechaNacimiento = $data->fecha_nacimiento ?? $data->fechaNacimiento ?? (isset($data->api->result->fechaNacimiento) ? $data->api->result->fechaNacimiento : null);
        $this->birth_date = $fechaNacimiento ? $this->parseApiBirthDate($fechaNacimiento) : null;
    }

    protected function handleError(string $message): void
    {
        $this->showErrorMessage = true;
        $this->errorMessage = $message;
        $this->showSuccessMessage = false;
        $this->error(__('Error'), $message, 'toast-top toast-center');
    }

    protected function handleSuccess(string $message): void
    {
        $this->showSuccessMessage = true;
        $this->successMessage = $message;
        $this->showErrorMessage = false;
        $this->success(__('Éxito'), $message, 'toast-top toast-center');
    }

    protected function clientExists(string $tipo, string $num_doc): bool
    {
        $client = $this->clientService->clientExists($tipo, $num_doc);
        if ($client) {
            $advisorName = $client->assignedAdvisor ? $client->assignedAdvisor->name : 'Sin asignar';
            $this->handleError('Cliente ya existe en la base de datos, asesor asignado: ' . $advisorName);
            return true;
        }
        return false;
    }

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

    public function buscarDocumento(): void
    {
        $tipo = strtolower($this->document_type);
        $num_doc = $this->document_number;

        if ($this->clientExists($tipo, $num_doc)) {
            return;
        }

        if ($tipo === 'dni' && strlen($num_doc) === 8) {
            $this->searchClientData($tipo, $num_doc);
        } else {
            $this->handleError('Ingrese un número de documento válido');
        }
    }
}
