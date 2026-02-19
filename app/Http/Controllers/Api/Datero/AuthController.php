<?php

namespace App\Http\Controllers\Api\Datero;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Inicio de sesión para usuarios datero usando DNI y PIN
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'dni' => 'required|string|size:8|regex:/^[0-9]{8}$/',
                'pin' => 'required|string|size:6|regex:/^[0-9]{6}$/',
            ], [
                'dni.required' => 'El DNI es obligatorio.',
                'dni.size' => 'El DNI debe tener exactamente 8 dígitos.',
                'dni.regex' => 'El DNI debe contener solo números.',
                'pin.required' => 'El PIN es obligatorio.',
                'pin.size' => 'El PIN debe tener exactamente 6 dígitos.',
                'pin.regex' => 'El PIN debe contener solo números.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Sanitizar DNI
            $dni = trim($request->input('dni'));
            $pin = $request->input('pin');

            // Buscar usuario por DNI
            $user = User::where('dni', $dni)->first();

            if (!$user) {
                Log::warning('Intento de login fallido - DNI no encontrado (Datero)', [
                    'dni' => $dni,
                    'ip' => $request->ip(),
                ]);
                
                return $this->unauthorizedResponse('Credenciales inválidas');
            }

            // Verificar que el usuario tiene rol datero
            if (!$user->isDatero()) {
                Log::warning('Intento de acceso con rol incorrecto (Datero)', [
                    'user_id' => $user->id,
                    'dni' => $user->dni,
                    'role' => $user->getRoleName(),
                    'ip' => $request->ip(),
                ]);
                
                return $this->forbiddenResponse('Acceso denegado. Solo usuarios con rol datero pueden acceder.');
            }

            // Verificar que el usuario esté activo
            if (!$user->isActive()) {
                Log::warning('Intento de acceso con cuenta inactiva (Datero)', [
                    'user_id' => $user->id,
                    'dni' => $user->dni,
                    'ip' => $request->ip(),
                ]);
                
                return $this->forbiddenResponse('Tu cuenta está desactivada. Contacta al administrador.');
            }

            // Verificar PIN
            if (!$user->pin || !Hash::check($pin, $user->pin)) {
                Log::warning('Intento de login fallido - PIN incorrecto (Datero)', [
                    'user_id' => $user->id,
                    'dni' => $dni,
                    'ip' => $request->ip(),
                ]);
                
                return $this->unauthorizedResponse('Credenciales inválidas');
            }

            // Generar token JWT
            $token = JWTAuth::fromUser($user);

            // Log de login exitoso
            Log::info('Login exitoso (Datero)', [
                'user_id' => $user->id,
                'dni' => $user->dni,
                'ip' => $request->ip(),
            ]);

            // Retornar respuesta exitosa con token y datos del usuario
            return $this->successResponse([
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60, // en segundos
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'dni' => $user->dni,
                    'role' => $user->getRoleName(),
                    'is_active' => $user->isActive(),
                ]
            ], 'Inicio de sesión exitoso');

        } catch (JWTException $e) {
            Log::error('Error JWT en login (Datero)', [
                'dni' => $request->input('dni'),
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            
            return $this->errorResponse('Error al generar el token', ['error' => 'No se pudo crear el token'], 500);
        } catch (\Exception $e) {
            Log::error('Error en login (Datero)', [
                'dni' => $request->input('dni'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error en el servidor');
        }
    }

    /**
     * Registro de nuevo usuario datero
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:20',
                'dni' => 'required|string|size:8|regex:/^[0-9]{8}$/|unique:users,dni',
                'pin' => 'required|string|size:6|regex:/^[0-9]{6}$/',
                'lider_id' => 'required|exists:users,id',
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
                'lider_id.required' => 'El cazador/líder asignado es obligatorio.',
                'lider_id.exists' => 'El cazador/líder seleccionado no existe.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Verificar que el lider_id sea un cazador o líder válido
            $lider = User::find($request->lider_id);
            if (!$lider) {
                return $this->errorResponse('El cazador/líder seleccionado no existe', null, 404);
            }

            // Verificar que el lider tenga un rol válido (admin, lider o vendedor/cazador)
            if (!$lider->canAccessCazadorApi()) {
                return $this->errorResponse('El usuario seleccionado no puede ser asignado como cazador/líder. Debe ser Administrador, Líder o Cazador.', null, 422);
            }

            // Sanitizar datos
            $dni = trim($request->input('dni'));
            $email = strtolower(trim($request->input('email')));

            // Crear el usuario
            $user = User::create([
                'name' => trim($request->input('name')),
                'email' => $email,
                'phone' => trim($request->input('phone')),
                'dni' => $dni,
                'pin' => Hash::make($request->input('pin')),
                'password' => Hash::make($request->input('pin')), // También establecer password con el PIN por compatibilidad
                'lider_id' => $request->lider_id,
                'banco' => $request->input('banco'),
                'cuenta_bancaria' => $request->input('cuenta_bancaria'),
                'cci_bancaria' => $request->input('cci_bancaria'),
                'is_active' => true, // Activar automáticamente
            ]);

            // Asignar rol datero
            $user->setRole('datero');

            // Generar token JWT
            $token = JWTAuth::fromUser($user);

            // Log de registro exitoso
            Log::info('Datero registrado exitosamente', [
                'user_id' => $user->id,
                'dni' => $user->dni,
                'email' => $user->email,
                'lider_id' => $user->lider_id,
                'ip' => $request->ip(),
            ]);

            // Retornar respuesta exitosa con token y datos del usuario
            return $this->successResponse([
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60, // en segundos
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'dni' => $user->dni,
                    'role' => $user->getRoleName(),
                    'is_active' => $user->isActive(),
                    'lider' => [
                        'id' => $lider->id,
                        'name' => $lider->name,
                        'email' => $lider->email,
                    ],
                ]
            ], 'Registro exitoso. Bienvenido.', 201);

        } catch (\Exception $e) {
            Log::error('Error al registrar datero', [
                'data' => $request->except(['pin', 'password']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error al registrar el usuario');
        }
    }

    /**
     * Cerrar sesión (invalidar token)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            
            if ($token) {
                JWTAuth::invalidate($token);
            }

            return $this->successResponse(null, 'Sesión cerrada exitosamente');

        } catch (JWTException $e) {
            return $this->errorResponse('Error al cerrar sesión', ['error' => 'No se pudo invalidar el token'], 500);
        }
    }

    /**
     * Obtener el usuario autenticado
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return $this->unauthorizedResponse('Usuario no autenticado');
            }

            return $this->successResponse([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'dni' => $user->dni,
                'role' => $user->getRoleName(),
                'is_active' => $user->isActive(),
            ]);

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener información del usuario');
        }
    }

    /**
     * Refrescar el token JWT
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $newToken = JWTAuth::refresh($token);

            return $this->successResponse([
                'token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ], 'Token renovado exitosamente');

        } catch (JWTException $e) {
            return $this->unauthorizedResponse('Token inválido o expirado');
        }
    }

    /**
     * Cambiar PIN del usuario autenticado
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePin(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return $this->unauthorizedResponse('Usuario no autenticado');
            }

            // Validar datos
            $validator = Validator::make($request->all(), [
                'current_pin' => 'required|string|size:6|regex:/^[0-9]{6}$/',
                'new_pin' => 'required|string|size:6|regex:/^[0-9]{6}$/|confirmed',
            ], [
                'current_pin.required' => 'El PIN actual es obligatorio.',
                'current_pin.size' => 'El PIN actual debe tener exactamente 6 dígitos.',
                'current_pin.regex' => 'El PIN actual debe contener solo números.',
                'new_pin.required' => 'El nuevo PIN es obligatorio.',
                'new_pin.size' => 'El nuevo PIN debe tener exactamente 6 dígitos.',
                'new_pin.regex' => 'El nuevo PIN debe contener solo números.',
                'new_pin.confirmed' => 'La confirmación de PIN no coincide.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Verificar que el usuario tenga un PIN configurado
            if (!$user->pin) {
                return $this->errorResponse('No tienes un PIN configurado. Contacta al administrador.', null, 422);
            }

            // Verificar PIN actual
            if (!Hash::check($request->current_pin, $user->pin)) {
                Log::warning('Intento de cambio de PIN con PIN actual incorrecto (Datero)', [
                    'user_id' => $user->id,
                    'dni' => $user->dni,
                    'ip' => $request->ip(),
                ]);
                
                return $this->errorResponse('El PIN actual es incorrecto', null, 422);
            }

            // Verificar que el nuevo PIN sea diferente al actual
            if (Hash::check($request->new_pin, $user->pin)) {
                return $this->errorResponse('El nuevo PIN debe ser diferente al PIN actual', null, 422);
            }

            // Al cambiar PIN/contraseña se actualizan siempre pin y password (mismo valor) en Datero
            $user->update([
                'pin' => Hash::make($request->new_pin),
                'password' => Hash::make($request->new_pin),
            ]);

            Log::info('PIN/contraseña actualizados exitosamente (Datero)', [
                'user_id' => $user->id,
                'dni' => $user->dni,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse(null, 'PIN actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al cambiar PIN (Datero)', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error al cambiar el PIN');
        }
    }
}

