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

    /**
     * Solo se exportan nombre y teléfono.
     * La colección de clientes ya viene filtrada por assigned_advisor_id = usuario actual.
     */
    public function headings(): array
    {
        return [
            'Nombre',
            'Teléfono',
        ];
    }

    public function map($client): array
    {
        return [
            $client->name ?? '',
            $client->phone ?? '',
        ];
    }
}
