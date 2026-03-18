<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AllClientsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $clients)
    {
    }

    public function collection(): Collection
    {
        return $this->clients;
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Telefono',
            'Documento',
            'Ciudad',
            'Modo alta',
            'Asesor asignado',
            'Creado por',
            'Ultima interaccion',
            'Fecha registro',
        ];
    }

    public function map($client): array
    {
        $activity = $client->activities->first();
        $document = $client->document_type && $client->document_number
            ? $client->document_type . ' ' . $client->document_number
            : ($client->document_number ?? '-');

        $lastInteraction = $activity?->title ?? 'Sin actividad';
        if ($activity?->start_date) {
            $lastInteraction .= ' (' . $activity->start_date->format('d/m/Y') . ')';
        }

        return [
            $client->name ?? '',
            $client->phone ?? '',
            $document,
            $client->city?->name ?? '-',
            $client->create_mode ? ucfirst($client->create_mode) : '-',
            $client->assignedAdvisor?->name ?? 'Sin asignar',
            $client->createdBy?->name ?? '-',
            $lastInteraction,
            $client->created_at?->format('d/m/Y H:i') ?? '',
        ];
    }
}
