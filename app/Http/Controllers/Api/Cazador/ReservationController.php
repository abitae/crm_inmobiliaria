<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Reservation;
use App\Models\Unit;
use App\Http\Requests\Cazador\ReservationIndexRequest;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    use ApiResponse;

    protected ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

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
            'client' => ($reservation->relationLoaded('client') && $reservation->client) ? [
                'id' => $reservation->client->id,
                'name' => $reservation->client->name,
                'phone' => $reservation->client->phone,
            ] : null,
            'project' => ($reservation->relationLoaded('project') && $reservation->project) ? [
                'id' => $reservation->project->id,
                'name' => $reservation->project->name,
            ] : null,
            'unit' => ($reservation->relationLoaded('unit') && $reservation->unit) ? [
                'id' => $reservation->unit->id,
                'unit_manzana' => $reservation->unit->unit_manzana,
                'unit_number' => $reservation->unit->unit_number,
                'full_identifier' => $reservation->unit->full_identifier,
            ] : null,
            'advisor' => ($reservation->relationLoaded('advisor') && $reservation->advisor) ? [
                'id' => $reservation->advisor->id,
                'name' => $reservation->advisor->name,
                'email' => $reservation->advisor->email,
            ] : null,
        ];
    }

    /**
     * Listar reservas del cazador autenticado
     * 
     * @param ReservationIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ReservationIndexRequest $request)
    {
        try {
            $filters = $request->validated();
            $perPage = $filters['per_page'] ?? 15;
            $user = Auth::user();

            $includes = $this->parseIncludes($request->get('include'), [
                'client',
                'project',
                'unit',
                'advisor',
            ]);

            $relations = [
                'client:id,name,phone',
                'project:id,name',
                'unit:id,project_id,unit_manzana,unit_number',
                'advisor:id,name,email',
            ];

            if (!empty($includes)) {
                $relations = array_unique(array_merge($relations, $includes));
            }

            // Construir la query base con relaciones necesarias
            $query = Reservation::with($relations);

            // Filtrar por asesor según rol (cazadores normales solo ven sus reservas)
            if (!$user->isAdmin() && !$user->isLider()) {
                $query->where('advisor_id', $user->id);
            } elseif (!empty($filters['advisor_id'])) {
                $query->where('advisor_id', $filters['advisor_id']);
            }

            if (!empty($filters['search'])) {
                $query->search($filters['search']);
            }

            if (!empty($filters['status'])) {
                $query->byStatus($filters['status']);
            }

            if (!empty($filters['payment_status'])) {
                $query->byPaymentStatus($filters['payment_status']);
            }

            if (!empty($filters['project_id'])) {
                $query->byProject($filters['project_id']);
            }

            if (!empty($filters['client_id'])) {
                $query->byClient($filters['client_id']);
            }

            // Paginar y ordenar por fecha de creación descendente
            $reservationsPage = $query->orderByDesc('created_at')->paginate($perPage);

            // Formatear reservas
            $formattedReservations = $reservationsPage->map(function ($reservation) {
                return $this->formatReservation($reservation);
            });

            return $this->successResponse([
                'reservations' => $formattedReservations,
                'pagination' => $this->formatPagination($reservationsPage),
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

            $includes = $this->parseIncludes(request()->get('include'), [
                'client',
                'project',
                'unit',
                'advisor',
                'createdBy',
                'updatedBy',
            ]);

            $relations = [
                'client:id,name,phone,document_type,document_number',
                'project:id,name,address,district,province',
                'unit:id,project_id,unit_manzana,unit_number,area,final_price',
                'advisor:id,name,email,phone',
                'createdBy:id,name,email',
                'updatedBy:id,name,email',
            ];

            if (!empty($includes)) {
                $relations = array_unique(array_merge($relations, $includes));
            }

            $reservation = Reservation::with($relations)->find($id);

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
                'reservation_amount' => 'required|numeric|min:0',
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
                'reservation_amount.required' => 'El monto de reserva es obligatorio.',
                'reservation_amount.numeric' => 'El monto de reserva debe ser un número.',
                'reservation_amount.min' => 'El monto de reserva debe ser mayor o igual a 0.',
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

            $reservation = $this->reservationService->createReservation([
                'client_id' => $request->client_id,
                'project_id' => $request->project_id,
                'unit_id' => $request->unit_id,
                'advisor_id' => Auth::id(),
                'reservation_amount' => $request->reservation_amount,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'notes' => $request->notes,
                'terms_conditions' => $request->terms_conditions,
            ], Auth::id());

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

        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), null, 422);
        } catch (\Exception $e) {
            
            Log::error('Error al crear reserva (Cazador)', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error al crear la reserva');
        }
    }

    /**
     * Crear reservas en batch
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchStore(Request $request)
    {
        $items = $request->input('reservations', []);

        if (!is_array($items) || count($items) === 0) {
            return $this->errorResponse('La lista de reservas es obligatoria', null, 422);
        }

        $created = [];
        $errors = [];

        foreach ($items as $index => $payload) {
            try {
                $validator = Validator::make($payload, [
                    'client_id' => 'required|exists:clients,id',
                    'project_id' => 'required|exists:projects,id',
                    'unit_id' => 'required|exists:units,id',
                    'reservation_amount' => 'required|numeric|min:0',
                    'payment_method' => 'nullable|string|max:255',
                    'payment_reference' => 'nullable|string|max:255',
                    'notes' => 'nullable|string',
                    'terms_conditions' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $errors[] = [
                        'index' => $index,
                        'errors' => $validator->errors()->toArray(),
                    ];
                    continue;
                }

                $unit = Unit::find($payload['unit_id']);
                if (!$unit || $unit->status !== 'disponible') {
                    $errors[] = [
                        'index' => $index,
                        'errors' => ['unit_id' => ['La unidad no esta disponible.']],
                    ];
                    continue;
                }

                if ($unit->project_id != $payload['project_id']) {
                    $errors[] = [
                        'index' => $index,
                        'errors' => ['project_id' => ['La unidad no pertenece al proyecto seleccionado.']],
                    ];
                    continue;
                }

                $reservation = $this->reservationService->createReservation([
                    'client_id' => $payload['client_id'],
                    'project_id' => $payload['project_id'],
                    'unit_id' => $payload['unit_id'],
                    'advisor_id' => Auth::id(),
                    'reservation_amount' => $payload['reservation_amount'],
                    'payment_method' => $payload['payment_method'] ?? null,
                    'payment_reference' => $payload['payment_reference'] ?? null,
                    'notes' => $payload['notes'] ?? null,
                    'terms_conditions' => $payload['terms_conditions'] ?? null,
                ], Auth::id());

                $reservation->load(['client', 'project', 'unit', 'advisor']);
                $created[] = $this->formatReservation($reservation);
            } catch (\InvalidArgumentException $e) {
                $errors[] = [
                    'index' => $index,
                    'errors' => ['reservation' => [$e->getMessage()]],
                ];
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'errors' => ['exception' => [$e->getMessage()]],
                ];
            }
        }

        return $this->successResponse([
            'created' => $created,
            'errors' => $errors,
        ], 'Batch de reservas procesado');
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

            /** @var Reservation|null $reservation */
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
                'payment_method' => 'nullable|string|max:255',
                'payment_status' => 'sometimes|required|in:pendiente,pagado,parcial',
                'payment_reference' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'terms_conditions' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $updateData = [
                'client_id' => $request->input('client_id', $reservation->client_id),
                'advisor_id' => $request->input('advisor_id', $reservation->advisor_id),
                'reservation_type' => $request->input('reservation_type', $reservation->reservation_type),
                'reservation_date' => $request->input(
                    'reservation_date',
                    $reservation->reservation_date?->format('Y-m-d')
                ),
                'expiration_date' => $request->has('expiration_date')
                    ? $request->input('expiration_date')
                    : $reservation->expiration_date?->format('Y-m-d'),
                'reservation_amount' => $request->input('reservation_amount', $reservation->reservation_amount),
                'payment_method' => $request->input('payment_method', $reservation->payment_method),
                'payment_status' => $request->input('payment_status', $reservation->payment_status),
                'payment_reference' => $request->input('payment_reference', $reservation->payment_reference),
                'notes' => $request->input('notes', $reservation->notes),
                'terms_conditions' => $request->input('terms_conditions', $reservation->terms_conditions),
            ];

            $this->reservationService->updateReservation($reservation, $updateData, Auth::id());

            // Cargar relaciones para respuesta
            $reservation->load(['client', 'project', 'unit', 'advisor']);

            return $this->successResponse(
                ['reservation' => $this->formatReservation($reservation)],
                'Reserva actualizada exitosamente'
            );

        } catch (\Exception $e) {
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

            /** @var Reservation|null $reservation */
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

            $updateData = [
                'reservation_date' => $request->input(
                    'reservation_date',
                    $reservation->reservation_date?->format('Y-m-d')
                ),
                'expiration_date' => $request->has('expiration_date')
                    ? $request->input('expiration_date')
                    : $reservation->expiration_date?->format('Y-m-d'),
                'reservation_amount' => $request->input('reservation_amount', $reservation->reservation_amount),
                'payment_method' => $request->input('payment_method', $reservation->payment_method),
                'payment_status' => $request->input('payment_status', $reservation->payment_status),
                'payment_reference' => $request->input('payment_reference', $reservation->payment_reference),
            ];

            $this->reservationService->confirmReservationWithImage(
                $reservation,
                $updateData,
                $request->file('image'),
                Auth::id()
            );

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

            /** @var Reservation|null $reservation */
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

            $this->reservationService->cancelReservation(
                $reservation,
                $request->cancel_note,
                Auth::id()
            );

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

    protected function formatPagination($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }

    protected function parseIncludes(?string $includeParam, array $allowed): array
    {
        if (!$includeParam) {
            return [];
        }

        return collect(explode(',', $includeParam))
            ->map(fn($item) => trim($item))
            ->filter(fn($item) => $item !== '' && in_array($item, $allowed, true))
            ->unique()
            ->values()
            ->all();
    }
}

