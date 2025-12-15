<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DateroController extends Controller
{
    use ApiResponse;

    /**
     * Registrar un nuevo usuario con rol Datero desde la app Cazador.
     *
     * El lider_id se tomará siempre del usuario autenticado (cazador / líder / admin).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            // Verificar autenticación y permisos
            if ($error = $this->checkAuthAndPermissions()) {
                return $error;
            }

            $currentUser = auth()->user();

            // Validar datos de entrada
            $validator = Validator::make($request->all(), $this->getRegisterValidationRules(), $this->getValidationMessages());

            if ($validator->fails()) {
                Log::warning('Validación fallida al registrar datero (API Cazador)', [
                    'user_id' => $currentUser->id,
                    'errors' => $validator->errors()->toArray(),
                    'ip' => $request->ip(),
                ]);
                return $this->validationErrorResponse($validator->errors());
            }

            // Crear el usuario Datero
            $user = $this->createDatero($request, $currentUser);

            // Log de registro exitoso
            Log::info('Datero registrado desde API Cazador', [
                'user_id' => $user->id,
                'dni' => $user->dni,
                'email' => $user->email,
                'lider_id' => $user->lider_id,
                'created_by' => $currentUser->id,
                'created_by_email' => $currentUser->email,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse([
                'user' => $this->formatUserResponse($user, $currentUser),
            ], 'Datero registrado exitosamente.', 201);

        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al registrar el datero', $request);
        }
    }

    /**
     * Listar dateros del cazador/líder autenticado
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Verificar autenticación y permisos
            if ($error = $this->checkAuthAndPermissions()) {
                return $error;
            }

            $currentUser = auth()->user();

            // Obtener parámetros de paginación y filtros
            $perPage = $this->getPerPage($request);
            $search = trim((string) $request->get('search', ''));
            $isActive = $this->getIsActiveFilter($request);

            // Construir query
            $query = User::bySingleRole('datero')
                ->where('lider_id', $currentUser->id);

            // Aplicar filtros
            if ($isActive !== null) {
                $query->byActiveStatus($isActive);
            }

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('dni', 'like', '%' . $search . '%');
                });
            }

            // Paginar resultados
            $dateros = $query->orderBy('name')->paginate($perPage);

            // Formatear respuesta
            $data = $dateros->map(function (User $user) use ($currentUser) {
                return $this->formatUserResponse($user, $currentUser);
            });

            Log::info('Dateros listados desde API Cazador', [
                'user_id' => $currentUser->id,
                'total' => $dateros->total(),
                'per_page' => $perPage,
                'search' => $search ?: null,
                'is_active' => $isActive,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse([
                'dateros' => $data,
                'pagination' => $this->formatPagination($dateros),
            ], 'Dateros obtenidos exitosamente');

        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al obtener los dateros', $request);
        }
    }

    /**
     * Ver detalle de un datero del cazador/líder autenticado
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            // Verificar autenticación y permisos
            if ($error = $this->checkAuthAndPermissions()) {
                return $error;
            }

            $currentUser = auth()->user();

            // Buscar datero y verificar propiedad
            $user = $this->findDateroAndCheckOwnership($id, $currentUser);
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
            }

            Log::info('Datero obtenido desde API Cazador', [
                'datero_id' => $id,
                'datero_name' => $user->name,
                'datero_email' => $user->email,
                'user_id' => $currentUser->id,
                'ip' => request()->ip(),
            ]);

            return $this->successResponse([
                'user' => $this->formatUserResponse($user, $currentUser),
            ], 'Datero obtenido exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al obtener datero desde API Cazador', [
                'datero_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return $this->serverErrorResponse($e, 'Error al obtener el datero');
        }
    }

    /**
     * Actualizar datos de un datero del cazador/líder autenticado
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        try {
            // Verificar autenticación y permisos
            if ($error = $this->checkAuthAndPermissions()) {
                return $error;
            }

            $currentUser = auth()->user();

            // Buscar datero y verificar propiedad
            $user = $this->findDateroAndCheckOwnership($id, $currentUser);
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
            }

            // Validar datos
            $validator = Validator::make($request->all(), $this->getUpdateValidationRules($user), $this->getValidationMessages());

            if ($validator->fails()) {
                Log::warning('Validación fallida al actualizar datero (API Cazador)', [
                    'datero_id' => $id,
                    'user_id' => $currentUser->id,
                    'errors' => $validator->errors()->toArray(),
                    'ip' => $request->ip(),
                ]);
                return $this->validationErrorResponse($validator->errors());
            }

            // Guardar datos originales para el log
            $originalData = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'dni' => $user->dni,
                'ocupacion' => $user->ocupacion,
                'is_active' => $user->is_active,
            ];

            // Actualizar usuario
            $this->updateDatero($user, $request);

            // Recargar usuario actualizado
            $user->refresh();

            Log::info('Datero actualizado desde API Cazador', [
                'datero_id' => $id,
                'datero_name' => $user->name,
                'datero_email' => $user->email,
                'updated_by' => $currentUser->id,
                'updated_by_email' => $currentUser->email,
                'original_data' => $originalData,
                'updated_fields' => array_keys($request->only([
                    'name', 'email', 'phone', 'dni', 'ocupacion', 
                    'banco', 'cuenta_bancaria', 'cci_bancaria', 'is_active', 'pin'
                ])),
                'ip' => $request->ip(),
            ]);

            return $this->successResponse([
                'user' => $this->formatUserResponse($user->fresh(), $currentUser),
            ], 'Datero actualizado exitosamente');

        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al actualizar el datero', $request, ['datero_id' => $id]);
        }
    }

    /**
     * Verificar autenticación y permisos del usuario actual
     *
     * @return \Illuminate\Http\JsonResponse|null
     */
    protected function checkAuthAndPermissions(): ?\Illuminate\Http\JsonResponse
    {
        $currentUser = auth()->user();

        if (!$currentUser) {
            Log::warning('Intento de acceso sin autenticación (API Cazador - Datero)', [
                'ip' => request()->ip(),
            ]);
            return $this->unauthorizedResponse('Usuario no autenticado');
        }

        if (!$currentUser->canAccessCazadorApi()) {
            Log::warning('Intento de acceso sin permisos (API Cazador - Datero)', [
                'user_id' => $currentUser->id,
                'user_email' => $currentUser->email,
                'role' => $currentUser->getRoleName(),
                'ip' => request()->ip(),
            ]);
            return $this->forbiddenResponse('No tienes permiso para realizar esta acción.');
        }

        return null;
    }

    /**
     * Buscar datero y verificar que pertenezca al usuario autenticado
     *
     * @param  int  $id
     * @param  User  $currentUser
     * @return User|\Illuminate\Http\JsonResponse
     */
    protected function findDateroAndCheckOwnership(int $id, User $currentUser)
    {
        $user = User::bySingleRole('datero')->find($id);

        if (!$user) {
            Log::warning('Intento de acceso a datero inexistente (API Cazador)', [
                'datero_id' => $id,
                'user_id' => $currentUser->id,
                'ip' => request()->ip(),
            ]);
            return $this->notFoundResponse('Datero');
        }

        if ($user->lider_id !== $currentUser->id) {
            Log::warning('Intento de acceso a datero de otro cazador (API Cazador)', [
                'datero_id' => $id,
                'datero_lider_id' => $user->lider_id,
                'user_id' => $currentUser->id,
                'ip' => request()->ip(),
            ]);
            return $this->forbiddenResponse('No tienes permiso para acceder a este datero.');
        }

        return $user;
    }

    /**
     * Crear un nuevo usuario datero
     *
     * @param  Request  $request
     * @param  User  $currentUser
     * @return User
     */
    protected function createDatero(Request $request, User $currentUser): User
    {
        $data = $this->sanitizeUserData($request->all());
        $pin = $request->input('pin');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'dni' => $data['dni'],
            'pin' => Hash::make($pin),
            'password' => Hash::make($pin), // Compatibilidad
            'lider_id' => $currentUser->id,
            'ocupacion' => $data['ocupacion'] ?? null,
            'banco' => $data['banco'] ?? null,
            'cuenta_bancaria' => $data['cuenta_bancaria'] ?? null,
            'cci_bancaria' => $data['cci_bancaria'] ?? null,
            'is_active' => true,
        ]);

        // Asignar rol datero
        $user->setRole('datero');

        return $user;
    }

    /**
     * Actualizar datos de un datero
     *
     * @param  User  $user
     * @param  Request  $request
     * @return void
     */
    protected function updateDatero(User $user, Request $request): void
    {
        $data = $request->only([
            'name',
            'email',
            'phone',
            'dni',
            'ocupacion',
            'banco',
            'cuenta_bancaria',
            'cci_bancaria',
            'is_active',
        ]);

        // Sanitizar datos proporcionados
        if ($request->filled('name')) {
            $data['name'] = trim($request->input('name'));
        }

        if ($request->filled('email')) {
            $data['email'] = strtolower(trim($request->input('email')));
        }

        if ($request->filled('dni')) {
            $data['dni'] = trim($request->input('dni'));
        }

        if ($request->filled('phone')) {
            $data['phone'] = trim($request->input('phone'));
        }

        if ($request->filled('ocupacion')) {
            $data['ocupacion'] = trim($request->input('ocupacion'));
        }

        // Actualizar PIN si se proporciona
        if ($request->filled('pin')) {
            $pin = $request->input('pin');
            $data['pin'] = Hash::make($pin);
            $data['password'] = Hash::make($pin);
        }

        $user->update($data);
    }

    /**
     * Formatear respuesta del usuario para API
     *
     * @param  User  $user
     * @param  User  $currentUser
     * @return array
     */
    protected function formatUserResponse(User $user, User $currentUser): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'dni' => $user->dni,
            'ocupacion' => $user->ocupacion,
            'role' => $user->getRoleName(),
            'is_active' => $user->isActive(),
            'banco' => $user->banco,
            'cuenta_bancaria' => $user->cuenta_bancaria,
            'cci_bancaria' => $user->cci_bancaria,
            'lider' => [
                'id' => $currentUser->id,
                'name' => $currentUser->name,
                'email' => $currentUser->email,
            ],
        ];
    }

    /**
     * Formatear información de paginación
     *
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator  $paginator
     * @return array
     */
    protected function formatPagination($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    /**
     * Sanitizar datos del usuario
     *
     * @param  array  $data
     * @return array
     */
    protected function sanitizeUserData(array $data): array
    {
        return [
            'name' => trim($data['name'] ?? ''),
            'email' => strtolower(trim($data['email'] ?? '')),
            'phone' => trim($data['phone'] ?? ''),
            'dni' => trim($data['dni'] ?? ''),
            'ocupacion' => isset($data['ocupacion']) ? trim($data['ocupacion']) : null,
            'banco' => $data['banco'] ?? null,
            'cuenta_bancaria' => $data['cuenta_bancaria'] ?? null,
            'cci_bancaria' => $data['cci_bancaria'] ?? null,
        ];
    }

    /**
     * Obtener reglas de validación para registro
     *
     * @return array
     */
    protected function getRegisterValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'dni' => 'required|string|size:8|regex:/^[0-9]{8}$/|unique:users,dni',
            'pin' => 'required|string|size:6|regex:/^[0-9]{6}$/',
            'ocupacion' => 'nullable|string|max:255',
            'banco' => 'nullable|string|max:255',
            'cuenta_bancaria' => 'nullable|string|max:255',
            'cci_bancaria' => 'nullable|string|max:255',
        ];
    }

    /**
     * Obtener reglas de validación para actualización
     *
     * @param  User  $user
     * @return array
     */
    protected function getUpdateValidationRules(User $user): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => 'sometimes|required|string|max:20',
            'dni' => [
                'sometimes',
                'required',
                'size:8',
                'regex:/^[0-9]{8}$/',
                Rule::unique('users', 'dni')->ignore($user->id),
            ],
            'pin' => 'sometimes|required|string|size:6|regex:/^[0-9]{6}$/',
            'ocupacion' => 'nullable|string|max:255',
            'banco' => 'nullable|string|max:255',
            'cuenta_bancaria' => 'nullable|string|max:255',
            'cci_bancaria' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Obtener mensajes de validación personalizados
     *
     * @return array
     */
    protected function getValidationMessages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser una dirección válida.',
            'email.unique' => 'Este email ya está registrado.',
            'phone.required' => 'El teléfono es obligatorio.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.size' => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.regex' => 'El DNI debe contener solo números.',
            'dni.unique' => 'Este DNI ya está registrado.',
            'pin.required' => 'El PIN es obligatorio.',
            'pin.size' => 'El PIN debe tener exactamente 6 dígitos.',
            'pin.regex' => 'El PIN debe contener solo números.',
        ];
    }

    /**
     * Obtener valor de per_page validado
     *
     * @param  Request  $request
     * @return int
     */
    protected function getPerPage(Request $request): int
    {
        return min(max((int) $request->get('per_page', 15), 1), 100);
    }

    /**
     * Obtener filtro de is_active
     *
     * @param  Request  $request
     * @return bool|null
     */
    protected function getIsActiveFilter(Request $request): ?bool
    {
        if (!$request->has('is_active')) {
            return null;
        }

        return filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Manejar excepciones de manera consistente
     *
     * @param  \Exception  $e
     * @param  string  $message
     * @param  Request  $request
     * @param  array  $additionalData
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleException(\Exception $e, string $message, Request $request, array $additionalData = []): \Illuminate\Http\JsonResponse
    {
        $logData = array_merge([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'data' => $request->except(['pin', 'password']),
        ], $additionalData);

        Log::error($message . ' (API Cazador)', $logData);

        return $this->serverErrorResponse($e, $message);
    }
}
