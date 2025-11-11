<?php

namespace App\Livewire\Reservations;

use App\Models\Reservation;
use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReservationList extends Component
{
    use WithPagination;

    // Filtros
    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $advisorFilter = '';
    public $projectFilter = '';
    public $clientFilter = '';
    public $paymentStatusFilter = '';

    // Modales
    public $showFormModal = false;
    public $showDetailModal = false;
    public $editingReservation = null;

    // Campos del formulario
    public $client_id = '';
    public $project_id = '';
    public $unit_id = '';
    public $advisor_id = '';
    public $reservation_type = 'pre_reserva';
    public $status = 'activa';
    public $reservation_date = '';
    public $expiration_date = '';
    public $reservation_amount = 0;
    public $reservation_percentage = 0;
    public $payment_method = '';
    public $payment_status = 'pendiente';
    public $payment_reference = '';
    public $notes = '';
    public $terms_conditions = '';

    public $clients = [];
    public $projects = [];
    public $units = [];
    public $advisors = [];

    protected $rules = [
        'client_id' => 'required|exists:clients,id',
        'project_id' => 'required|exists:projects,id',
        'unit_id' => 'required|exists:units,id',
        'advisor_id' => 'required|exists:users,id',
        'reservation_type' => 'required|in:pre_reserva,reserva_firmada,reserva_confirmada',
        'status' => 'required|in:activa,confirmada,cancelada,vencida,convertida_venta',
        'reservation_date' => 'required|date',
        'expiration_date' => 'nullable|date|after:reservation_date',
        'reservation_amount' => 'required|numeric|min:0',
        'reservation_percentage' => 'nullable|numeric|min:0|max:100',
        'payment_method' => 'nullable|string|max:255',
        'payment_status' => 'required|in:pendiente,pagado,parcial',
        'payment_reference' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'terms_conditions' => 'nullable|string',
    ];

    public function mount()
    {
        $this->advisors = User::getAvailableAdvisors(Auth::user());
        $this->projects = Project::active()->get();
        $this->clients = Client::active()->get();
        $user = Auth::user();
        $this->advisorFilter = ($user->isAdmin() || $user->isLider()) ? '' : $user->id;
        $this->reservation_date = now()->format('Y-m-d');
    }

    public function updatedProjectId()
    {
        if ($this->project_id) {
            $this->units = Unit::where('project_id', $this->project_id)
                ->where('status', 'disponible')
                ->get();
        } else {
            $this->units = [];
        }
        $this->unit_id = '';
    }

    // Métodos para resetear paginación cuando cambian los filtros
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedAdvisorFilter()
    {
        $this->resetPage();
    }

    public function updatedProjectFilter()
    {
        $this->resetPage();
    }

    public function updatedClientFilter()
    {
        $this->resetPage();
    }

    public function updatedPaymentStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'statusFilter',
            'typeFilter',
            'advisorFilter',
            'projectFilter',
            'clientFilter',
            'paymentStatusFilter'
        ]);
        $this->resetPage();
    }

    public function openCreateModal($reservationId = null)
    {
        if ($reservationId) {
            $this->editingReservation = Reservation::with(['client', 'project', 'unit', 'advisor'])->find($reservationId);
            if ($this->editingReservation) {
                $this->fillFormFromReservation($this->editingReservation);
            }
        } else {
            $this->resetForm();
            $this->editingReservation = null;
        }
        $this->showFormModal = true;
    }

    public function openDetailModal($reservationId)
    {
        $this->editingReservation = Reservation::with(['client', 'project', 'unit', 'advisor', 'createdBy', 'updatedBy'])->find($reservationId);
        $this->showDetailModal = true;
    }

    public function closeModals()
    {
        $this->reset(['showFormModal', 'showDetailModal', 'editingReservation']);
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'client_id',
            'project_id',
            'unit_id',
            'advisor_id',
            'reservation_type',
            'status',
            'reservation_date',
            'expiration_date',
            'reservation_amount',
            'reservation_percentage',
            'payment_method',
            'payment_status',
            'payment_reference',
            'notes',
            'terms_conditions'
        ]);
        $this->reservation_date = now()->format('Y-m-d');
        $this->status = 'activa';
        $this->payment_status = 'pendiente';
        $this->reservation_type = 'pre_reserva';
        $this->units = [];
    }

    public function fillFormFromReservation($reservation)
    {
        $this->client_id = $reservation->client_id;
        $this->project_id = $reservation->project_id;
        $this->unit_id = $reservation->unit_id;
        $this->advisor_id = $reservation->advisor_id;
        $this->reservation_type = $reservation->reservation_type;
        $this->status = $reservation->status;
        $this->reservation_date = $reservation->reservation_date ? $reservation->reservation_date->format('Y-m-d') : '';
        $this->expiration_date = $reservation->expiration_date ? $reservation->expiration_date->format('Y-m-d') : '';
        $this->reservation_amount = $reservation->reservation_amount;
        $this->reservation_percentage = $reservation->reservation_percentage;
        $this->payment_method = $reservation->payment_method ?? '';
        $this->payment_status = $reservation->payment_status;
        $this->payment_reference = $reservation->payment_reference ?? '';
        $this->notes = $reservation->notes ?? '';
        $this->terms_conditions = $reservation->terms_conditions ?? '';

        // Cargar unidades del proyecto
        if ($this->project_id) {
            $this->units = Unit::where('project_id', $this->project_id)->get();
        }
    }

    public function createReservation()
    {
        $this->validate();

        $reservation = Reservation::create([
            'client_id' => $this->client_id,
            'project_id' => $this->project_id,
            'unit_id' => $this->unit_id,
            'advisor_id' => $this->advisor_id,
            'reservation_type' => $this->reservation_type,
            'status' => $this->status,
            'reservation_date' => $this->reservation_date,
            'expiration_date' => $this->expiration_date,
            'reservation_amount' => $this->reservation_amount,
            'reservation_percentage' => $this->reservation_percentage,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'payment_reference' => $this->payment_reference,
            'notes' => $this->notes,
            'terms_conditions' => $this->terms_conditions,
            'reservation_number' => $this->generateReservationNumber(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Bloquear la unidad
        if ($reservation->unit) {
            $reservation->unit->update(['status' => 'reservado']);
            $reservation->unit->project->updateUnitCounts();
        }

        $this->closeModals();
        $this->dispatch('show-success', message: 'Reserva creada exitosamente.');
    }

    public function updateReservation()
    {
        $this->validate();

        if (!$this->editingReservation) {
            return;
        }

        $this->editingReservation->update([
            'client_id' => $this->client_id,
            'project_id' => $this->project_id,
            'unit_id' => $this->unit_id,
            'advisor_id' => $this->advisor_id,
            'reservation_type' => $this->reservation_type,
            'status' => $this->status,
            'reservation_date' => $this->reservation_date,
            'expiration_date' => $this->expiration_date,
            'reservation_amount' => $this->reservation_amount,
            'reservation_percentage' => $this->reservation_percentage,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'payment_reference' => $this->payment_reference,
            'notes' => $this->notes,
            'terms_conditions' => $this->terms_conditions,
            'updated_by' => Auth::id(),
        ]);

        $this->closeModals();
        $this->dispatch('show-success', message: 'Reserva actualizada exitosamente.');
    }

    public function confirmReservation($reservationId)
    {
        $reservation = Reservation::find($reservationId);
        if ($reservation && $reservation->confirm()) {
            $this->dispatch('show-success', message: 'Reserva confirmada exitosamente.');
        } else {
            $this->dispatch('show-error', message: 'No se pudo confirmar la reserva.');
        }
    }

    public function cancelReservation($reservationId)
    {
        $reservation = Reservation::find($reservationId);
        if ($reservation && $reservation->cancel()) {
            $this->dispatch('show-success', message: 'Reserva cancelada exitosamente.');
        } else {
            $this->dispatch('show-error', message: 'No se pudo cancelar la reserva.');
        }
    }

    public function convertToSale($reservationId)
    {
        $reservation = Reservation::find($reservationId);
        if ($reservation && $reservation->convertToSale()) {
            $this->dispatch('show-success', message: 'Reserva convertida a venta exitosamente.');
        } else {
            $this->dispatch('show-error', message: 'No se pudo convertir la reserva a venta.');
        }
    }

    private function generateReservationNumber(): string
    {
        $prefix = 'RES';
        $year = now()->format('Y');
        $sequence = str_pad(Reservation::max('id') + 1, 6, '0', STR_PAD_LEFT);
        return "{$prefix}-{$year}-{$sequence}";
    }

    public function render()
    {
        $query = Reservation::with(['client', 'project', 'unit', 'advisor'])
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($this->search) {
            $query->where(function($q) {
                $q->where('reservation_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('client', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('project', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->statusFilter) {
            $query->byStatus($this->statusFilter);
        }

        if ($this->typeFilter) {
            $query->byType($this->typeFilter);
        }

        if ($this->advisorFilter) {
            $query->byAdvisor($this->advisorFilter);
        }

        if ($this->projectFilter) {
            $query->byProject($this->projectFilter);
        }

        if ($this->clientFilter) {
            $query->byClient($this->clientFilter);
        }

        if ($this->paymentStatusFilter) {
            $query->byPaymentStatus($this->paymentStatusFilter);
        }

        $reservations = $query->paginate(15);

        return view('livewire.reservations.reservation-list', [
            'reservations' => $reservations
        ]);
    }
}

