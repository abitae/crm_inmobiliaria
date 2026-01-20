<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientsExport implements FromCollection, WithHeadings, WithMapping
{
    private Collection $clients;

    public function __construct(Collection $clients)
    {
        $this->clients = $clients;
    }

    public function collection(): Collection
    {
        return $this->clients;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Tipo Documento',
            'Numero Documento',
            'Telefono',
            'Direccion',
            'Fecha Nacimiento',
            'Tipo Cliente',
            'Fuente',
            'Estado',
            'Score',
            'Asesor Asignado',
            'Creado Por',
            'Notas',
            'Fecha Creacion',
            'Ultima Actualizacion',
        ];
    }

    public function map($client): array
    {
        return [
            $client->id,
            $client->name,
            $client->document_type,
            $client->document_number,
            $client->phone,
            $client->address,
            $client->birth_date ? $client->birth_date->format('Y-m-d') : '',
            $client->client_type ? ucwords(str_replace('_', ' ', $client->client_type)) : '',
            $client->source ? ucwords(str_replace('_', ' ', $client->source)) : '',
            $client->status ? ucwords(str_replace('_', ' ', $client->status)) : '',
            $client->score,
            $client->assignedAdvisor ? $client->assignedAdvisor->name : '',
            $client->createdBy ? $client->createdBy->name : '',
            $client->notes,
            $client->created_at ? $client->created_at->format('Y-m-d H:i:s') : '',
            $client->updated_at ? $client->updated_at->format('Y-m-d H:i:s') : '',
        ];
    }
}
