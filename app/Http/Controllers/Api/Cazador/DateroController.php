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
            $currentUser = auth()->user();

            if (!$currentUser) {
                return $this->unauthorizedResponse('Usuario no autenticado');
            }

            // Sólo usuarios que pueden acceder al API de Cazador pueden crear dateros
            if (!$currentUser->canAccessCazadorApi()) {
                return $this->forbiddenResponse('No tienes permiso para registrar dateros.');
            }

            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:20',
                'dni' => 'required|string|size:8|regex:/^[0-9]{8}$/|unique:users,dni',
                'pin' => 'required|string|size:6|regex:/^[0-9]{6}$/',
                'banco' => 'nullable|string|max:255',
                'cuenta_bancaria' => 'nullable|string|max:255',
                'cci_bancaria' => 'nullable|string|max:255',
            ], [
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
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Sanitizar datos
            $dni = trim($request->input('dni'));
            $email = strtolower(trim($request->input('email')));

            // Crear el usuario Datero, usando como lider_id al usuario autenticado
            $user = User::create([
                'name' => trim($request->input('name')),
                'email' => $email,
                'phone' => trim($request->input('phone')),
                'dni' => $dni,
                'pin' => Hash::make($request->input('pin')),
                'password' => Hash::make($request->input('pin')), // También establecer password con el PIN por compatibilidad
                'lider_id' => $currentUser->id,
                'banco' => $request->input('banco'),
                'cuenta_bancaria' => $request->input('cuenta_bancaria'),
                'cci_bancaria' => $request->input('cci_bancaria'),
                'is_active' => true, // Activar automáticamente
            ]);

            // Asignar rol datero
            $user->setRole('datero');

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
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'dni' => $user->dni,
                    'role' => $user->getRoleName(),
                    'is_active' => $user->isActive(),
                    'lider' => [
                        'id' => $currentUser->id,
                        'name' => $currentUser->name,
                        'email' => $currentUser->email,
                    ],
                ],
            ], 'Datero registrado exitosamente.', 201);

        } catch (\Exception $e) {
            Log::error('Error al registrar datero desde API Cazador', [
                'data' => $request->except(['pin', 'password']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return $this->serverErrorResponse($e, 'Error al registrar el datero');
        }
    }

    /**
     * Listar dateros del cazador/líder autenticado
     */
    public function index(Request $request)
    {
        try {
            $currentUser = auth()->user();

            if (!$currentUser) {
                return $this->unauthorizedResponse('Usuario no autenticado');
            }

            if (!$currentUser->canAccessCazadorApi()) {
                return $this->forbiddenResponse('No tienes permiso para listar dateros.');
            }

            $perPage = min(max((int) $request->get('per_page', 15), 1), 100);
            $search = trim((string) $request->get('search', ''));
            $isActive = $request->has('is_active')
                ? filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                : null;

            $query = User::bySingleRole('datero')
                ->where('lider_id', $currentUser->id);

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

            $dateros = $query->orderBy('name')->paginate($perPage);

            $data = $dateros->map(function (User $user) use ($currentUser) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'dni' => $user->dni,
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
            });

            return $this->successResponse([
                'dateros' => $data,
                'pagination' => [
                    'current_page' => $dateros->currentPage(),
                    'per_page' => $dateros->PerPage(),
                    'total' => $dateros->total(),
                    'last_page' => $dateros->lastPage(),
                    'from' => $dateros->firstItem(),
                    'to' => $dateros->lastItem(),
                ],
            ], 'Dateros obtenidos exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al listar dateros desde API Cazador', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return $this->serverErrorResponse($e, 'Error al obtener los dateros');
        }
    }

    /**
     * Ver detalle de un datero del cazador/líder autenticado
     */
    public function show(int $id)
    {
        try {
            $currentUser = auth()->user();

            if (!$currentUser) {
                return $this->unauthorizedResponse('Usuario no autenticado');
            }

            if (!$currentUser->canAccessCazadorApi()) {
                return $this->forbiddenResponse('No tienes permiso para ver dateros.');
            }

            $user = User::bySingleRole('datero')->find($id);

            if (!$user) {
                return $this->notFoundResponse('Datero');
            }

            if ($user->lider_id !== $currentUser->id) {
                return $this->forbiddenResponse('No tienes permiso para acceder a este datero.');
            }

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'dni' => $user->dni,
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
                ],
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
     */
    public function update(Request $request, int $id)
    {
        try {
            $currentUser = auth()->user();

            if (!$currentUser) {
                return $this->unauthorizedResponse('Usuario no autenticado');
            }

            if (!$currentUser->canAccessCazadorApi()) {
                return $this->forbiddenResponse('No tienes permiso para editar dateros.');
            }

            $user = User::bySingleRole('datero')->find($id);

            if (!$user) {
                return $this->notFoundResponse('Datero');
            }

            if ($user->lider_id !== $currentUser->id) {
                return $this->forbiddenResponse('No tienes permiso para editar este datero.');
            }

            $validator = Validator::make($request->all(), [
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
                'banco' => 'nullable|string|max:255',
                'cuenta_bancaria' => 'nullable|string|max:255',
                'cci_bancaria' => 'nullable|string|max:255',
                'is_active' => 'sometimes|boolean',
            ], [
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
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $data = $request->only([
                'name',
                'email',
                'phone',
                'dni',
                'banco',
                'cuenta_bancaria',
                'cci_bancaria',
                'is_active',
            ]);

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

            if ($request->filled('pin')) {
                $pin = $request->input('pin');
                $data['pin'] = Hash::make($pin);
                $data['password'] = Hash::make($pin);
            }

            $user->update($data);

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'dni' => $user->dni,
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
                ],
            ], 'Datero actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar datero desde API Cazador', [
                'datero_id' => $id,
                'data' => $request->except(['pin', 'password']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return $this->serverErrorResponse($e, 'Error al actualizar el datero');
        }
    }

}


