<?php

namespace App\Services\Clients;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Servicio de clientes para la aplicación Datero (app móvil/API).
 * Solo opera sobre clientes asignados al datero autenticado (assigned_advisor_id).
 */
class ClientServiceDatero
{
    /**
     * Crear cliente (asignado al usuario autenticado)
     */
    public function createClient(array $formData, ?int $createdById = null): Client
    {
        try {
            $userId = $createdById ?? Auth::id();
            $data = $this->prepareFormData($formData, $userId);
            $createMode = $formData['create_mode'] ?? null;
            if (!$createMode) {
                $createMode = empty($data['document_number']) ? 'phone' : 'dni';
            }
            $this->validateClientData($data, null, $createMode);
            $client = Client::create($data);
            Log::info("Cliente creado exitosamente ID: {$client->id} (Datero)");
            return $client;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al crear cliente (Datero): ' . $e->getMessage());
            throw new \Exception('Error al crear el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar cliente (solo si está asignado al datero)
     */
    public function updateClient(int $id, array $formData): bool
    {
        try {
            if ($id <= 0) {
                throw new \Exception('ID de cliente inválido');
            }
            $client = Client::find($id);
            if (!$client) {
                throw new \Exception('Cliente no encontrado');
            }
            if (Auth::check() && $client->assigned_advisor_id !== Auth::id()) {
                throw new \Exception('No tienes permiso para actualizar este cliente');
            }
            $data = $this->prepareFormData($formData, null, $client);
            $createMode = $formData['create_mode'] ?? null;
            if (!$createMode) {
                $createMode = empty($data['document_number']) ? 'phone' : 'dni';
            }
            $this->validateClientData($data, $id, $createMode);
            $updated = $client->update($data);
            if ($updated) {
                Log::info("Cliente actualizado exitosamente ID: {$id} (Datero)");
                return true;
            }
            return false;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Error al actualizar cliente ID {$id} (Datero): " . $e->getMessage());
            throw new \Exception('Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    public function getFormOptions(): array
    {
        return [
            'document_types' => ['DNI' => 'DNI', 'RUC' => 'RUC', 'CE' => 'Carné de Extranjería', 'PASAPORTE' => 'Pasaporte'],
            'client_types' => ['inversor' => 'Inversor', 'comprador' => 'Comprador', 'empresa' => 'Empresa', 'constructor' => 'Constructor'],
            'sources' => ['redes_sociales' => 'Redes Sociales', 'ferias' => 'Ferias', 'referidos' => 'Referidos', 'formulario_web' => 'Formulario Web', 'publicidad' => 'Publicidad'],
            'statuses' => ['nuevo' => 'Nuevo', 'contacto_inicial' => 'Contacto Inicial', 'en_seguimiento' => 'En Seguimiento', 'cierre' => 'Cierre', 'perdido' => 'Perdido']
        ];
    }

    public function prepareFormData(array $formData, ?int $createdById = null, ?Client $editingClient = null): array
    {
        $formData = $this->sanitizeFormData($formData);
        $createMode = $formData['create_mode'] ?? null;
        if (!$createMode) {
            $createMode = empty($formData['document_number'] ?? null) ? 'phone' : 'dni';
        }
        $data = [
            'name' => $formData['name'],
            'phone' => $formData['phone'],
            'document_type' => $formData['document_type'],
            'document_number' => $formData['document_number'],
            'address' => $formData['address'] ?? null,
            'city_id' => $formData['city_id'] ?? null,
            'birth_date' => $formData['birth_date'] ?? null,
            'client_type' => $formData['client_type'],
            'source' => $formData['source'],
            'status' => $formData['status'] ?? null,
            'score' => $formData['score'] ?? null,
            'notes' => $formData['notes'] ?? null,
            'assigned_advisor_id' => $formData['assigned_advisor_id'] ?? null,
            'create_mode' => $createMode,
        ];
        if (!$editingClient) {
            $userId = $createdById ?? Auth::id();
            if ($userId === null) throw new \Exception('No se puede crear un cliente sin especificar el usuario creador (created_by)');
            $user = User::find($userId);
            if (!$user) throw new \Exception('Usuario creador no encontrado');
            $data['assigned_advisor_id'] = $userId;
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;
            $data['create_type'] = $user->isDatero() ? 'datero' : 'propio';
            if (!isset($formData['status'])) $data['status'] = 'nuevo';
            if (!isset($formData['score'])) $data['score'] = 0;
        } else {
            $data['updated_by'] = Auth::id();
        }
        return $data;
    }

    protected function sanitizeFormData(array $formData): array
    {
        $data = $formData;
        if (isset($data['name'])) $data['name'] = trim($data['name']);
        if (isset($data['phone'])) $data['phone'] = preg_replace('/[^0-9]/', '', (string) $data['phone']);
        if (array_key_exists('create_mode', $data)) {
            $createMode = strtolower(trim((string) $data['create_mode']));
            $data['create_mode'] = $createMode === '' ? null : $createMode;
        }
        if (array_key_exists('document_type', $data)) {
            $documentType = trim((string) $data['document_type']);
            $data['document_type'] = $documentType === '' ? null : strtoupper($documentType);
        }
        if (array_key_exists('document_number', $data)) {
            $documentNumber = trim((string) $data['document_number']);
            if ($documentNumber === '') {
                $data['document_number'] = null;
                return $data;
            }
            $documentType = $data['document_type'] ?? null;
            $data['document_number'] = in_array($documentType, ['DNI', 'RUC'], true)
                ? preg_replace('/[^0-9]/', '', $documentNumber)
                : strtoupper(preg_replace('/\s+/', '', $documentNumber));
        }
        if (isset($data['address'])) $data['address'] = trim((string) $data['address']);
        if (isset($data['notes'])) $data['notes'] = trim((string) $data['notes']);
        return $data;
    }

    public function getValidationRules(?int $clientId = null, ?string $createMode = null): array
    {
        $isPhoneMode = $createMode === 'phone';
        $documentTypeRules = $isPhoneMode ? ['nullable', 'in:DNI,RUC,CE,PASAPORTE'] : ['required', 'in:DNI,RUC,CE,PASAPORTE'];
        $documentNumberRules = $isPhoneMode ? ['nullable', 'string', 'max:20'] : ['required', 'string', 'max:20'];
        if ($isPhoneMode) {
            $uniqueRule = Rule::unique('clients', 'document_number')->where(fn($q) => $q->where('document_number', '!=', '00000000'));
            if ($clientId) $uniqueRule = $uniqueRule->ignore($clientId);
            $documentNumberRules[] = $uniqueRule;
        } else {
            $documentNumberRules[] = $clientId ? Rule::unique('clients', 'document_number')->ignore($clientId) : Rule::unique('clients', 'document_number');
        }
        return [
            'create_mode' => 'required|in:dni,phone',
            'name' => 'required|string|max:255',
            'phone' => $clientId ? ['required', 'string', 'regex:/^9[0-9]{8}$/', 'unique:clients,phone,' . $clientId] : ['required', 'string', 'regex:/^9[0-9]{8}$/', 'unique:clients,phone'],
            'document_type' => $documentTypeRules,
            'document_number' => $documentNumberRules,
            'address' => 'nullable|string|max:500',
            'city_id' => 'required|exists:cities,id',
            'birth_date' => 'required|date',
            'client_type' => 'required|in:inversor,comprador,empresa,constructor',
            'source' => 'required|in:redes_sociales,ferias,referidos,formulario_web,publicidad',
            'status' => 'required|in:nuevo,contacto_inicial,en_seguimiento,cierre,perdido',
            'score' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'assigned_advisor_id' => 'nullable|exists:users,id'
        ];
    }

    public function getValidationMessages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'El teléfono debe tener 9 dígitos y comenzar con el número 9.',
            'phone.unique' => 'El teléfono ya está en uso.',
            'document_type.required' => 'El tipo de documento es obligatorio.',
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.unique' => 'El número de documento ya está en uso.',
            'city_id.required' => 'La ciudad es obligatoria.',
            'birth_date.required' => 'La fecha de nacimiento es obligatoria.',
            'client_type.required' => 'El tipo de cliente es obligatorio.',
            'source.required' => 'El origen es obligatorio.',
            'status.required' => 'El estado es obligatorio.',
            'score.required' => 'La puntuación es obligatoria.',
        ];
    }

    protected function validateClientData(array $data, ?int $clientId = null, ?string $createMode = null): void
    {
        $validator = Validator::make($data, $this->getValidationRules($clientId, $createMode), $this->getValidationMessages());
        if ($validator->fails()) {
            Log::warning('Validación fallida al guardar cliente (Datero)', ['errors' => $validator->errors()->toArray()]);
            throw new ValidationException($validator);
        }
    }
}
