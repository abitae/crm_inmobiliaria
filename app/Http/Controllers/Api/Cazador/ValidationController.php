<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\ClientService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidationController extends Controller
{
    use ApiResponse;

    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function validateClient(Request $request)
    {
        if (!$request->filled('create_mode')) {
            $documentNumber = $request->input('document_number');
            $request->merge([
                'create_mode' => empty($documentNumber) ? 'phone' : 'dni'
            ]);
        }

        $rules = $this->clientService->getValidationRules(
            $request->input('id'),
            $request->input('create_mode')
        );
        $messages = $this->clientService->getValidationMessages();

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $duplicateOwner = $this->getDuplicateOwnerInfo(
                $request->input('phone'),
                $request->input('document_number')
            );
            return $this->successResponse([
                'valid' => false,
                'errors' => $validator->errors(),
                'duplicate_owner' => $duplicateOwner,
            ], 'Validacion de cliente fallida');
        }

        return $this->successResponse(['valid' => true], 'Validacion de cliente exitosa');
    }

    public function validateReservation(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            return $this->successResponse([
                'valid' => false,
                'errors' => $validator->errors(),
            ], 'Validacion de reserva fallida');
        }

        return $this->successResponse(['valid' => true], 'Validacion de reserva exitosa');
    }

    public function validateDni(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|size:8|regex:/^[0-9]{8}$/',
        ], [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.size' => 'El DNI debe tener exactamente 8 digitos.',
            'dni.regex' => 'El DNI debe contener solo numeros.',
        ]);

        if ($validator->fails()) {
            return $this->successResponse([
                'valid' => false,
                'errors' => $validator->errors(),
            ], 'Validacion de DNI fallida');
        }

        return $this->successResponse(['valid' => true], 'Validacion de DNI exitosa');
    }

    private function getDuplicateOwnerInfo(?string $phone, ?string $documentNumber): ?array
    {
        $normalizedPhone = $phone ? preg_replace('/[^0-9]/', '', (string) $phone) : null;
        $normalizedDocument = $documentNumber ? trim((string) $documentNumber) : null;

        if ($normalizedDocument !== null && $normalizedDocument !== '') {
            if (preg_match('/^[0-9]+$/', $normalizedDocument)) {
                $normalizedDocument = preg_replace('/[^0-9]/', '', $normalizedDocument);
            } else {
                $normalizedDocument = strtoupper(preg_replace('/\s+/', '', $normalizedDocument));
            }
        }

        if (!$normalizedPhone && !$normalizedDocument) {
            return null;
        }

        $client = Client::query()
            ->where(function ($q) use ($normalizedPhone, $normalizedDocument) {
                if ($normalizedPhone) {
                    $q->orWhere('phone', $normalizedPhone);
                }
                if ($normalizedDocument) {
                    $q->orWhere('document_number', $normalizedDocument);
                }
            })
            ->with(['assignedAdvisor:id,name', 'createdBy:id,name'])
            ->first();

        if (!$client) {
            return null;
        }

        $owner = $client->createdBy ?: $client->assignedAdvisor;
        if (!$owner) {
            return null;
        }

        $field = $normalizedPhone && $client->phone === $normalizedPhone ? 'phone' : 'document_number';

        return [
            'name' => $owner->name,
            'user_id' => $owner->id,
            'client_id' => $client->id,
            'field' => $field,
        ];
    }
}
