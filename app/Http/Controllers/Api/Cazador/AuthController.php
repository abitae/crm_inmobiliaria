<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Inicio de sesión para usuarios cazador (vendedor/asesor)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|size:6|regex:/^[0-9]{6}$/',
            ], [
                'email.required' => 'El email es obligatorio.',
                'email.email' => 'El email debe ser una dirección válida.',
                'password.required' => 'El PIN es obligatorio.',
                'password.size' => 'El PIN debe tener exactamente 6 dígitos.',
                'password.regex' => 'El PIN debe contener solo números.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Sanitizar email y PIN (login por correo y PIN; el PIN se guarda con Hash::make)
            $email = strtolower(trim($request->input('email')));
            $pin = $request->input('password'); // campo "password" en request = PIN de 6 dígitos

            $user = User::where('email', $email)->first();
            if (! $user || ! $user->pin || ! Hash::check($pin, $user->pin)) {
                Log::warning('Intento de login fallido (Cazador)', [
                    'email' => $email,
                    'ip' => $request->ip(),
                ]);
                return $this->unauthorizedResponse('Credenciales inválidas');
            }

            // Generar token JWT
            $token = JWTAuth::fromUser($user);

            // Verificar que el usuario puede acceder al API de Cazador
            // Permite: Administrador, Lider y Cazador (vendedor)
            // NO permite: Dateros
            if (!$user->canAccessCazadorApi()) {
                JWTAuth::invalidate($token);
                Log::warning('Intento de acceso con rol incorrecto (Cazador)', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->getRoleName(),
                    'ip' => $request->ip(),
                ]);
                
                return $this->forbiddenResponse('Acceso denegado. Solo usuarios Administrador, Lider o Cazador pueden acceder.');
            }

            // Verificar que el usuario esté activo
            if (!$user->isActive()) {
                JWTAuth::invalidate($token);
                Log::warning('Intento de acceso con cuenta inactiva (Cazador)', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                ]);
                
                return $this->forbiddenResponse('Tu cuenta está desactivada. Contacta al administrador.');
            }

            // Log de login exitoso
            Log::info('Login exitoso (Cazador)', [
                'user_id' => $user->id,
                'email' => $user->email,
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
                    'role' => $user->getRoleName(),
                    'is_active' => $user->isActive(),
                ]
            ], 'Inicio de sesión exitoso');

        } catch (JWTException $e) {
            Log::error('Error JWT en login (Cazador)', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            
            return $this->errorResponse('Error al generar el token de acceso', ['error' => 'No se pudo crear el token'], 500);
        } catch (\Exception $e) {
            Log::error('Error en login (Cazador)', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error al iniciar sesión');
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
            return $this->errorResponse('Error al cerrar sesión (token inválido)', ['error' => 'No se pudo invalidar el token'], 500);
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
            $user = Auth::user();

            if (!$user) {
                return $this->unauthorizedResponse('Usuario no autenticado');
            }

            return $this->successResponse([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->getRoleName(),
                'is_active' => $user->isActive(),
            ]);

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener el usuario autenticado');
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
            return $this->unauthorizedResponse('Token inválido o expirado (no se pudo refrescar)');
        }
    }

    /**
     * Cambiar contraseña del usuario autenticado
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->unauthorizedResponse('Usuario no autenticado');
            }

            // Validar datos (PIN de 6 dígitos)
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|size:6|regex:/^[0-9]{6}$/|confirmed',
            ], [
                'current_password.required' => 'El PIN actual es obligatorio.',
                'new_password.required' => 'El nuevo PIN es obligatorio.',
                'new_password.size' => 'El nuevo PIN debe tener exactamente 6 dígitos.',
                'new_password.regex' => 'El nuevo PIN debe contener solo números.',
                'new_password.confirmed' => 'La confirmación del PIN no coincide.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Verificar PIN actual
            if (!Hash::check($request->current_password, $user->password)) {
                Log::warning('Intento de cambio de PIN con PIN actual incorrecto (Cazador)', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                ]);
                
                return $this->errorResponse('El PIN actual es incorrecto', null, 422);
            }

            // Verificar que el nuevo PIN sea diferente al actual
            if (Hash::check($request->new_password, $user->password)) {
                return $this->errorResponse('El nuevo PIN debe ser diferente al PIN actual', null, 422);
            }

            // Al cambiar contraseña se actualizan siempre pin y password (mismo valor, ambos con Hash::make)
            $user->update([
                'password' => Hash::make($request->new_password),
                'pin' => Hash::make($request->new_password),
            ]);

            Log::info('PIN/contraseña actualizados exitosamente (Cazador)', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse(null, 'PIN actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al cambiar PIN (Cazador)', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error al actualizar el PIN');
        }
    }
}

