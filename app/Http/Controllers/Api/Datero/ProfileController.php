<?php

namespace App\Http\Controllers\Api\Datero;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use ApiResponse;

    /**
     * Obtener perfil del usuario autenticado
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        try {
            $user = Auth::user();

            return $this->successResponse([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->getRoleName(),
                'is_active' => $user->isActive(),
                'banco' => $user->banco,
                'cuenta_bancaria' => $user->cuenta_bancaria,
                'cci_bancaria' => $user->cci_bancaria,
            ], 'Perfil obtenido exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener el perfil');
        }
    }

    /**
     * Actualizar perfil del usuario autenticado
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            // Validar datos
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => [
                    'sometimes',
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],
                'phone' => 'nullable|string|max:20',
                'banco' => 'nullable|string|max:255',
                'cuenta_bancaria' => 'nullable|string|max:255',
                'cci_bancaria' => 'nullable|string|max:255',
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'email.required' => 'El email es obligatorio.',
                'email.email' => 'El email debe ser una dirección válida.',
                'email.unique' => 'Este email ya está en uso.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Actualizar solo los campos proporcionados
            $updateData = $request->only([
                'name',
                'email',
                'phone',
                'banco',
                'cuenta_bancaria',
                'cci_bancaria'
            ]);

            $user->update($updateData);

            return $this->successResponse(
                [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'banco' => $user->banco,
                    'cuenta_bancaria' => $user->cuenta_bancaria,
                    'cci_bancaria' => $user->cci_bancaria,
                ],
                'Perfil actualizado exitosamente'
            );

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al actualizar el perfil');
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
                return $this->errorResponse('La contraseña actual es incorrecta', null, 422);
            }

            // Actualizar contraseña
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return $this->successResponse(null, 'Contraseña actualizada exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al cambiar la contraseña');
        }
    }
}

