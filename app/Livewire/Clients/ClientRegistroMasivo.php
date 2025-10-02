<?php

namespace App\Livewire\Clients;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Client;
use App\Services\ClientService;
use App\Traits\SearchDocument;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;

#[Layout('components.layouts.auth')]
class ClientRegistroMasivo extends Component
{
    use SearchDocument;
    
    // Propiedades del formulario
    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('required|string|max:9|min:9')]
    public $phone = '';

    #[Rule('required|in:DNI')]
    public $document_type = 'DNI';

    #[Rule('required|string|max:8|min:8')]
    public $document_number = '';

    #[Rule('nullable|string|max:500')]
    public $address = '';

    #[Rule('nullable|date')]
    public $birth_date = '';

    #[Rule('required|in:inversor,comprador,empresa,constructor')]
    // Mensajes de validación personalizados
    

    public $client_type = 'comprador';

    #[Rule('required|in:redes_sociales,ferias,referidos,formulario_web,publicidad')]
    public $source = 'formulario_web';

    #[Rule('required|in:nuevo,contacto_inicial,en_seguimiento,cierre,perdido')]
    public $status = 'nuevo';

    #[Rule('required|integer|min:0|max:100')]
    public $score = 50;

    #[Rule('nullable|string')]
    public $notes = '';

    // El asesor asignado será automáticamente el usuario autenticado
    public $assigned_advisor_id = null;

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
    ];

    protected $clientService;
    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.max' => 'El nombre no debe exceder los 255 caracteres.',
        'phone.required' => 'El teléfono es obligatorio.',
        'phone.max' => 'El teléfono debe tener como máximo 9 dígitos.',
        'phone.min' => 'El teléfono debe tener al menos 9 dígitos.',
        'document_type.required' => 'El tipo de documento es obligatorio.',
        'document_type.in' => 'El tipo de documento seleccionado no es válido.',
        'document_number.required' => 'El número de documento es obligatorio.',
        'document_number.max' => 'El número de documento debe tener como máximo 8 dígitos.',
        'document_number.min' => 'El número de documento debe tener al menos 8 dígitos.',
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

    public function mount($id = null)
    {
        
        $this->assigned_advisor_id = $id ? $id : Auth::id();
    }

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
                        $birthDate = \Carbon\Carbon::createFromFormat($format, $this->birth_date)->format('Y-m-d');
                        break; // Si funciona, salir del bucle
                    } catch (\Exception $e) {
                        continue; // Intentar el siguiente formato
                    }
                }
                
                // Si ningún formato funcionó, intentar parse automático
                if (!$birthDate) {
                    try {
                        $birthDate = \Carbon\Carbon::parse($this->birth_date)->format('Y-m-d');
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
            'assigned_advisor_id'
        ]);

        // Resetear a valores por defecto
        $this->document_type = 'DNI';
        $this->client_type = 'comprador';
        $this->source = 'formulario_web';
        $this->status = 'nuevo';
        $this->score = 50;
        // Mantener el asesor asignado como el usuario autenticado
        $this->assigned_advisor_id = Auth::id();
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
        $client = client::where('document_number', $num_doc)->where('document_type', $tipo)->first();
        if ($client) {
            $this->showErrorMessage = true;
            $this->errorMessage = 'Cliente ya existe en la base de datos, asesor asignado: '. $client->assignedAdvisor->name;
            return;
        }
        
        if ($tipo == 'dni' and strlen($num_doc) == 8) {
            $result = $this->searchComplete($tipo, $num_doc);
            if ($result['encontrado']) {
                $this->document_type = 'DNI';
                $this->document_number = $num_doc;
                $this->name = $result['data']->nombre;
                // Convertir la fecha de nacimiento del formato DD/MM/YYYY a Y-m-d
                $fecha_nacimiento = $result['data']->fecha_nacimiento;
                try {
                    // Intentar parsear el formato DD/MM/YYYY
                    $this->birth_date = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha_nacimiento)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Si falla, intentar otros formatos comunes
                    try {
                        $this->birth_date = \Carbon\Carbon::parse($fecha_nacimiento)->format('Y-m-d');
                    } catch (\Exception $e2) {
                        // Si todo falla, dejar vacío
                        $this->birth_date = '';
                    }
                }
                $this->showSuccessMessage = true;
                $this->successMessage = 'Cliente encontrado: ' . $this->name;
            } else {
                $this->showErrorMessage = true;
                $this->errorMessage = 'No encontrado';
            }
        }else{
            $this->showErrorMessage = true;
            $this->errorMessage = 'ingrese un número de documento válido';
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
    public function render()
    {
        $url = url('clients/registro-masivo/'.Auth::id());
        $qrcode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)
                            ->color(0, 0, 255)
                            ->margin(2)
                            ->backgroundColor(0, 255, 0)
                            ->generate($url);
        return view('livewire.clients.client-registro-masivo',compact('qrcode'));
    }
}
