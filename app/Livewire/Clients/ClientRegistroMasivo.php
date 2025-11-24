<?php

namespace App\Livewire\Clients;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\ClientService;
use App\Traits\ClientFormTrait;
use App\Traits\SearchDocument;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Componente Livewire para el registro masivo de clientes
 * 
 * @package App\Livewire\Clients
 */
#[Layout('components.layouts.auth')]
class ClientRegistroMasivo extends Component
{
    use SearchDocument, ClientFormTrait;
    
    // Constantes para configuraciones
    private const QR_SIZE = 150;

    protected ClientService $clientService;
    protected ?string $cachedQRCode = null;
    
    // Modal para ver el QR
    public bool $showQRModal = false;

    // Reglas de validación específicas para este componente
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|size:9',
            'document_type' => 'required|in:DNI',
            'document_number' => 'required|string|size:8',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'required|date',
            'client_type' => 'required|in:inversor,comprador,empresa,constructor',
            'source' => 'required|in:redes_sociales,ferias,referidos,formulario_web,publicidad',
            'status' => 'required|in:nuevo,contacto_inicial,en_seguimiento,cierre,perdido',
            'score' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ];
    }

    // Mensajes de validación específicos para este componente
    protected function messages()
    {
        return [
            'phone.size' => 'El teléfono debe tener exactamente 9 dígitos.',
            'document_number.size' => 'El número de documento debe tener exactamente 8 dígitos.',
        ];
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
            $this->handleSuccess("Cliente '{$client->name}' registrado exitosamente.");
        } catch (\Exception $e) {
            $this->handleError($e->getMessage());
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
