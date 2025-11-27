<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Reservation;
use App\Models\Client;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    use ApiResponse;

    /**
     * Formatear reserva para respuesta API
     */
    protected function formatReservation(Reservation $reservation): array
    {
        return [
            'id' => $reservation->id,
            'reservation_number' => $reservation->reservation_number,
            'client_id' => $reservation->client_id,
            'project_id' => $reservation->project_id,
            'unit_id' => $reservation->unit_id,
            'advisor_id' => $reservation->advisor_id,
            'reservation_type' => $reservation->reservation_type,
            'status' => $reservation->status,
            'reservation_date' => $reservation->reservation_date?->format('Y-m-d'),
            'expiration_date' => $reservation->expiration_date?->format('Y-m-d'),
            'reservation_amount' => (float) $reservation->reservation_amount,
            'reservation_percentage' => (float) $reservation->reservation_percentage,
            'payment_method' => $reservation->payment_method,
            'payment_status' => $reservation->payment_status,
            'payment_reference' => $reservation->payment_reference,
            'notes' => $reservation->notes,
            'terms_conditions' => $reservation->terms_conditions,
            'image' => $reservation->image,
            'image_url' => $reservation->image_url,
            'client_signature' => $reservation->client_signature,
            'advisor_signature' => $reservation->advisor_signature,
            'is_active' => $reservation->is_active,
            'is_confirmed' => $reservation->is_confirmed,
            'is_cancelled' => $reservation->is_cancelled,
            'is_expired' => $reservation->is_expired,
            'is_converted' => $reservation->is_converted,
            'is_expiring_soon' => $reservation->is_expiring_soon,
            'days_until_expiration' => $reservation->days_until_expiration,
            'status_color' => $reservation->status_color,
            'payment_status_color' => $reservation->payment_status_color,
            'formatted_reservation_amount' => $reservation->formatted_reservation_amount,
            'formatted_reservation_percentage' => $reservation->formatted_reservation_percentage,
            'can_be_confirmed' => $reservation->canBeConfirmed(),
            'can_be_cancelled' => $reservation->canBeCancelled(),
            'can_be_converted' => $reservation->canBeConverted(),
            'created_at' => $reservation->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $reservation->updated_at->format('Y-m-d H:i:s'),
            // Relaciones
            'client' => $reservation->relationLoaded('client') ? [
                'id' => $reservation->client->id,
                'name' => $reservation->client->name,
                'phone' => $reservation->client->phone,
            ] : null,
            'project' => $reservation->relationLoaded('project') ? [
                'id' => $reservation->project->id,
                'name' => $reservation->project->name,
            ] : null,
            'unit' => $reservation->relationLoaded('unit') ? [
                'id' => $reservation->unit->id,
                'unit_manzana' => $reservation->unit->unit_manzana,
                'unit_number' => $reservation->unit->unit_number,
                'full_identifier' => $reservation->unit->full_identifier,
            ] : null,
            'advisor' => $reservation->relationLoaded('advisor') ? [
                'id' => $reservation->advisor->id,
                'name' => $reservation->advisor->name,
                'email' => $reservation->advisor->email,
            ] : null,
        ];
    }

    /**
     * Listar reservas del cazador autenticado
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Validar y obtener parámetros de paginación
            $perPage = min(max((int)$request->input('per_page', 15), 1), 100);
            $user = Auth::user();

            // Construir la query base con relaciones necesarias
            $query = Reservation::with([
                'client:id,name,phone',
                'project:id,name',
                'unit:id,project_id,unit_manzana,unit_number',
                'advisor:id,name,email',
            ]);

            // Filtrar por asesor según rol (cazadores normales solo ven sus reservas)
            if (!$user->isAdmin() && !$user->isLider()) {
                $query->where('advisor_id', $user->id);
            }

            // Paginar y ordenar por fecha de creación descendente
            $reservationsPage = $query->orderByDesc('created_at')->paginate($perPage);

            // Formatear reservas
            $formattedReservations = $reservationsPage->map(function ($reservation) {
                return $this->formatReservation($reservation);
            });

            return $this->successResponse([
                'reservations' => $formattedReservations,
                'pagination' => [
                    'current_page' => $reservationsPage->currentPage(),
                    'per_page' => $reservationsPage->perPage(),
                    'total' => $reservationsPage->total(),
                    'last_page' => $reservationsPage->lastPage(),
                    'from' => $reservationsPage->firstItem(),
                    'to' => $reservationsPage->lastItem(),
                ]
            ], 'Reservas obtenidas exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al obtener reservas (Cazador)', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error al obtener las reservas');
        }
    }

    /**
     * Obtener una reserva específica
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            if (!is_numeric($id)) {
                return $this->errorResponse('ID de reserva inválido', null, 400);
            }

            $reservation = Reservation::with([
                'client:id,name,phone,document_type,document_number',
                'project:id,name,address,district,province',
                'unit:id,project_id,unit_manzana,unit_number,area,final_price',
                'advisor:id,name,email,phone',
                'createdBy:id,name,email',
                'updatedBy:id,name,email',
            ])->find($id);

            if (!$reservation) {
                return $this->notFoundResponse('Reserva');
            }

            // Verificar permisos: solo puede ver sus propias reservas (excepto admin/líder)
            $user = Auth::user();
            if (!$user->isAdmin() && !$user->isLider() && $reservation->advisor_id !== $user->id) {
                return $this->forbiddenResponse('No tienes permiso para acceder a esta reserva');
            }

            return $this->successResponse([
                'reservation' => $this->formatReservation($reservation)
            ], 'Reserva obtenida exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener la reserva');
        }
    }

    /**
     * Crear una nueva reserva
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'client_id' => 'required|exists:clients,id',
                'project_id' => 'required|exists:projects,id',
                'unit_id' => 'required|exists:units,id',
                'advisor_id' => 'required|exists:users,id',
                'reservation_date' => 'required|date',
                'expiration_date' => 'nullable|date|after:reservation_date',
                'reservation_amount' => 'required|numeric|min:0',
                'reservation_percentage' => 'nullable|numeric|min:0|max:100',
                'payment_method' => 'nullable|string|max:255',
                'payment_reference' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'terms_conditions' => 'nullable|string',
            ], [
                'client_id.required' => 'El cliente es obligatorio.',
                'client_id.exists' => 'El cliente seleccionado no existe.',
                'project_id.required' => 'El proyecto es obligatorio.',
                'project_id.exists' => 'El proyecto seleccionado no existe.',
                'unit_id.required' => 'La unidad es obligatoria.',
                'unit_id.exists' => 'La unidad seleccionada no existe.',
                'advisor_id.required' => 'El asesor es obligatorio.',
                'advisor_id.exists' => 'El asesor seleccionado no existe.',
                'reservation_date.required' => 'La fecha de reserva es obligatoria.',
                'reservation_date.date' => 'La fecha de reserva debe ser una fecha válida.',
                'expiration_date.date' => 'La fecha de vencimiento debe ser una fecha válida.',
                'expiration_date.after' => 'La fecha de vencimiento debe ser posterior a la fecha de reserva.',
                'reservation_amount.required' => 'El monto de reserva es obligatorio.',
                'reservation_amount.numeric' => 'El monto de reserva debe ser un número.',
                'reservation_amount.min' => 'El monto de reserva debe ser mayor o igual a 0.',
                'reservation_percentage.numeric' => 'El porcentaje debe ser un número.',
                'reservation_percentage.min' => 'El porcentaje debe ser mayor o igual a 0.',
                'reservation_percentage.max' => 'El porcentaje no puede ser mayor a 100.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Validar que la unidad esté disponible
            $unit = Unit::find($request->unit_id);
            if (!$unit) {
                return $this->errorResponse('La unidad seleccionada no existe', null, 404);
            }

            if ($unit->status !== 'disponible') {
                return $this->errorResponse('La unidad seleccionada no está disponible', null, 422);
            }

            // Validar que el proyecto y unidad coincidan
            if ($unit->project_id != $request->project_id) {
                return $this->errorResponse('La unidad no pertenece al proyecto seleccionado', null, 422);
            }

            DB::beginTransaction();

            // Crear reserva con valores forzados
            $reservation = Reservation::create([
                'client_id' => $request->client_id,
                'project_id' => $request->project_id,
                'unit_id' => $request->unit_id,
                'advisor_id' => $request->advisor_id,
                'reservation_type' => 'pre_reserva', // Siempre 'pre_reserva' al crear
                'status' => 'activa', // Siempre 'activa' al crear
                'payment_status' => 'pendiente', // Siempre 'pendiente' al crear
                'reservation_date' => $request->reservation_date,
                'expiration_date' => $request->expiration_date,
                'reservation_amount' => $request->reservation_amount,
                'reservation_percentage' => $request->reservation_percentage ?? 0,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'notes' => $request->notes,
                'terms_conditions' => $request->terms_conditions,
                'image' => null, // No se sube imagen al crear
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // NO reservar la unidad al crear (solo cuando se confirma con imagen)
            // La unidad permanece disponible hasta que se confirme la reserva

            DB::commit();

            // Cargar relaciones para respuesta
            $reservation->load(['client', 'project', 'unit', 'advisor']);

            Log::info('Reserva creada (Cazador)', [
                'user_id' => Auth::id(),
                'reservation_id' => $reservation->id,
                'reservation_number' => $reservation->reservation_number,
            ]);

            return $this->successResponse(
                ['reservation' => $this->formatReservation($reservation)],
                'Reserva creada exitosamente. Para confirmarla, sube la imagen del comprobante de pago.',
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear reserva (Cazador)', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error al crear la reserva');
        }
    }

    /**
     * Actualizar una reserva (solo si está activa)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return $this->errorResponse('ID de reserva inválido', null, 400);
            }

            $reservation = Reservation::find($id);

            if (!$reservation) {
                return $this->notFoundResponse('Reserva');
            }

            // Verificar permisos
            $user = Auth::user();
            if (!$user->isAdmin() && !$user->isLider() && $reservation->advisor_id !== $user->id) {
                return $this->forbiddenResponse('No tienes permiso para actualizar esta reserva');
            }

            // Solo se puede editar si está activa
            if ($reservation->status !== 'activa') {
                return $this->errorResponse('Solo se pueden editar reservas con estado activa', null, 422);
            }

            $validator = Validator::make($request->all(), [
                'client_id' => 'sometimes|required|exists:clients,id',
                'advisor_id' => 'sometimes|required|exists:users,id',
                'reservation_type' => 'sometimes|required|in:pre_reserva,reserva_firmada,reserva_confirmada',
                'reservation_date' => 'sometimes|required|date',
                'expiration_date' => 'nullable|date|after:reservation_date',
                'reservation_amount' => 'sometimes|required|numeric|min:0',
                'reservation_percentage' => 'nullable|numeric|min:0|max:100',
                'payment_method' => 'nullable|string|max:255',
                'payment_status' => 'sometimes|required|in:pendiente,pagado,parcial',
                'payment_reference' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'terms_conditions' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            DB::beginTransaction();

            // Proyecto y unidad NO se pueden editar (mantener valores originales)
            $updateData = $request->only([
                'client_id',
                'advisor_id',
                'reservation_type',
                'reservation_date',
                'expiration_date',
                'reservation_amount',
                'reservation_percentage',
                'payment_method',
                'payment_status',
                'payment_reference',
                'notes',
                'terms_conditions',
            ]);

            $updateData['updated_by'] = Auth::id();

            $reservation->update($updateData);

            DB::commit();

            // Cargar relaciones para respuesta
            $reservation->load(['client', 'project', 'unit', 'advisor']);

            return $this->successResponse(
                ['reservation' => $this->formatReservation($reservation)],
                'Reserva actualizada exitosamente'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->serverErrorResponse($e, 'Error al actualizar la reserva');
        }
    }

    /**
     * Confirmar reserva (subir imagen de comprobante)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return $this->errorResponse('ID de reserva inválido', null, 400);
            }

            $reservation = Reservation::find($id);

            if (!$reservation) {
                return $this->notFoundResponse('Reserva');
            }

            // Verificar permisos
            $user = Auth::user();
            if (!$user->isAdmin() && !$user->isLider() && $reservation->advisor_id !== $user->id) {
                return $this->forbiddenResponse('No tienes permiso para confirmar esta reserva');
            }

            // Solo se puede confirmar si está activa
            if ($reservation->status !== 'activa') {
                return $this->errorResponse('Solo se pueden confirmar reservas con estado activa', null, 422);
            }

            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
                'reservation_date' => 'sometimes|required|date',
                'expiration_date' => 'nullable|date|after:reservation_date',
                'reservation_amount' => 'sometimes|required|numeric|min:0',
                'reservation_percentage' => 'nullable|numeric|min:0|max:100',
                'payment_method' => 'nullable|string|max:255',
                'payment_status' => 'sometimes|required|in:pendiente,pagado,parcial',
                'payment_reference' => 'nullable|string|max:255',
            ], [
                'image.required' => 'La imagen del comprobante es obligatoria.',
                'image.image' => 'El archivo debe ser una imagen.',
                'image.mimes' => 'La imagen debe ser jpeg, png, jpg, gif o webp.',
                'image.max' => 'La imagen no puede ser mayor a 10MB.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            DB::beginTransaction();

            // Procesar imagen
            $imagePath = null;
            if ($request->hasFile('image')) {
                // Eliminar imagen anterior si existe
                if ($reservation->image && Storage::disk('public')->exists($reservation->image)) {
                    Storage::disk('public')->delete($reservation->image);
                }
                
                $imagePath = $request->file('image')->store('reservations', 'public');
            }

            // Actualizar reserva con imagen y datos
            $updateData = [
                'image' => $imagePath,
                'status' => 'confirmada', // Cambiar a confirmada cuando se sube imagen
                'updated_by' => Auth::id(),
            ];

            // Actualizar campos opcionales si se proporcionan
            if ($request->has('reservation_date')) {
                $updateData['reservation_date'] = $request->reservation_date;
            }
            if ($request->has('expiration_date')) {
                $updateData['expiration_date'] = $request->expiration_date;
            }
            if ($request->has('reservation_amount')) {
                $updateData['reservation_amount'] = $request->reservation_amount;
            }
            if ($request->has('reservation_percentage')) {
                $updateData['reservation_percentage'] = $request->reservation_percentage;
            }
            if ($request->has('payment_method')) {
                $updateData['payment_method'] = $request->payment_method;
            }
            if ($request->has('payment_status')) {
                $updateData['payment_status'] = $request->payment_status;
            }
            if ($request->has('payment_reference')) {
                $updateData['payment_reference'] = $request->payment_reference;
            }

            $reservation->update($updateData);

            // Actualizar estado de la unidad a 'reservado'
            if ($reservation->unit && $reservation->unit->status !== 'reservado') {
                $reservation->unit->update(['status' => 'reservado']);
                $reservation->unit->project->updateUnitCounts();
            }

            DB::commit();

            // Cargar relaciones para respuesta
            $reservation->load(['client', 'project', 'unit', 'advisor']);

            Log::info('Reserva confirmada (Cazador)', [
                'user_id' => Auth::id(),
                'reservation_id' => $reservation->id,
                'reservation_number' => $reservation->reservation_number,
            ]);

            return $this->successResponse(
                ['reservation' => $this->formatReservation($reservation)],
                'Reserva confirmada exitosamente. La unidad ha sido marcada como reservada.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al confirmar reserva (Cazador)', [
                'user_id' => Auth::id(),
                'reservation_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error al confirmar la reserva');
        }
    }

    /**
     * Cancelar una reserva
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return $this->errorResponse('ID de reserva inválido', null, 400);
            }

            $reservation = Reservation::find($id);

            if (!$reservation) {
                return $this->notFoundResponse('Reserva');
            }

            // Verificar permisos
            $user = Auth::user();
            if (!$user->isAdmin() && !$user->isLider() && $reservation->advisor_id !== $user->id) {
                return $this->forbiddenResponse('No tienes permiso para cancelar esta reserva');
            }

            // Verificar que se puede cancelar
            if (!$reservation->canBeCancelled()) {
                return $this->errorResponse('La reserva no puede ser cancelada en su estado actual', null, 422);
            }

            $validator = Validator::make($request->all(), [
                'cancel_note' => 'required|string|min:10|max:500',
            ], [
                'cancel_note.required' => 'La nota de cancelación es obligatoria.',
                'cancel_note.min' => 'La nota de cancelación debe tener al menos 10 caracteres.',
                'cancel_note.max' => 'La nota de cancelación no puede tener más de 500 caracteres.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            DB::beginTransaction();

            // Cancelar reserva usando el método del modelo
            $cancelNote = "[Cancelada] " . $request->cancel_note;
            $reservation->cancel($cancelNote);
            $reservation->update(['updated_by' => Auth::id()]);

            DB::commit();

            // Cargar relaciones para respuesta
            $reservation->load(['client', 'project', 'unit', 'advisor']);

            Log::info('Reserva cancelada (Cazador)', [
                'user_id' => Auth::id(),
                'reservation_id' => $reservation->id,
                'reservation_number' => $reservation->reservation_number,
            ]);

            return $this->successResponse(
                ['reservation' => $this->formatReservation($reservation)],
                'Reserva cancelada exitosamente. La unidad ha sido liberada.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al cancelar reserva (Cazador)', [
                'user_id' => Auth::id(),
                'reservation_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error al cancelar la reserva');
        }
    }

    /**
     * Convertir reserva a venta
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertToSale(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return $this->errorResponse('ID de reserva inválido', null, 400);
            }

            $reservation = Reservation::with('unit')->find($id);

            if (!$reservation) {
                return $this->notFoundResponse('Reserva');
            }

            // Verificar permisos
            $user = Auth::user();
            if (!$user->isAdmin() && !$user->isLider() && $reservation->advisor_id !== $user->id) {
                return $this->forbiddenResponse('No tienes permiso para convertir esta reserva');
            }

            // Verificar que se puede convertir
            if (!$reservation->canBeConverted()) {
                return $this->errorResponse('Solo se pueden convertir reservas con estado confirmada', null, 422);
            }

            // Verificar que la unidad puede venderse
            if (!$reservation->unit || !$reservation->unit->canBeSold()) {
                return $this->errorResponse('La unidad no puede ser vendida en su estado actual', null, 422);
            }

            // Convertir a venta usando el método del modelo
            $reservation->convertToSale(Auth::id());

            // Cargar relaciones para respuesta
            $reservation->load(['client', 'project', 'unit', 'advisor']);

            Log::info('Reserva convertida a venta (Cazador)', [
                'user_id' => Auth::id(),
                'reservation_id' => $reservation->id,
                'reservation_number' => $reservation->reservation_number,
            ]);

            return $this->successResponse(
                ['reservation' => $this->formatReservation($reservation)],
                'Reserva convertida a venta exitosamente. La unidad ha sido marcada como vendida.'
            );

        } catch (\Exception $e) {
            Log::error('Error al convertir reserva a venta (Cazador)', [
                'user_id' => Auth::id(),
                'reservation_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error al convertir la reserva a venta');
        }
    }
}

