<?php

namespace App\Livewire\Clients;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\ClientService;
use App\Traits\ClientFormTrait;
use App\Traits\SearchDocument;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Componente Livewire para el registro masivo de clientes
 * 
 * @package App\Livewire\Clients
 */
#[Layout('components.layouts.auth.mobile')]
class ClientRegistroMasivo extends Component
{
    use SearchDocument, ClientFormTrait;
    
    // Constantes para configuraciones
    private const QR_SIZE = 150;

    protected ClientService $clientService;
    protected ?string $cachedQRCode = null;
    
    // Modal para ver el QR
    public bool $showQRModal = false;
    public $cities = [];

    // Reglas de validación específicas para este componente
    protected function rules(): array
    {
        $rules = $this->clientService->getValidationRules(null, $this->create_mode);

        if ($this->create_mode === 'dni') {
            $rules['document_type'] = ['required', 'in:DNI'];
            $rules['document_number'] = ['required', 'string', 'size:8', Rule::unique('clients', 'document_number')];
        }

        return $rules;
    }

    // Mensajes de validación específicos para este componente
    protected function messages()
    {
        $messages = $this->clientService->getValidationMessages();
        $messages['document_number.size'] = 'El número de documento debe tener exactamente 8 dígitos.';

        return $messages;
    }

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

    /**
     * Guardar el cliente en la base de datos
     */
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
     * Obtener el código QR (con caché)
     */
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
}
