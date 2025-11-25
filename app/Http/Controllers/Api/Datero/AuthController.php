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
     * Inicio de sesión para usuarios datero
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
                'password' => 'required|string|min:6',
            ], [
                'email.required' => 'El email es obligatorio.',
                'email.email' => 'El email debe ser una dirección válida.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Sanitizar email
            $email = strtolower(trim($request->input('email')));
            $credentials = [
                'email' => $email,
                'password' => $request->input('password')
            ];

            // Intentar autenticar
            if (!$token = JWTAuth::attempt($credentials)) {
                Log::warning('Intento de login fallido (Datero)', [
                    'email' => $email,
                    'ip' => $request->ip(),
                ]);
                
                return $this->unauthorizedResponse('Credenciales inválidas');
            }

            // Obtener el usuario autenticado
            $user = auth()->user();

            // Verificar que el usuario tiene rol datero
            if (!$user->isDatero()) {
                JWTAuth::invalidate($token);
                Log::warning('Intento de acceso con rol incorrecto (Datero)', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->getRoleName(),
                    'ip' => $request->ip(),
                ]);
                
                return $this->forbiddenResponse('Acceso denegado. Solo usuarios con rol datero pueden acceder.');
            }

            // Verificar que el usuario esté activo
            if (!$user->isActive()) {
                JWTAuth::invalidate($token);
                Log::warning('Intento de acceso con cuenta inactiva (Datero)', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                ]);
                
                return $this->forbiddenResponse('Tu cuenta está desactivada. Contacta al administrador.');
            }

            // Log de login exitoso
            Log::info('Login exitoso (Datero)', [
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
            Log::error('Error JWT en login (Datero)', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            
            return $this->errorResponse('Error al generar el token', ['error' => 'No se pudo crear el token'], 500);
        } catch (\Exception $e) {
            Log::error('Error en login (Datero)', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error en el servidor');
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
     * Cambiar contraseña del usuario autenticado
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return $this->unauthorizedResponse('Usuario no autenticado');
            }

            // Validar datos
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ], [
                'current_password.required' => 'La contraseña actual es obligatoria.',
                'new_password.required' => 'La nueva contraseña es obligatoria.',
                'new_password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
                'new_password.confirmed' => 'La confirmación de contraseña no coincide.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Verificar contraseña actual
            if (!Hash::check($request->current_password, $user->password)) {
                Log::warning('Intento de cambio de contraseña con contraseña actual incorrecta (Datero)', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                ]);
                
                return $this->errorResponse('La contraseña actual es incorrecta', null, 422);
            }

            // Verificar que la nueva contraseña sea diferente a la actual
            if (Hash::check($request->new_password, $user->password)) {
                return $this->errorResponse('La nueva contraseña debe ser diferente a la contraseña actual', null, 422);
            }

            // Actualizar contraseña
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            Log::info('Contraseña cambiada exitosamente (Datero)', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse(null, 'Contraseña actualizada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al cambiar contraseña (Datero)', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
            ]);
            
            return $this->serverErrorResponse($e, 'Error al cambiar la contraseña');
        }
    }
}

