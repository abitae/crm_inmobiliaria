<?php

namespace App\Livewire\Clients;

use App\Traits\SearchDocument;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Services\ClientService;
use App\Models\Client;
use App\Models\User;

#[Layout('components.layouts.auth')]
class ClientRegistroDatero extends Component
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

    public function boot(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function mount($id)
    {
        $user = User::find($id);
        $user->isDatero();
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
        $url = url('clients/registro-datero/'.$this->assigned_advisor_id);
        $qrcode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)
                            ->color(0, 0, 255)
                            ->margin(2)
                            ->backgroundColor(0, 255, 0)
                            ->generate($url);
        return view('livewire.clients.client-registro-datero',compact('qrcode'));
    }
}
