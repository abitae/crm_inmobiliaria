<?php

namespace App\Livewire\Reservations;

use App\Models\Reservation;
use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        // 'image' removido - solo se sube desde el modal de confirmación
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
            // Cargar solo unidades libres (disponibles) del proyecto seleccionado
            $query = Unit::where('project_id', $this->project_id)
                ->where('status', 'disponible');
            
            // Si estamos editando y la unidad actual pertenece a esta reserva, incluirla aunque esté reservada
            if ($this->editingReservation && $this->editingReservation->unit_id) {
                $query->orWhere('id', $this->editingReservation->unit_id);
            }
            
            // Ordenar primero por manzana y luego por número de unidad
            $this->units = $query->orderBy('unit_manzana')
                ->orderBy('unit_number')
                ->get();
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
            $unitId = $reservation->unit_id;
            $this->units = Unit::where('project_id', $this->project_id)
                ->where(function($q) use ($unitId) {
                    $q->where('status', 'disponible')
                      ->orWhere('id', $unitId);
                })
                ->orderBy('unit_manzana')
                ->orderBy('unit_number')
                ->get();
        }
    }

    public function createReservation()
    {
        $this->validate();

        // Validar que la unidad esté disponible
        $unit = Unit::find($this->unit_id);
        if (!$unit) {
            $this->error('La unidad seleccionada no existe.');
            return;
        }

        if ($unit->status !== 'disponible') {
            $this->error('La unidad seleccionada no está disponible.');
            return;
        }

        try {
            DB::beginTransaction();

            // Al crear, siempre será 'activa' y 'pre_reserva'
            // El estado y estado de pago se fuerzan a los valores por defecto
            // La imagen solo se sube a través del modal de confirmación
            $reservation = Reservation::create([
                'client_id' => $this->client_id,
                'project_id' => $this->project_id,
                'unit_id' => $this->unit_id,
                'advisor_id' => $this->advisor_id,
                'reservation_type' => 'pre_reserva', // Siempre 'pre_reserva' al crear
                'status' => 'activa', // Siempre 'activa' al crear (forzado)
                'payment_status' => 'pendiente', // Siempre 'pendiente' al crear (forzado)
                'reservation_date' => $this->reservation_date,
                'expiration_date' => $this->expiration_date,
                'reservation_amount' => $this->reservation_amount,
                'reservation_percentage' => $this->reservation_percentage,
                'payment_method' => $this->payment_method,
                'payment_reference' => $this->payment_reference,
                'notes' => $this->notes,
                'terms_conditions' => $this->terms_conditions,
                'image' => null, // No se sube imagen al crear
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // NO reservar la unidad al crear (solo cuando se confirma con imagen)
            // La unidad permanece disponible hasta que se confirme la reserva

            DB::commit();

            $this->closeModals();
            $this->success('Reserva creada exitosamente. Para confirmarla, use el botón "Subir imagen de confirmación".');
        } catch (\Exception $e) {
            DB::rollBack();
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
            DB::beginTransaction();

            // Mantener imagen existente (no se puede cambiar desde aquí)
            // La imagen solo se cambia desde el modal de confirmación
            $imagePath = $this->editingReservation->image;

            // Mantener el status actual (no se puede editar)
            // El status solo cambia automáticamente cuando se confirma con imagen, cancela, etc.
            $status = $this->editingReservation->status; // No usar $this->status, mantener el actual

            $updateData = [
                'client_id' => $this->client_id,
                'project_id' => $originalProjectId, // Mantener proyecto original (no editable)
                'unit_id' => $originalUnitId, // Mantener unidad original (no editable)
                'advisor_id' => $this->advisor_id,
                'reservation_type' => $this->reservation_type,
                'status' => $status, // Mantener status actual
                'reservation_date' => $this->reservation_date,
                'expiration_date' => $this->expiration_date,
                'reservation_amount' => $this->reservation_amount,
                'reservation_percentage' => $this->reservation_percentage,
                'payment_method' => $this->payment_method,
                'payment_status' => $this->payment_status,
                'payment_reference' => $this->payment_reference,
                'notes' => $this->notes,
                'terms_conditions' => $this->terms_conditions,
                'image' => $imagePath, // Mantener imagen existente
                'updated_by' => Auth::id(),
            ];

            $this->editingReservation->update($updateData);

            // No se gestiona cambio de unidad porque no se puede cambiar

            DB::commit();

            $this->closeModals();
            $this->success('Reserva actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
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
            'confirmation_reservation_percentage' => 'nullable|numeric|min:0|max:100',
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
            DB::beginTransaction();

            // Procesar imagen
            $imagePath = null;
            if ($this->confirmation_image) {
                // Eliminar imagen anterior si existe
                if ($this->confirmingReservation->image && Storage::disk('public')->exists($this->confirmingReservation->image)) {
                    Storage::disk('public')->delete($this->confirmingReservation->image);
                }
                $imagePath = $this->confirmation_image->store('reservations', 'public');
            }

            // Actualizar la reserva con los datos y la imagen
            $this->confirmingReservation->update([
                'reservation_date' => $this->confirmation_reservation_date,
                'expiration_date' => $this->confirmation_expiration_date,
                'reservation_amount' => $this->confirmation_reservation_amount,
                'reservation_percentage' => $this->confirmation_reservation_percentage,
                'payment_method' => $this->confirmation_payment_method,
                'payment_status' => $this->confirmation_payment_status,
                'payment_reference' => $this->confirmation_payment_reference,
                'image' => $imagePath,
                'status' => 'confirmada', // Cambiar status a confirmada cuando se sube la imagen
                'updated_by' => Auth::id(),
            ]);

            // Actualizar estado de la unidad a 'reservado'
            if ($this->confirmingReservation->unit) {
                $unit = $this->confirmingReservation->unit;
                if ($unit->status !== 'reservado') {
                    $unit->update(['status' => 'reservado']);
                    $unit->project->updateUnitCounts();
                }
            }

            DB::commit();

            $this->closeModals();
            $this->success('Reserva confirmada exitosamente con la imagen del comprobante.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Eliminar imagen si se subió pero falló la actualización
            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
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
            DB::beginTransaction();

            // Actualizar la reserva con la nota de cancelación
            $this->cancelingReservation->update([
                'status' => 'cancelada',
                'notes' => ($this->cancelingReservation->notes ?? '') . "\n\n[Cancelada] " . $this->cancel_note,
                'updated_by' => Auth::id(),
            ]);

            // Actualizar la unidad a 'disponible'
            if ($this->cancelingReservation->unit) {
                $unit = $this->cancelingReservation->unit;
                $unit->update(['status' => 'disponible']);
                $unit->project->updateUnitCounts();
            }

            DB::commit();

            $this->closeModals();
            $this->success('Reserva cancelada exitosamente y unidad liberada.');
        } catch (\Exception $e) {
            DB::rollBack();
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
            'reservations' => $reservations,
            'projects' => $this->projects,
            'clients' => $this->clients,
        ]);
    }
}

