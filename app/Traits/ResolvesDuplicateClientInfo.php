<?php

namespace App\Traits;

use App\Models\Client;
use Carbon\Carbon;

trait ResolvesDuplicateClientInfo
{
    /**
     * Localiza el cliente existente por teléfono o documento y devuelve datos para respuestas de duplicado.
     *
     * @return array<string, mixed>|null
     */
    protected function getDuplicateOwnerInfo(?string $phone, ?string $documentNumber): ?array
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
            ->orderBy('id')
            ->first();

        if (!$client) {
            return null;
        }

        $field = $normalizedPhone && $client->phone === $normalizedPhone ? 'phone' : 'document_number';

        $createdAt = $client->created_at;
        $tz = (string) config('app.timezone');
        $registeredAtIso = $createdAt
            ? $createdAt->copy()->timezone($tz)->toIso8601String()
            : null;

        $payload = [
            'client_id' => $client->id,
            'client_name' => $client->name,
            'registered_at' => $registeredAtIso,
            'field' => $field,
        ];

        $owner = $client->createdBy ?: $client->assignedAdvisor;
        if ($owner) {
            $payload['owner_name'] = $owner->name;
            $payload['owner_user_id'] = $owner->id;
            $payload['name'] = $owner->name;
            $payload['user_id'] = $owner->id;
        }

        return $payload;
    }

    protected function buildDuplicateMessage(array $duplicateOwner): string
    {
        $rawName = (string) ($duplicateOwner['client_name'] ?? 'Cliente');
        $clientDisplay = '«' . str_replace(['«', '»'], ['‹', '›'], $rawName) . '»';

        $dateStr = 'fecha desconocida';
        if (!empty($duplicateOwner['registered_at'])) {
            $dateStr = Carbon::parse($duplicateOwner['registered_at'])
                ->timezone((string) config('app.timezone'))
                ->format('d/m/Y H:i');
        }

        $isPhone = ($duplicateOwner['field'] ?? '') === 'phone';

        if ($isPhone) {
            return "Este teléfono ya está registrado a nombre de {$clientDisplay} desde el {$dateStr}.";
        }

        return "Este DNI ya está registrado a nombre de {$clientDisplay} desde el {$dateStr}.";
    }
}
