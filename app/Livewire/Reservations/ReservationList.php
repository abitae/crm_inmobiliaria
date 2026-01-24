<?php

namespace App\Livewire\Reservations;

use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ReservationList extends Component
{
    use WithPagination, WithFileUploads, Toast;

    // Filtros
    public $search = '';
    public $statusFilter = '';
    public $advisorFilter = '';
    public $projectFilter = '';
    public $clientFilter = '';
    public $paymentStatusFilter = '';

    // Modales
    public $showFormModal = false;
    public $showDetailModal = false;
    public $showConfirmationModal = false;
    public $showCancelModal = false;
    public $editingReservation = null;
    public $confirmingReservation = null;
    public $cancelingReservation = null;
    public $cancel_note = '';

    // Campos del formulario
    public $client_id = '';
    public $project_id = '';
    public $unit_id = '';
    public $advisor_id = '';
    public $reservation_type = 'pre_reserva';
    public $status = 'activa'; // Se cambiará automáticamente a 'reservado' si hay imagen
    public $reservation_date = '';
    public $expiration_date = '';
    public $reservation_amount = 0;
    public $reservation_percentage = 0;
    public $payment_method = '';
    public $payment_status = 'pendiente';
    public $payment_reference = '';
    public $notes = '';
    public $terms_conditions = '';
    public $image;
    public $imagePreview;
    
    // Campos para modal de confirmación
    public $confirmation_reservation_date = '';
    public $confirmation_expiration_date = '';
    public $confirmation_reservation_amount = 0;
    public $confirmation_reservation_percentage = 0;
    public $confirmation_payment_method = '';
    public $confirmation_payment_status = 'pendiente';
    public $confirmation_payment_reference = '';
    public $confirmation_image;
    public $confirmation_imagePreview;

    public $clients = [];
    public $projects = [];
    public $units = [];
    public $advisors = [];

    protected ReservationService $reservationService;

    protected function rules(): array
    {
        $baseRules = [
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'required|exists:projects,id',
            'unit_id' => 'required|exists:units,id',
            'advisor_id' => 'required|exists:users,id',
            'reservation_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
        ];

        if ($this->editingReservation) {
            $baseRules['reservation_type'] = 'required|in:pre_reserva,reserva_firmada,reserva_confirmada';
            $baseRules['status'] = 'required|in:activa,confirmada,cancelada,vencida,convertida_venta';
            $baseRules['reservation_date'] = 'required|date';
            $baseRules['expiration_date'] = 'nullable|date|after:reservation_date';
            $baseRules['payment_status'] = 'required|in:pendiente,pagado,parcial';
        }

        return $baseRules;
    }

    public function boot(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function mount()
    {
        $this->advisors = User::getAvailableAdvisors(Auth::user());
        $this->projects = $this->reservationService->getActiveProjects();
        $this->clients = $this->reservationService->getActiveClients();
        $user = Auth::user();
        $this->advisorFilter = ($user->isAdmin() || $user->isLider()) ? '' : $user->id;
        $this->reservation_date = now()->format('Y-m-d');
    }

    public function updatedProjectId()
    {
        if ($this->project_id) {
            $includeUnitId = $this->editingReservation ? $this->editingReservation->unit_id : null;
            $this->units = $this->reservationService->getAvailableUnitsForProject($this->project_id, $includeUnitId);
        } else {
            $this->units = [];
        }
        
        // Solo resetear unit_id si no estamos editando o si cambió el proyecto
        if (!$this->editingReservation || ($this->editingReservation && $this->editingReservation->project_id != $this->project_id)) {
            $this->unit_id = '';
        }
    }

    // Método removido - la imagen solo se sube desde el modal de confirmación

    // Métodos para resetear paginación cuando cambian los filtros
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
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

    public function openConfirmationModal($reservationId)
    {
        $this->confirmingReservation = Reservation::with(['client', 'project', 'unit', 'advisor'])->find($reservationId);
        if ($this->confirmingReservation) {
            $this->confirmation_reservation_date = $this->confirmingReservation->reservation_date ? $this->confirmingReservation->reservation_date->format('Y-m-d') : now()->format('Y-m-d');
            $this->confirmation_expiration_date = $this->confirmingReservation->expiration_date ? $this->confirmingReservation->expiration_date->format('Y-m-d') : '';
            $this->confirmation_reservation_amount = $this->confirmingReservation->reservation_amount ?? 0;
            $this->confirmation_reservation_percentage = $this->confirmingReservation->reservation_percentage ?? 0;
            $this->confirmation_payment_method = $this->confirmingReservation->payment_method ?? '';
            $this->confirmation_payment_status = $this->confirmingReservation->payment_status ?? 'pendiente';
            $this->confirmation_payment_reference = $this->confirmingReservation->payment_reference ?? '';
            $this->confirmation_imagePreview = $this->confirmingReservation->image ? $this->confirmingReservation->image_url : null;
        }
        $this->showConfirmationModal = true;
    }

    public function closeModals()
    {
        $this->reset(['showFormModal', 'showDetailModal', 'showConfirmationModal', 'showCancelModal', 'editingReservation', 'confirmingReservation', 'cancelingReservation']);
        $this->resetForm();
        $this->resetConfirmationForm();
        $this->resetCancelForm();
    }

    public function resetCancelForm()
    {
        $this->reset(['cancel_note']);
    }

    public function openCancelModal($reservationId)
    {
        $this->cancelingReservation = Reservation::with(['client', 'project', 'unit'])->find($reservationId);
        if (!$this->cancelingReservation) {
            $this->error('Reserva no encontrada.');
            return;
        }

        if (!$this->cancelingReservation->canBeCancelled()) {
            $this->error('La reserva no puede ser cancelada en su estado actual.');
            return;
        }

        $this->cancel_note = '';
        $this->showCancelModal = true;
    }

    public function resetConfirmationForm()
    {
        $this->reset([
            'confirmation_reservation_date',
            'confirmation_expiration_date',
            'confirmation_reservation_amount',
            'confirmation_reservation_percentage',
            'confirmation_payment_method',
            'confirmation_payment_status',
            'confirmation_payment_reference',
            'confirmation_image',
            'confirmation_imagePreview'
        ]);
    }

    public function updatedConfirmationImage()
    {
        $this->validateOnly('confirmation_image', [
            'confirmation_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);
        if ($this->confirmation_image) {
            $this->confirmation_imagePreview = $this->confirmation_image->temporaryUrl();
        }
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
            'terms_conditions',
            // 'image' y 'imagePreview' removidos - solo se manejan en modal de confirmación
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
        // imagePreview removido - la imagen solo se muestra en el modal de confirmación

        // Cargar unidades del proyecto (disponibles + la unidad actual si está reservada)
        if ($this->project_id) {
            $this->units = $this->reservationService->getAvailableUnitsForProject(
                $this->project_id,
                $reservation->unit_id
            );
        }
    }

    public function createReservation()
    {
        $this->validate();

        try {
            $this->reservationService->createReservation([
                'client_id' => $this->client_id,
                'project_id' => $this->project_id,
                'unit_id' => $this->unit_id,
                'advisor_id' => $this->advisor_id,
                'reservation_amount' => $this->reservation_amount,
                'payment_method' => $this->payment_method,
                'payment_reference' => $this->payment_reference,
                'notes' => $this->notes,
                'terms_conditions' => $this->terms_conditions,
            ], Auth::id());

            $this->closeModals();
            $this->success('Reserva creada exitosamente. Para confirmarla, use el botón "Subir imagen de confirmación".');
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());
        } catch (\Exception $e) {
            $this->error('Error al crear la reserva: ' . $e->getMessage());
        }
    }

    public function updateReservation()
    {
        $this->validate();

        if (!$this->editingReservation) {
            $this->error('Reserva no encontrada.');
            return;
        }

        // Proyecto y unidad NO se pueden editar
        // Mantener los valores originales de la reserva
        $originalProjectId = $this->editingReservation->project_id;
        $originalUnitId = $this->editingReservation->unit_id;

        try {
            $this->reservationService->updateReservation($this->editingReservation, [
                'client_id' => $this->client_id,
                'project_id' => $originalProjectId,
                'unit_id' => $originalUnitId,
                'advisor_id' => $this->advisor_id,
                'reservation_type' => $this->reservation_type,
                'reservation_date' => $this->reservation_date,
                'expiration_date' => $this->expiration_date,
                'reservation_amount' => $this->reservation_amount,
                'payment_method' => $this->payment_method,
                'payment_status' => $this->payment_status,
                'payment_reference' => $this->payment_reference,
                'notes' => $this->notes,
                'terms_conditions' => $this->terms_conditions,
            ], Auth::id());

            $this->closeModals();
            $this->success('Reserva actualizada exitosamente.');
        } catch (\Exception $e) {
            $this->error('Error al actualizar la reserva: ' . $e->getMessage());
        }
    }

    public function confirmReservation($reservationId)
    {
        $reservation = Reservation::find($reservationId);
        
        if (!$reservation) {
            $this->error('Reserva no encontrada.');
            return;
        }

        if (!$reservation->canBeConfirmed()) {
            $this->error('La reserva no puede ser confirmada. Verifique que esté activa y firmada por ambas partes.');
            return;
        }

        try {
            if ($reservation->confirm()) {
                $this->success('Reserva confirmada exitosamente.');
            } else {
                $this->error('No se pudo confirmar la reserva.');
            }
        } catch (\Exception $e) {
            $this->error('Error al confirmar la reserva: ' . $e->getMessage());
        }
    }

    public function submitConfirmation()
    {
        $this->validate([
            'confirmation_reservation_date' => 'required|date',
            'confirmation_expiration_date' => 'nullable|date|after:confirmation_reservation_date',
            'confirmation_reservation_amount' => 'required|numeric|min:0',
            'confirmation_payment_method' => 'nullable|string|max:255',
            'confirmation_payment_status' => 'required|in:pendiente,pagado,parcial',
            'confirmation_payment_reference' => 'nullable|string|max:255',
            'confirmation_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        if (!$this->confirmingReservation) {
            $this->error('Reserva no encontrada.');
            return;
        }

        try {
            $this->reservationService->confirmReservationWithImage($this->confirmingReservation, [
                'reservation_date' => $this->confirmation_reservation_date,
                'expiration_date' => $this->confirmation_expiration_date,
                'reservation_amount' => $this->confirmation_reservation_amount,
                'payment_method' => $this->confirmation_payment_method,
                'payment_status' => $this->confirmation_payment_status,
                'payment_reference' => $this->confirmation_payment_reference,
            ], $this->confirmation_image, Auth::id());

            $this->closeModals();
            $this->success('Reserva confirmada exitosamente con la imagen del comprobante.');
        } catch (\Exception $e) {
            $this->error('Error al confirmar la reserva: ' . $e->getMessage());
        }
    }

    public function cancelReservation($reservationId)
    {
        // Abrir modal de cancelación en lugar de cancelar directamente
        $this->openCancelModal($reservationId);
    }

    public function submitCancellation()
    {
        $this->validate([
            'cancel_note' => 'required|string|min:10|max:500',
        ], [
            'cancel_note.required' => 'La nota de cancelación es obligatoria.',
            'cancel_note.min' => 'La nota debe tener al menos 10 caracteres.',
            'cancel_note.max' => 'La nota no puede exceder 500 caracteres.',
        ]);

        if (!$this->cancelingReservation) {
            $this->error('Reserva no encontrada.');
            return;
        }

        if (!$this->cancelingReservation->canBeCancelled()) {
            $this->error('La reserva no puede ser cancelada en su estado actual.');
            return;
        }

        try {
            $this->reservationService->cancelReservation($this->cancelingReservation, $this->cancel_note, Auth::id());

            $this->closeModals();
            $this->success('Reserva cancelada exitosamente y unidad liberada.');
        } catch (\Exception $e) {
            $this->error('Error al cancelar la reserva: ' . $e->getMessage());
        }
    }

    public function convertToSale($reservationId)
    {
        $reservation = Reservation::with(['unit', 'client', 'project'])->find($reservationId);
        
        if (!$reservation) {
            $this->error('Reserva no encontrada.');
            return;
        }

        if (!$reservation->canBeConverted()) {
            $this->error('La reserva no puede ser convertida a venta en su estado actual.');
            return;
        }

        // Validar que la unidad pueda venderse
        if ($reservation->unit && !$reservation->unit->canBeSold()) {
            $this->error('La unidad no puede ser vendida en su estado actual.');
            return;
        }

        try {
            if ($reservation->convertToSale(Auth::id())) {
                $this->success('Reserva convertida a venta exitosamente. La unidad ha sido marcada como vendida.');
            } else {
                $this->error('No se pudo convertir la reserva a venta.');
            }
        } catch (\Exception $e) {
            $this->error('Error al convertir la reserva: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $reservations = $this->reservationService->getReservationsPaginated([
            'search' => $this->search,
            'status' => $this->statusFilter,
            'advisor_id' => $this->advisorFilter,
            'project_id' => $this->projectFilter,
            'client_id' => $this->clientFilter,
            'payment_status' => $this->paymentStatusFilter,
        ]);

        return view('livewire.reservations.reservation-list', [
            'reservations' => $reservations,
            'projects' => $this->projects,
            'clients' => $this->clients,
        ]);
    }
}

