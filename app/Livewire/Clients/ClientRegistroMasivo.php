<?php

namespace App\Livewire\Clients;

use App\Traits\SearchDocument;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

#[Layout('components.layouts.auth')]
class ClientRegistroMasivo extends Component
{
    use SearchDocument;
    // Propiedades del formulario
    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('nullable|string|max:20')]
    public $phone = '';

    #[Rule('required|in:DNI,RUC,CE,PASAPORTE')]
    public $document_type = 'DNI';

    #[Rule('required|string|max:20')]
    public $document_number = '';

    #[Rule('nullable|string|max:500')]
    public $address = '';

    #[Rule('nullable|date')]
    public $birth_date = '';

    #[Rule('required|in:inversor,comprador,empresa,constructor')]

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
    public $qrcode = '';

    // Opciones para los selects
    public $documentTypes = [
        'DNI' => 'DNI',
        'RUC' => 'RUC',
        'CE' => 'Carné de Extranjería',
        'PASAPORTE' => 'Pasaporte'
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

    public function boot(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function mount()
    {
        // Asignar automáticamente el usuario autenticado como asesor
        $this->assigned_advisor_id = Auth::id();
        $this->qrcode = QrCodeGenerator::size(300)->generate('www.nigmacode.com');
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
                //dd($result);
                $this->name = $result['data']->nombre;
                // Asegurar que la fecha esté en formato Y-m-d
                try {
                    $this->birth_date = \Carbon\Carbon::parse($result['data']->fecha_nacimiento)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Si falla el parse, intentar diferentes formatos
                    $fecha = $result['data']->fecha_nacimiento;
                    $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y'];
                    $this->birth_date = null;
                    
                    foreach ($formats as $format) {
                        try {
                            $this->birth_date = \Carbon\Carbon::createFromFormat($format, $fecha)->format('Y-m-d');
                            break;
                        } catch (\Exception $e2) {
                            continue;
                        }
                    }
                }
            } else {
                $this->showErrorMessage = true;
                $this->errorMessage = 'No encontrado';
            }
        }else{
            $this->showErrorMessage = true;
            $this->errorMessage = 'ingrese un número de documento válido';
        }
    }

    public function render()
    {
        return view('livewire.clients.client-registro-masivo');
    }
}
